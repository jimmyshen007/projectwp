/**
 * Created by root on 8/1/16.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'charges';

// *************** Functions to deal with wrap charge object **************

export function getWCharges() {
    return common.getServices(serviceName);
}

export function getWChargeByID(id){
    return common.getServiceById(serviceName, id);
}

export function getWChargesByUserID(userID){
    return common.getServicesByAttribute(serviceName, "userID", userID);
}

export function getWChargesByPostID(postID){
    return common.getServicesByAttribute(serviceName, "postID", postID);
}

export function getWChargesByPostAuthorID(postAuthorID){
    return common.getServicesByAttribute(serviceName, "postAuthorID", postAuthorID);
}

export function editWCharge(id, charge) {
    return common.editService(id, charge, serviceName);
}

//*************************

export function getChargeByID(id){
    return common.getStripeServicesByAttribute(serviceName, '_id', id);
}

export function getChargesByPostID(pid){
    return common.getStripeServicesByAttribute(serviceName, 'postID', pid);
}

export function getChargesByPostAuthorID(paid){
    return common.getStripeServicesByAttribute(serviceName, 'postAuthorID', paid);
}

export function getChargesByUserID(uid){
    return common.getStripeServicesByAttribute(serviceName, 'userID', uid);
}

export function getChargesByStripeAccID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeAccID', id);
}

export function getChargeByStripeID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeChargeID', id);
}

export function getCharges(){
    return common.listStripeService(serviceName, {});
}

export function addCharge(charge) {
    return common.addStripeService(serviceName, charge);
}

export function editChargeByStripeID(stripeID, charge) {
    return common.updateStripeService(serviceName, 'stripeChargeID', stripeID, charge);
}

export function editCharge(id, charge) {
    return common.updateStripeService(serviceName, '_id', id, charge);
}

export function captureCharge(id, charge) {
    return common.captureStripeCharge(serviceName, '_id', id, charge);
}

export function captureChargeByStripeID(stripeID, charge) {
    return common.captureStripeCharge(serviceName, 'stripeChargeID', stripeID, charge);
}