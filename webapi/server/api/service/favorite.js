/**
 * Created by root on 7/16/16.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'favorites';

export function getFavorites() {
    return common.getServices(serviceName);
}

export function getFavoriteById(favorID){
    return common.getServiceById(serviceName, favorID);
}

export function getFavoritesByUserID(userID){
    return common.getServicesByAttribute(serviceName, 'userID', userID);
}

export function addFavorite(favor) {
    return common.addService(serviceName, favor);
}

export function editFavorite(id, favor) {
    return common.editService(id, favor, serviceName);
}

export function deleteFavorite(id) {
    return common.deleteService(id, serviceName);
}