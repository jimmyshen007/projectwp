import * as common from './common';
import m from 'mongoose';
import {db, orderSchema, customerSchema} from '../db';

let serviceName = 'customers';

export function getCustomers() {
    return common.getServices(serviceName);
}

export function getCustomerById(orderId){
    return common.getServiceById(serviceName, orderId);
}

export function addCustomer(order) {
    return common.addService(serviceName, order);
}

export function editCustomer(id, order) {
    return common.editService(id, order, serviceName);
}

export function deleteCustomer(id) {
    return common.deleteService(id, serviceName);
}

export function addFavoriteElem(id, favor){
    let customer = m.model('customers', customerSchema);
    return customer.findOne({_id: id}, function(err, data){
        if(err){
            console.log(err);
            return err;
        }else{
            data.favoriteList.push(favor);
            return data.save();
        }
    });
}

export function delFavoriteElem(id, favorID){
    let customer = m.model('customers', customerSchema);
    return customer.findOne({_id: id}, function(err, data){
        if(err){
            console.log(err);
            return err;
        }else{
            data.favoriteList.id(favorID).remove();
            return data.save();
        }
    });
}