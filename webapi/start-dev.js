var emdb = require('./test.db.js');
var config = require('config');
var mongodImportCmd = 'mongoimport --host ' + config.get('mongodb.host') + ' --port '
    + config.get('mongodb.port') + ' --db test --drop --file ';
var importCitiesCmd = mongodImportCmd + 'test/data/cities.json --collection cities';
var importSchoolsCmd = mongodImportCmd + 'test/data/schools.json --collection schools';
var cmds = [importCitiesCmd, importSchoolsCmd];
var dbPath = 'test/data/db/';
var logPath = 'test/log/mongod.log';
emdb.start(dbPath, logPath, config.get('mongodb.port'), function(err) {
    console.log('Embedded mongodb started.');
    var code = 0;
    for(var i = 0; i < cmds.length; i++){
        console.log('Executing: ' + cmds[i]);
        exec(cmds[i], function(err, out, code) {
            if (err instanceof Error)
                throw err;
            console.log('Import Data Finished :)')
        });
    }
    require('./server.babel.js');
    require('./server.webpack.js');
});
