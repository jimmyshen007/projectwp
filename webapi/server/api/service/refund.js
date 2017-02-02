/**
 * Created by root on 8/1/16.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'refunds';

// *************** Functions to deal with wrap refund object **************

export function getWRefunds() {
    return common.getServices(serviceName);
}

export function getWRefundByID(id){
    return common.getServiceById(serviceName, id);
}

export function getWRefundsByUserID(userID){
    return common.getServicesByAttribute(serviceName, "userID", userID);
}

export function getWRefundsByPostID(postID){
    return common.getServicesByAttribute(serviceName, "postID", postID);
}

export function getWRefundsByPostAuthorID(postAuthorID){
    return common.getServicesByAttribute(serviceName, "postAuthorID", postAuthorID);
}

export function editWRefund(id, refund) {
    return common.editService(id, refund, serviceName);
}

//*************************

export function getRefundByID(id){
    return common.getStripeServicesByAttribute(serviceName, '_id', id);
}

export function getRefundsByPostID(pid){
    return common.getStripeServicesByAttribute(serviceName, 'postID', pid);
}

export function getRefundsByPostAuthorID(paid){
    return common.getStripeServicesByAttribute(serviceName, 'postAuthorID', paid);
}

export function getRefundsByUserID(uid){
    return common.getStripeServicesByAttribute(serviceName, 'userID', uid);
}

export function getRefundsByStripeAccID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeAccID', id);
}

export function getRefundByStripeID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeRefundID', id);
}

export function getRefunds(){
    return common.listStripeService(serviceName, {});
}

export function addRefund(refund) {
    return common.addStripeService(serviceName, refund);
}

export function editRefundByStripeID(stripeID, refund) {
    return common.updateStripeService(serviceName, 'stripeRefundID', stripeID, refund);
}

export function editRefund(id, refund) {
    return common.updateStripeService(serviceName, '_id', id, refund);
}

export function captureRefund(id, refund) {
    return common.captureStripeRefund(serviceName, '_id', id, refund);
}

export function captureRefundByStripeID(stripeID, refund) {
    return common.captureStripeRefund(serviceName, 'stripeRefundID', stripeID, refund);
}