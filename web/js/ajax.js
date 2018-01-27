/**
 * Created by M4rk0 on 1/25/2018.
 */
function sendRequest(endpoint, method, data, callback) {
    var request;

    if (window.XMLHttpRequest) {
        // code for modern browsers
        request = new XMLHttpRequest();
    } else {
        // code for old IE browsers
        request = new ActiveXObject("Microsoft.XMLHTTP");
    }

    request.onreadystatechange = function() {
        if (request.readyState == 4) {
            callback(this.response, this.status);
        }
    };

    request.open(method, BASE_URL + endpoint, true);
    if(data!=null) {
        if(!(data instanceof FormData)) {
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        }
        request.send(data);
    }
    else
        request.send();
}