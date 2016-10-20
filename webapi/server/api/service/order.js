import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'orders';

// *************** Functions to deal with wrap order object **************

export function getWOrders() {
    return common.getServices(serviceName);
}

export function getWOrderByID(id){
    return common.getServiceById(serviceName, id);
}

export function getWOrdersByUserID(userID){
    return common.getServicesByAttribute(serviceName, "userID", userID);
}

export function getWOrdersByPostID(postID){
    return common.getServicesByAttribute(serviceName, "postID", postID);
}

export function getWOrdersByPostAuthorID(postAuthorID){
    return common.getServicesByAttribute(serviceName, "postAuthorID", postAuthorID);
}

export function getWOrdersBySkuID(skuID){
    return common.getServicesByAttribute(serviceName, "skuID", skuID);
}

export function addWOrder(order){
    return common.addService(serviceName, order);
}

/*
 * Note: useful to attach an orphan stripe order into a wrap order by updating its
 * stripeOrderID.
 */
export function editWOrder(id, order) {
    return common.editService(id, order, serviceName);
}

/*
 * function to add and attach a stripe order to a wrap order object.
 * This provides a shortcut to create stripe order after wrap order
 * has been approved.
 */
export function addAttachSOrder(id, sorder){
    return common.addAttachStripeOrder(serviceName, id, sorder);
}

export function getOrders() {
    return common.listStripeService(serviceName, {});
}

export function getOrderByStripeID(stripeID){
    return common.getStripeServicesByAttribute(serviceName, 'stripeOrderID', stripeID);
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

export function getOrdersByStripeAccID(stripeAccID){
    return common.getStripeServicesByAttribute(serviceName, 'stripeAccID', stripeAccID);
}

export function addOrder(order) {
    return common.addStripeService(serviceName, order);
}

export function editOrder(id, order) {
    return common.updateStripeService(serviceName, '_id', id, order);
}

export function editOrderByStripeID(stripeID, order) {
    return common.updateStripeService(serviceName, 'stripeOrderID', stripeID, order);
}

export function deleteOrder(id) {
    return common.delStripeService(serviceName, id);
}

export function payOrder(id, servObject){
    return common.payStripeOrder(serviceName, '_id', id, servObject);
}

export function returnOrder(id, servObject){
    return common.returnStripeOrder(serviceName,'_id', id, servObject);
}

export function payOrderByStripeID(id, servObject) {
    return common.payStripeOrder(serviceName, 'stripeOrderID', id, servObject);
}

export function returnOrderByStripeID(id, servObject) {
    return common.returnStripeOrder(serviceName, 'stripeOrderID', id, servObject);
}