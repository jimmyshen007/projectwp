/**
 * Created by root on 8/1/16.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'products';

export function getProductsByPostID(postID) {
    return common.getStripeServicesByAttribute(serviceName, 'postID', postID);
}

export function getProductByStripeID(stripeID){
    return common.getStripeServicesByAttribute(serviceName, 'stripeProdID', stripeID, true);
}

export function getProductByID(id){
    return common.getStripeServicesByAttribute(serviceName, '_id', id);
}

export function getProducts(){
    return common.listStripeService(serviceName, {});
}

export function getWrapProducts(){
    return common.getServices(serviceName);
}

export function getWrapProductByID(id){
    return common.getServiceById(serviceName, id);
}

export function getWrapProductsByPostID(postId){
    return common.getServicesByAttribute(serviceName, 'postID', postId);
}

export function editWrapProduct(id, product){
    return common.editService(id, product, serviceName);
}

export function addProduct(prod) {
    return common.addStripeService(serviceName, prod);
}

export function editProductByStripeID(stripeID, product) {
    return common.updateStripeService(serviceName, stripeID, product, true);
}

export function editProduct(id, product) {
    return common.updateStripeService(serviceName, id, product);
}

export function deleteProduct(id) {
    return common.delStripeService(serviceName, id);
}