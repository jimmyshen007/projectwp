/**
 * Created by Jimmy on 29/01/2016.
 */
import m from 'mongoose';
import config from 'config';
import xss from 'xss';
import {db, orderSchema, favoriteSchema} from '../db';
function chooseSchema(serviceName){
    switch(serviceName){
        case 'orders':
            return orderSchema;
        case 'favorites':
            return favoriteSchema;
        default:
            undefined;
    }
}

function preprocessRoute(serviceName, servObj){
    switch(serviceName){
        case 'orders':
            servObj.postID = xss(servObj.postID);
            servObj.postAuthorID = xss(servObj.postAuthorID);
            servObj.userID = xss(servObj.userID);
            servObj.orderValue = xss(servObj.orderValue);
            servObj.valueUnit = xss(servObj.valueUnit);
            servObj.orderStatus = xss(servObj.orderStatus);
            break;
        default:
            undefined;
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
    let jsonObj = {};
    jsonObj[attrName] = attrValue;
    return jsonObj;
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

