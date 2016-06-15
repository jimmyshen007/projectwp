/**
 * Created by Jimmy on 29/01/2016.
 */
import m from 'mongoose';
import config from 'config';
import xss from 'xss';

let db = null;
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

export function connect(callback) {
    if(!db) {
        m.connect('mongodb://localhost/' + config.get('mongodb.db'));
        db = m.connection;
        db.on('error', console.error.bind(console, 'connection error:'));
    }
    else{
        db.once('open', () => {
            callback();
        });
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
    let servInstances = null;
    connect(()=>{
        let service = m.model(serviceName, chooseSchema(serviceName));
        service.find({}, (err, instances) => {
            if(!err) {
                servInstances = instances;
            } else {
                console.log(err);
            }
        });
    });
    return servInstances;
}

export function getServiceById(serviceName, serviceId){
    let servInstances = null;
    connect(()=>{
        let service = m.model(serviceName, chooseSchema(serviceName));
        service.find({_id: serviceId}, (err, instances) => {
            if(!err) {
                servInstances = instances;
            } else {
                console.log(err);
            }
        });
    });
    return servInstances;
}

export function addService(serviceName, servObj){
    let completed = false;
    preprocessRoute(serviceName, servObj);
    connect(()=>{
        let service = m.model(serviceName, chooseSchema(serviceName));
        let newInstance = new service(servObj);
        newInstance.save(function (err) {
            if (err) {
                console.log(err);
            } else {
                completed = true;
            }
        });
    });
    return {result: completed};
}

export function editService(serviceId, servObj, serviceName) {
    let completed = false;
    preprocessRoute(serviceName, servObj);
    connect(()=>{
        let service = m.model(serviceName, chooseSchema(serviceName));
        service.update({_id: serviceId}, {$set: servObj}, (err, raw) => {
            if(err) {
                console.log(err);
            } else {
                completed = true;
            }
        });
    });
    return {result: completed};
}

export function deleteService(serviceId, serviceName) {
    let completed = false;
    connect(()=>{
        let service = m.model(serviceName, chooseSchema(serviceName));
        service.remove({_id: serviceId}, (err) => {
            if(err) {
                console.log(err);
            } else {
                completed = true;
            }
        });
    });
    return {result: completed};
}