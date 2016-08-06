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

export function getOrdersByUserID(req, res){
    let ret = service.getOrdersByUserID(req.params.uid);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Orders by UserID Error");
    });
}

export function getOrdersByPostID(req, res){
    let ret = service.getOrdersByPostID(req.params.pid);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Orders by PostID Error");
    });
}

export function getOrdersByPostAuthorID(req, res){
    let ret = service.getOrdersByPostAuthorID(req.params.paid);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Orders by PostAuthorID Error");
    });
}

export function getFavoritesByUserID(req, res){
    let ret = service.getFavoritesByUserID(req.params.uid);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Favorites by UserID Error");
    });
}

export function getFavorites(req, res) {
    let ret = service.getFavorites();
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Favoriates Error");
    });
}

export function getFavoriteByID(req, res) {
    let ret = service.getFavoriteById(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Favorite Error");
    });
}

export function addFavorite(req, res) {
    let ret = service.addFavorite(req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Add Favorite Error");
    });
}

export function editFavorite(req, res) {
    let ret = service.editFavorite(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Edit Favorite Error");
    });
}

export function deleteFavorite(req, res) {
    let ret = service.deleteFavorite(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Delete Favorite Error");
    });
}









//The following functions are deprecated.
/*

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
*/

//TODO
//All other API calls come here.