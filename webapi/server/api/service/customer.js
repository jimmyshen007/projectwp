import * as common from './common';

let serviceName = 'customers';

export function getCustomers() {
    return common.getServices(serviceName);
}

export function getCustomerById(orderId){
    return common.getServiceById(serviceName, orderId);
}

export function addCustomer(order) {
    return common.addService(serviceName, order);
}

export function editCustomer(id, order) {
    return common.editService(id, order, serviceName);
}

export function deleteCustomer(id) {
    return common.deleteService(id, serviceName);
}