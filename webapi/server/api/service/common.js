/**
 * Created by Jimmy on 29/01/2016.
 */
import m from 'mongoose';
import config from 'config';
import xss from 'xss';
import stripe from 'stripe';
import {db, orderSchema, favoriteSchema, productSchema, skuSchema} from '../db';

let sapi = stripe('sk_test_tpFrMjZ9ivdUjnEeEXDiqq98');
function chooseSchema(serviceName){
    switch(serviceName){
        case 'orders':
            return orderSchema;
        case 'favorites':
            return favoriteSchema;
        case 'products':
            return productSchema;
        case 'skus':
            return skuSchema;
        default:
            undefined;
    }
}

function preprocessRoute(serviceName, servObj){
    for(let [k,v] of Object.entries(servObj)){
        if(typeof v === 'string'){
            servObj[k] = xss(v);
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

export function addService(serviceName, servObj){
    preprocessRoute(serviceName, servObj);

    let service = m.model(serviceName, chooseSchema(serviceName));
    let newInstance = new service(servObj);
    return newInstance.save();
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

function callStripeAPI(params){
    let service = m.model(params.serviceName, chooseSchema(params.serviceName));
    switch(params.serviceName){
        case 'products':
            switch(params.action) {
                case 'create':
                    return sapi.products.create(params.serviceObj).then(
                        (product) => {
                            let servObj = {"stripeProdID": product.id,
                                "postID": product.metadata.postID};
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                case 'retrieve':
                    // Directly retrieve a stripe object.
                    if(params.direct){
                        return sapi.products.retrieve(params.serviceAttrValue);
                    }else {
                        return service.find(
                            constructJSONHelper(params.serviceAttrName,
                                params.serviceAttrValue)).then((data) => {
                                let ids = [];
                                for (let d of data) {
                                    ids.push(d.stripeProdID);
                                }
                                if(ids.length > 0) {
                                    return sapi.products.list({"ids": ids});
                                }else {
                                    let p = new Promise((resolve, reject) => {
                                       resolve({});
                                    });
                                    return p;
                                }
                            }, (err) => {
                                throw err;
                            });
                    }
                case 'update':
                    if(params.direct) {
                        return sapi.products.update(params.serviceId, params.serviceObj);
                    }else{
                        return service.findOne({_id: params.serviceId}).then(
                            (data) => {
                                return sapi.products.update(data.stripeProdID, params.serviceObj);
                            }, (err) => {
                                throw err;
                            });
                    }
                case 'list':
                    return service.find().then((data)=> {
                        let ids = [];
                        for(let d of data){
                            ids.push(d.stripeProdID);
                        }
                        return sapi.products.list(Object.assign({}, {"ids": ids}, params.serviceObj));
                    }, (err) => {
                        throw err;
                    });
                case 'delete':
                    return service.findOne({_id: params.serviceId}).then((data) => {
                        let id = data._id;
                        return sapi.products.del(data.stripeProdID).then((confirm) => {
                                return service.remove({_id: id});
                            }, (err) => {
                                console.log(err);
                                return service.remove({_id: id});
                            });
                        }, (err) => {
                            throw err;
                        });
            }
        case 'skus':
            switch(params.action) {
                case 'create':
                    return sapi.skus.create(params.serviceObj).then(
                        (sku) => {
                            let servObj = {"stripeSkuID": sku.id,
                                "postID": sku.metadata.postID};
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                case 'retrieve':
                    // Directly retrieve a stripe object.
                    if(params.direct){
                        return sapi.skus.retrieve(params.serviceAttrValue);
                    }else {
                        return service.find(
                            constructJSONHelper(params.serviceAttrName,
                                params.serviceAttrValue)).then((data) => {
                            let ids = [];
                            for (let d of data) {
                                ids.push(d.stripeSkuID);
                            }
                            if(ids.length > 0) {
                                return sapi.skus.list({"ids": ids});
                            }else {
                                let p = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                return p;
                            }
                        }, (err) => {
                            throw err;
                        });
                    }
                case 'update':
                    if(params.direct) {
                        return sapi.skus.update(params.serviceId, params.serviceObj);
                    }else{
                        return service.findOne({_id: params.serviceId}).then(
                            (data) => {
                                return sapi.skus.update(data.stripeSkuID, params.serviceObj);
                            }, (err) => {
                                throw err;
                            });
                    }
                case 'list':
                    return service.find().then((data)=> {
                        let ids = [];
                        for(let d of data){
                            ids.push(d.stripeSkuID);
                        }
                        return sapi.skus.list(Object.assign({}, {"ids": ids}, params.serviceObj));
                    }, (err) => {
                        throw err;
                    });
                case 'delete':
                    return service.findOne({_id: params.serviceId}).then((data) => {
                        let id = data._id;
                        return sapi.skus.del(data.stripeSkuID).then((confirm) => {
                            return service.remove({_id: id});
                        }, (err) => {
                            console.log(err);
                            return service.remove({_id: id});
                        });
                    }, (err) => {
                        throw err;
                    });
            }
        case 'orders':
            switch(params.action){
                case 'create':
                    return sapi.orders.create(params.serviceObj).then(
                        (order) => {
                            let servObj = {"stripeOrderID": order.id,
                                "postID": order.metadata.postID,
                                "postAuthorID": order.metadata.postAuthorID,
                                "userID": order.metadata.userID,
                                "skuID": order.metadata.skuID};
                            let newInstance = new service(servObj);
                            return newInstance.save();
                        }, (err) => {
                            throw err;
                        });
                case 'retrieve':
                    // Directly retrieve a stripe object.
                    if(params.direct){
                        return sapi.orders.retrieve(params.serviceAttrValue);
                    }else {
                        return service.find(
                            constructJSONHelper(params.serviceAttrName,
                                params.serviceAttrValue)).then((data) => {
                            let ids = [];
                            for (let d of data) {
                                ids.push(d.stripeOrderID);
                            }
                            if(ids.length > 0) {
                                return sapi.orders.list({"ids": ids});
                            }else {
                                let p = new Promise((resolve, reject) => {
                                    resolve({});
                                });
                                return p;
                            }
                        }, (err) => {
                            throw err;
                        });
                    }
                case 'update':
                    if(params.direct) {
                        return sapi.orders.update(params.serviceId, params.serviceObj);
                    }else{
                        return service.findOne({_id: params.serviceId}).then(
                            (data) => {
                                return sapi.orders.update(data.stripeOrderID, params.serviceObj);
                            }, (err) => {
                                throw err;
                            });
                    }
                case 'list':
                    return service.find().then((data)=> {
                        let ids = [];
                        for(let d of data){
                            ids.push(d.stripeOrderID);
                        }
                        return sapi.orders.list(Object.assign({}, {"ids": ids}, params.serviceObj));
                    }, (err) => {
                        throw err;
                    });
                case 'delete':
                    return service.findOne({_id: params.serviceId}).then((data) => {
                        let id = data._id;
                        return sapi.orders.del(data.stripeOrderID).then((confirm) => {
                            return service.remove({_id: id});
                        }, (err) => {
                            console.log(err);
                            return service.remove({_id: id});
                        });
                    }, (err) => {
                        throw err;
                    });
                case 'pay':
                    if(params.direct) {
                        return sapi.orders.pay(params.serviceId, params.serviceObj);
                    }else{
                        return service.findOne({_id: params.serviceId}).then(
                            (data) => {
                                return sapi.orders.pay(data.stripeOrderID, params.serviceObj);
                            }, (err) => {
                                throw err;
                            });
                    }
                case 'return':
                    if(params.direct) {
                        return sapi.orders.returnOrder(params.serviceId, params.serviceObj);
                    }else{
                        return service.findOne({_id: params.serviceId}).then(
                            (data) => {
                                return sapi.orders.returnOrder(data.stripeOrderID, params.serviceObj);
                            }, (err) => {
                                throw err;
                            });
                    }
            }
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
 * direct: indicate whether to retrieve stripe object directly. If true, serviceAttrValue
 * must be stripe object ID.
 */
export function getStripeServicesByAttribute(serviceName, serviceAttrName, serviceAttrValue,
  direct=false){
    let params = {'serviceName': serviceName,
                 'serviceAttrName': serviceAttrName,
                 'serviceAttrValue': serviceAttrValue,
                 'direct': direct,
                 'action': 'retrieve'};
    return callStripeAPI(params);
}

/**
 * serviceName: generally corresponding to object collection name such as products, orders..
 * serviceId: an object ID.
 * serviceObj: an object to update the original content
 * direct: indicate whether to retrieve stripe object directly. If true, serviceId
 * must be stripe object ID.
 */
export function updateStripeService(serviceName, serviceId, serviceObj, direct=false){
    preprocessRoute(serviceName, serviceObj);
    let params = {'serviceName': serviceName, 'serviceId': serviceId,
        'serviceObj': serviceObj, 'direct': direct, 'action': 'update'};
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

export function payStripeOrder(serviceName, serviceId, serviceObj){
    preprocessRoute(serviceName, servObj);
    let params = {'serviceName': serviceName, 'serviceId': serviceId, 'serviceObj': serviceObj, 'action': 'pay'};
    return callStripeAPI(params);
}

export function returnStripeOrder(serviceName, serviceId, serviceObj){
    preprocessRoute(serviceName, servObj);
    let params = {'serviceName': serviceName, 'serviceId': serviceId, 'serviceObj': serviceObj, 'action': 'return'};
    return callStripeAPI(params);
}