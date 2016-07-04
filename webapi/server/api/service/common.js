/**
 * Created by Jimmy on 29/01/2016.
 */
import m from 'mongoose';
import config from 'config';
import xss from 'xss';

let Schema = m.Schema;
let orderSchema = new Schema({
    postID: String,
    customerID: String,
    status: String
});

let customerSchema = new Schema({
    userID: String,
    favoriteList: [{type: String, value: String}]
});

function chooseSchema(serviceName){
    switch(serviceName){
        case 'orders':
            return orderSchema;
        case 'customers':
            return customerSchema;
        default:
            undefined;
    }
}

function preprocessRoute(serviceName, servObj){
    switch(serviceName){
        case 'orders':
            servObj.postID = xss(servObj.postID);
            servObj.status = xss(servObj.status);
            servObj.customerID = xss(servObj.customerID);
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
    return service.find({_id: serviceId}).lean().exec();
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