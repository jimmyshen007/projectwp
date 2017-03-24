/**
 * Created by root on 1/3/17.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'cities';

export function getCities() {
    return common.getServices(serviceName);
}

export function getCityById(cityID){
    return common.getServiceById(serviceName, cityID);
}

export function getCitiesByHits(hits){
    return common.getServicesByAttribute(serviceName, 'hits', hits);
}

export function getCitiesByGreaterHitsSorted(servObj){
    return common.getServiceByGreaterHitsSorted(serviceName, servObj);
}

export function addCity(city) {
    return common.addService(serviceName, city);
}

export function editCity(id, city) {
    return common.editService(id, city, serviceName);
}

export function deleteCity(id) {
    return common.deleteService(id, serviceName);
}