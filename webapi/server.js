import path from 'path';
import bodyParser from 'body-parser';
import express from 'express';
import http from 'http';
import config from 'config';

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
app.use(require('serve-static')(path.join(__dirname, config.get('buildDirectory'))));
app.use(bodyParser.urlencoded({
  extended: true
}));
app.use(bodyParser.json());

/**
 * API Endpoints
 */
app.get('/api/0/orders', api.getOrders);
app.get('/api/0/orders/:id', api.getOrderByID);
app.get('/api/0/orders/user/:uid', api.getOrdersByUserID);
app.get('/api/0/orders/post/:pid', api.getOrdersByPostID);
app.get('/api/0/orders/postAuthor/:paid', api.getOrdersByPostAuthorID);
app.post('/api/0/orders', api.addOrder);
app.post('/api/0/orders/:id', api.editOrder);
app.delete('/api/0/orders/:id', api.deleteOrder);

app.get('/api/0/favorites', api.getFavorites);
app.get('/api/0/favorites/:id', api.getFavoriteByID);
app.get('/api/0/favorites/user/:uid', api.getFavoritesByUserID);
app.post('/api/0/favorites', api.addFavorite);
app.post('/api/0/favorites/:id', api.editFavorite);
app.delete('/api/0/favorites/:id', api.deleteFavorite);

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

db.once('open', ()=> {
    httpServer.listen(port);
});