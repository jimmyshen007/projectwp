import * as common from './common';

let serviceName = 'orders';

export function getOrders() {
    return common.getService(serviceName);
}

export function getOrderById(orderId){
    return common.getServiceById(serviceName, orderId);
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