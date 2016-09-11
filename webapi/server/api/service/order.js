import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'orders';

export function getOrders() {
    return common.listStripeService(serviceName, {});
}

export function getOrderByStripeID(stripeID){
    return common.getStripeServicesByAttribute(serviceName, 'stripeOrderID', stripeID, true);
}

export function getOrderById(id){
    return common.getStripeServicesByAttribute(serviceName, '_id', id);
}

export function getOrdersByUserID(userID){
    return common.getStripeServicesByAttribute(serviceName, "userID", userID);
}

export function getOrdersByPostID(postID){
    return common.getStripeServicesByAttribute(serviceName, "postID", postID);
}

export function getOrdersByPostAuthorID(postAuthorID){
    return common.getStripeServicesByAttribute(serviceName, "postAuthorID", postAuthorID);
}

export function getOrdersBySkuID(skuID){
    return common.getStripeServicesByAttribute(serviceName, "skuID", skuID);
}

export function addOrder(order) {
    return common.addStripeService(serviceName, order);
}

export function editOrder(id, order) {
    return common.updateStripeService(serviceName, id, order);
}

export function editOrderByStripeID(stripeID, order) {
    return common.updateStripeService(serviceName, stripeID, order, true);
}

export function deleteOrder(id) {
    return common.delStripeService(serviceName, id);
}

export function payOrder(id, servObject){
    return common.payStripeOrder(serviceName, id, servObject);
}

export function returnOrder(id, servObject){
    return common.returnStripeOrder(serviceName, id, servObject);
}

export function payOrderByStripeID(id, servObject) {
    return common.payStripeOrder(serviceName, id, servObject, true);
}

export function returnOrderByStripeID(id, servObject) {
    return common.returnStripeOrder(serviceName, id, servObject, true);
}