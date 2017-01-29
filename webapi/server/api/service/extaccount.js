/**
 * Created by root on 8/1/16.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'extaccounts';

// *************** Functions to deal with wrap extaccount object **************

export function getWExtaccounts() {
    return common.getServices(serviceName);
}

export function getWExtaccountByID(id){
    return common.getServiceById(serviceName, id);
}

export function getWExtaccountsByUserID(userID){
    return common.getServicesByAttribute(serviceName, "userID", userID);
}

export function editWExtaccount(id, extaccount) {
    return common.editService(id, extaccount, serviceName);
}

//*************************

export function getExtaccountByID(id){
    return common.getStripeServicesByAttribute(serviceName, '_id', id);
}

export function getExtaccountsByUserID(uid){
    return common.getStripeServicesByAttribute(serviceName, 'userID', uid);
}

export function getExtaccountsByStripeAccID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeAccID', id);
}

export function getExtaccountByStripeID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeExtaccountID', id);
}

export function getExtaccounts(){
    return common.listStripeService(serviceName, {});
}

export function addExtaccount(extaccount) {
    return common.addStripeService(serviceName, extaccount);
}

export function editExtaccountByStripeID(stripeID, extaccount) {
    return common.updateStripeService(serviceName, 'stripeExtaccountID', stripeID, extaccount);
}

export function editExtaccount(id, extaccount) {
    return common.updateStripeService(serviceName, '_id', id, extaccount);
}

export function deleteExtaccount(id) {
    return common.delStripeService(serviceName, id);
}