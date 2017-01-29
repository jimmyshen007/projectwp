/**
 * Created by root on 8/1/16.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'transfers';

// *************** Functions to deal with wrap transfer object **************

export function getWTransfers() {
    return common.getServices(serviceName);
}

export function getWTransferByID(id){
    return common.getServiceById(serviceName, id);
}

export function getWTransfersByUserID(userID){
    return common.getServicesByAttribute(serviceName, "userID", userID);
}

export function editWTransfer(id, transfer) {
    return common.editService(id, transfer, serviceName);
}

//*************************

export function getTransferByID(id){
    return common.getStripeServicesByAttribute(serviceName, '_id', id);
}

export function getTransfersByUserID(uid){
    return common.getStripeServicesByAttribute(serviceName, 'userID', uid);
}

export function getTransfersByStripeAccID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeAccID', id);
}

export function getTransferByStripeID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeTransID', id);
}

export function getTransfers(){
    return common.listStripeService(serviceName, {});
}

export function addTransfer(transfer) {
    return common.addStripeService(serviceName, transfer);
}

export function editTransferByStripeID(stripeID, transfer) {
    return common.updateStripeService(serviceName, 'stripeTransID', stripeID, transfer);
}

export function editTransfer(id, transfer) {
    return common.updateStripeService(serviceName, '_id', id, transfer);
}
