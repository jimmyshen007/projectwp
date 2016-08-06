import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'orders';

export function getOrders() {
    return common.getServices(serviceName);
}

export function getOrderById(orderId){
    return common.getServiceById(serviceName, orderId);
}

export function getOrdersByUserID(userID){
    return common.getServicesByAttribute(serviceName, "userID", userID);
}

export function getOrdersByPostID(postID){
    return common.getServicesByAttribute(serviceName, "postID", postID);
}

export function getOrdersByPostAuthorID(postAuthorID){
    return common.getServicesByAttribute(serviceName, "postAuthorID", postAuthorID);
}

export function addOrder(order) {
    return common.addService(serviceName, order);
}

export function editOrder(id, order) {
    return common.editService(id, order, serviceName);
}

export function deleteOrder(id) {
    return common.deleteService(id, serviceName);
}