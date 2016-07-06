import path from 'path';
import bodyParser from 'body-parser';
import express from 'express';
import http from 'http';
import config from 'config';

import * as api from './server/api/http';
import db from './server/api/db';

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
app.post('/api/0/orders', api.addOrder);
app.post('/api/0/orders/:id', api.editOrder);
app.delete('/api/0/orders/:id', api.deleteOrder);

app.get('/api/0/customers', api.getCustomers);
app.get('/api/0/customers/:id', api.getCustomerByID);
app.post('/api/0/customers', api.addCustomer);
app.post('/api/0/customers/:id', api.editCustomer);
app.delete('/api/0/customers/:id', api.deleteCustomer);

db.once('open', ()=> {
    httpServer.listen(port);
});