/**
 * Created by root on 1/2/17.
 */
var embeddedMongoDB = require('node-embedded-mongodb');
var config = require('config');

// If you don't want the logs, set true.
embeddedMongoDB.silentMode(false);

//process.stdin.resume(); //so the program will not close instantly

function exitHandler(options, err) {

    //Stop embedded mongodb
    embeddedMongoDB.stop(config.get('mongodb.port'), true, function(err) {
        console.log('Embedded mongodb stopped.');
    });

    if (options.cleanup){ console.log('clean');}
    else if (err) {console.log(err.stack);}
    else if(options.exit) {process.exit();}
    else if(options.ctrlc) {process.exit()}
}

//do something when app is closing
process.on('exit', exitHandler.bind(null,{cleanup:true}));

//catches ctrl+c event
process.on('SIGINT', exitHandler.bind(null, {ctrlc:true}));

//catches uncaught exceptions
process.on('uncaughtException', exitHandler.bind(null, {exit:true}));

module.exports = embeddedMongoDB;
