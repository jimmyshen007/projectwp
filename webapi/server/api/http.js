import * as service from './service';
import stripe from 'stripe';
let sapi = stripe('sk_test_PPBb1cXlmXUWCFBUMUxrw6v9');

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

function handleRet(ret, res, err_msg, scheduling=null){
    ret.then((data) => {
        if(scheduling){
            scheduling(data);
        }
        handle_response(res, data, null, null);
    }, (err) => {
        handle_response(res, null, err, err_msg);
    });
}

export function getCities(req, res){
    let ret = service.getCities();
    handleRet(ret, res,  "Get Cities Error");
}

export function getSchools(req, res){
    let ret = service.getSchools();
    handleRet(ret, res,  "Get Schools Error");
}

export function getSchoolsByGreaterHitsSorted(req, res){
    let hits = req.params.hits;
    req.query["hits"] = hits;
    let ret = service.getSchoolsByGreaterHitsSorted(req.query);
    handleRet(ret, res,  "Get Schools By Greater Hits Sorted Error");
}

export function getCitiesByGreaterHitsSorted(req, res){
    let hits = req.params.hits;
    req.query["hits"] = hits;
    let ret = service.getCitiesByGreaterHitsSorted(req.query);
    handleRet(ret, res,  "Get Cities By Greater Hits Sorted Error");
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

export function testCreateCharge(req, res){
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
        let ret = createPercentSKUCharge({
            amount: 2000,
            currency: "usd",
            source: token.id, // obtained with Stripe.js
            description: "Charge for maidongxi1",
            capture: false,
            metadata: {postID: 'fakep', userID: 'fakeu',
                postAuthorID: 'fakepa', stripeAccID: 'acct_18m2ZBLUxBeddbgv'}
        });
        ret.then((data) => {
            handle_response(res, data, null, null);
        }, (err) => {
            handle_response(res, null, err, "Creat Charge Error");
        });
    });
}

export function testCreatePercentSKUCharge(req, res){
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
        let ret = service.createPercentSKUCharge({
            "currency": "usd",
            "source": token.id, // obtained with Stripe.js
            "description": "Charge for maidongxi1",
            "metadata": {"postID": "fakep", "userID": "fakeu",
                "postAuthorID": "fakepa", "stripeAccID": "acct_18m2ZBLUxBeddbgv",
                "stripeSkuID" : "sku_A1OiEnD1Jjy146", "days": 5, "type": "day"
            }
        });
        ret.then((data) => {
            handle_response(res, data, null, null);
        }, (err) => {
            handle_response(res, null, err, "Creat Percent SKUCharge Error");
        });
    });
}


export function testCreateCustomerAndCard(req, res) {
    service.addCustomer({
        description: "aaa",
        metadata: {userID: 'fakeu'}
    }).then((cus) => {
        sapi.tokens.create({
            email: 'maidongxi1@example.com',
            card: {
                "number": '4242424242424242',
                "exp_month": 12,
                "exp_year": 2017,
                "cvc": '123'
            }
        }, function (err, token) {
            // asynchronously called
            service.getCustomerByID(cus.id).then((scus) => {
                let ret = service.addCard({source: token.id, metadata: {stripeCusID: scus[0].id}});
                ret.then((data) => {
                    handle_response(res, data, null, null);
                }, (err) => {
                    handle_response(res, null, err, "Creat Customer and Card Error");
                });
            }, (err) => {
                console.log(err);
            });
        });
    }, (err) => {
        console.log(err);
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
    handleRet(ret, res,  "Get Favoriates Error");
}

export function getFavoriteByID(req, res) {
    let ret = service.getFavoriteById(req.params.id);
    handleRet(ret, res, "Get Favorite By ID Error");
}

export function addFavorite(req, res) {
    let ret = service.addFavorite(req.body);
    handleRet(ret, res, "Add Favorite Error");
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
 * Get active (i.e. : approved or paid) orders by greaterEndDate.
 * Sample input: API_INITPATH/worders/ActiveGreaterEndDate/2017-02-17
 * Sample output:
 * {"data":[{"_id":"58a99f9eec00b3a06874d8ef","paymentType":"stripe","rentalType":"daily","postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","stripeAccID":"acct_18m2ZBLUxBeddbgv","startDate":"2016-11-15T00:00:00.000Z","endDate":"2017-02-28T00:00:00.000Z","numTenant":2,"stripeChargeIDs":[],"__v":0},{"_id":"58a99fcaec00b3a06874d8f0","paymentType":"stripe","rentalType":"daily","postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","stripeAccID":"acct_18m2ZBLUxBeddbgv","startDate":"2016-11-15T00:00:00.000Z","endDate":"2017-03-01T00:00:00.000Z","numTenant":2,"stripeChargeIDs":[],"__v":0}]}
 * Note: 2017-02-17 is converted to Date object in backend.
 *
 */
export function getWOrdersActiveByGreaterEndDate(req, res){
    let ret = service.getWOrdersActiveByGreaterEndDate(req.params.edstr);
    handleRet(ret, res, "Get Active WOrders By GreaterEndDate Error");
}

/**
 * Sample input: API_INITPATH/worders with JSON:
 * {"paymentType": "stripe", "rentalType": "term",  "postID": "1234", "postAuthorID": "a123", "userID": "u123", "skuID": "s123", "stripeAccID": "acct_18m2ZBLUxBeddbgv", "startDate": "2016-11-15", "term": "100", "numTenant": 2}
 *
 * Note: Date type variable such as startDate can be automatically constructed from string provided.
 * i.e. "2016-11-15" will be constructed as Date format automatically.
 */
export function addWOrder(req, res){
    let ret = service.addWOrder(req.body);
    handleRet(ret, res, "Add WOrder Error", service.scheduling);
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
    handleRet(ret, res, "Edit WOrder Error", service.scheduling);
}

export function getWCharges(req, res) {
    let ret = service.getWCharges();
    handleRet(ret, res, "Get WCharges Error");
}

export function getWChargeByID(req, res){
    let ret = service.getWChargeByID(req.params.id);
    handleRet(ret, res, "Get WCharge By ID Error");
}

export function getWChargesByUserID(req, res){
    let ret = service.getWChargesByUserID(req.params.uid);
    handleRet(ret, res, "Get WCharges By UserID Error");
}

export function getWChargesByPostID(req, res){
    let ret = service.getWChargesByPostID(req.params.pid);
    handleRet(ret, res, "Get WCharges By PostID Error");
}

export function getWChargesByPostAuthorID(req, res){
    let ret = service.getWChargesByPostAuthorID(req.params.paid);
    handleRet(ret, res, "Get WCharges By PostAuthorID Error");
}

export function editWCharge(req, res){
    let ret = service.editWCharge(req.params.id, req.body);
    handleRet(ret, res, "Edit WCharge Error");
}

export function getWCustomers(req, res) {
    let ret = service.getWCustomers();
    handleRet(ret, res, "Get WCustomers Error");
}

export function getWCustomerByID(req, res){
    let ret = service.getWCustomersByID(req.params.id);
    handleRet(ret, res, "Get WCustomers By ID Error");
}

export function getWCustomersByUserID(req, res){
    let ret = service.getWCustomersByUserID(req.params.uid);
    handleRet(ret, res, "Get WCustomers By UserID Error");
}

export function editWCustomer(req, res) {
    let ret = service.editWCustomer(req.params.id, req.body);
    handleRet(ret, res, "Edit WCustomers Error");
}

export function getWCards(req, res) {
    let ret = service.getWCards();
    handleRet(ret, res, "Get WCards Error");
}

export function getWCardByID(req, res){
    let ret = service.getWCardByID(req.params.id);
    handleRet(ret, res, "Get WCard By ID Error");
}

export function getWCardsByUserID(req, res){
    let ret = service.getWCardsByUserID(req.params.uid);
    handleRet(ret, res, "Get WCards By UserID Error");
}

export function editWCard(req, res) {
    let ret = service.editWCard(req.params.id, req.body);
    handleRet(ret, res, "Edit WCard Error");
}

export function getWRefunds(req, res) {
    let ret = service.getWRefunds();
    handleRet(ret, res, "Get WRefunds Error");
}

export function getWRefundByID(req, res){
    let ret = service.getWRefundByID(req.params.id);
    handleRet(ret, res, "Get WRefund By ID Error");
}

export function getWRefundsByUserID(req, res){
    let ret = service.getWRefundsByUserID(req.params.uid);
    handleRet(ret, res, "Get WRefunds By UserID Error");
}

export function editWRefund(req, res) {
    let ret = service.editWRefund(req.params.id, req.body);
    handleRet(ret, res, "Edit WRefund Error");
}

export function getWRefundsByPostID(req, res){
    let ret = service.getWRefundsByPostID(req.params.pid);
    handleRet(ret, res, "Get WRefunds By PostID Error");
}

export function getWRefundsByPostAuthorID(req, res){
    let ret = service.getWRefundsByPostAuthorID(req.params.paid);
    handleRet(ret, res, "Get WRefunds By PostAuthorID Error");
}

export function getWTransfers(req, res) {
    let ret = service.getWTransfers();
    handleRet(ret, res, "Get WTransfers Error");
}

export function getWTransferByID(req, res){
    let ret = service.getWTransferByID(req.params.id);
    handleRet(ret, res, "Get WTransfer By ID Error");
}

export function getWTransfersByUserID(req, res){
    let ret = service.getWTransfersByUserID(req.params.uid);
    handleRet(ret, res, "Get WTransfers By UserID Error");
}

export function editWTransfer(req, res) {
    let ret = service.editWTransfer(req.params.id, req.body);
    handleRet(ret, res, "Edit WTransfer Error");
}

export function getWExtaccounts(req, res) {
    let ret = service.getWTransfers();
    handleRet(ret, res, "Get WTransfers Error");
}

export function getWExtaccountByID(req, res){
    let ret = service.getWTransferByID(req.params.id);
    handleRet(ret, res, "Get WTransfer By ID Error");
}

export function getWExtaccountsByUserID(req, res){
    let ret = service.getWTransfersByUserID(req.params.uid);
    handleRet(ret, res, "Get WTransfers By UserID Error");
}

export function editWExtaccount(req, res) {
    let ret = service.editWTransfer(req.params.id, req.body);
    handleRet(ret, res, "Edit WTransfer Error");
}
//*********************

/*
 * Sample input: api_init_path/customers/587cc25c73c0fe250d131b21
 * Sample output:
 * {"data":[{"id":"cus_9wSv1SiJ13RqYS","object":"customer","account_balance":0,"created":1484571228,"currency":null,"default_source":null,"delinquent":false,"description":"whatever","discount":null,"email":"change@a.b","livemode":false,"metadata":{"userID":"uid"},"shipping":null,"sources":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/sources"},"subscriptions":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/subscriptions"}}]}
 */
export function getCustomerByID(req, res){
    let ret = service.getCustomerByID(req.params.id);
    handleRet(ret, res, "Get Customer By ID Error");
}

/*
 * Sample input: api_init_path/customers/user/uid
 * Sample output:
 * {"data":[{"id":"cus_9wSv1SiJ13RqYS","object":"customer","account_balance":0,"created":1484571228,"currency":null,"default_source":null,"delinquent":false,"description":"whatever","discount":null,"email":"change@a.b","livemode":false,"metadata":{"userID":"uid"},"shipping":null,"sources":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/sources"},"subscriptions":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/subscriptions"}}]}
 */
export function getCustomersByUserID(req, res){
    let ret = service.getCustomersByUserID(req.params.uid);
    handleRet(ret, res, "Get Customer By UserID Error");
}

export function getCustomersByStripeAccID(req, res){
    let ret = service.getCustomersByStripeAccID(req.params.id);
    handleRet(ret, res, "Get Customer By StripeAccID Error");
}

/*
 * Sample input: api_init_path/customers/stripeAcc/xxx
 *               Note: if no stripeAccID is set, we cannot use this function at the moment.
 * Sample output:
 * {"data":[{"id":"cus_9wSv1SiJ13RqYS","object":"customer","account_balance":0,"created":1484571228,"currency":null,"default_source":null,"delinquent":false,"description":"whatever","discount":null,"email":"change@a.b","livemode":false,"metadata":{"userID":"uid"},"shipping":null,"sources":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/sources"},"subscriptions":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/subscriptions"}}]}
 */
export function getCustomerByStripeID(req, res){
    let ret = service.getCustomerByStripeID(req.params.id);
    handleRet(ret, res, "Get Customer By Stripe ID Error");
}

/*
 * Sample input: api_init_path/customers
 * Sample output:
 * {"data":[{"id":"cus_9wSv1SiJ13RqYS","object":"customer","account_balance":0,"created":1484571228,"currency":null,"default_source":null,"delinquent":false,"description":"whatever","discount":null,"email":"change@a.b","livemode":false,"metadata":{"userID":"uid"},"shipping":null,"sources":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/sources"},"subscriptions":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/subscriptions"}}]}
 */
export function getCustomers(req, res){
    let ret = service.getCustomers();
    handleRet(ret, res, "Get Customers Error");
}

/*
 * Sample input: api_init_path/customers with json ﻿{"email": "email@a.b", "description": "whatever", "metadata": {"userID": "uid"}}
 * Sample output:
 * {"data":{"__v":0,"stripeCusID":"cus_9wSv1SiJ13RqYS","userID":"uid","_id":"587cc25c73c0fe250d131b21"}}
 */
export function addCustomer(req, res) {
    let ret = service.addCustomer(req.body);
    handleRet(ret, res, "Add Customer Error");
}

/*
 * Sample input: api_init_path/customers/587cc25c73c0fe250d131b21 with json ﻿{"email": "change@a.b"}
 * Sample output:
 * {"data":{"id":"cus_9wSv1SiJ13RqYS","object":"customer","account_balance":0,"created":1484571228,"currency":null,"default_source":null,"delinquent":false,"description":"whatever","discount":null,"email":"change@a.b","livemode":false,"metadata":{"userID":"uid"},"shipping":null,"sources":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/sources"},"subscriptions":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/subscriptions"}}}
 */
export function editCustomerByStripeID(req, res) {
    let ret = service.editCustomerByStripeID(req.params.id, req.body);
    handleRet(ret, res, "Edit Customer By StripeID Error");
}

/*
 * Sample input: api_init_path/customers/stripe/cus_9wSv1SiJ13RqYS with json ﻿{"email": "change@a.b"}
 * Sample output:
 * {"data":{"id":"cus_9wSv1SiJ13RqYS","object":"customer","account_balance":0,"created":1484571228,"currency":null,"default_source":null,"delinquent":false,"description":"whatever","discount":null,"email":"change@a.b","livemode":false,"metadata":{"userID":"uid"},"shipping":null,"sources":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/sources"},"subscriptions":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/customers/cus_9wSv1SiJ13RqYS/subscriptions"}}}
 */
export function editCustomer(req, res) {
    let ret = service.editCustomer(req.params.id, req.body);
    handleRet(ret, res, "Edit Customer by StripeID Error");
}

/*
 * Sample input: api_init_path/customers/587b938544c06d0cb7cbcb2a
 * Sample output:
 * {"data":{"n":1,"ok":1}}
 */
export function deleteCustomer(req, res) {
    let ret = service.deleteCustomer(req.params.id);
    handleRet(ret, res, "Delete Customer Error");
}

/*
 * Sample input: api_init_path/cards/587b938544c06d0cb7cbcb2a
 * Sample output:
 * {"data":[{"id":"card_19cHLrBfJLgIcXuMWBIXv1jx","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":"cus_9w9fTIRkglzZbW","cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"DJvFLGesJ7SrFA7c","funding":"credit","last4":"4242","metadata":{"stripeCusID":"cus_9w9fTIRkglzZbW"},"name":null,"tokenization_method":null}]}
 */
export function getCardByID(req, res){
    let ret = service.getCardByID(req.params.id);
    handleRet(ret, res, "Get Card By ID Error");
}

/*
 * Sample input: api_init_path/cards/user/xxx
 * Sample output:
 * {"data":[{"id":"card_19cHLrBfJLgIcXuMWBIXv1jx","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":"cus_9w9fTIRkglzZbW","cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"DJvFLGesJ7SrFA7c","funding":"credit","last4":"4242","metadata":{"stripeCusID":"cus_9w9fTIRkglzZbW"},"name":null,"tokenization_method":null}]}
 */
export function getCardsByUserID(req, res){
    let ret = service.getCardsByUserID(req.params.uid);
    handleRet(ret, res, "Get Card By UserID Error");
}

/*
 * Sample input: api_init_path/cards/stripeAcc/xxx
 *              Note: if no stripeAccID is set, we cannot use this function at the moment.
 * Sample output:
 * {"data":[{"id":"card_19cHLrBfJLgIcXuMWBIXv1jx","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":"cus_9w9fTIRkglzZbW","cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"DJvFLGesJ7SrFA7c","funding":"credit","last4":"4242","metadata":{"stripeCusID":"cus_9w9fTIRkglzZbW"},"name":null,"tokenization_method":null}]}
 */
export function getCardsByStripeAccID(req, res){
    let ret = service.getCardsByStripeAccID(req.params.id);
    handleRet(ret, res, "Get Card By StripeAccID Error");
}

/*
 * Sample input: api_init_path/cards/stripe/card_19cHLrBfJLgIcXuMWBIXv1jx
 * Sample output:
 * {"data":[{"id":"card_19cHLrBfJLgIcXuMWBIXv1jx","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":"cus_9w9fTIRkglzZbW","cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"DJvFLGesJ7SrFA7c","funding":"credit","last4":"4242","metadata":{"stripeCusID":"cus_9w9fTIRkglzZbW"},"name":null,"tokenization_method":null}]}
 */
export function getCardByStripeID(req, res){
    let ret = service.getCardByStripeID(req.params.id);
    handleRet(ret, res, "Get Card By Stripe ID Error");
}

/*
 * Sample input: api_init_path/cards/stripeCus/card_19cHLrBfJLgIcXuMWBIXv1jx
 * Sample output:
 * {"data":[{"id":"card_19cHLrBfJLgIcXuMWBIXv1jx","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":"cus_9w9fTIRkglzZbW","cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"DJvFLGesJ7SrFA7c","funding":"credit","last4":"4242","metadata":{"stripeCusID":"cus_9w9fTIRkglzZbW"},"name":null,"tokenization_method":null}]}
 */
export function getCardsByStripeCusID(req, res){
    let ret = service.getCardsByStripeCusID(req.params.id);
    handleRet(ret, res, "Get Card By StripeCusID Error");
}

/*
 * Sample input: api_init_path/cards
 * Sample output:
 * {"data":[{"id":"card_19cHLrBfJLgIcXuMWBIXv1jx","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":"cus_9w9fTIRkglzZbW","cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"DJvFLGesJ7SrFA7c","funding":"credit","last4":"4242","metadata":{"stripeCusID":"cus_9w9fTIRkglzZbW"},"name":null,"tokenization_method":null}]}
 */
export function getCards(req, res){
    let ret = service.getCards();
    handleRet(ret, res, "Get Cards Error");
}

/*
 * Pre-requisite: need to have card info either in an object or token returned by stripe.js.
 * Sample input: api_init_path/cards
 *               with json ﻿{"source": TOKEN_RETURNED_BY_STRIPE_DOT_JS, "metadata": {"stripeCusID": cus_xxxxxx}}
 *               "stripeCusID" is important and must be provided. stripe API is using it for creating card source.
 * Sample output:
 * {"data":{"__v":0,"stripeCardID":"card_19ccrBBfJLgIcXuMmFCuFSV2","stripeCusID":"cus_9wVsrZPAalr6Z1","_id":"587ced5fa21e686807c2adef"}}
 *
 *  A complete test case to create a customer and a card is under api_path/cards/test/createCusAndCard.
 */
export function addCard(req, res) {
    let ret = service.addCard(req.body);
    handleRet(ret, res, "Add Card Error");
}

/*
 * Sample input: api_init_path/cards/stripe/card_19ccrBBfJLgIcXuMmFCuFSV2 with json ﻿{"name": "Apple Pie"}
 * Sample output:
 * {"data":{"id":"card_19ccrBBfJLgIcXuMmFCuFSV2","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":"cus_9wVsrZPAalr6Z1","cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"DJvFLGesJ7SrFA7c","funding":"credit","last4":"4242","metadata":{"stripeCusID":"cus_9wVsrZPAalr6Z1"},"name":"Apple Pie","tokenization_method":null}}
 */
export function editCardByStripeID(req, res) {
    let ret = service.editCardByStripeID(req.params.id, req.body);
    handleRet(ret, res, "Edit Card By StripeID Error");
}

/*
 * Sample input: api_init_path/cards/xxxxxxxxxx  with json ﻿{"name": "Apple Pie"}
 * Sample output:
 * {"data":{"id":"card_19ccrBBfJLgIcXuMmFCuFSV2","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":"cus_9wVsrZPAalr6Z1","cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"DJvFLGesJ7SrFA7c","funding":"credit","last4":"4242","metadata":{"stripeCusID":"cus_9wVsrZPAalr6Z1"},"name":"Apple Pie","tokenization_method":null}}
 */
export function editCard(req, res) {
    let ret = service.editCard(req.params.id, req.body);
    handleRet(ret, res, "Edit Card Error");
}

/*
 * Sample input: api_init_path/cards/xxxxxxxxxx
 * Sample output:
 * {"data":{"n":1,"ok":1}}
 */
export function deleteCard(req, res) {
    let ret = service.deleteCard(req.params.id);
    handleRet(ret, res, "Delete Card Error");
}

/*
 * Sample input: api_init_path/refunds/xxxxxxxxxx
 * Sample output:
 * {"data":[{"id":"re_19cdsMLUxBeddbgvWvU9kasP","object":"refund","amount":2000,"balance_transaction":null,"charge":"ch_19cG7DLUxBeddbgv9MzEtFWv","created":1484586154,"currency":"usd","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","random":"random"},"reason":null,"receipt_number":null,"status":"succeeded"}]}
 */
export function getRefundByID(req, res){
    let ret = service.getRefundByID(req.params.id);
    handleRet(ret, res, "Get Refund By ID Error");
}

/*
 * Sample input: api_init_path/refunds/user/xxx
 * Sample output:
 * {"data":[{"id":"re_19cdsMLUxBeddbgvWvU9kasP","object":"refund","amount":2000,"balance_transaction":null,"charge":"ch_19cG7DLUxBeddbgv9MzEtFWv","created":1484586154,"currency":"usd","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","random":"random"},"reason":null,"receipt_number":null,"status":"succeeded"}]}
 */
export function getRefundsByUserID(req, res){
    let ret = service.getRefundsByUserID(req.params.uid);
    handleRet(ret, res, "Get Refund By UserID Error");
}

/*
 * Sample input: api_init_path/refunds/post/xxx
 * Sample output:
 * {"data":[{"id":"re_19cdsMLUxBeddbgvWvU9kasP","object":"refund","amount":2000,"balance_transaction":null,"charge":"ch_19cG7DLUxBeddbgv9MzEtFWv","created":1484586154,"currency":"usd","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","random":"random"},"reason":null,"receipt_number":null,"status":"succeeded"}]}
 */
export function getRefundsByPostID(req, res){
    let ret = service.getRefundsByPostID(req.params.pid);
    handleRet(ret, res, "Get Refund By PostID Error");
}

/*
 * Sample input: api_init_path/refunds/postAuthor/xxx
 * Sample output:
 * {"data":[{"id":"re_19cdsMLUxBeddbgvWvU9kasP","object":"refund","amount":2000,"balance_transaction":null,"charge":"ch_19cG7DLUxBeddbgv9MzEtFWv","created":1484586154,"currency":"usd","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","random":"random"},"reason":null,"receipt_number":null,"status":"succeeded"}]}
 */
export function getRefundsByPostAuthorID(req, res){
    let ret = service.getRefundsByPostAuthorID(req.params.paid);
    handleRet(ret, res, "Get Refund By UserID Error");
}

/*
 * Sample input: api_init_path/refunds/stripeAcc/xxx
 * Sample output:
 * {"data":[{"id":"re_19cdsMLUxBeddbgvWvU9kasP","object":"refund","amount":2000,"balance_transaction":null,"charge":"ch_19cG7DLUxBeddbgv9MzEtFWv","created":1484586154,"currency":"usd","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","random":"random"},"reason":null,"receipt_number":null,"status":"succeeded"}]}
 */
export function getRefundsByStripeAccID(req, res){
    let ret = service.getRefundsByStripeAccID(req.params.id);
    handleRet(ret, res, "Get Refund By StripeAccID Error");
}

/*
 * Sample input: api_init_path/refunds/stripe/xxx
 * Sample output:
 * {"data":[{"id":"re_19cdsMLUxBeddbgvWvU9kasP","object":"refund","amount":2000,"balance_transaction":null,"charge":"ch_19cG7DLUxBeddbgv9MzEtFWv","created":1484586154,"currency":"usd","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","random":"random"},"reason":null,"receipt_number":null,"status":"succeeded"}]}
 */
export function getRefundByStripeID(req, res){
    let ret = service.getRefundByStripeID(req.params.id);
    handleRet(ret, res, "Get Refund By Stripe ID Error");
}

/*
 * Sample input: api_init_path/refunds
 * Sample output:
 * {"data":[{"id":"re_19cdsMLUxBeddbgvWvU9kasP","object":"refund","amount":2000,"balance_transaction":null,"charge":"ch_19cG7DLUxBeddbgv9MzEtFWv","created":1484586154,"currency":"usd","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","random":"random"},"reason":null,"receipt_number":null,"status":"succeeded"}]}
 */
export function getRefunds(req, res){
    let ret = service.getRefunds();
    handleRet(ret, res, "Get Refunds Error");
}

/*
 * Sample input: api_init_path/refunds with
 * json {"charge": "ch_19cG7DLUxBeddbgv9MzEtFWv", "metadata": {"userID": "fakeu", "postID": "fakep", postAuthorID: "fakepa", "stripeAccID": "acct_18m2ZBLUxBeddbgv"}}
 *
 * Sample output:
 * {"data":{"__v":0,"stripeRefundID":"re_19cdsMLUxBeddbgvWvU9kasP","stripeAccID":"acct_18m2ZBLUxBeddbgv","stripeChargeID":"ch_19cG7DLUxBeddbgv9MzEtFWv","_id":"587cfcab244985773b7a7e4f"}}
 */
export function addRefund(req, res) {
    let ret = service.addRefund(req.body);
    handleRet(ret, res, "Add Refund Error");
}

/*
 * Sample input: api_init_path/refunds/stripe/re_19cdsMLUxBeddbgvWvU9kasP with
 * json {"metadata": {"random": "random"}}
 *
 * Sample output:
 * {"data":{"id":"re_19cdsMLUxBeddbgvWvU9kasP","object":"refund","amount":2000,"balance_transaction":null,"charge":"ch_19cG7DLUxBeddbgv9MzEtFWv","created":1484586154,"currency":"usd","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","random":"random"},"reason":null,"receipt_number":null,"status":"succeeded"}}
 */
export function editRefundByStripeID(req, res) {
    let ret = service.editRefundByStripeID(req.params.id, req.body);
    handleRet(ret, res, "Edit Refund By StripeID Error");
}

/*
 * Sample input: api_init_path/refunds/587cfcab244985773b7a7e4f with
 * json {"metadata": {"random": "random"}}
 *
 * Sample output:
 * {"data":{"id":"re_19cdsMLUxBeddbgvWvU9kasP","object":"refund","amount":2000,"balance_transaction":null,"charge":"ch_19cG7DLUxBeddbgv9MzEtFWv","created":1484586154,"currency":"usd","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","random":"random"},"reason":null,"receipt_number":null,"status":"succeeded"}}
 */
export function editRefund(req, res) {
    let ret = service.editRefund(req.params.id, req.body);
    handleRet(ret, res, "Edit Refund Error");
}

/*
 * Sample input: api_init_path/transfers/5884c79ecad158d63089f04c
 * Sample output:
 * {"data":[{"id":"tr_19emjZLUxBeddbgvwPC5H0di","object":"transfer","amount":400,"amount_reversed":0,"application_fee":null,"balance_transaction":"txn_19emjZLUxBeddbgvQk0k0HR4","bank_account":{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","routing_number":"11 0000","status":"new"},"created":1485096861,"currency":"aud","date":1485129600,"description":"transfer edit","destination":"ba_19ekDELUxBeddbgvfA5XQdet","failure_code":null,"failure_message":null,"livemode":false,"metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv"},"method":"standard","recipient":null,"reversals":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/transfers/tr_19emjZLUxBeddbgvwPC5H0di/reversals"},"reversed":false,"source_transaction":null,"source_type":"card","statement_descriptor":null,"status":"paid","type":"bank_account"}]}
 */
export function getTransferByID(req, res){
    let ret = service.getTransferByID(req.params.id);
    handleRet(ret, res, "Get Transfer By ID Error");
}

/*
 * Sample input: api_init_path/transfers/user/xxx
 * Sample output:
 * {"data":[{"id":"tr_19emjZLUxBeddbgvwPC5H0di","object":"transfer","amount":400,"amount_reversed":0,"application_fee":null,"balance_transaction":"txn_19emjZLUxBeddbgvQk0k0HR4","bank_account":{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","routing_number":"11 0000","status":"new"},"created":1485096861,"currency":"aud","date":1485129600,"description":"transfer edit","destination":"ba_19ekDELUxBeddbgvfA5XQdet","failure_code":null,"failure_message":null,"livemode":false,"metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv"},"method":"standard","recipient":null,"reversals":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/transfers/tr_19emjZLUxBeddbgvwPC5H0di/reversals"},"reversed":false,"source_transaction":null,"source_type":"card","statement_descriptor":null,"status":"paid","type":"bank_account"}]}
 */
export function getTransfersByUserID(req, res){
    let ret = service.getTransfersByUserID(req.params.uid);
    handleRet(ret, res, "Get Transfer By UserID Error");
}

/*
 * Sample input: api_init_path/transfers/stripeAcc/acct_18m2ZBLUxBeddbgv
 * Sample output:
 * {"data":[{"id":"tr_19emjZLUxBeddbgvwPC5H0di","object":"transfer","amount":400,"amount_reversed":0,"application_fee":null,"balance_transaction":"txn_19emjZLUxBeddbgvQk0k0HR4","bank_account":{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","routing_number":"11 0000","status":"new"},"created":1485096861,"currency":"aud","date":1485129600,"description":"transfer edit","destination":"ba_19ekDELUxBeddbgvfA5XQdet","failure_code":null,"failure_message":null,"livemode":false,"metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv"},"method":"standard","recipient":null,"reversals":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/transfers/tr_19emjZLUxBeddbgvwPC5H0di/reversals"},"reversed":false,"source_transaction":null,"source_type":"card","statement_descriptor":null,"status":"paid","type":"bank_account"}]}
 */
export function getTransfersByStripeAccID(req, res){
    let ret = service.getTransfersByStripeAccID(req.params.id);
    handleRet(ret, res, "Get Transfer By StripeAccID Error");
}

/*
 * Sample input: api_init_path/transfers/stripe/tr_19emjZLUxBeddbgvwPC5H0di
 * Sample output:
 * {"data":[{"id":"tr_19emjZLUxBeddbgvwPC5H0di","object":"transfer","amount":400,"amount_reversed":0,"application_fee":null,"balance_transaction":"txn_19emjZLUxBeddbgvQk0k0HR4","bank_account":{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","routing_number":"11 0000","status":"new"},"created":1485096861,"currency":"aud","date":1485129600,"description":"transfer edit","destination":"ba_19ekDELUxBeddbgvfA5XQdet","failure_code":null,"failure_message":null,"livemode":false,"metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv"},"method":"standard","recipient":null,"reversals":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/transfers/tr_19emjZLUxBeddbgvwPC5H0di/reversals"},"reversed":false,"source_transaction":null,"source_type":"card","statement_descriptor":null,"status":"paid","type":"bank_account"}]}
 */
export function getTransferByStripeID(req, res){
    let ret = service.getTransferByStripeID(req.params.id);
    handleRet(ret, res, "Get Transfer By Stripe ID Error");
}

/*
 * Sample input: api_init_path/transfers
 * Sample output:
 * {"data":[{"id":"tr_19emjZLUxBeddbgvwPC5H0di","object":"transfer","amount":400,"amount_reversed":0,"application_fee":null,"balance_transaction":"txn_19emjZLUxBeddbgvQk0k0HR4","bank_account":{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","routing_number":"11 0000","status":"new"},"created":1485096861,"currency":"aud","date":1485129600,"description":"transfer edit","destination":"ba_19ekDELUxBeddbgvfA5XQdet","failure_code":null,"failure_message":null,"livemode":false,"metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv"},"method":"standard","recipient":null,"reversals":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/transfers/tr_19emjZLUxBeddbgvwPC5H0di/reversals"},"reversed":false,"source_transaction":null,"source_type":"card","statement_descriptor":null,"status":"paid","type":"bank_account"}]}
 */
export function getTransfers(req, res){
    let ret = service.getTransfers();
    handleRet(ret, res, "Get Transfers Error");
}

/*
 * Pre-requisite: need to have account activated.
 * Sample input: api_init_path/transfers
 *   with json {"amount": 400, "currency": "aud", "destination": "default_for_currency",  "description": "Transfer for test@example.com", "metadata": {"stripeAccID": "acct_18m2ZBLUxBeddbgv"}}
 * Sample output:
 * {"data":{"__v":0,"stripeTransID":"tr_19emjZLUxBeddbgvwPC5H0di","stripeAccID":"acct_18m2ZBLUxBeddbgv","_id":"5884c79ecad158d63089f04c"}}
 *
 *  A complete test case to create a customer and a card is under api_path/cards/test/createCusAndCard.
 */
export function addTransfer(req, res) {
    let ret = service.addTransfer(req.body);
    handleRet(ret, res, "Add Transfer Error");
}

/*
 * Sample input: api_init_path/transfers/stripe/tr_19emjZLUxBeddbgvwPC5H0di with json {"description": "transfer edit"}
 * Sample output:
 * {"data":{"id":"tr_19emjZLUxBeddbgvwPC5H0di","object":"transfer","amount":400,"amount_reversed":0,"application_fee":null,"balance_transaction":"txn_19emjZLUxBeddbgvQk0k0HR4","bank_account":{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","routing_number":"11 0000","status":"new"},"created":1485096861,"currency":"aud","date":1485129600,"description":"transfer edit","destination":"ba_19ekDELUxBeddbgvfA5XQdet","failure_code":null,"failure_message":null,"livemode":false,"metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv"},"method":"standard","recipient":null,"reversals":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/transfers/tr_19emjZLUxBeddbgvwPC5H0di/reversals"},"reversed":false,"source_transaction":null,"source_type":"card","statement_descriptor":null,"status":"paid","type":"bank_account"}}
 */
export function editTransferByStripeID(req, res) {
    let ret = service.editTransferByStripeID(req.params.id, req.body);
    handleRet(ret, res, "Edit Transfer By StripeID Error");
}

/*
 * Sample input: api_init_path/transfers/5884c79ecad158d63089f04c  with json {"description": "transfer edit"}
 *
 * Sample output:
 * {"data":{"id":"tr_19emjZLUxBeddbgvwPC5H0di","object":"transfer","amount":400,"amount_reversed":0,"application_fee":null,"balance_transaction":"txn_19emjZLUxBeddbgvQk0k0HR4","bank_account":{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","routing_number":"11 0000","status":"new"},"created":1485096861,"currency":"aud","date":1485129600,"description":"transfer edit","destination":"ba_19ekDELUxBeddbgvfA5XQdet","failure_code":null,"failure_message":null,"livemode":false,"metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv"},"method":"standard","recipient":null,"reversals":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/transfers/tr_19emjZLUxBeddbgvwPC5H0di/reversals"},"reversed":false,"source_transaction":null,"source_type":"card","statement_descriptor":null,"status":"paid","type":"bank_account"}}
 */
export function editTransfer(req, res) {
    let ret = service.editTransfer(req.params.id, req.body);
    handleRet(ret, res, "Edit Transfer Error");
}

/*
 * Sample input: api_init_path/extaccounts/5884a1c0cad158d63089f049
 * Sample output:
 * {"data":[{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account":"acct_18m2ZBLUxBeddbgv","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","default_for_currency":true,"fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","userID":"fakeu","edit":"random"},"routing_number":"11 0000","status":"new"}]}
 */
export function getExtaccountByID(req, res){
    let ret = service.getExtaccountByID(req.params.id);
    handleRet(ret, res, "Get Extaccount By ID Error");
}

/*
 * Sample input: api_init_path/extaccounts/user/fakeu
 * Sample output:
 * {"data":[{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account":"acct_18m2ZBLUxBeddbgv","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","default_for_currency":true,"fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","userID":"fakeu","edit":"random"},"routing_number":"11 0000","status":"new"}]}
 */
export function getExtaccountsByUserID(req, res){
    let ret = service.getExtaccountsByUserID(req.params.uid);
    handleRet(ret, res, "Get Extaccount By UserID Error");
}

/*
 * Sample input: api_init_path/extaccounts/stripeAcc/acct_18m2ZBLUxBeddbgv
 * Sample output:
 * {"data":[{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account":"acct_18m2ZBLUxBeddbgv","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","default_for_currency":true,"fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","userID":"fakeu","edit":"random"},"routing_number":"11 0000","status":"new"}]}
 */
export function getExtaccountsByStripeAccID(req, res){
    let ret = service.getExtaccountsByStripeAccID(req.params.id);
    handleRet(ret, res, "Get Extaccount By StripeAccID Error");
}

/*
 * Sample input: api_init_path/extaccounts/stripe/ba_19ekDELUxBeddbgvfA5XQdet
 * Sample output:
 * {"data":[{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account":"acct_18m2ZBLUxBeddbgv","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","default_for_currency":true,"fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","userID":"fakeu","edit":"random"},"routing_number":"11 0000","status":"new"}]}
 */
export function getExtaccountByStripeID(req, res){
    let ret = service.getExtaccountByStripeID(req.params.id);
    handleRet(ret, res, "Get Extaccount By Stripe ID Error");
}

/*
 * Sample input: api_init_path/extaccounts
 * Sample output:
 * {"data":[{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account":"acct_18m2ZBLUxBeddbgv","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","default_for_currency":true,"fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","userID":"fakeu","edit":"random"},"routing_number":"11 0000","status":"new"}]}
 */
export function getExtaccounts(req, res){
    let ret = service.getExtaccounts();
    handleRet(ret, res, "Get Extaccounts Error");
}

/*
 * Sample input: api_init_path/extaccounts with json {"external_account":
 *      {"object": "bank_account", "account_number": "000123456", "country": "AU", "currency": "aud",
 *      "routing_number": "110000"}, "metadata": {"stripeAccID": "acct_18m2ZBLUxBeddbgv",
 *      "userID": "fakeu"}} or {"external_account": TOKEN_FROM_STRIPE_JS}
 *
 *      The first one is best for testing purpose, and the latter one is preferred, since user info
 *      won't leak by going through our own server. Stripe.js takes care of converting the info into
 *      a token.
 *
 *      Note: the above info is for testing Australian based accounts.
 *      "routing_number" is actually the BSB number in Australia.
 *      For complete testing account number or routing number, please refer to:
 *      https://stripe.com/docs/testing#managed-accounts
 *
 * Sample output:
 * {"data":{"__v":0,"stripeExtaccountID":"ba_19ekDELUxBeddbgvfA5XQdet","userID":"fakeu","stripeAccID":"acct_18m2ZBLUxBeddbgv","_id":"5884a1c0cad158d63089f049"}}
 */
export function addExtaccount(req, res) {
    let ret = service.addExtaccount(req.body);
    handleRet(ret, res, "Add Extaccount Error");
}

/*
 * Sample input: api_init_path/extaccounts/587cc25c73c0fe250d131b21 with json ﻿{"metadata": {"edit": "random"}}
 * Sample output:
 * {"data":{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account":"acct_18m2ZBLUxBeddbgv","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","default_for_currency":true,"fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","userID":"fakeu","edit":"random"},"routing_number":"11 0000","status":"new"}}
 */
export function editExtaccountByStripeID(req, res) {
    let ret = service.editExtaccountByStripeID(req.params.id, req.body);
    handleRet(ret, res, "Edit Extaccount By StripeID Error");
}

/*
 * Sample input: api_init_path/extaccounts/stripe/cus_9wSv1SiJ13RqYS with json ﻿{"metadata": {"edit": "random"}}
 * Sample output:
 * {"data":{"id":"ba_19ekDELUxBeddbgvfA5XQdet","object":"bank_account","account":"acct_18m2ZBLUxBeddbgv","account_holder_name":null,"account_holder_type":null,"bank_name":"STRIPE TEST BANK","country":"AU","currency":"aud","default_for_currency":true,"fingerprint":"ilYd0gjZFiyFWQwU","last4":"3456","metadata":{"stripeAccID":"acct_18m2ZBLUxBeddbgv","userID":"fakeu","edit":"random"},"routing_number":"11 0000","status":"new"}}
 */
export function editExtaccount(req, res) {
    let ret = service.editExtaccount(req.params.id, req.body);
    handleRet(ret, res, "Edit Extaccount by StripeID Error");
}

/*
 * Sample input: api_init_path/extaccounts/5884a1c0cad158d63089f049
 * Sample output:
 * {"data":{"n":1,"ok":1}}
 */
export function deleteExtaccount(req, res) {
    let ret = service.deleteExtaccount(req.params.id);
    handleRet(ret, res, "Delete Extaccount Error");
}

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
 * Sample input: API_INITIAL_PATH/charges with json: {
            amount: 2000,
            currency: "usd",
            source: token.id, // obtained with Stripe.js
            description: "Charge for maidongxi1",
            metadata: {postID: 'fakep', userID: 'fakeu',
                postAuthorID: 'fakepa', stripeAccID: 'acct_18m2ZBLUxBeddbgv'}
        }
 * Sample data response:
 * {"data":{"__v":0,"stripeChargeID":"ch_19TVPBLUxBeddbgvhe3nrxMg","postID":"fakep","userID":"fakeu","postAuthorID":"fakepa","stripeAccID":"acct_18m2ZBLUxBeddbgv","_id":"585bc2c18fbafb740637f7aa"}}
 */
export function addCharge(req, res) {
    let ret = service.addCharge(req.body);
    handleRet(ret, res, "Add Charge Error");
}

/**
 * Sample input: API_INITIAL_PATH/charges/post/fakep
 * Sample data response:
 * {"data":[{"id":"ch_19TVPBLUxBeddbgvhe3nrxMg","object":"charge","amount":2000,"amount_refunded":0,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"balance_transaction":"txn_19TVPBLUxBeddbgvWcisftgY","captured":true,"created":1482408641,"currency":"usd","customer":null,"description":"Charge for maidongxi1","destination":null,"dispute":null,"failure_code":null,"failure_message":null,"fraud_details":{},"invoice":null,"livemode":false,"metadata":{"postID":"fakep","userID":"fakeu","postAuthorID":"fakepa","stripeAccID":"acct_18m2ZBLUxBeddbgv"},"order":null,"outcome":{"network_status":"approved_by_network","reason":null,"seller_message":"Payment complete.","type":"authorized"},"paid":true,"receipt_email":null,"receipt_number":null,"refunded":false,"refunds":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/charges/ch_19TVPBLUxBeddbgvhe3nrxMg/refunds"},"review":null,"shipping":null,"source":{"id":"card_19TVPBLUxBeddbgvwjAgidwh","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":null,"cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"kjhhM6WHBuCzoDPH","funding":"credit","last4":"4242","metadata":{},"name":null,"tokenization_method":null},"source_transfer":null,"statement_descriptor":null,"status":"succeeded"}]}
 *
 */
export function getChargesByPostID(req, res) {
    let ret = service.getChargesByPostID(req.params.pid);
    handleRet(ret, res, "Get Charges by postID Error");
}

/**
 * Sample input: API_INITIAL_PATH/charges/postAuthor/fakepa
 * Sample data response:
 * {"data":[{"id":"ch_19TVPBLUxBeddbgvhe3nrxMg","object":"charge","amount":2000,"amount_refunded":0,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"balance_transaction":"txn_19TVPBLUxBeddbgvWcisftgY","captured":true,"created":1482408641,"currency":"usd","customer":null,"description":"Charge for maidongxi1","destination":null,"dispute":null,"failure_code":null,"failure_message":null,"fraud_details":{},"invoice":null,"livemode":false,"metadata":{"postID":"fakep","userID":"fakeu","postAuthorID":"fakepa","stripeAccID":"acct_18m2ZBLUxBeddbgv"},"order":null,"outcome":{"network_status":"approved_by_network","reason":null,"seller_message":"Payment complete.","type":"authorized"},"paid":true,"receipt_email":null,"receipt_number":null,"refunded":false,"refunds":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/charges/ch_19TVPBLUxBeddbgvhe3nrxMg/refunds"},"review":null,"shipping":null,"source":{"id":"card_19TVPBLUxBeddbgvwjAgidwh","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":null,"cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"kjhhM6WHBuCzoDPH","funding":"credit","last4":"4242","metadata":{},"name":null,"tokenization_method":null},"source_transfer":null,"statement_descriptor":null,"status":"succeeded"}]}
 */
export function getChargesByPostAuthorID(req, res) {
    let ret = service.getChargesByPostAuthorID(req.params.paid);
    handleRet(ret, res, "Get Charges by postAuthorID Error");
}

/**
 * Sample input: API_INITIAL_PATH/users/fakeu
 * Sample data response:
 * {"data":[{"id":"ch_19TVPBLUxBeddbgvhe3nrxMg","object":"charge","amount":2000,"amount_refunded":0,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"balance_transaction":"txn_19TVPBLUxBeddbgvWcisftgY","captured":true,"created":1482408641,"currency":"usd","customer":null,"description":"Charge for maidongxi1","destination":null,"dispute":null,"failure_code":null,"failure_message":null,"fraud_details":{},"invoice":null,"livemode":false,"metadata":{"postID":"fakep","userID":"fakeu","postAuthorID":"fakepa","stripeAccID":"acct_18m2ZBLUxBeddbgv"},"order":null,"outcome":{"network_status":"approved_by_network","reason":null,"seller_message":"Payment complete.","type":"authorized"},"paid":true,"receipt_email":null,"receipt_number":null,"refunded":false,"refunds":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/charges/ch_19TVPBLUxBeddbgvhe3nrxMg/refunds"},"review":null,"shipping":null,"source":{"id":"card_19TVPBLUxBeddbgvwjAgidwh","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":null,"cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"kjhhM6WHBuCzoDPH","funding":"credit","last4":"4242","metadata":{},"name":null,"tokenization_method":null},"source_transfer":null,"statement_descriptor":null,"status":"succeeded"}]}
 */
export function getChargesByUserID(req, res) {
    let ret = service.getChargesByUserID(req.params.uid);
    handleRet(ret, res, "Get Charges by UserID Error");
}

/**
 *
 * Sample input: API_INITIAL_PATH/charges
 * Sample data response:
 * {"data":[{"id":"ch_19TVPBLUxBeddbgvhe3nrxMg","object":"charge","amount":2000,"amount_refunded":0,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,
 */
export function getCharges(req, res){
    let ret = service.getCharges();
    handleRet(ret, res, "Get Charges Error");
}

/**
 * Sample input: API_INITIAL_PATH/charges/stripe/ch_19TVPBLUxBeddbgvhe3nrxMg
 * Sample data response:
 * {"data":[{"id":"ch_19TVPBLUxBeddbgvhe3nrxMg","object":"charge","amount":2000,"amount_refunded":0,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"balance_transaction":"txn_19TVPBLUxBeddbgvWcisftgY",
 */
export function getChargeByStripeID(req, res){
    let ret = service.getChargeByStripeID(req.params.id);
    handleRet(ret, res, "Get Charge by StripeID Error");
}

/**
 * Sample input: API_INITIAL_PATH/charges/585bc2c18fbafb740637f7aa
 * Sample data response:
 * {"data":[{"id":"ch_19TVPBLUxBeddbgvhe3nrxMg","object":"charge","amount":2000,"amount_refunded":0,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,
 */
export function getChargeByID(req, res){
    let ret = service.getChargeByID(req.params.id);
    handleRet(ret, res, "Get Charge by ID Error");
}

/**
 * Sample input: API_INITIAL_PATH/charges/stripeAcc/acct_18m2ZBLUxBeddbgv
 * Sample data response:
 * {"data":[{"id":"ch_19TVPBLUxBeddbgvhe3nrxMg","object":"charge","amount":2000,"amount_refunded":0,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"balance_transaction":"txn_19TVPBLUxBeddbgvWcisftgY","captured":true,"created":1482408641,"currency":"usd","customer":null,"description":"Charge for maidongxi1","destination":null,"dispute":null,"failure_code":null,"failure_message":null,"fraud_details":{},"invoice":null,"livemode":false,"metadata":{"postID":"fakep","userID":"fakeu","postAuthorID":"fakepa","stripeAccID":"acct_18m2ZBLUxBeddbgv"},"order":null,"outcome":{"network_status":"approved_by_network","reason":null,"seller_message":"Payment complete.","type":"authorized"},"paid":true,"receipt_email":null,"receipt_number":null,"refunded":false,"refunds":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/charges/ch_19TVPBLUxBeddbgvhe3nrxMg/refunds"},"review":null,"shipping":null,"source":{"id":"card_19TVPBLUxBeddbgvwjAgidwh","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":null,"cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"kjhhM6WHBuCzoDPH","funding":"credit","last4":"4242","metadata":{},"name":null,"tokenization_method":null},"source_transfer":null,"statement_descriptor":null,"status":"succeeded"}]}
 */
export function getChargesByStripeAccID(req, res){
    let ret = service.getChargesByStripeAccID(req.params.id);
    handleRet(ret, res, "Get Charges By StripeAccID Error");
}

/**
 * Sample input: API_INITIAL_PATH/charges/stripe/ch_19TVPBLUxBeddbgvhe3nrxMg with json: {"metadata": {"random": "1234"}}
 * Sample data response:
 * {"data":{"id":"ch_19TVPBLUxBeddbgvhe3nrxMg","object":"charge","amount":2000,"amount_refunded":0,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"balance_transaction":"txn_19TVPBLUxBeddbgvWcisftgY","captured":true,"created":1482408641,"currency":"usd","customer":null,"description":"Charge for maidongxi1","destination":null,"dispute":null,"failure_code":null,"failure_message":null,"fraud_details":{},"invoice":null,"livemode":false,"metadata":{"postID":"fakep","userID":"fakeu","postAuthorID":"fakepa","stripeAccID":"acct_18m2ZBLUxBeddbgv","random":"1234"},"order":null,"outcome":{"network_status":"approved_by_network","reason":null,"seller_message":"Payment complete.","type":"authorized"},"paid":true,"receipt_email":null,"receipt_number":null,"refunded":false,"refunds":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/charges/ch_19TVPBLUxBeddbgvhe3nrxMg/refunds"},"review":null,"shipping":null,"source":{"id":"card_19TVPBLUxBeddbgvwjAgidwh","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":null,"cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"kjhhM6WHBuCzoDPH","funding":"credit","last4":"4242","metadata":{},"name":null,"tokenization_method":null},"source_transfer":null,"statement_descriptor":null,"status":"succeeded"}}
 */
export function editChargeByStripeID(req, res){
    let ret = service.editChargeByStripeID(req.params.id, req.body);
    handleRet(ret, res, "Edit Charge By StripeID Error");
}

/**
 * Sample input: API_INITIAL_PATH/charges/xxx with json: {"metadata": {"random": "4321"}}
 * Sample data response:
 * {"data":{"id":"ch_19TVPBLUxBeddbgvhe3nrxMg","object":"charge","amount":2000,"amount_refunded":0,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"balance_transaction":"txn_19TVPBLUxBeddbgvWcisftgY","captured":true,"created":1482408641,"currency":"usd","customer":null,"description":"Charge for maidongxi1","destination":null,"dispute":null,"failure_code":null,"failure_message":null,"fraud_details":{},"invoice":null,"livemode":false,"metadata":{"postID":"fakep","userID":"fakeu","postAuthorID":"fakepa","stripeAccID":"acct_18m2ZBLUxBeddbgv","random":"4321"},"order":null,"outcome":{"network_status":"approved_by_network","reason":null,"seller_message":"Payment complete.","type":"authorized"},"paid":true,"receipt_email":null,"receipt_number":null,"refunded":false,"refunds":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/charges/ch_19TVPBLUxBeddbgvhe3nrxMg/refunds"},"review":null,"shipping":null,"source":{"id":"card_19TVPBLUxBeddbgvwjAgidwh","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":null,"cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"kjhhM6WHBuCzoDPH","funding":"credit","last4":"4242","metadata":{},"name":null,"tokenization_method":null},"source_transfer":null,"statement_descriptor":null,"status":"succeeded"}}
 */
export function editCharge(req, res) {
    let ret = service.editCharge(req.params.id, req.body);
    handleRet(ret, res, "Edit Charge Error");
}

/**
 * Sample input: API_INITIAL_PATH/charges/capture/ with json: {}
 * Sample data response:
 * {"data":{"id":"ch_19TXVTLUxBeddbgvfArLKkzn","object":"charge","amount":2000,"amount_refunded":0,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"balance_transaction":"txn_19TXYBLUxBeddbgvqtyjjxtN","captured":true,"created":1482416719,"currency":"usd","customer":null,"description":"Charge for maidongxi1","destination":null,"dispute":null,"failure_code":null,"failure_message":null,"fraud_details":{},"invoice":null,"livemode":false,"metadata":{"postID":"fakep","userID":"fakeu","postAuthorID":"fakepa","stripeAccID":"acct_18m2ZBLUxBeddbgv"},"order":null,"outcome":{"network_status":"approved_by_network","reason":null,"seller_message":"Payment complete.","type":"authorized"},"paid":true,"receipt_email":null,"receipt_number":null,"refunded":false,"refunds":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/charges/ch_19TXVTLUxBeddbgvfArLKkzn/refunds"},"review":null,"shipping":null,"source":{"id":"card_19TXVTLUxBeddbgv2K3ERjxa","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":null,"cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"kjhhM6WHBuCzoDPH","funding":"credit","last4":"4242","metadata":{},"name":null,"tokenization_method":null},"source_transfer":null,"statement_descriptor":null,"status":"succeeded"}}
 */
export function captureCharge(req, res) {
    let ret = service.captureCharge(req.params.id, req.body);
    handleRet(ret, res, "Capture Charge Error");
}

/**
 * Sample input: API_INITIAL_PATH/charges/capture/stripe/ch_19TXBxLUxBeddbgvdbrlPe18 with json: {"amount":, 1000}
 * Sample data response:
 * {"data":{"id":"ch_19TXBxLUxBeddbgvdbrlPe18","object":"charge","amount":2000,"amount_refunded":1000,"application":"ca_90MuxhKCrlTjRee2x8ZqxATL39DtL9E1","application_fee":null,"balance_transaction":"txn_19TXCLLUxBeddbgvFoCGKHph","captured":true,"created":1482415509,"currency":"usd","customer":null,"description":"Charge for maidongxi1","destination":null,"dispute":null,"failure_code":null,"failure_message":null,"fraud_details":{},"invoice":null,"livemode":false,"metadata":{"postID":"fakep","userID":"fakeu","postAuthorID":"fakepa","stripeAccID":"acct_18m2ZBLUxBeddbgv"},"order":null,"outcome":{"network_status":"approved_by_network","reason":null,"seller_message":"Payment complete.","type":"authorized"},"paid":true,"receipt_email":null,"receipt_number":null,"refunded":false,"refunds":{"object":"list","data":[{"id":"re_19TXCLLUxBeddbgvLfRY2UHs","object":"refund","amount":1000,"balance_transaction":"txn_19TXCLLUxBeddbgvfyiQAhqS","charge":"ch_19TXBxLUxBeddbgvdbrlPe18","created":1482415533,"currency":"usd","metadata":{},"reason":null,"receipt_number":null,"status":"succeeded"}],"has_more":false,"total_count":1,"url":"/v1/charges/ch_19TXBxLUxBeddbgvdbrlPe18/refunds"},"review":null,"shipping":null,"source":{"id":"card_19TXBwLUxBeddbgvN14nFzv6","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":null,"cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2017,"fingerprint":"kjhhM6WHBuCzoDPH","funding":"credit","last4":"4242","metadata":{},"name":null,"tokenization_method":null},"source_transfer":null,"statement_descriptor":null,"status":"succeeded"}}
 * Note: the interesting part is if amount capture is less than the initial amount, "amount_refunded"
 * is set to 1000.
 */
export function captureChargeByStripeID(req, res) {
    let ret = service.captureChargeByStripeID(req.params.id, req.body);
    handleRet(ret, res, "Capture Charge By StripeID Error");
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
 *
 *  * To active the test account, the following json can be used:
 * {"legal_entity": {"dob": {"day": "01", "month": "01", "year": "1990"}, "first_name": "bob", "last_name": "dylan",
 *  "type": "individual", "address": {"line1": "417 St kilda Rd", "city": "South Yarra",
 *  "postal_code": "3141", "state": "Victoria"}}, "tos_acceptance": {"date": "1485095876", "ip": "10.134.12.134"}}
 *
 * date field is in secend. so one can use "new Date().getTime() / 1000" to get one.
 *
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

/**
 * Sample input: API_INITIAL_PATH/charges/createPercentSKUCharge with json: {
            "currency": "usd",
            "source": token.id, // obtained with Stripe.js
            "description": "Charge for maidongxi1",
            "metadata": {"postID": "fakep", "userID": "fakeu",
                "postAuthorID": "fakepa", "stripeAccID": "acct_18m2ZBLUxBeddbgv",
                "stripeSkuID": "sku_A1OiEnD1Jjy146", "days": 5, "type": "day"
                }
        }
   For an example, please see testCreatePercentSKUCharge function.
 * Sample data response:
 * {"data":{"__v":0,"stripeChargeID":"ch_19hMRJLUxBeddbgvz0HZRkBD","postID":"fakep","userID":"fakeu","postAuthorID":"fakepa","stripeAccID":"acct_18m2ZBLUxBeddbgv","_id":"588e25b22392cf6e693b0bb9"}}
 */
export function createPercentSKUCharge(req, res){
    let ret = service.createPercentSKUCharge(req.body);
    handleRet(ret, res, "Creat PercentSKUCharge Error.");
}

/* Email */
export function sendMail(req, res) {
    var nodemailer = require('nodemailer');
    var sender = 'info@hi5fang.com';
    var reciever = req.body.to;
    var subject  = req.body.subject;
    var html = req.body.html;
    var xoauth2 = require('xoauth2');
/*
    generator.on('token', function(token){
        console.log('New token for %s: %s', token.user, token.accessToken);
    });*/

    var transporter = nodemailer.createTransport({
        service: 'Gmail',
        auth: {
            //user: sender,
            //pass: "Hi11235813"
            XOAuth2: xoauth2.createXOAuth2Generator({
                user: sender,
                clientId: "951721460534-db44nc96110ct9ga1so8qs05tielnhne.apps.googleusercontent.com",
                clientSecret: "MPefeg3FemhmRuUp0arjX9gs",
                refreshToken: "1/iopyNvbKxukxLQ5WjrO1dYzcsEmPd2EQDN7AcITHx1G4XOcDv3MhVRaGNjWrNGSo"//,
                //accessToken: "ya29.Ci-fAzllrMkh1-uj4rKk-O7Z9rc1r632-rKlmMbhA91kAkP6BnmJW34ovXZH_lIxGg"
            })
        }
    });

    var mailOptions = {
        from: sender, // sender address
        to: reciever,  // list of receivers
        subject: subject, // Subject line
        //text: text //, // plaintext body
        html:  html// You can choose to send an HTML body instead
    };
    transporter.sendMail(mailOptions, function(error, info){
     if(error){
     console.log(error);
     res.json({data: 'error'});
     }else{
     console.log('Message sent: ' + info.response);
     res.json({data: info.response});
     };
     });
}
/*
export function sendMail(req, res) {
    let ret = service.sendEmail(req.body);
    handleRet(ret, res, "Send Mail Error");
}*/
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