/**
 * Created by root on 1/3/17.
 */
import * as common from './common';

//This is the mongodb collection name.
let serviceName = 'schools';

export function getSchools() {
    return common.getServices(serviceName);
}

export function getSchoolById(schoolID){
    return common.getServiceById(serviceName, schoolID);
}

export function getSchoolsByHits(hits){
    return common.getServicesByAttribute(serviceName, 'hits', hits);
}

export function getSchoolsByGreaterHitsSorted(servObj){
    return common.getServiceByGreaterHitsSorted(serviceName, servObj);
}

export function addSchool(school) {
    return common.addService(serviceName, school);
}

export function editSchool(id, school) {
    return common.editService(id, school, serviceName);
}

export function deleteSchool(id) {
    return common.deleteService(id, serviceName);
}