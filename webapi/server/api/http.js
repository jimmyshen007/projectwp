import * as service from './service';
import stripe from 'stripe';
let sapi = stripe('sk_test_tpFrMjZ9ivdUjnEeEXDiqq98');

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

function handleRet(ret, res, err_msg){
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, err_msg);
    });
}

export function testPayOrder(req, res){
    sapi.tokens.create({
        email: 'maidongxi1@example.com',
        card: {
            "number": '4242424242424242',
            "exp_month": 12,
            "exp_year": 2017,
            "cvc": '123'
        }
    }, function(err, token) {
        // asynchronously called
        let ret = service.payOrder(req.params.id, {source: token.id, email: 'maidongxi1@example.com'});
        ret.then((data) => {
            handle_response(res, data, null, null);
        }, (err) => {
            handle_response(res, null, err, "Pay Order by ID Error");
        });
    });
}

export function testReturnOrder(req, res){
    let ret = service.returnOrder(req.params.id, {});
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Return Order by ID Error");
    });
}

/**
 *
 * Sample input: API_INITIAL_PATH/orders
 * Sample data response:
 * {"data":[{},{},{"id":"or_18fv2ABfJLgIcXuM8gmA5vhv","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470590758,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantit
 */
export function getOrders(req, res) {
    let ret = service.getOrders();
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Orders Error");
    });
}

/**
 *
 * Sample input: API_INITIAL_PATH/orders/xxx
 * Sample data response:
 * {"data":{"object":"list","data":[{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470647822,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18g9sYBfJLgIcXuMhGIBgKMb"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470647822}],"has_more":false,"url":"/v1/orders"}}
 */
export function getOrderByID(req, res) {
    let ret = service.getOrderById(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Order by ID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/orders with JSON: {"currency": "USD", "metadata": {"postID": "1234", "postAuthorID": "a123", "userID": "u123", "skuID": "s123", "stripeAccID": "acct_18m2ZBLUxBeddbgv", "startDate": "2016-11-12", "term": "365", "numTenant": 2}, "items": [{"type": "sku", "parent": "sku_8xpkjNkOjKlb8D"}]}
 * Sample data response:
 * {"data":{"__v":0,"stripeOrderID":"or_196WFcLUxBeddbgv8oCRUaTU","postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","appStatus":"init","term":"365","numTenant":2,"startDate":"2016-11-12T00:00:00.000Z","stripeAccID":"acct_18m2ZBLUxBeddbgv","_id":"58082c3840749eb7c1810cc9"}}
 */
export function addOrder(req, res) {
    let ret = service.addOrder(req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Add Order Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/orders/687798901afafea with JSON: {"metadata": {"testattr": "testvalue"}}
 * Sample data response:
 * {"data":{"id":"or_196WFcLUxBeddbgv8oCRUaTU","object":"order","amount":2000,"amount_returned":null,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"charge":null,"created":1476930348,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":2000,"currency":"usd","description":"testp111","parent":"sku_9P7tjxNgD6Osr8","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","stripeAccID":"acct_18m2ZBLUxBeddbgv","startDate":"2016-11-12","term":"365","numTenant":"2","testattr":"testvalue"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_196WFcLUxBeddbgv8oCRUaTU"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1476960721}}
 */
export function editOrder(req, res) {
    let ret = service.editOrder(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Edit Order Error");
    });
}

/**
 *
 * Sample input: API_INITIAL_PATH/orders/stripe/xxx with JSON {"metadata": {"testattr2": "testvalue2"}}
 * Sample data response:
 * {"data":{"id":"or_196WFcLUxBeddbgv8oCRUaTU","object":"order","amount":2000,"amount_returned":null,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"charge":null,"created":1476930348,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":2000,"currency":"usd","description":"testp111","parent":"sku_9P7tjxNgD6Osr8","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","stripeAccID":"acct_18m2ZBLUxBeddbgv","startDate":"2016-11-12","term":"365","numTenant":"2","testattr":"testvalue","testattr2":"testvalue2"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_196WFcLUxBeddbgv8oCRUaTU"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1476961111}}
 */
export function editOrderByStripeID(req, res){
    let ret = service.editOrderByStripeID(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Edit Order By stripeID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/orders/xxx
 * Sample data response:
 *
 */
export function deleteOrder(req, res) {
    let ret = service.deleteOrder(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Delete Order Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/orders/stripe/xxx
 * Sample data response:
 * {"data":{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470647822,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","testattr":"testvalue","testattr2":"testvalue2"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18g9sYBfJLgIcXuMhGIBgKMb"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470658350}}
 */
export function getOrderByStripeID(req, res){
    let ret = service.getOrderByStripeID(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Order by StripeID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/orders/stripeAcc/xxx
 * Sample data response:
 * {"data":[{"id":"or_196KhNLUxBeddbgvxHMFWhj7","object":"order","amount":1000,"amount_returned":null,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null
 */
export function getOrdersByStripeAccID(req, res){
    let ret = service.getOrdersByStripeAccID(req.params.id);
    handleRet(ret, res, "Get Orders by StripeAccID Error");
}

/**
 * Sample input: API_INITIAL_PATH/orders/sku/xxx
 * Sample data response:
 * {"data":[{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":3000,"application":null,"application_fee":null,"charge":"ch_18rUQ2BfJLgIcXuMS20NKwCy"
 */
export function getOrdersBySkuID(req, res){
    let ret = service.getOrdersBySkuID(req.params.kid);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Order by SkuID Error");
    });
}


/**
 * Sample input: API_INITIAL_PATH/orders/user/xxx
 * Sample data response:
 * {"data":[{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":3000,"application":null,"application_fee":null,"charge":"ch_18rUQ2BfJLgIcXuMS20NKwCy",
 */
export function getOrdersByUserID(req, res){
    let ret = service.getOrdersByUserID(req.params.uid);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Orders by UserID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/orders/post/xxx
 * Sample data response:
 * {"data":[{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":3000,"application":null,"application_fee":null,"charge":"ch_18rUQ2BfJLgIcXuMS20NKwCy",
 */
export function getOrdersByPostID(req, res){
    let ret = service.getOrdersByPostID(req.params.pid);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Orders by PostID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/orders/postAuthor/xxx
 * Sample data response:
 * {"data":[{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":3000,"application":null,"application_fee":null,"charge":"ch_18rUQ2BfJLgIcXuMS20NKwCy",
 */
export function getOrdersByPostAuthorID(req, res){
    let ret = service.getOrdersByPostAuthorID(req.params.paid);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Orders by PostAuthorID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/orders/pay/xxx for details, please see stripe docs
 * A test case is included here in testPayOrder.
 * Sample data response:
 * {"data":{"id":"or_196WFcLUxBeddbgv8oCRUaTU","object":"order","amount":2000,"amount_returned":null,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"charge":"ch_196fwMLUxBeddbgvX4YT3Ndu","created":1476930348,"currency":"usd","customer":null,"email":"maidongxi1@example.com","items":[{"object":"order_item","amount":2000,"currency":"usd","description":"testp111","parent":"sku_9P7tjxNgD6Osr8","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","stripeAccID":"acct_18m2ZBLUxBeddbgv","startDate":"2016-11-12","term":"365","numTenant":"2","testattr":"testvalue","testattr2":"testvalue2"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_196WFcLUxBeddbgv8oCRUaTU"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"paid","status_transitions":null,"updated":1476967594}}
 */
export function payOrder(req, res){
    let ret = service.payOrder(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Pay Order Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/orders/return/xxxx
 * A test case is included in testReturnOrder.
 * Sample data response:
 * {"data":{"id":"orret_196g4MLUxBeddbgvSHW6Ryam","object":"order_return","amount":2000,"created":1476968090,"currency":"usd","items":[{"object":"order_item","amount":2000,"currency":"usd","description":"testp111","parent":"sku_9P7tjxNgD6Osr8","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"order":"or_196WFcLUxBeddbgv8oCRUaTU","refund":"re_196g4MLUxBeddbgv5jAhC9EX"}}
 */
export function returnOrder(req, res){
    let ret = service.returnOrder(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Return Order Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/orders/pay/stripe/xxx for details, please see stripe docs.
 * Sample data response:
 * {"data":{"id":"or_196WFcLUxBeddbgv8oCRUaTU","object":"order","amount":2000,"amount_returned":null,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"charge":"ch_196fwMLUxBeddbgvX4YT3Ndu","created":1476930348,"currency":"usd","customer":null,"email":"maidongxi1@example.com","items":[{"object":"order_item","amount":2000,"currency":"usd","description":"testp111","parent":"sku_9P7tjxNgD6Osr8","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","stripeAccID":"acct_18m2ZBLUxBeddbgv","startDate":"2016-11-12","term":"365","numTenant":"2","testattr":"testvalue","testattr2":"testvalue2"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_196WFcLUxBeddbgv8oCRUaTU"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"paid","status_transitions":null,"updated":1476967594}}
 */
export function payOrderByStripeID(req, res){
    let ret = service.payOrderByStripeID(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Pay Order By Stripe ID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/orders/return/stripe/xxxx
 * Sample data response:
 * {"data":{"id":"orret_18rVn5BfJLgIcXuMeeqwZQLc","object":"order_return","amount":3000,"created":1473353659,"currency":"usd","items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"order":"or_18g9sYBfJLgIcXuMhGIBgKMb","refund":"re_18rVn5BfJLgIcXuMTZ1Eewk1"}}
 */
export function returnOrderByStripeID(req, res){
    let ret = service.returnOrderByStripeID(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Return Order By Stripe ID Error");
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

//**** W in front of those functions indicates it is dealing with wrapper
//**** objects. The reason to just appending W in front of a noun in error msg
//**** is to hide our architecture details.

/*
 * Sample input: API_INITIAL_PATH/wproducts
 * Sample output: {"data":[{"_id":"578a2e36f83da040654db989", ...}]}
 */
export function getWProducts(req, res){
    let ret = service.getWrapProducts();
    handleRet(ret, res, "Get WProduct Error");
}

/*
 * Sample input: API_INITIAL_PATH/wproducts/xxxxxxxxxx
 * Sample output: {"data":{"_id":"578a2e36f83da040654db989", ...}}
 */
export function getWProductByID(req, res){
    let ret = service.getWrapProductByID(req.params.id);
    handleRet(ret, res, "Get WProduct By ID Error");
}

/*
 * Sample input: API_INITIAL_PATH/wproducts/post/xxxx
 * Sample output: {"data":[{"_id":"578a2e36f83da040654db989", ...}]}
 */
export function getWProductsByPostID(req, res){
    let ret = service.getWrapProductsByPostID(req.params.pid);
    handleRet(ret, res, "Get WProduct By PostID Error");
}

/*
 * Sample input: API_INITIAL_PATH/wproducts/xxxxxxxxx
 * Sample output: {"data":{"ok":1,"nModified":1,"n":1}}
 */
export function editWProduct(req, res){
    let ret = service.editWrapProduct(req.params.id, req.body);
    handleRet(ret, res, "Edit WProduct Error");
}

export function getWSkus(req, res){
    let ret = service.getWrapSkus();
    handleRet(ret, res, "Get WSku Error");
}

export function getWSkuByID(req, res){
    let ret = service.getWrapSkuByID(req.params.id);
    handleRet(ret, res, "Get WSku By ID Error");
}

export function getWSkusByPostID(req, res){
    let ret = service.getWrapSkusByPostID(req.params.pid);
    handleRet(ret, res, "Get WSku By PostID Error");
}

export function editWSku(req, res){
    let ret = service.editWrapSku(req.params.id, req.body);
    handleRet(ret, res, "Edit WSku Error");
}

export function getWOrders(req, res) {
    let ret = service.getWOrders();
    handleRet(ret, res, "Get WOrders Error");
}

export function getWOrderByID(req, res){
    let ret = service.getWOrderByID(req.params.id);
    handleRet(ret, res, "Get WOrders By ID Error");
}

export function getWOrdersByUserID(req, res){
    let ret = service.getWOrdersByUserID(req.params.uid);
    handleRet(ret, res, "Get WOrders By UserID Error");
}

export function getWOrdersByPostID(req, res){
    let ret = service.getWOrdersByPostID(req.params.pid);
    handleRet(ret, res, "Get WOrders By PostID Error");
}

export function getWOrdersByPostAuthorID(req, res){
    let ret = service.getWOrdersByPostAuthorID(req.params.paid);
    handleRet(ret, res, "Get WOrders By PostAuthorID Error");
}

export function getWOrdersBySkuID(req, res){
    let ret = service.getWOrdersBySkuID(req.params.kid);
    handleRet(ret, res, "Get WOrders By SkuID Error");
}

/**
 * Sample input: API_INITPATH/worders/addSOrder/5afareafagareaceaf with JSON:
 * {"postID": "1234", "postAuthorID": "a123", "userID": "u123", "skuID": "s123", "stripeAccID": "acct_18m2ZBLUxBeddbgv", "startDate": "2016-11-15", "term": "100", "numTenant": 2}
 *
 * Note: Date type variable such as startDate can be automatically constructed from string provided.
 * i.e. "2016-11-15" will be constructed as Date format automatically.
 */
export function addWOrder(req, res){
    let ret = service.addWOrder(req.body);
    handleRet(ret, res, "Add WOrder Error");
}

export function getWAccounts(req, res){
    let ret = service.getWrapAccounts();
    handleRet(ret, res, "Get WAccounts Error");
}

export function getWAccountsByID(req, res){
    let ret = service.getWrapAccountsByID(req.params.id);
    handleRet(ret, res, "Get WAccounts By ID Error");
}

export function getWAccountsByUserID(req, res){
    let ret = service.getWrapAccountsByUserID(req.params.uid);
    handleRet(ret, res, "Get WAccounts By UserID Error");
}

export function editWAccount(req, res){
    let ret = service.editWrapAccount(req.params.id, req.body);
    handleRet(ret, res, "Edit WAccounts Error");
}

/*
 * Note: useful to attach an orphan stripe order into a wrap order by updating its
 * stripeOrderID.
 */
export function editWOrder(req, res) {
    let ret = service.editWOrder(req.params.id, req.body);
    handleRet(ret, res, "Edit WOrder Error");
}

/*
 * function to add and attach a stripe order to a wrap order object.
 * This provides a shortcut to create stripe order after wrapper order
 * has been approved. S in front of Order in error msg is to hide architecture
 * details.
 *
 * Sample input: API_INITIAL_PATH/worders/addSOrder/xxxxx with JSON: {"currency": "USD", "metadata": {"postID": "1234", "postAuthorID": "a123", "userID": "u123", "skuID": "s123"}, "items": [{"type": "sku", "parent": "sku_8xpkjNkOjKlb8D"}]}
 * Sample output: {"data":{"ok":1,"nModified":1,"n":1}}
 *
 */
export function addAttachSOrder(req, res){
    let ret = service.addAttachSOrder(req.params.id, req.body);
    handleRet(ret, res, "AddAttach SOrder Error");
}
//*********************


/**
 * Sample input: API_INITIAL_PATH/products with json: {"name": "testp111", "shippable": false, "metadata": {"postID": "1234", "stripeAccID": "acct_18m2ZBLUxBeddbgv"}}
 * Sample data response:
 * {"data":{"__v":0,"stripeProdID":"prod_9O2oLiUyjaht4j","postID":"1234","stripeAccID":"acct_18m2ZBLUxBeddbgv","_id":"58039ffec483310518bc1ba7"}}
 */
export function addProduct(req, res) {
    let ret = service.addProduct(req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Add Product Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/products/post/123
 * Sample data response:
 * {"data":[{"id":"prod_8wdxsWrVRGR1cf","object":"product","active":true,"attributes":[],....
 *
 */
export function getProductsByPostID(req, res) {
    let ret = service.getProductsByPostID(req.params.pid);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Product By PostID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/products/57eab5ccd747b0ad7651618f
 * Sample data response:
 * {"data":[{"id":"prod_9GxuuKJc067ikI","object":"product","active":true,"attributes":[],"caption":null,"created":1474999581,"deactivate_on":[],"description":null,"images":[],
 */
export function getProductByID(req, res) {
    let ret = service.getProductByID(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Product By ID Error");
    });
}

/**
 *
 * Sample input: API_INITIAL_PATH/products
 * Sample data response:
 * {"data":[{"id":"prod_8wdwq9JsEqAibO","object":"product","active":true,"attributes":[],"caption":null,"created":1470312529,"deactivate_on":[],"description":null,"images":[
 */
export function getProducts(req, res){
    let ret = service.getProducts();
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Products Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/products/stripe/prod_xxx
 * Sample data response:
 * {"data":[{"id":"prod_8wdwq9JsEqAibO","object":"product","active":true,"attributes":[],"caption":null,"created":1470312529,"deactivate_on":[],
 */
export function getProductByStripeID(req, res){
    let ret = service.getProductByStripeID(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Product By StripeID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/products/stripeAcc/acc_fxfsfafesf
 * Sample data response:
 * {"data":[{"id":"prod_9OOiCXK5LBttvk","object":"product","active":true,"attributes":[],"caption":null,"created":1476713810,"description":null,
 */
export function getProductsByStripeAccID(req, res){
    let ret = service.getProductsByStripeAccID(req.params.id);
    handleRet(ret, res, "Get Product By StripeAccID Error");
}

/**
 * Sample input: API_INITIAL_PATH/products/stripe/prod_xxx with json: {"name": "test2222222", "metadata": {"postID": "1234"}}
 * Sample data response:
 * {"data":{"id":"prod_8weFswEoY6i2IW","object":"product","active":true,"attributes":[],"caption":null,"created":1470313671,"deactivate_on":[],"description":null,"images":[],"livemode":false,"metadata":{"postID":"1234"},"name":"test2222222","package_dimensions":null,"shippable":true,"skus":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/skus?product=prod_8weFswEoY6i2IW&active=true"},"updated":1470397595,"url":null}}
 */
export function editProductByStripeID(req, res){
    let ret = service.editProductByStripeID(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Edit Product By StripeID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/products/xxx with json: {"name": "test2222222", "metadata": {"postID": "1234"}}
 * Sample data response:
 * {"data":{"id":"prod_8weFswEoY6i2IW","object":"product","active":true,"attributes":[],"caption":null,"created":1470313671,"deactivate_on":[],"description":null,"images":[],"livemode":false,"metadata":{"postID":"1234"},"name":"test2222222","package_dimensions":null,"shippable":true,"skus":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/skus?product=prod_8weFswEoY6i2IW&active=true"},"updated":1470397595,"url":null}}
 */
export function editProduct(req, res) {
    let ret = service.editProduct(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Edit Product Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/products/xxx
 * Sample data response:
 * {"data":{"ok":1,"n":1}}
 */
export function deleteProduct(req, res) {
    let ret = service.deleteProduct(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Delete Product Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/skus/post/xxx
 * Sample data response:
 * {"data":[{"id":"sku_9P7tjxNgD6Osr8","object":"sku","active":true,"attributes":{},"created":1476881850,"currency":"usd","image":null
 */
export function getSkusByPostID(req, res) {
    let ret = service.getSkusByPostID(req.params.pid);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Skus by postID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/skus/stripe/sku_8xpkjNkOjKlb8D
 * Sample data response:
 * {"data":[{"id":"sku_9P7tjxNgD6Osr8","object":"sku","active":true,"attributes":{},"created":1476881850,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234","stripeAccID":"acct_18m2ZBLUxBeddbgv"},"package_dimensions":null,"price":2000,"product":"prod_9P6HnSMCHPBk1Y","updated":1476881850}]}
 */
export function getSkuByStripeID(req, res){
    let ret = service.getSkuByStripeID(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Sku by StripeID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/skus/stripeAcc/acc_8xpkjNkOjKlb8D
 * Sample data response:
 * {"data":[{"id":"sku_9P7tjxNgD6Osr8","object":"sku","active":true,"attributes":{},"created":1476881850,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234","stripeAccID":"acct_18m2ZBLUxBeddbgv"},"package_dimensions":null,"price":2000,"product":"prod_9P6HnSMCHPBk1Y","updated":1476881850}]}
 */
export function getSkusByStripeAccID(req, res){
    let ret = service.getSkusByStripeAccID(req.params.id);
    handleRet(ret, res, "Get Sku By StripeAccID Error");
}

/**
 * Sample input: API_INITIAL_PATH/skus/xxx
 * Sample data response:
 * {"data":[{"id":"sku_9P7tjxNgD6Osr8","object":"sku","active":true,"attributes":{},"created":1476881850,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234","stripeAccID":"acct_18m2ZBLUxBeddbgv"},"package_dimensions":null,"price":2000,"product":"prod_9P6HnSMCHPBk1Y","updated":1476881850}]}
 */
export function getSkuByID(req, res){
    let ret = service.getSkuByID(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Sku by ID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/skus
 * Sample data response:
 * {"data":[{"id":"sku_8xpkjNkOjKlb8D","object":"sku","active":true,"attributes":{},"created":1470587090,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234"},"package_dimensions":null,"price":2000,"product":"prod_8weFswEoY6i2IW","updated":1470587090}],"has_more":false,"url":"/v1/skus"}}
 */
export function getSkus(req, res){
    let ret = service.getSkus();
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Skus Error");
    });
}

/**
 *
 * Sample input: API_INITIAL_PATH/skus with JSON: {"currency": "USD", "inventory": {"type": "finite", "quantity": 1}, "metadata": {"postID": "1234", "stripeAccID": "acct_18m2ZBLUxBeddbgv"}, "price": 2000, "product": "prod_9P6HnSMCHPBk1Y"}
 * Sample response:
 * {"data":{"__v":0,"stripeSkuID":"sku_9P7GTOpdmZeQgf","postID":"1234","stripeAccID":"acct_18m2ZBLUxBeddbgv","_id":"58076599ea8f57e45c3aeafe"}}
 */
export function addSku(req, res) {
    let ret = service.addSku(req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Add Sku Error");
    });
}

/**
 *
 * Sample input: API_INITIAL_PATH/skus with JSON: { "product": {"name": "testp222", "shippable": false, "metadata": {"postID": "1234", "stripeAccID":"acct_18m2ZBLUxBeddbgv"}}, "sku": {"currency": "USD", "inventory": {"type": "finite", "quantity": 1}, "metadata": {"postID": "1234", "stripeAccID":"acct_18m2ZBLUxBeddbgv"}, "price": 3000}}
 * Sample response:
 * {"data":{"__v":0,"stripeSkuID":"sku_9P8WHEShbEy4Og","postID":"1234","stripeAccID":"acct_18m2ZBLUxBeddbgv","_id":"580777c132e070f774a32f50"}}
 */
export function addProductAndSku(req, res) {
    let ret = service.addProductAndSku(req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Add Product and Sku Error");
    });
}

/**
 *
 * Sample input: API_INITIAL_PATH/skus/stripe/sku_8xpkjNkOjKlb8D with JSON: {"price": 5000}
 * Sample response:
 * {"data":{"id":"sku_9P8WHEShbEy4Og","object":"sku","active":true,"attributes":{},"created":1476884190,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234","stripeAccID":"acct_18m2ZBLUxBeddbgv"},"package_dimensions":null,"price":5000,"product":"prod_9P8WYMwKcvuoWQ","updated":1476884429}}
 */
export function editSkuByStripeID(req, res) {
    let ret = service.editSkuByStripeID(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Edit Sku by StripeID Error");
    });
}

/**
 *
 * Sample input: API_INITIAL_PATH/skus/feafeafwfewa with JSON: {"price": 1000}
 * Sample response:
 * {"data":{"id":"sku_9P8WHEShbEy4Og","object":"sku","active":true,"attributes":{},"created":1476884190,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234","stripeAccID":"acct_18m2ZBLUxBeddbgv"},"package_dimensions":null,"price":1000,"product":"prod_9P8WYMwKcvuoWQ","updated":1476884548}}
 */
export function editSku(req, res) {
    let ret = service.editSku(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Edit Sku Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/skus/57a75af67cfb6759f39f0319
 * Sample response:
 * {"data":{"ok":1,"n":1}}
 */
export function deleteSku(req, res) {
    let ret = service.deleteSku(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Delete Sku Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/accounts/user/xxx
 * Sample data response:
 * {"data":[{"id":"acct_18mQzLAClEnsczj2","object":"account","business_logo":null,"business_name":null,"business_url":null,"charges_enabled":true,"country":"AU","debit_negative_balances":false,"decline_charge_on":{"avs_failure":false,"cvc_failure":false},"default_currency":"aud","details_submitted":false,"display_name":null,"email":"abc@a.com","external_accounts":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/accounts/acct_18mQzLAClEnsczj2/external_accounts"},"legal_entity":{"address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"business_name":null,"business_tax_id_provided":false,"dob":{"day":null,"month":null,"year":null},"first_name":null,"last_name":null,"personal_address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"type":null,"verification":{"details":null,"details_code":null,"document":null,"status":"unverified"}},"managed":true,"metadata":{"userID":"123"},"product_description":null,"statement_descriptor":null
 * Note: If a stripe account is deleted from stripe side, it will produce an empty object in
 * the array.
 * If the ID given is completely not found: data will be {} instead of [{}]. This is the
 * nature of mongoose to return earlier with unknown ID.
 */
export function getAccountsByUserID(req, res) {
    let ret = service.getAccountByUserID(req.params.uid);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Accounts by userID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/accounts/stripe/xxx
 * Sample data response:
 * {"data":{"id":"acct_18mQzLAClEnsczj2","object":"account","business_logo":null,"business_name":null,"business_url":null,"charges_enabled":true,"country":"AU","debit_negative_balances":false,"decline_charge_on":{"avs_failure":false,"cvc_failure":false},"default_currency":"aud","details_submitted":false,"display_name":null,"email":"abc@a.com","external_accounts":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/accounts/acct_18mQzLAClEnsczj2/external_accounts"},"legal_entity":{"address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"business_name":null,"business_tax_id_provided":false,"dob":{"day":null,"month":null,"year":null},"first_name":null,"last_name":null,"personal_address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"type":null,"verification":{"details":null,"details_code":null,"document":null,"status":"unverified"}},"managed":true,"metadata":{"userID":"123"},"product_description":null,"statement_descriptor":null,"support_email":null,"support_phone":null,"timezone":"Etc/UTC","tos_acceptance":{"date":null,"ip":null,"user_agent":null},"transfer_schedule":{"delay_days":2,"interval":"daily"},"transfers_enabled":false,"verification":{"disabled_reason":"fields_needed","due_by":null,"fields_needed":["external_account","legal_entity.dob.day","legal_entity.dob.month","legal_entity.dob.year","legal_entity.first_name","legal_entity.last_name","legal_entity.type","tos_acceptance.date","tos_acceptance.ip"]},"currencies_supported":["usd","aed","all","ang","ars","aud","awg","bbd","bdt","bif","bmd","bnd","bob","brl","bsd","bwp","bzd","cad","chf","clp","cny","cop","crc","cve","czk","djf","dkk","dop","dzd","egp","etb","eur","fjd","fkp","gbp","gip","gmd","gnf","gtq","gyd","hkd","hnl","hrk","htg","huf","idr","ils","inr","isk","jmd","jpy","kes","khr","kmf","krw","kyd","kzt","lak","lbp","lkr","lrd","ltl","mad","mdl","mnt","mop","mro","mur","mvr","mwk","mxn","myr","nad","ngn","nio","nok","npr","nzd","pab","pen","pgk","php","pkr","pln","pyg",
 */
export function getAccountByStripeID(req, res){
    let ret = service.getAccountByStripeID(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Account by StripeID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/accounts/xxx
 * Sample data response:
 * {"data":[{"id":"acct_18mQzLAClEnsczj2","object":"account","business_logo":null,"business_name":null,"business_url":null,"charges_enabled":true,"country":"AU","debit_negative_balances":false,"decline_charge_on":{"avs_failure":false,"cvc_failure":false},"default_currency":"aud","details_submitted":false,"display_name":null,"email":"abc@a.com","external_accounts":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/accounts/acct_18mQzLAClEnsczj2/external_accounts"},"legal_entity":{"address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"business_name":null,"business_tax_id_provided":false,"dob":{"day":null,"month":null,"year":null},"first_name":null,"last_name":null,"personal_address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"type":null,"verification":{"details":
 * Note: If a stripe account is deleted from stripe side, it will produce an empty object in
 * the array.
 * If the ID given is completely not found: data will be {} instead of [{}]. This is the
 * nature of mongoose to return earlier with unknown ID.
 */
export function getAccountByID(req, res){
    let ret = service.getAccountByID(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Account by ID Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/accounts
 * Sample data response:
 * {"data":{"object":"list","data":[{"id":"acct_18mQzLAClEnsczj2","object":"account","business_logo":null,"business_name":null,"business_url":null,"charges_enabled":true,"country":"AU","debit_negative_balances":false,"decline_charge_on":{"avs_failure":false,"cvc_failure":false},"default_currency":"aud","details_submitted":false,"display_name":null,"email":"abc@a.com","external_accounts":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/accounts/acct_18mQzLAClEnsczj2/external_accounts"},"legal_entity":{"address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"business_name":null,"business_tax_id_provided":false,"dob":{"day":null,"month":null,"year":null},"first_name":null,"last_name":null,"personal_address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"type":null,"verification":{"details":null,"details_code":null,"document":null,"status":"unverified"}},"managed":true,"metadata":{"userID":"123"},"product_description":null,"statement_descriptor":null,"support_email":null,"support_phone":null,"timezone":"Etc/UTC","tos_acceptance":{"date":null,"ip":null,"user_agent":null},"transfer_schedule":{"delay_days":2,"interval":"daily"},"transfers_enabled":false,"verification":{"disabled_reason":"fields_needed","due_by":null,"fields_needed":["external_account","legal_entity.dob.day","legal_entity.dob.month","legal_entity.dob.year","legal_entity.first_name","legal_entity.last_name","legal_entity.type","tos_acceptance.date","tos_acceptance.ip"]},"currencies_supported":["usd","aed","all","ang","ars","aud","awg","bbd","bdt","bif","bmd","bnd","bob","brl","bsd","bwp","bzd","cad","chf","clp","cny","cop","crc","cve","czk","djf","dkk","dop","dzd","egp","etb","eur","fjd","fkp","gbp","gip","gmd","gnf","gtq","gyd","hkd","hnl","hrk","htg","huf","idr","ils","inr","isk","jmd","jpy","kes","khr","kmf","krw","kyd","kzt","lak","lbp","lkr","lrd","ltl","mad","mdl","mnt","mop","mro","mur","mvr","mwk","mxn","myr","nad","ngn","nio","nok","npr","nzd","pab","pen","pgk","php","pkr","pln","pyg","qar","rub","sar","sbd","scr","sek","sgd","shp","sll","sos","std","svc","szl","thb","top","ttd","twd","tzs","uah","ugx","uyu","uzs","vnd","vuv","wst","xaf","xof","xpf","yer","zar"]},{"id":"acct_18m2ZBLUxBeddbgv","object":"account","business_logo":null,"business_name":null,"business_url":null,"charges_enabled":true,"country":"AU","debit_negative_balances":false,"decline_charge_on":{"avs_failure":false,"cvc_failure":false},"default_currency":"aud","details_submitted":false,"display_name":null,"email":"jimmyshen007@gmail.com","external_accounts":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/accounts/acct_18m2ZBLUxBeddbgv/external_accounts"},"legal_entity":{"address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"business_name":null,"business_tax_id_provided":false,"dob":{"day":null,"month":null,"year":null},"first_name":null,"last_name":null,"personal_address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"type":null,"verification":{"details":null,"details_code":null,"document":null,"status":"unverified"}},"managed":true,"metadata":{},"product_description":null,"statement_descriptor":null ...
 */
export function getAccounts(req, res){
    let ret = service.getAccounts();
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Get Accounts Error");
    });
}

/**
 *
 * Sample input: API_INITIAL_PATH/accounts with JSON: {"country": "AU", "managed": true, "email": "jimmyshen007@gmail.com", "metadata": {"userID": "123"}}
 * Sample response:
 * {"data":{"__v":0,"stripeAccID":"acct_18mQzLAClEnsczj2","userID":"123","_id":"57bf21174bc54c71d680bce3"}}
 */
export function addAccount(req, res) {
    let ret = service.addAccount(req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Add Account Error");
    });
}

/**
 *
 * Sample input: API_INITIAL_PATH/accounts/stripe/xxx with JSON: {"email": "abc@a.com"}
 * Sample response:
 * {"data":{"id":"acct_18mQzLAClEnsczj2","object":"account","business_logo":null,"business_name":null,"business_url":null,"charges_enabled":true,"country":"AU","debit_negative_balances":false,"decline_charge_on":{"avs_failure":false,"cvc_failure":false},"default_currency":"aud","details_submitted":false,"display_name":null,"email":"abc@a.com","external_accounts":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/accounts/acct_18mQzLAClEnsczj2/external_accounts"},"legal_entity":{"address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"business_name":null,"business_tax_id_provided":false,"dob":{"day":null,"month":null,"year":null},"first_name":null,"last_name":null,"personal_address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"type":null,"verification":{"details":null,"details_code":null,"document":null,"status":"unverified"}},"managed":true,"metadata":{"userID":"123"},"product_description":null,"statement_descriptor":null,"support_email":null,"support_phone":null,"timezone":"Etc/UTC","tos_acceptance":{"date":null,"ip":null,"user_agent":null},"transfer_schedule":{"delay_days":2,"interval":"daily"},"transfers_enabled":false,"verification":{"disabled_reason":"fields_needed","due_by":null,"fields_needed":["external_account","legal_entity.dob.day","legal_entity.dob.month","legal_entity.dob.year","legal_entity.first_name","legal_entity.last_name","legal_entity.type","tos_acceptance.date","tos_acceptance.ip"]},"currencies_supported":["usd","aed","all","ang","ars","aud","awg","bbd","bdt","bif","bmd","bnd","bob","brl","bsd","bwp","bzd","cad","chf","clp","cny","cop","crc","cve","czk","djf","dkk","dop","dzd","egp","etb","eur","fjd","fkp","gbp","gip","gmd","gnf","gtq","gyd","hkd","hnl","hrk","htg","huf","idr","ils","inr","isk","jmd","jpy","kes","khr","kmf","krw","kyd","kzt","lak","lbp","lkr","lrd","ltl","mad","mdl","mnt","mop","mro","mur","mvr","mwk","mxn","myr","nad","ngn","nio","nok","npr","nzd","pab","pen","pgk","php","pkr","pln","pyg","qar","rub","sar","sbd","scr","sek","sgd","shp","sll","sos","std","svc","szl","thb","top","ttd","twd","tzs","uah","ugx","uyu","uzs","vnd","vuv","wst","xaf","xof","xpf","yer","zar"]}}
 */
export function editAccountByStripeID(req, res) {
    let ret = service.editAccountByStripeID(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Edit Account by StripeID Error");
    });
}

/**
 *
 * Sample input: API_INITIAL_PATH/accounts/xxxx with JSON: {"email": "abc@a.com"}
 * Sample response:
 * {"data":{"id":"acct_18mQzLAClEnsczj2","object":"account","business_logo":null,"business_name":null,"business_url":null,"charges_enabled":true,"country":"AU","debit_negative_balances":false,"decline_charge_on":{"avs_failure":false,"cvc_failure":false},"default_currency":"aud","details_submitted":false,"display_name":null,"email":"abc@a.com","external_accounts":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/accounts/acct_18mQzLAClEnsczj2/external_accounts"},"legal_entity":{"address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"business_name":null,"business_tax_id_provided":false,"dob":{"day":null,"month":null,"year":null},"first_name":null,"last_name":null,"personal_address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"type":null,"verification":{"details":null,"details_code":null,"document":null,"status":"unverified"}},"managed":true,"metadata":{"userID":"123"},"product_description":null,"statement_descriptor":null,"support_email":null,"support_phone":null,"timezone":"Etc/UTC","tos_acceptance":{"date":null,"ip":null,"user_agent":null},"transfer_schedule":{"delay_days":2,"interval":"daily"},"transfers_enabled":false,"verification":{"disabled_reason":"fields_needed","due_by":null,"fields_needed":["external_account","legal_entity.dob.day","legal_entity.dob.month","legal_entity.dob.year","legal_entity.first_name","legal_entity.last_name","legal_entity.type","tos_acceptance.date","tos_acceptance.ip"]},"currencies_supported":["usd","aed","all","ang","ars","aud","awg","bbd","bdt","bif","bmd","bnd","bob","brl","bsd","bwp","bzd","cad","chf","clp","cny","cop","crc","cve","czk","djf","dkk","dop","dzd","egp","etb","eur","fjd","fkp","gbp","gip","gmd","gnf","gtq","gyd","hkd","hnl","hrk","htg","huf","idr","ils","inr","isk","jmd","jpy","kes","khr","kmf","krw","kyd","kzt","lak","lbp","lkr","lrd","ltl","mad","mdl","mnt","mop","mro","mur","mvr","mwk","mxn","myr","nad","ngn","nio","nok","npr","nzd","pab","pen","pgk","php","pkr","pln","pyg","qar","rub","sar","sbd","scr","sek","sgd","shp","sll","sos","std","svc","szl","thb","top","ttd","twd","tzs","uah","ugx","uyu","uzs","vnd","vuv","wst","xaf","xof","xpf","yer","zar"]}}
 */
export function editAccont(req, res) {
    let ret = service.editAccount(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Edit Account Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/accounts/57a75af67cfb6759f39f0319
 * Sample response:
 * {"data":{"ok":1,"n":1}}
 */
export function deleteAccount(req, res) {
    let ret = service.deleteAccount(req.params.id);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Delete Account Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/accounts/reject/xxxx with JSON {"reason": "fraud"}
 * Sample response:
 * {"data":{"id":"acct_18sYTLLV24J3QINA","object":"account","business_logo":null,"business_name":null,"business_url":null,"charges_enabled":false,"country":"AU","debit_negative_balances":false,"decline_charge_on":{"avs_failure":false,"cvc_failure":false},"default_currency":"aud","details_submitted":true,"display_name":null,"email":"jimmyshen007@gmail.com","external_accounts":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/accounts/acct_18sYTLLV24J3QINA/external_accounts"},"legal_entity":{"address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"business_name":null,"business_tax_id_provided":false,"dob":{"day":null,"month":null,"year":null},"first_name":null,"last_name":null,"personal_address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"type":null,"verification":{"details":null,"details_code":null,"document":null,"status":"unverified"}},"managed":true,"metadata":{"userID":"123"},"product_description":null,"statement_descriptor":null,"support_email":null,"support_phone":null,"timezone":"Etc/UTC","tos_acceptance":{"date":null,"ip":null,"user_agent":null},"transfer_schedule":{"delay_days":2,"interval":"daily"},"transfers_enabled":false,"verification":{"disabled_reason":"rejected.fraud","due_by":null,"fields_needed":[]}}}
 */
export function rejectAccount(req, res){
    let ret = service.rejectAccount(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Reject Account Error");
    });
}

/**
 * Sample input: API_INITIAL_PATH/accounts/reject/stripe/xxxx
 * Sample response:
 * {"data":{"id":"acct_18sYTLLV24J3QINA","object":"account","business_logo":null,"business_name":null,"business_url":null,"charges_enabled":false,"country":"AU","debit_negative_balances":false,"decline_charge_on":{"avs_failure":false,"cvc_failure":false},"default_currency":"aud","details_submitted":true,"display_name":null,"email":"jimmyshen007@gmail.com","external_accounts":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/accounts/acct_18sYTLLV24J3QINA/external_accounts"},"legal_entity":{"address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"business_name":null,"business_tax_id_provided":false,"dob":{"day":null,"month":null,"year":null},"first_name":null,"last_name":null,"personal_address":{"city":null,"country":"AU","line1":null,"line2":null,"postal_code":null,"state":null},"type":null,"verification":{"details":null,"details_code":null,"document":null,"status":"unverified"}},"managed":true,"metadata":{"userID":"123"},"product_description":null,"statement_descriptor":null,"support_email":null,"support_phone":null,"timezone":"Etc/UTC","tos_acceptance":{"date":null,"ip":null,"user_agent":null},"transfer_schedule":{"delay_days":2,"interval":"daily"},"transfers_enabled":false,"verification":{"disabled_reason":"rejected.fraud","due_by":null,"fields_needed":[]}}}
 */
export function rejectAccountByStripeID(req, res){
    let ret = service.rejectAccountByStripeID(req.params.id, req.body);
    ret.then((data) => {
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, "Reject Account By Stripe ID Error");
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