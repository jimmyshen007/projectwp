/**
 * Created by root on 7/4/16.
 */
import promise from 'bluebird';
import config from 'config';
import m from 'mongoose';

m.Promise = promise;
m.connect('mongodb://' + config.get('mongodb.host') + ':' + config.get('mongodb.port') + '/' + config.get('mongodb.db'));
export let db = m.connection;
db.on('error', console.error.bind(console, 'connection error:'));
process.on('SIGINT', function() {
    m.connection.close(function () {
        console.log('Mongoose default connection disconnected through app termination');
        process.exit(0);
    });
});

let Schema = m.Schema;
export let orderSchema = new Schema({
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
});

export let favoriteSchema = new Schema({
    fType: String,
    fValue: String,
    userID: String
});

export let productSchema = new Schema({
    postID: String,
    stripeProdID: String,
    stripeAccID: String
});

export let skuSchema = new Schema({
    postID: String,
    stripeSkuID: String,
    stripeAccID: String
});

export let chargeSchema = new Schema({
    postID: String,
    postAuthorID: String,
    userID: String,
    stripeChargeID: String,
    stripeAccID: String
});

export let accountSchema = new Schema({
    userID: {type: String, index: true, unique: true},
    stripeAccID: String
});

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
