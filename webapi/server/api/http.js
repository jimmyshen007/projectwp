import * as service from './service';
const OK = 0;

function handle_response(err, res, data, errmsg){
    if(!err) {
        if(data) {
            res.json({"data": data});
        }else{
            res.json({});
        }
    } else {
        console.log(err);
        res.status(400);
        res.json({"errors": errmsg});
    }
}

export function getOrders(req, res) {
    let ret = service.getOrders();
    ret.then((data, err) => {
        handle_response(err, res, data, "Get Orders Error");
    });
}

export function addOrder(req, res) {
    let ret = service.addOrder(req.body);
    ret.then((err) => {
        handle_response(err, res, null, "Add Order Error");
    });
}

export function editOrder(req, res) {
    let ret = service.editOrder(req.params.id, req.body);
    ret.then((raw, err) => {
        handle_response(err, res, null, "Edit Order Error");
    });
}

export function deleteOrder(req, res) {
    let ret = service.deleteOrder(req.params.id);
    ret.then((err) => {
        handle_response(err, res, null, "Delete Order Error");
    });
}

export function getCustomers(req, res) {
    let ret = service.getCustomers();
    ret.then((data, err) => {
        handle_response(err, res, data, "Get Customers Error");
    });}

export function addCustomer(req, res) {
    let ret = service.addCustomer(req.body);
    ret.then((err) => {
        handle_response(err, res, null, "Add Customer Error");
    });}

export function editCustomer(req, res) {
    let ret = service.editCustomer(req.params.id, req.body);
    ret.then((raw, err) => {
        handle_response(err, res, null, "Edit Customer Error");
    });}

export function deleteCustomer(req, res) {
    let ret = service.deleteCustomer(req.params.id);
    ret.then((err) => {
        handle_response(err, res, null, "Delete Customer Error");
    });
}
//TODO
//All other API calls come here.