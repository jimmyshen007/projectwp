/**
 * Created by Jimmy on 29/01/2016.
 */
import m from 'mongoose';
import config from 'config';
import xss from 'xss';
import stripe from 'stripe';
import {db, schemas} from '../db';

let sapi = stripe('sk_test_tpFrMjZ9ivdUjnEeEXDiqq98');

function chooseSchema(serviceName){
    return schemas[serviceName];
}

function preprocessRoute(serviceName, servObj){
    for(let [k,v] of Object.entries(servObj)){
        if(typeof v === 'string'){
            servObj[k] = xss(v);
        }else if(v != null && typeof v === 'object'){
            preprocessRoute(serviceName, v);
        }
    }
}

export function getServices(serviceName){
    let service = m.model(serviceName, chooseSchema(serviceName));
    return service.find().lean().exec();
}

export function getServiceById(serviceName, serviceId){
    let service = m.model(serviceName, chooseSchema(serviceName));
    return service.findOne({_id: serviceId}).lean().exec();
}

//We need to convert attrName as a JSON key. Otherwise,
//JSON passed into find() will literally have 'attrName' as key,
//which is incorrect.
function constructJSONHelper(attrName, attrValue){
    // if(attrName == "_id") {
    let jsonObj = {};
    jsonObj[attrName] = attrValue;
    return jsonObj;
    // }else{
    //     return {attrName: attrValue};
    // }
}

export function getServicesByAttribute(serviceName, attrName, attrValue){
    let service = m.model(serviceName, chooseSchema(serviceName));
    return service.find(constructJSONHelper(attrName, attrValue)).lean().exec();
}

export function getActiveWOrderByGreaterDate(serviceName, attrName, attrValue){
    let service = m.model(serviceName, chooseSchema(serviceName));
    let jsonObj = constructJSONHelper(attrName, {$gt: new Date(attrValue)});
    jsonObj['appStatus'] = { $in: [ 'Approved', 'Completed' ] };
    return service.find(jsonObj).lean().exec();
}

export function addService(serviceName, servObj){
    preprocessRoute(serviceName, servObj);

    let service = m.model(serviceName, chooseSchema(serviceName));
    return service.findOne(servObj).lean().exec().then((data)=> {
        if(!data) {
            let newInstance = new service(servObj);
            return newInstance.save();
        }else{
            let p = new Promise((resolve, reject) => {
                resolve(data);
            });
            return p;
        }
    }, (err) => {
        throw err;
    })
}

export function editService(serviceId, servObj, serviceName) {
    preprocessRoute(serviceName, servObj);

    let service = m.model(serviceName, chooseSchema(serviceName));
    return service.update({_id: serviceId}, {$set: servObj});
}

export function deleteService(serviceId, serviceName) {
    let service = m.model(serviceName, chooseSchema(serviceName));
    return service.remove({_id: serviceId});
}

/**
 *
 * @param obj1: read-only reference object.
 * @param obj2: existing object needs to be updated based on reference object.
 * @param obj3: new object to updating the reference object with a callback.
 * @param key: object property
 */
function setConsistentKVPair(obj1, obj2, obj3, key){
    if(key in obj2){
        obj3[key] = obj2[key];
    }else if(key in obj1){
        obj2[key] = obj1[key];
    }
}

function callStripeAPI(params){
    let service = m.model(params.serviceName, chooseSchema(params.serviceName));
    switch(params.serviceName){
        case 'products':
            switch(params.action) {
                case 'create':
                    return sapi.products.create(params.serviceObj,
                        {stripe_account: params.serviceObj.metadata.stripeAccID}).then(
                        (product) => {
                            let servObj = {"stripeProdID": product.id,
                                "postID": product.metadata.postID,
                                "stripeAccID": product.metadata.stripeAccID};
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'retrieve':
                    return service.find(
                        constructJSONHelper(params.serviceAttrName,
                            params.serviceAttrValue)).sort([['_id', -1]]).then((data) => {
                        let prodPs = [];
                        for (let d of data) {
                            if(d.stripeProdID) {
                                prodPs.push(sapi.products.retrieve(d.stripeProdID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeProdID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                prodPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(prodPs.length > 0) {
                            return Promise.all(prodPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'update':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then(
                        (data) => {
                            return sapi.products.update(data.stripeProdID,
                                params.serviceObj, {stripe_account: data.stripeAccID});
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'list':
                    return service.find().sort([['_id', -1]]).then((data)=> {
                        let prodPs = [];
                        for (let d of data) {
                            if(d.stripeProdID) {
                                prodPs.push(sapi.products.retrieve(d.stripeProdID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        //This is a background task which we do not
                                        //expect when it is done. This is when the
                                        //stripe product is gone, so we need to remove
                                        //the wrapper from our database.
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeProdID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                prodPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(prodPs.length > 0) {
                            return Promise.all(prodPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'delete':
                    return service.findOne({_id: params.serviceId}).then((data) => {
                        let id = data._id;
                        if(data.stripeProdID) {
                            return sapi.products.del(data.stripeProdID,
                                {stripe_account: data.stripeAccID}).then((confirm) => {
                                return service.remove({_id: id});
                            }, (err) => {
                                console.log(err);
                                return service.remove({_id: id});
                            });
                        }else{
                            return service.remove({_id: id});
                        }
                    }, (err) => {
                        throw err;
                    });
            }
            break;
        case 'skus':
            switch(params.action) {
                case 'create':
                    return sapi.skus.create(params.serviceObj,
                        {stripe_account: params.serviceObj.metadata.stripeAccID}).then(
                        (sku) => {
                            let servObj = {"stripeSkuID": sku.id,
                                "postID": sku.metadata.postID,
                                "stripeAccID": sku.metadata.stripeAccID};
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'createPandS':
                    return sapi.products.create(params.serviceObj.product,
                        {stripe_account: params.serviceObj.product.metadata.stripeAccID}).then(
                        (product) => {
                            let servObj = {"stripeProdID": product.id,
                                "postID": product.metadata.postID,
                                "stripeAccID": product.metadata.stripeAccID};
                            let pservice = m.model('products', chooseSchema('products'));
                            let newInstance = new pservice(servObj);
                            return newInstance.save().then(
                                (data) => {
                                    let skuObj = Object.assign({},
                                        params.serviceObj.sku, {"product": data.stripeProdID});
                                    return sapi.skus.create(skuObj,
                                        {stripe_account: params.serviceObj.sku.metadata.stripeAccID}).then(
                                        (sku) => {
                                            let servObj = {"stripeSkuID": sku.id,
                                                "postID": sku.metadata.postID,
                                                "stripeAccID": sku.metadata.stripeAccID};
                                            let newInstance = new service(servObj);
                                            return newInstance.save();
                                        }, (err) => {
                                            throw err;
                                        });
                                },
                                (err) => {
                                    throw err;
                                }
                            );
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'retrieve':
                    // Directly retrieve a stripe object.
                    return service.find(
                        constructJSONHelper(params.serviceAttrName,
                            params.serviceAttrValue)).sort([['_id', -1]]).then((data) => {
                        let skuPs = [];
                        for (let d of data) {
                            if(d.stripeSkuID) {
                                skuPs.push(sapi.skus.retrieve(d.stripeSkuID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeSkuID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                skuPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(skuPs.length > 0) {
                            return Promise.all(skuPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'update':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then(
                        (data) => {
                            return sapi.skus.update(data.stripeSkuID, params.serviceObj,
                                {stripe_account: data.stripeAccID});
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'list':
                    return service.find().sort([['_id', -1]]).then((data)=> {
                        let skuPs = [];
                        for (let d of data) {
                            if(d.stripeSkuID) {
                                skuPs.push(sapi.skus.retrieve(d.stripeSkuID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);

                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeSkuID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                skuPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(skuPs.length > 0) {
                            return Promise.all(skuPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'delete':
                    return service.findOne({_id: params.serviceId}).then((data) => {
                        let id = data._id;
                        if(data.stripeSkuID) {
                            return sapi.skus.del(data.stripeSkuID,
                                {stripe_account: data.stripeAccID}).then((confirm) => {
                                return service.remove({_id: id});
                            }, (err) => {
                                console.log(err);
                                return service.remove({_id: id});
                            });
                        }else{
                            return service.remove({_id: id});
                        }
                    }, (err) => {
                        throw err;
                    });
            }
            break;
        case 'orders':
            switch(params.action){
                case 'create':
                    return sapi.orders.create(params.serviceObj,
                        {stripe_account: params.serviceObj.metadata.stripeAccID}).then(
                        (order) => {
                            let appStatus = "init";
                            if(order.metadata.appStatus){
                                appStatus = order.metadata.appStatus;
                            }
                            let term = "365";
                            if(order.metadata.term){
                                term = order.metadata.term;
                            }
                            let numTenant = 1;
                            if(order.metadata.numTenant){
                                numTenant = order.metadata.numTenant;
                            }
                            let startDate = new Date();
                            if(order.metadata.startDate){
                                startDate = new Date(order.metadata.startDate);
                            }
                            let servObj = {"stripeOrderID": order.id,
                                "postID": order.metadata.postID,
                                "postAuthorID": order.metadata.postAuthorID,
                                "userID": order.metadata.userID,
                                "skuID": order.metadata.skuID,
                                "appStatus": appStatus,
                                "term": term,
                                "numTenant": numTenant,
                                "startDate": startDate,
                                "stripeAccID": order.metadata.stripeAccID};
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'retrieve':
                    // Directly retrieve a stripe object.
                    return service.find(
                        constructJSONHelper(params.serviceAttrName,
                            params.serviceAttrValue)).sort([['_id', -1]]).then((data) => {
                        let orderPs = [];
                        for (let d of data) {
                            if(d.stripeOrderID) {
                                orderPs.push(sapi.orders.retrieve(d.stripeOrderID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);

                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeOrderID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                orderPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(orderPs.length > 0) {
                            return Promise.all(orderPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'update':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then(
                        (data) => {
                            return sapi.orders.update(data.stripeOrderID, params.serviceObj,
                                {stripe_account: data.stripeAccID});
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'list':
                    return service.find().sort([['_id', -1]]).then((data)=> {
                        let orderPs = [];
                        for (let d of data) {
                            if(d.stripeOrderID) {
                                orderPs.push(sapi.orders.retrieve(d.stripeOrderID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);

                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                orderPs.push(fakeP);
                            }
                        }
                        if(orderPs.length > 0) {
                            return Promise.all(orderPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'delete':
                    return service.findOne({_id: params.serviceId}).then((data) => {
                        let id = data._id;
                        if(data.stripeOrderID) {
                            return sapi.orders.del(data.stripeOrderID,
                                {stripe_account: data.stripeAccID}).then((confirm) => {
                                return service.remove({_id: id});
                            }, (err) => {
                                console.log(err);
                                return service.remove({_id: id});
                            });
                        }else{
                            return service.remove({_id: id});
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'pay':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then(
                        (data) => {
                            return sapi.orders.pay(data.stripeOrderID, params.serviceObj,
                                {stripe_account: data.stripeAccID});
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'return':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then(
                        (data) => {
                            return sapi.orders.returnOrder(data.stripeOrderID, params.serviceObj,
                                {stripe_account: data.stripeAccID});
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'addAttach':
                    return service.findOne({_id: params.serviceId}).then(
                        (data) => {
                            let servObj = {};
                            if(!params.serviceObj.metadata){
                                params.serviceObj.metadata = {};
                            }
                            for(const key of ['postID', 'postAuthorID', 'userID', 'skuID', 'appStatus',
                                'term', 'numTenant', 'startDate', 'stripeAccID']){
                                setConsistentKVPair(data, params.serviceObj.metadata, servObj, key);
                            }
                            return sapi.orders.create(params.serviceObj,
                                {stripe_account: params.serviceObj.metadata.stripeAccID}).then(
                                (order) => {
                                    servObj.stripeOrderID = order.id;
                                    return service.update({_id: params.serviceId}, {$set: servObj});
                                }, (err) => {
                                    throw err;
                                });
                        }, (err) => {
                            throw err;
                        });
            }
            break;
        case 'charges':
            switch(params.action) {
                case 'create':
                    return sapi.charges.create(params.serviceObj,
                        {stripe_account: params.serviceObj.metadata.stripeAccID}).then(
                        (charge) => {
                            let servObj = {"stripeChargeID": charge.id,
                                "postID": charge.metadata.postID,
                                "userID": charge.metadata.userID,
                                "postAuthorID": charge.metadata.postAuthorID,
                                "stripeAccID": charge.metadata.stripeAccID};
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'retrieve':
                    return service.find(
                        constructJSONHelper(params.serviceAttrName,
                            params.serviceAttrValue)).sort([['_id', -1]]).then((data) => {
                        let chargePs = [];
                        for (let d of data) {
                            if(d.stripeChargeID) {
                                chargePs.push(sapi.charges.retrieve(d.stripeChargeID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeChargeID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                chargePs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(chargePs.length > 0) {
                            return Promise.all(chargePs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'update':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then(
                        (data) => {
                            return sapi.charges.update(data.stripeChargeID,
                                params.serviceObj, {stripe_account: data.stripeAccID});
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'list':
                    return service.find().sort([['_id', -1]]).then((data)=> {
                        let chargePs = [];
                        for (let d of data) {
                            if(d.stripeChargeID) {
                                chargePs.push(sapi.charges.retrieve(d.stripeChargeID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeChargeID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                chargePs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(chargePs.length > 0) {
                            return Promise.all(chargePs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'capture':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then((data) => {
                            return sapi.charges.capture(data.stripeChargeID, params.serviceObj,
                                {stripe_account: data.stripeAccID});
                    }, (err) => {
                        throw err;
                    });
            }
            break;
        case 'accounts':
            switch(params.action) {
                case 'create':
                    return sapi.accounts.create(params.serviceObj).then(
                        (account) => {
                            let servObj = {"stripeAccID": account.id,
                                "userID": account.metadata.userID};
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'retrieve':
                    return service.find(
                        constructJSONHelper(params.serviceAttrName,
                            params.serviceAttrValue)).sort([['_id', -1]]).then((data) => {
                        let accPs = [];
                        for (let d of data) {
                            if(d.stripeAccID) {
                                accPs.push(sapi.accounts.retrieve(d.stripeAccID).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeAccID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                accPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(accPs.length > 0) {
                            return Promise.all(accPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'update':
                     return service.findOne(constructJSONHelper(params.serviceAttrName,
                         params.serviceAttrValue)).then(
                            (data) => {
                                return sapi.accounts.update(data.stripeAccID, params.serviceObj);
                            }, (err) => {
                                throw err;
                            });
                    break;
                case 'list':
                    return sapi.accounts.list(params.serviceObj);
                    break;
                case 'delete':
                    return service.findOne({_id: params.serviceId}).then((data) => {
                        let id = data._id;
                        if(data.stripeAccID) {
                            return sapi.accounts.del(data.stripeAccID).then((confirm) => {
                                return service.remove({_id: id});
                            }, (err) => {
                                console.log(err);
                                return service.remove({_id: id});
                            });
                        }else{
                            return service.remove({_id: id});
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'reject':
                    if(params.direct) {
                        return sapi.accounts.reject(params.serviceId, params.serviceObj);
                    }else {
                        return service.findOne({_id: params.serviceId}).then((data) => {
                            return sapi.accounts.reject(data.stripeAccID, params.serviceObj);
                        }, (err) => {
                            throw err;
                        });
                    }
            }
        case 'refunds':
            switch(params.action) {
                case 'create':
                    return sapi.refunds.create(params.serviceObj,
                        {stripe_account: params.serviceObj.metadata.stripeAccID}).then(
                        (refund) => {
                            let servObj = {"stripeRefundID": refund.id,
                                "userID": refund.metadata.userID,
                                "stripeAccID": refund.metadata.stripeAccID,
                                "stripeChargeID": refund.charge};
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'retrieve':
                    return service.find(
                        constructJSONHelper(params.serviceAttrName,
                            params.serviceAttrValue)).sort([['_id', -1]]).then((data) => {
                        let refundPs = [];
                        for (let d of data) {
                            if(d.stripeRefundID) {
                                refundPs.push(sapi.refunds.retrieve(d.stripeRefundID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeRefundID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                refundPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(refundPs.length > 0) {
                            return Promise.all(refundPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'update':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then(
                        (data) => {
                            return sapi.refunds.update(data.stripeRefundID,
                                params.serviceObj, {stripe_account: data.stripeAccID});
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'list':
                    return service.find().sort([['_id', -1]]).then((data)=> {
                        let refundPs = [];
                        for (let d of data) {
                            if(d.stripeRefundID) {
                                refundPs.push(sapi.refunds.retrieve(d.stripeRefundID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeRefundID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                refundPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(refundPs.length > 0) {
                            return Promise.all(refundPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
            }
            break;
        case 'cards':
            switch(params.action) {
                case 'create':
                    return sapi.customers.createSource(params.serviceObj.metadata.stripeCusID,
                        params.serviceObj,
                        {stripe_account: params.serviceObj.metadata.stripeAccID}).then(
                        (card) => {
                            let servObj = {"stripeCardID": card.id,
                                "userID": card.metadata.userID,
                                "stripeAccID": card.metadata.stripeAccID,
                                "stripeCusID": card.metadata.stripeCusID};
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'retrieve':
                    return service.find(
                        constructJSONHelper(params.serviceAttrName,
                            params.serviceAttrValue)).sort([['_id', -1]]).then((data) => {
                        let cardPs = [];
                        for (let d of data) {
                            if(d.stripeCardID) {
                                cardPs.push(sapi.customers.retrieveCard(d.stripeCusID, d.stripeCardID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeCardID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                cardPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(cardPs.length > 0) {
                            return Promise.all(cardPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'update':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then(
                        (data) => {
                            return sapi.customers.updateCard(data.stripeCusID, data.stripeCardID,
                                params.serviceObj, {stripe_account: data.stripeAccID});
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'list':
                    return service.find().sort([['_id', -1]]).then((data)=> {
                        let cardPs = [];
                        for (let d of data) {
                            if(d.stripeCardID) {
                                cardPs.push(sapi.customers.retrieveCard(d.stripeCusID, d.stripeCardID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeCardID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                cardPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(cardPs.length > 0) {
                            return Promise.all(cardPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'delete':
                    return service.findOne({_id: params.serviceId}).then((data) => {
                        let id = data._id;
                        if(data.stripeCardID) {
                            return sapi.customers.deleteCard(data.stripeCusID, data.stripeCardID,
                                {stripe_account: data.stripeAccID}).then((confirm) => {
                                return service.remove({_id: id});
                            }, (err) => {
                                console.log(err);
                                return service.remove({_id: id});
                            });
                        }else{
                            return service.remove({_id: id});
                        }
                    }, (err) => {
                        throw err;
                    });
            }
            break;
        case 'customers':
            switch(params.action) {
                case 'create':
                    return sapi.customers.create(params.serviceObj,
                        {stripe_account: params.serviceObj.metadata.stripeAccID}).then(
                        (customer) => {
                            let servObj = {"stripeCusID": customer.id,
                                "userID": customer.metadata.userID,
                                "stripeAccID": customer.metadata.stripeAccID};
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'retrieve':
                    return service.find(
                        constructJSONHelper(params.serviceAttrName,
                            params.serviceAttrValue)).sort([['_id', -1]]).then((data) => {
                        let cusPs = [];
                        for (let d of data) {
                            if(d.stripeCusID) {
                                cusPs.push(sapi.customers.retrieve(d.stripeCusID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeCusID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                cusPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(cusPs.length > 0) {
                            return Promise.all(cusPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'update':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then(
                        (data) => {
                            return sapi.customers.update(data.stripeCusID,
                                params.serviceObj, {stripe_account: data.stripeAccID});
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'list':
                    return service.find().sort([['_id', -1]]).then((data)=> {
                        let cusPs = [];
                        for (let d of data) {
                            if(d.stripeCusID) {
                                cusPs.push(sapi.customers.retrieve(d.stripeCusID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        //This is a background task which we do not
                                        //expect when it is done. This is when the
                                        //stripe customer is gone, so we need to remove
                                        //the wrapper from our database.
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeCusID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                cusPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(cusPs.length > 0) {
                            return Promise.all(cusPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'delete':
                    return service.findOne({_id: params.serviceId}).then((data) => {
                        let id = data._id;
                        if(data.stripeCusID) {
                            return sapi.customers.del(data.stripeCusID,
                                {stripe_account: data.stripeAccID}).then((confirm) => {
                                return service.remove({_id: id});
                            }, (err) => {
                                console.log(err);
                                return service.remove({_id: id});
                            });
                        }else{
                            return service.remove({_id: id});
                        }
                    }, (err) => {
                        throw err;
                    });
            }
            break;
        case 'transfers':
            switch(params.action) {
                case 'create':
                    return sapi.transfers.create(params.serviceObj,
                        {stripe_account: params.serviceObj.metadata.stripeAccID}).then(
                        (transfer) => {
                            let servObj = {"stripeTransID": transfer.id,
                                "userID": transfer.metadata.userID,
                                "stripeAccID": transfer.metadata.stripeAccID
                                };
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'retrieve':
                    return service.find(
                        constructJSONHelper(params.serviceAttrName,
                            params.serviceAttrValue)).sort([['_id', -1]]).then((data) => {
                        let transferPs = [];
                        for (let d of data) {
                            if(d.stripeTransID) {
                                transferPs.push(sapi.transfers.retrieve(d.stripeTransID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeTransID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                transferPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(transferPs.length > 0) {
                            return Promise.all(transferPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'update':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then(
                        (data) => {
                            return sapi.transfers.update(data.stripeTransID,
                                params.serviceObj, {stripe_account: data.stripeAccID});
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'list':
                    return service.find().sort([['_id', -1]]).then((data)=> {
                        let transferPs = [];
                        for (let d of data) {
                            if(d.stripeTransID) {
                                transferPs.push(sapi.transfers.retrieve(d.stripeTransID,
                                    {stripe_account: d.stripeAccID}).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeTransID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                transferPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(transferPs.length > 0) {
                            return Promise.all(transferPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
            }
            break;
        case 'extaccounts':
            switch(params.action) {
                case 'create':
                    return sapi.accounts.createExternalAccount(params.serviceObj.metadata.stripeAccID,
                        params.serviceObj).then(
                        (extaccount) => {
                            let servObj = {"stripeExtaccountID": extaccount.id,
                                "userID": extaccount.metadata.userID,
                                "stripeAccID": extaccount.metadata.stripeAccID
                            };
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'retrieve':
                    return service.find(
                        constructJSONHelper(params.serviceAttrName,
                            params.serviceAttrValue)).sort([['_id', -1]]).then((data) => {
                        let extaccountPs = [];
                        for (let d of data) {
                            if(d.stripeExtaccountID) {
                                extaccountPs.push(sapi.accounts.retrieveExternalAccount(d.stripeAccID,
                                    d.stripeExtaccountID).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeExtaccountID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                extaccountPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(extaccountPs.length > 0) {
                            return Promise.all(extaccountPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'update':
                    return service.findOne(constructJSONHelper(params.serviceAttrName,
                        params.serviceAttrValue)).then(
                        (data) => {
                            return sapi.accounts.updateExternalAccount(data.stripeAccID,
                                data.stripeExtaccountID,
                                params.serviceObj);
                        }, (err) => {
                            throw err;
                        });
                    break;
                case 'list':
                    return service.find().sort([['_id', -1]]).then((data)=> {
                        let extaccountPs = [];
                        for (let d of data) {
                            if(d.stripeExtaccountID) {
                                extaccountPs.push(sapi.accounts.retrieveExternalAccount(d.stripeAccID,
                                    d.stripeExtaccountID).then(
                                    (data) => {
                                        return new Promise((resolve, reject) =>{
                                            resolve(data);
                                        });
                                    }, (err) => {
                                        console.log(err);
                                        let fakeP = new Promise((resolve, reject) => {
                                            resolve({});
                                        });
                                        return fakeP;
                                    }
                                ));
                            }else{
                                //This is a background task which we do not
                                //expect when it is done. This is when the stripeExtaccountID
                                //is undefined, then the wrapper is a invalid one, and
                                //we need to remove it.
                                let fakeP = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                extaccountPs.push(service.remove({_id: d._id}).then(
                                    (data) => {
                                        return fakeP;
                                    }, (err)=> {
                                        return fakeP;
                                    }));
                            }
                        }
                        if(extaccountPs.length > 0) {
                            return Promise.all(extaccountPs);
                        }else {
                            let p = new Promise((resolve, reject) => {
                                resolve([{}]);
                            });
                            return p;
                        }
                    }, (err) => {
                        throw err;
                    });
                    break;
                case 'delete':
                    return service.findOne({_id: params.serviceId}).then((data) => {
                        let id = data._id;
                        if(data.stripeExtaccountID) {
                            return sapi.accounts.deleteExternalAccount(data.stripeAccID,
                                data.stripeExtaccountID).then((confirm) => {
                                return service.remove({_id: id});
                            }, (err) => {
                                console.log(err);
                                return service.remove({_id: id});
                            });
                        }else{
                            return service.remove({_id: id});
                        }
                    }, (err) => {
                        throw err;
                    });
            }
            break;
    }
}

export function addStripeService(serviceName, serviceObj){
    preprocessRoute(serviceName, serviceObj);
    let params = {'serviceName': serviceName, 'serviceObj': serviceObj, 'action': 'create'};
    return callStripeAPI(params);
}

/**
 * serviceName: generally corresponding to object collection name such as products, orders..
 * serviceAttrName: an object attribute name such as postID.
 * serviceValue: an object attribute value such as the value of postID.
 */
export function getStripeServicesByAttribute(serviceName, serviceAttrName, serviceAttrValue){
    let params = {'serviceName': serviceName,
        'serviceAttrName': serviceAttrName,
        'serviceAttrValue': serviceAttrValue,
        'action': 'retrieve'};
    return callStripeAPI(params);
}

/**
 * serviceName: generally corresponding to object collection name such as products, orders..
 * serviceAttrName: an object attribute name such as postID.
 * serviceValue: an object attribute value such as the value of postID.
 * serviceObj: an object to update the original content.
 */
export function updateStripeService(serviceName, serviceAttrName, serviceAttrValue, serviceObj){
    preprocessRoute(serviceName, serviceObj);
    let params = {'serviceName': serviceName, 'serviceAttrName': serviceAttrName,
        'serviceAttrValue': serviceAttrValue, 'serviceObj': serviceObj, 'action': 'update'};
    return callStripeAPI(params);
}

export function listStripeService(serviceName, serviceObj){
    preprocessRoute(serviceName, serviceObj);
    let params = {'serviceName': serviceName, 'serviceObj': serviceObj, 'action': 'list'};
    return callStripeAPI(params);
}

export function delStripeService(serviceName, serviceId){
    let params = {'serviceName': serviceName, 'serviceId': serviceId, 'action': 'delete'};
    return callStripeAPI(params);
}

export function addAttachStripeOrder(serviceName, serviceId, serviceObj){
    preprocessRoute(serviceName, serviceObj);
    let params = {'serviceName': serviceName, 'serviceId': serviceId, 'serviceObj': serviceObj,
        'action': 'addAttach'};
    return callStripeAPI(params);
}

export function addProductAndSku(serviceName, serviceObj){
    preprocessRoute(serviceName, serviceObj);
    let params = {'serviceName': serviceName, 'serviceObj': serviceObj,
        'action': 'createPandS'};
    return callStripeAPI(params);
}

export function payStripeOrder(serviceName, serviceAttrName, serviceAttrValue, serviceObj){
    preprocessRoute(serviceName, serviceObj);
    let params = {'serviceName': serviceName, 'serviceAttrName': serviceAttrName,
        'serviceAttrValue': serviceAttrValue, 'serviceObj': serviceObj, 'action': 'pay'};
    return callStripeAPI(params);
}

export function returnStripeOrder(serviceName, serviceAttrName, serviceAttrValue, serviceObj){
    preprocessRoute(serviceName, serviceObj);
    let params = {'serviceName': serviceName, 'serviceAttrName': serviceAttrName,
        'serviceAttrValue': serviceAttrValue, 'serviceObj': serviceObj, 'action': 'return'};
    return callStripeAPI(params);
}

export function rejectStripeAccount(serviceName, serviceId, serviceObj, direct=false){
    preprocessRoute(serviceName, serviceObj);
    let params = {'serviceName': serviceName, 'serviceId': serviceId, 'serviceObj': serviceObj,
        'direct': direct, 'action': 'reject'};
    return callStripeAPI(params);
}

export function captureStripeCharge(serviceName, serviceAttrName, serviceAttrValue, serviceObj){
    preprocessRoute(serviceName, serviceObj);
    let params = {'serviceName': serviceName, 'serviceAttrName': serviceAttrName,
        'serviceAttrValue': serviceAttrValue, 'serviceObj': serviceObj, 'action': 'capture'};
    return callStripeAPI(params);
}
