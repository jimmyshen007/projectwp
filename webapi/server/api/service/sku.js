/**
 * Created by root on 8/7/16.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'skus';

export function getWrapSkus(){
    return common.getServices(serviceName);
}

export function getWrapSkuByID(id){
    return common.getServiceById(serviceName, id);
}

export function getWrapSkusByPostID(postId){
    return common.getServicesByAttribute(serviceName, 'postID', postId);
}

export function editWrapSku(id, sku){
    return common.editService(id, sku, serviceName);
}

export function getSkusByPostID(postID) {
    return common.getStripeServicesByAttribute(serviceName, 'postID', postID);
}

export function getSkuByStripeID(stripeID){
    return common.getStripeServicesByAttribute(serviceName, 'stripeSkuID', stripeID, true);
}

export function getSkuByID(id){
    return common.getStripeServicesByAttribute(serviceName, '_id', id);
}

export function getSkus(){
    return common.listStripeService(serviceName, {});
}

export function addSku(sku) {
    return common.addStripeService(serviceName, sku);
}

export function editSkuByStripeID(stripeID, sku) {
    return common.updateStripeService(serviceName, stripeID, sku, true);
}

export function editSku(id, sku) {
    return common.updateStripeService(serviceName, id, sku);
}

export function deleteSku(id) {
    return common.delStripeService(serviceName, id);
}