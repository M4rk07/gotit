/**
 * Created by M4rk0 on 1/24/2018.
 */

function saveReport(itemId) {
    var endpoint = '/items/reports/'+itemId;
    var description = $('#report-description');
    var messagePlace = $('#message');

    var formData = new FormData();
    formData.append("description", description.val());

    sendRequest(endpoint, "POST", formData, function(data, responseCode) {
        if (responseCode == 200) {
            messagePlace.html('<div class="alert alert-success"><strong>Success!</strong> Thank you for helping our community. You will now be redirected to homepage.</div>');
            setTimeout(function () {
                window.location.href = homepage;
            }, 4000);
        }
        else
            handleErrorResponse(data,'#message');
    });
}