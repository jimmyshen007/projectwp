/**
 * Created by root on 12/31/16.
 */

var NODE_API_INIT = '/node/api/0/';
var MAPBOX_TOKEN = 'pk.eyJ1IjoianNvbnd1IiwiYSI6ImNpa3YwZnpzMzAwZTN1YWtzYWcwNXg2ZzMifQ.v6YZ9axqDwZSlzbjmMOfTg';

// Function to parse queryString into JSON object.
var queryStringToJSON = function (url) {
    if (url === '')
        return '';
    var pairs = (url || location.search).split('&');
    var result = {};
    for (var idx in pairs) {
        var pair = pairs[idx].split('=');
        if (!!pair[0]) {
            var key = pair[0].toLowerCase();
            //If we need to construct an array for this attribute.
            if(key.endsWith('[]')){
                key = key.substr(0, key.length-2);
                if(key in result){
                    result[key].push(decodeURIComponent(pair[1] || ''));
                }else{
                    result[key] = [decodeURIComponent(pair[1] || '')];
                }
            }else {
                result[key] = decodeURIComponent(pair[1] || '');
            }
        }
    }
    return result;
};
