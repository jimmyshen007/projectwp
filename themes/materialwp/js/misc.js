/**
 * Created by root on 12/31/16.
 */
// Function to parse queryString into JSON object.

var queryStringToJSON = function (url) {
    if (url === '')
        return '';
    var pairs = (url || location.search).slice(1).split('&');
    var result = {};
    for (var idx in pairs) {
        var pair = pairs[idx].split('=');
        if (!!pair[0])
            result[pair[0].toLowerCase()] = decodeURIComponent(pair[1] || '');
    }
    return result;
};
