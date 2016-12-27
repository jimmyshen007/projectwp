import path from 'path';
import bodyParser from 'body-parser';
import express from 'express';
import http from 'http';
import config from 'config';
//import expressJWT from 'express-jwt';
//import jwt from 'jsonwebtoken';
import * as api from './server/api/http';
import {db, orderSchema, favoriteSchema} from './server/api/db';

const app = express();
const httpServer = http.createServer(app);
const port = config.get('express.port') || 3000;

app.set('views', path.join(__dirname, 'server', 'views'));
app.set('view engine', 'ejs');

/**
 * Server middleware
 */
//app.use(expressJWT({secret: 'cannottellyou'}));
app.use(require('serve-static')(path.join(__dirname, config.get('buildDirectory'))));
app.use(bodyParser.urlencoded({
    extended: true
}));
app.use(bodyParser.json());

/**
 * API Endpoints
 */
//***** apis for wrapper order objects.
app.get('/api/0/worders', api.getWOrders);
app.get('/api/0/worders/:id', api.getWOrderByID);
app.get('/api/0/worders/user/:uid', api.getWOrdersByUserID);
app.get('/api/0/worders/post/:pid', api.getWOrdersByPostID);
app.get('/api/0/worders/postAuthor/:paid', api.getWOrdersByPostAuthorID);
app.get('/api/0/worders/sku/:kid', api.getWOrdersBySkuID);
app.post('/api/0/worders', api.addWOrder);
app.post('/api/0/worders/:id', api.editWOrder);
app.post('/api/0/worders/addSOrder/:id', api.addAttachSOrder);

app.get('/api/0/wproducts', api.getWProducts);
app.get('/api/0/wproducts/:id', api.getWProductByID);
app.get('/api/0/wproducts/post/:pid', api.getWProductsByPostID);
app.post('/api/0/wproducts/:id', api.editWProduct);

app.get('/api/0/wskus', api.getWSkus);
app.get('/api/0/wskus/:id', api.getWSkuByID);
app.get('/api/0/wskus/post/:pid', api.getWSkusByPostID);
app.post('/api/0/wskus/:id', api.editWSku);

app.get('/api/0/waccounts', api.getWAccounts);
app.get('/api/0/waccounts/:id', api.getWAccountsByID);
app.get('/api/0/waccounts/user/:uid', api.getWAccountsByUserID);
app.post('/api/0/waccounts/:id', api.editWAccount);

app.get('/api/0/wcharges', api.getWCharges);
app.get('/api/0/wcharges/:id', api.getWChargeByID);
app.get('/api/0/wcharges/user/:uid', api.getWChargesByUserID);
app.get('/api/0/wcharges/post/:pid', api.getWChargesByPostID);
app.get('/api/0/wcharges/postAuthor/:paid', api.getWChargesByPostAuthorID);
app.post('/api/0/wcharges/:id', api.editWCharge);
//*****

app.get('/api/0/orders', api.getOrders);
app.get('/api/0/orders/:id', api.getOrderByID);
app.get('/api/0/orders/stripe/:id', api.getOrderByStripeID);
app.get('/api/0/orders/user/:uid', api.getOrdersByUserID);
app.get('/api/0/orders/post/:pid', api.getOrdersByPostID);
app.get('/api/0/orders/postAuthor/:paid', api.getOrdersByPostAuthorID);
app.get('/api/0/orders/sku/:kid', api.getOrdersBySkuID);
app.get('/api/0/orders/stripeAcc/:id', api.getOrdersByStripeAccID);
app.post('/api/0/orders', api.addOrder);
app.post('/api/0/orders/:id', api.editOrder);
app.post('/api/0/orders/stripe/:id', api.editOrderByStripeID);
app.post('/api/0/orders/pay/:id', api.payOrder);
app.post('/api/0/orders/return/:id', api.returnOrder);
app.post('/api/0/orders/pay/stripe/:id', api.payOrderByStripeID);
app.post('/api/0/orders/return/stripe/:id', api.returnOrderByStripeID);
app.delete('/api/0/orders/:id', api.deleteOrder);
app.post('/api/0/orders/test/pay/:id', api.testPayOrder);
app.post('/api/0/orders/test/return/:id', api.testReturnOrder);

app.get('/api/0/favorites', api.getFavorites);
app.get('/api/0/favorites/:id', api.getFavoriteByID);
app.get('/api/0/favorites/user/:uid', api.getFavoritesByUserID);
app.post('/api/0/favorites', api.addFavorite);
app.post('/api/0/favorites/:id', api.editFavorite);
app.delete('/api/0/favorites/:id', api.deleteFavorite);

app.get('/api/0/products', api.getProducts);
app.get('/api/0/products/:id', api.getProductByID);
app.get('/api/0/products/stripe/:id', api.getProductByStripeID);
app.get('/api/0/products/post/:pid', api.getProductsByPostID);
app.get('/api/0/products/stripeAcc/:id', api.getProductsByStripeAccID);
app.post('/api/0/products', api.addProduct);
app.post('/api/0/products/:id', api.editProduct);
app.post('/api/0/products/stripe/:id', api.editProductByStripeID);
app.delete('/api/0/products/:id', api.deleteProduct);

app.get('/api/0/skus', api.getSkus);
app.get('/api/0/skus/:id', api.getSkuByID);
app.get('/api/0/skus/stripe/:id', api.getSkuByStripeID);
app.get('/api/0/skus/post/:pid', api.getSkusByPostID);
app.get('/api/0/skus/stripeAcc/:id', api.getSkusByStripeAccID);
app.post('/api/0/skus', api.addSku);
app.post('/api/0/skus/pas', api.addProductAndSku)
app.post('/api/0/skus/:id', api.editSku);
app.post('/api/0/skus/stripe/:id', api.editSkuByStripeID);
app.delete('/api/0/skus/:id', api.deleteSku);

app.get('/api/0/charges', api.getCharges);
app.get('/api/0/charges/:id', api.getChargeByID);
app.get('/api/0/charges/stripe/:id', api.getChargeByStripeID);
app.get('/api/0/charges/post/:pid', api.getChargesByPostID);
app.get('/api/0/charges/user/:uid', api.getChargesByUserID);
app.get('/api/0/charges/postAuthor/:paid', api.getChargesByPostAuthorID);
app.get('/api/0/charges/stripeAcc/:id', api.getChargesByStripeAccID);
app.post('/api/0/charges', api.addCharge);
app.post('/api/0/charges/:id', api.editCharge);
app.post('/api/0/charges/stripe/:id', api.editChargeByStripeID);
app.post('/api/0/charges/capture/:id', api.captureCharge);
app.post('/api/0/charges/capture/stripe/:id', api.captureChargeByStripeID);
app.post('/api/0/charges/test/create', api.testCreateCharge);

app.get('/api/0/accounts', api.getAccounts);
app.get('/api/0/accounts/:id', api.getAccountByID);
app.get('/api/0/accounts/stripe/:id', api.getAccountByStripeID);
app.get('/api/0/accounts/user/:uid', api.getAccountsByUserID);
app.post('/api/0/accounts', api.addAccount);
app.post('/api/0/accounts/:id', api.editAccont);
app.post('/api/0/accounts/stripe/:id', api.editAccountByStripeID);
app.post('/api/0/accounts/reject/:id', api.rejectAccount);
app.post('/api/0/accounts/reject/stripe/:id', api.rejectAccountByStripeID);
app.delete('/api/0/accounts/:id', api.deleteAccount);

// The following apis are deprecated.
/*
 app.get('/api/0/customers', api.getCustomers);
 app.get('/api/0/customers/:id', api.getCustomerByID);
 app.post('/api/0/customers', api.addCustomer);
 app.post('/api/0/customers/:id', api.editCustomer);
 app.delete('/api/0/customers/:id', api.deleteCustomer);

 app.post('/api/0/customers/:id/favor', api.addFavoriteElem);
 app.delete('/api/0/customers/:id/favor/:fid', api.delFavoriteElem);
 */

//
app.post('/api/0/sendMail', api.sendMail);


db.once('open', ()=> {
    httpServer.listen(port);
});