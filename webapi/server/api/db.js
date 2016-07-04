/**
 * Created by root on 7/4/16.
 */
import promise from 'bluebird';
import config from 'config';
import m from 'mongoose';

m.Promise = promise;
m.connect('mongodb://localhost:' + config.get('mongodb.port') + '/' + config.get('mongodb.db'));
let db = m.connection;
db.on('error', console.error.bind(console, 'connection error:'));
process.on('SIGINT', function() {
    m.connection.close(function () {
        console.log('Mongoose default connection disconnected through app termination');
        process.exit(0);
    });
});

export {db as default};

