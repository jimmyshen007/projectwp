/**
 * Created by root on 12/17/16.
 */
import chai from 'chai';
import sinon from 'sinon';
import stripe from 'stripe';
import * as service from '../server/api/service';

let sapi = stripe('sk_test_tpFrMjZ9ivdUjnEeEXDiqq98');
let expect = chai.expect;

describe('TestCharge', () => {

    describe('Charge User', ()=> {
        it('should success', () => {
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
                let ret = service.addCharge(  {amount: 2000,
                    currency: "usd",
                    source: "tok_189fjG2eZvKYlo2C3EBNrSQm", // obtained with Stripe.js
                    description: "Charge for abigail.thompson@example.com"});

                expect(ret, {})
            });
        });
    }, {
        callbackMode: 'promises'
    });
});