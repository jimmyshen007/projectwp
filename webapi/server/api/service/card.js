/**
 * Created by root on 8/1/16.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'cards';

// *************** Functions to deal with wrap card object **************

export function getWCards() {
    return common.getServices(serviceName);
}

export function getWCardByID(id){
    return common.getServiceById(serviceName, id);
}

export function getWCardsByUserID(userID){
    return common.getServicesByAttribute(serviceName, "userID", userID);
}

export function editWCard(id, card) {
    return common.editService(id, card, serviceName);
}

//*************************

export function getCardByID(id){
    return common.getStripeServicesByAttribute(serviceName, '_id', id);
}

export function getCardsByUserID(uid){
    return common.getStripeServicesByAttribute(serviceName, 'userID', uid);
}

export function getCardsByStripeAccID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeAccID', id);
}

export function getCardsByStripeCusID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeCusID', id);
}

export function getCardByStripeID(id){
    return common.getStripeServicesByAttribute(serviceName, 'stripeCardID', id);
}

export function getCards(){
    return common.listStripeService(serviceName, {});
}

export function addCard(card) {
    return common.addStripeService(serviceName, card);
}

export function editCardByStripeID(stripeID, card) {
    return common.updateStripeService(serviceName, 'stripeCardID', stripeID, card);
}

export function editCard(id, card) {
    return common.updateStripeService(serviceName, '_id', id, card);
}

export function deleteCard(id) {
    return common.delStripeService(serviceName, id);
}