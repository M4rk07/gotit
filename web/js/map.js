var marker;
var map;
var imageBlob = null;
var shownMarkers = [];
var baseUrl = "http://localhost/gotit/web/app.php";
// Config
var maxImageSize = 800;

jQuery(document).ready(function($){
    initMap();
    getMarkers();
    getConfiguration();
});

function initMap() {
    var uluru = {lat: 44.771111, lng: 20.514565};

    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 9,
        center: uluru
    });

    // Try HTML5 geolocation.
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            map.setCenter(pos);
            map.setZoom(12);
        }, function() {
            //handleLocationError(true, infoWindow, map.getCenter());
        });
    } else {
        // Browser doesn't support Geolocation
        //handleLocationError(false, infoWindow, map.getCenter());
    }

    google.maps.event.addListener(map, "click", function(event) {
        if(marker != null) {
            marker.setMap(null);
        }

        marker = new google.maps.Marker({
            position: event.latLng,
            map: map
        });

        $("#mainModal").modal('show');
    });
}

function imageOptimization(input) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        if(!file.type.match(/image.*/)) {

        }
        var reader = new FileReader();

        reader.onload = function (e) {

            var image = new Image();
            image.src = e.target.result;
            // Resize the image
            var canvas = document.createElement('canvas'),
                width = image.width,
                height = image.height;
            if (width > height) {
                if (width > maxImageSize) {
                    height *= maxImageSize / width;
                    width = maxImageSize;
                }
            } else {
                if (height > maxImageSize) {
                    width *= maxImageSize / height;
                    height = maxImageSize;
                }
            }
            canvas.width = width;
            canvas.height = height;
            canvas.getContext('2d').drawImage(image, 0, 0, width, height);
            var dataUrl = canvas.toDataURL('image/jpeg');
            imageBlob = dataURLToBlob(dataUrl);
        };

        reader.readAsDataURL(file);
    }
}

function saveItemData() {
    var endpoint = '/items';
    var description = document.getElementById('itemDescription').value;
    var latlng = marker.getPosition();

    var formData = new FormData();
    formData.append("description", description);
    formData.append("lat", latlng.lat());
    formData.append("lng", latlng.lng());
    if(imageBlob != null)
        formData.append("image", imageBlob);

    sendRequest(endpoint, "POST", formData, function(data, responseCode) {
        if (responseCode == 200) {

        }
    });
}

function getConfiguration() {
    var endpoint = '/config';

    sendRequest(endpoint, "GET", null, function(data, responseCode) {
        if (responseCode == 200) {
            data = JSON.parse(data);
            maxImageSize = data.maxImageSize;
        }
    });
}

function getMarkers() {
    var endpoint = '/markers';

    sendRequest(endpoint, "GET", null, function(data, responseCode) {
        if (responseCode == 200) {
            var markers = JSON.parse(data);

            Array.prototype.forEach.call(markers, function (markerElem) {
                var point = new google.maps.LatLng(
                    parseFloat(markerElem.lat),
                    parseFloat(markerElem.lng)
                );

                var icon = {};

                var marker = new google.maps.Marker({
                    map: map,
                    position: point,
                    label: icon.label
                });

                var infowindow = new google.maps.InfoWindow();
                marker.addListener('click', function() {
                    closeCurrentMarker();
                    $("#mainModal").modal('show');
                    getItems(markerElem.marker_id);
                });

                shownMarkers.push({marker: marker});
            });
        }
    });
}

function getItems(markerId) {
    var endpoint = '/items?markerId=' + markerId;
    sendRequest(endpoint, "GET", null, function(data, responseCode) {
        if (responseCode == 200) {
            document.getElementById("mainListing").innerHTML = "";
            document.getElementById("mainListing").appendChild(getItemsListing(JSON.parse(data)));
        }
    });
}

function getItemsListing(items) {
    var itemlisting = document.createElement('div');
    var row = document.createElement("row");
    var firstCol12 = document.createElement("div");

    firstCol12.setAttribute("class", "col-md-12");

    Array.prototype.forEach.call(items, function (itemElem){
        var description = document.createElement("strong");
        description.textContent = itemElem.description;
        firstCol12.appendChild(description);
    });

    row.appendChild(firstCol12);
    itemlisting.appendChild(row);

    return itemlisting;
}

function closeCurrentMarker() {
    imageBlob = null;
    if(marker!=null) {
        marker.setMap(null);
        marker = null;
    }
}

function dataURLToBlob(dataURL) {
    var BASE64_MARKER = ';base64,';
    var parts, contentType, raw;
    if (dataURL.indexOf(BASE64_MARKER) == -1) {
        parts = dataURL.split(',');
        contentType = parts[0].split(':')[1];
        raw = parts[1];

        return new Blob([raw], {type: contentType});
    }

    parts = dataURL.split(BASE64_MARKER);
    contentType = parts[0].split(':')[1];
    raw = window.atob(parts[1]);
    var rawLength = raw.length;

    var uInt8Array = new Uint8Array(rawLength);

    for (var i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
    }

    return new Blob([uInt8Array], {type: contentType});
}
