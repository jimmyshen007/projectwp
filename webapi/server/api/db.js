/**
 * Created by root on 7/4/16.
 */
import promise from 'bluebird';
import config from 'config';
import m from 'mongoose';

m.Promise = promise;

//Need to start connection later for the service to be ready.
console.log("Wait for mongoose connection");
setTimeout(function() {
    m.connect('mongodb://' + config.get('mongodb.host') + ':' + config.get('mongodb.port') + '/' + config.get('mongodb.db'),
    {socketOptions: {connectTimeoutMS: 360000, soctMS: 360000}});
    console.log("Mongoose connection ready!");
}, 10000);

export let db = m.connection;
db.on('error', console.error.bind(console, 'connection error:'));
process.on('SIGINT', function() {
    m.connection.close(function () {
        console.log('Mongoose default connection disconnected through app termination');
    });
});

let Schema = m.Schema;
export let schemas = {
    orders: new Schema({
        postID: String,
        postAuthorID: String,
        userID: String,
        skuID: String,
        stripeOrderID: String,
        stripeChargeIDs: [String],
        appStatus: String,
        startDate: Date,
        term: String,
        numTenant: Number,
        stripeAccID: String
    }),
    favorites: new Schema({
        fType: String,
        fValue: String,
        userID: String
    }),
    products: new Schema({
        postID: String,
        stripeProdID: String,
        stripeAccID: String
    }),
    skus: new Schema({
        postID: String,
        stripeSkuID: String,
        stripeAccID: String
    }),
    charges: new Schema({
        postID: String,
        postAuthorID: String,
        userID: String,
        stripeChargeID: String,
        stripeAccID: String
    }),
    refunds: new Schema({
        stripeRefundID: String,
        stripeChargeID: String,
        stripeAccID: String,
        userID: String
    }),
    cards: new Schema({
        stripeCardID: String,
        stripeAccID: String,
        stripeCusID: String,
        userID: String
    }),
    customers: new Schema({
        stripeCusID: String,
        stripeAccID: String,
        userID: {type: String, index: true, unique: true},
    }),
    accounts: new Schema({
        userID: {type: String, index: true, unique: true},
        stripeAccID: String
    }),
    transfers: new Schema({
        stripeTransID: String,
        stripeAccID: String,
        userID: String
    }),
    extaccounts: new Schema({
        stripeExtaccountID: String,
        stripeAccID: String,
        userID: String
    }),
    cities:  new Schema({
        name: String,
        state: String,
        country: String,
        zh_name: String,
        icon_id: String,
        hits: Number
    }),
    schools: new Schema({
        name: String,
        state: String,
        country: String,
        zh_name: String,
        icon_id: String,
        hits: Number
    })
}

/*
 * Deprecated.
 */
let favorSchema = new Schema({
    type: String,
    value: String
});

let customerSchema = new Schema({
    userID: String,
    favoriteList: [favorSchema]
});
////////////////////////////////
