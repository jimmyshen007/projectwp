import * as service from './service';

export function getOrders(req, res) {
  service.getOrders()
  .then((orders) => res.json(orders))
  .catch(err => {
    res.status(400);
    res.json({error: err});
  });
}

export function addOrder(req, res) {
  service.addOrder(req.body)
  .then((order) => res.json(order))
  .catch(err => {
    res.status(400);
    res.json({error: err, order: req.body});
  });
}

export function editOrder(req, res) {
  service.editOrder(req.params.id, req.body)
  .then((order) => res.json(order))
  .catch(err => {
    res.status(400);
    res.json({error: err, order: req.body});
  });
}

export function deleteOrder(req, res) {
  service.deleteOrder(req.params.id)
  .then((order) => res.json(order))
  .catch(err => {
    res.status(400);
    res.json({error: err, order: req.body});
  });
}

export function getCustomers(req, res) {
  service.getCustomers()
      .then((customers) => res.json(customers))
      .catch(err => {
        res.status(400);
        res.json({error: err});
      });
}

export function addCustomer(req, res) {
  service.addCustomer(req.body)
      .then((customer) => res.json(customer))
      .catch(err => {
        res.status(400);
        res.json({error: err, customer: req.body});
      });
}

export function editCustomer(req, res) {
  service.editCustomer(req.params.id, req.body)
      .then((customer) => res.json(customer))
      .catch(err => {
        res.status(400);
        res.json({error: err, customer: req.body});
      });
}

export function deleteCustomer(req, res) {
  service.deleteCustomer(req.params.id)
      .then((customer) => res.json(customer))
      .catch(err => {
        res.status(400);
        res.json({error: err, customer: req.body});
      });
}
//TODO
//All other API calls come here.