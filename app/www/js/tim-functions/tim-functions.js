//------------------------------------------------------------------------------/
// TIM FUNCTIONS
//------------------------------------------------------------------------------/
/**
 * 2015-12-11
 * LingTalfi
 * 
 * Dependencies: jquery
 * 
 */

function _timErrorToString(error, sep) {
    var s = "";
    if (!sep) {
        sep = "\n";
    }
    // assuming is either a string, or an array
    console.log(Object.prototype.toString.call(error));
    if (-1 !== $.inArray(Object.prototype.toString.call(error), ['[object Array]', '[object Object]'])) {
        var c = 0;
        for (var i in error) {
            if (0 !== c) {
                s += sep;
            }
            s += error[i];
            c++;
        }
    }
    else {
        s = error;
    }
    return s;
}

// errors meant for the end user
function timError(error) {
    alert("tim error: " + _timErrorToString(error));
}

// errors meant for the developer
function timLog(error) {
    console.log("tim log: " + error);
}

function timPost(url, data, onSuccess, onFailure) {
    return $.post(url, data, function (thedata) {
        if (thedata.t) {
            if ('m' in thedata) {
                if ('s' === thedata.t) {
                    onSuccess(thedata.m);
                }
                else if ('e' === thedata.t) {
                    if (!!onFailure) {
                        onFailure(thedata.m);
                    }
                    else {
                        timError(thedata.m);
                    }
                }
                else {
                    timLog("protocol violation, t must be either s or e");
                }
            }
            else {
                timLog("protocol violation, m key not found");
            }
        }
        else {
            timLog("protocol violation, t key not found");
        }
    }, 'json');
}