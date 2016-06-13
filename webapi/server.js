import path from 'path';
import bodyParser from 'body-parser';
import express from 'express';
import http from 'http';
import config from 'config';

import * as api from './server/api/http';
import * as eventService from './server/api/service/event';
import * as uni from './server/app.js';

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
app.post('/api/0/orders', api.addOrder);
app.post('/api/0/orders/:id', api.editOrder);
app.delete('/api/0/orders/:id', api.deleteOrder);

httpServer.listen(port);