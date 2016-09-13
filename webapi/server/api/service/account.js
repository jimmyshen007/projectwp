/**
 * Created by root on 8/1/16.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'accounts';

export function getAccountByUserID(userID) {
    return common.getStripeServicesByAttribute(serviceName, 'userID', userID);
}

export function getAccountByStripeID(stripeID){
    return common.getStripeServicesByAttribute(serviceName, 'stripeAccID', stripeID, true);
}

export function getAccountByID(id){
    return common.getStripeServicesByAttribute(serviceName, '_id', id);
}

export function getAccounts(){
    return common.listStripeService(serviceName, {});
}

export function addAccount(acc) {
    return common.addStripeService(serviceName, acc);
}

export function editAccountByStripeID(stripeID, acc) {
    return common.updateStripeService(serviceName, stripeID, acc, true);
}

export function editAccount(id, acc) {
    return common.updateStripeService(serviceName, id, acc);
}

export function deleteAccount(id) {
    return common.delStripeService(serviceName, id);
}

export function rejectAccount(id, servObj){
    return common.rejectStripeAccount(serviceName, id, servObj);
}

export function rejectAccountByStripeID(id, servObj){
    return common.rejectStripeAccount(serviceName, id, servObj, true);
}