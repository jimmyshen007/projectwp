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

/**
 *
 * Sample input: API_INITIAL_PATH/orders
 * Sample data response:
 * {"data":{"object":"list","data":[{"id":"or_18fv2ABfJLgIcXuM8gmA5vhv","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470590758,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18fv2ABfJLgIcXuM8gmA5vhv"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470590758}],"has_more":false,"url":"/v1/orders"}}
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
 * Sample input: API_INITIAL_PATH/orders with JSON: {"currency": "USD", "metadata": {"postID": "1234", "postAuthorID": "a123", "userID": "u123", "skuID": "s123"}, "items": [{"type": "sku", "parent": "sku_8xpkjNkOjKlb8D"}]}
 * Sample data response:
 * {"data":{"__v":0,"stripeOrderID":"or_18g9sYBfJLgIcXuMhGIBgKMb","postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","_id":"57a84e2d7cfb6759f39f031c"}}
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
 * Sample input: API_INITIAL_PATH/orders with JSON: {"metadata": {"testattr": "testvalue"}}
 * Sample data response:
 * {"data":{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470647822,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","testattr":"testvalue"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18g9sYBfJLgIcXuMhGIBgKMb"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470657491}}
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
 * {"data":{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470647822,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","testattr":"testvalue","testattr2":"testvalue2"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18g9sYBfJLgIcXuMhGIBgKMb"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470658350}}
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
 * Sample input: API_INITIAL_PATH/orders/sku/xxx
 * Sample data response:
 * {"data":{"object":"list","data":[{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470647822,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","testattr":"testvalue","testattr2":"testvalue2"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18g9sYBfJLgIcXuMhGIBgKMb"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470658350}],"has_more":false,"url":"/v1/orders"}}
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
 * {"data":{"object":"list","data":[{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470647822,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","testattr":"testvalue","testattr2":"testvalue2"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18g9sYBfJLgIcXuMhGIBgKMb"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470658350},{"id":"or_18fv2ABfJLgIcXuM8gmA5vhv","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470590758,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18fv2ABfJLgIcXuM8gmA5vhv"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470590758}],"has_more":false,"url":"/v1/orders"}}
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
 * {"data":{"object":"list","data":[{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470647822,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","testattr":"testvalue","testattr2":"testvalue2"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18g9sYBfJLgIcXuMhGIBgKMb"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470658350},{"id":"or_18fv2ABfJLgIcXuM8gmA5vhv","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470590758,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18fv2ABfJLgIcXuM8gmA5vhv"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470590758}],"has_more":false,"url":"/v1/orders"}}
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
 * {"data":{"object":"list","data":[{"id":"or_18g9sYBfJLgIcXuMhGIBgKMb","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470647822,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{"postID":"1234","postAuthorID":"a123","userID":"u123","skuID":"s123","testattr":"testvalue","testattr2":"testvalue2"},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18g9sYBfJLgIcXuMhGIBgKMb"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470658350},{"id":"or_18fv2ABfJLgIcXuM8gmA5vhv","object":"order","amount":3000,"amount_returned":null,"application":null,"application_fee":null,"charge":null,"created":1470590758,"currency":"usd","customer":null,"email":null,"items":[{"object":"order_item","amount":3000,"currency":"usd","description":"test22222223333","parent":"sku_8xpkjNkOjKlb8D","quantity":1,"type":"sku"},{"object":"order_item","amount":0,"currency":"usd","description":"Taxes (included)","parent":null,"quantity":null,"type":"tax"},{"object":"order_item","amount":0,"currency":"usd","description":"Free shipping","parent":"ship_free-shipping","quantity":null,"type":"shipping"}],"livemode":false,"metadata":{},"returns":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/order_returns?order=or_18fv2ABfJLgIcXuM8gmA5vhv"},"selected_shipping_method":"ship_free-shipping","shipping":null,"shipping_methods":[{"id":"ship_free-shipping","amount":0,"currency":"usd","delivery_estimate":null,"description":"Free shipping"}],"status":"created","status_transitions":null,"updated":1470590758}],"has_more":false,"url":"/v1/orders"}}
 */
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


/**
 * Sample input: API_INITIAL_PATH/products with json: {"name": "testp111", "shippable": false, "metadata": {"postID": "1234"}}
 * Sample data response:
 * {"data":{"__v":0,"stripeProdID":"prod_8weFswEoY6i2IW","postID":"1234","_id":"57a334dba1bccafedbd4a581"}}
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
 * {"data":{"object":"list","data":[{"id":"prod_8wdxsWrVRGR1cf","object":"product","active":true,"attributes":[],"caption":null,"created":1470312596,"deactivate_on":[],"description":null,"images":[],"livemode":false,"metadata":{"postID":"123"},"name":"testp111","package_dimensions":null,"shippable":true,"skus":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/skus?product=prod_8wdxsWrVRGR1cf&active=true"},"updated":1470312596,"url":null},{"id":"prod_8wdwq9JsEqAibO","object":"product","active":true,"attributes":[],"caption":null,"created":1470312529,"deactivate_on":[],"description":null,"images":[],"livemode":false,"metadata":{"postID":"123"},"name":"testpfff","package_dimensions":null,"shippable":true,"skus":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/skus?product=prod_8wdwq9JsEqAibO&active=true"},"updated":1470312529,"url":null}],"has_more":false,"url":"/v1/products"}}
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
 * Sample input: API_INITIAL_PATH/products/123
 * Sample data response:
 * {"data":{"object":"list","data":[{"id":"prod_8wdxsWrVRGR1cf","object":"product","active":true,"attributes":[],"caption":null,"created":1470312596,"deactivate_on":[],"description":null,"images":[],"livemode":false,"metadata":{"postID":"123"},"name":"testp111","package_dimensions":null,"shippable":true,"skus":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/skus?product=prod_8wdxsWrVRGR1cf&active=true"},"updated":1470312596,"url":null},{"id":"prod_8wdwq9JsEqAibO","object":"product","active":true,"attributes":[],"caption":null,"created":1470312529,"deactivate_on":[],"description":null,"images":[],"livemode":false,"metadata":{"postID":"123"},"name":"testpfff","package_dimensions":null,"shippable":true,"skus":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/skus?product=prod_8wdwq9JsEqAibO&active=true"},"updated":1470312529,"url":null}],"has_more":false,"url":"/v1/products"}}
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
 * {"data":{"object":"list","data":[{"id":"prod_8weFswEoY6i2IW","object":"product","active":true,"attributes":[],"caption":null,"created":1470313671,"deactivate_on":[],"description":null,"images":[],"livemode":false,"metadata":{"postID":"1234"},"name":"test222 ...
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
 * {"data":{"id":"prod_8weFswEoY6i2IW","object":"product","active":true,"attributes":[],"caption":null,"created":1470313671,"deactivate_on":[],"description":null,"images":[],"livemode":false,"metadata":{"postID":"1234"},"name":"test22222223333","package_dimensions":null,"shippable":true,"skus":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"/v1/skus?product=prod_8weFswEoY6i2IW&active=true"},"updated":1470402119,"url":null}}
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
 * {"data":{"object":"list","data":[{"id":"sku_8xpkjNkOjKlb8D","object":"sku","active":true,"attributes":{},"created":1470587090,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234"},"package_dimensions":null,"price":2000,"product":"prod_8weFswEoY6i2IW","updated":1470587090}],"has_more":false,"url":"/v1/skus"}}
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
 * Sample input: API_INITIAL_PATH/skus/stripe/xxx
 * Sample data response:
 * {"data":{"id":"sku_8xpkjNkOjKlb8D","object":"sku","active":true,"attributes":{},"created":1470587090,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234"},"package_dimensions":null,"price":2000,"product":"prod_8weFswEoY6i2IW","updated":1470587090}}
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
 * Sample input: API_INITIAL_PATH/skus/xxx
 * Sample data response:
 * {"data":{"object":"list","data":[{"id":"sku_8xpkjNkOjKlb8D","object":"sku","active":true,"attributes":{},"created":1470587090,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234"},"package_dimensions":null,"price":2000,"product":"prod_8weFswEoY6i2IW","updated":1470587090}],"has_more":false,"url":"/v1/skus"}}
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
 * {"data":{"object":"list","data":[{"id":"sku_8xpkjNkOjKlb8D","object":"sku","active":true,"attributes":{},"created":1470587090,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234"},"package_dimensions":null,"price":2000,"product":"prod_8weFswEoY6i2IW","updated":1470587090}],"has_more":false,"url":"/v1/skus"}}
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
 * Sample input: API_INITIAL_PATH/skus with JSON: {"currency": "USD", "inventory": {"type": "finite", "quantity": 1}, "metadata": {"postID": "1234"}, "price": 2000, "product": "prod_8weFswEoY6i2IW"}
 * Sample response:
 * {"data":{"__v":0,"stripeSkuID":"sku_8xpkjNkOjKlb8D","postID":"1234","_id":"57a760ef7cfb6759f39f031a"}}
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
 * Sample input: API_INITIAL_PATH/skus with JSON: {"price": 3000}
 * Sample response:
 * {"data":{"id":"sku_8xpkjNkOjKlb8D","object":"sku","active":true,"attributes":{},"created":1470587090,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234"},"package_dimensions":null,"price":3000,"product":"prod_8weFswEoY6i2IW","updated":1470588472}}
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
 * Sample input: API_INITIAL_PATH/skus with JSON: {"price": 1000}
 * Sample response:
 * {"data":{"id":"sku_8xpkjNkOjKlb8D","object":"sku","active":true,"attributes":{},"created":1470587090,"currency":"usd","image":null,"inventory":{"quantity":1,"type":"finite","value":null},"livemode":false,"metadata":{"postID":"1234"},"package_dimensions":null,"price":1000,"product":"prod_8weFswEoY6i2IW","updated":1470588376}}
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