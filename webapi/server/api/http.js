import * as service from './service';

export function getDishes(req, res) {
  service.getDishes()
  .then((dishes) => res.json(dishes))
  .catch(err => {
    res.status(400);
    res.json({error: err});
  });
}

export function addDish(req, res) {
  service.addDish(req.body)
  .then((dish) => res.json(dish))
  .catch(err => {
    res.status(400);
    res.json({error: err, dish: req.body});
  });
}

export function editDish(req, res) {
  service.editDish(req.params.id, req.body)
  .then((dish) => res.json(dish))
  .catch(err => {
    res.status(400);
    res.json({error: err, dish: req.body});
  });
}

export function deleteDish(req, res) {
  service.deleteDish(req.params.id)
  .then((dish) => res.json(dish))
  .catch(err => {
    res.status(400);
    res.json({error: err, dish: req.body});
  });
}

//TODO
//All other API calls come here.