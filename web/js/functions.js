/**
 * Created by marko on 11.7.17..
*/

$('#accTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');
});

$('#boardTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');
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