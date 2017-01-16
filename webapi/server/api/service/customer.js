/**
 * Created by root on 8/1/16.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'customers';

// *************** Functions to deal with wrap customer object **************

export function getWCustomers() {
    return common.getServices(serviceName);
}

export function getWCustomerByID(id){
    return common.getServiceById(serviceName, id);
}

export function getWCustomersByUserID(userID){
    return common.getServicesByAttribute(serviceName, "userID", userID);
}

export function editWCustomer(id, customer) {
    return common.editService(id, customer, serviceName);
}

//*************************

export function getCustomerByID(id){
    return common.getStripeServicesByAttribute(serviceName, '_id', id);
}

export function getCustomersByUserID(uid){
    return common.getStripeServicesByAttribute(serviceName, 'userID', uid);
}

export function getCustomersByStripeAccID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeAccID', id);
}

export function getCustomerByStripeID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeCusID', id);
}

export function getCustomers(){
    return common.listStripeService(serviceName, {});
}

export function addCustomer(customer) {
    return common.addStripeService(serviceName, customer);
}

export function editCustomerByStripeID(stripeID, customer) {
    return common.updateStripeService(serviceName, 'stripeCusID', stripeID, customer);
}

export function editCustomer(id, customer) {
    return common.updateStripeService(serviceName, '_id', id, customer);
}

export function deleteCustomer(id) {
    return common.delStripeService(serviceName, id);
}