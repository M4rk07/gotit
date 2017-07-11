/**
 * Created by marko on 11.7.17..
*/
$('#accTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show')
});

function showLoginForm() {
    $('#loginTab').tab('show');
    $("#loginModal").modal('show');
}

function showRegistrationForm() {
    $('#registrationTab').tab('show');
    $("#loginModal").modal('show');
}

function registerUser () {
    var endpoint = '/register';
    var regEmail = document.getElementById('regEmail').value;
    var regDisplayName = document.getElementById('regDisplayName').value;
    var regPassword = document.getElementById('regPassword').value;
    var regRepeatedPassword = document.getElementById('regRepeatedPassword').value;
    var regPhoneNumber = document.getElementById('regPhoneNumber').value;

    var formData = "email="+regEmail+"&displayName="+regDisplayName+"&password="+regPassword;
    if(regPhoneNumber==null)
        formData+="&phoneNumber="+regPhoneNumber;

    sendRequest(endpoint, "POST", formData, function(data, responseCode) {
        if (responseCode == 200) {
        }
    });
}

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

    request.open(method, baseUrl + endpoint, true);
    if(data!=null) {
        if(!(data instanceof FormData)) {
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        }
        request.send(data);
    }
    else
        request.send();
}