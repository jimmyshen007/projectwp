import * as service from './service';

function handle_response(res, data, err, errmsg){
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
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Orders Error");
    });
}

export function getOrderByID(req, res) {
    let ret = service.getOrderById(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Order Error");
    });
}

export function addOrder(req, res) {
    let ret = service.addOrder(req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Add Order Error");
    });
}

export function editOrder(req, res) {
    let ret = service.editOrder(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Edit Order Error");
    });
}

export function deleteOrder(req, res) {
    let ret = service.deleteOrder(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Delete Order Error");
    });
}

export function getCustomerByID(req, res) {
    let ret = service.getCustomerById(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Customer Error");
    });
}

export function getCustomers(req, res) {
    let ret = service.getCustomers();
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Customers Error");
    });
}

export function addCustomer(req, res) {
    let ret = service.addCustomer(req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Add Customer Error");
    });
}

export function editCustomer(req, res) {
    let ret = service.editCustomer(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Edit Customer Error");
    });
}

export function deleteCustomer(req, res) {
    let ret = service.deleteCustomer(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Delete Customer Error");
    });
}

export function addFavoriteElem(req, res){
    let ret = service.addFavoriteElem(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data.toJSON(), null, null);
    }, (err) => {
        handle_response(res, null, err, "Add Favorite Element Error");
    });
}

export function delFavoriteElem(req, res){
    let ret = service.delFavoriteElem(req.params.id, req.params.fid);
    ret.then((data) => {
        handle_response(res, data.toJSON(), null, null);
    }, (err) => {
        handle_response(res, null, err, "Del Favorite Element Error");
    });
}

export function getOrderByUserID(){

}

export function getOrderByPostID(){

}

export function getCustomerByUserID(){

}
//TODO
//All other API calls come here.