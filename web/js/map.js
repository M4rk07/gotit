var marker;
var markerClosable = false;
var map;
var imageBlob = null;
var shownMarkers = [];
var baseUrl = "http://localhost/gotit/web/app_dev.php";
var baseImgUrl = "http://localhost/gotit/web/images";
// Config
var maxImageSize = 800;

jQuery(document).ready(function($){
    initMap();
    getMarkers();
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
        closeCurrentMarker();
        clearTheListing();

        marker = new google.maps.Marker({
            position: event.latLng,
            map: map
        });
        markerClosable = true;

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

                var itemMarker = new google.maps.Marker({
                    map: map,
                    position: point,
                    label: icon.label
                });

                var infowindow = new google.maps.InfoWindow();
                itemMarker.addListener('click', function() {
                    closeCurrentMarker();
                    marker = itemMarker;
                    markerClosable = false;
                    $("#mainModal").modal('show');
                    getItems(markerElem.marker_id);
                });

                shownMarkers.push({marker: itemMarker});
            });
        }
    });
}

function getItems(markerId) {
    var endpoint = '/items?markerId=' + markerId;
    sendRequest(endpoint, "GET", null, function(data, responseCode) {
        if (responseCode == 200) {
            data = JSON.parse(data);
            document.getElementById("mainListing").innerHTML = "";
            document.getElementById("mainListing").appendChild(getItemsListing(data));
            var numOfItems = data.length;
            var itemsWord = "items";
            if(numOfItems == 1)
                itemsWord = "item";

            document.getElementById("numOfItems").innerText = data.length + " " + itemsWord;
        }
    });
}

function getItemsListing(items) {

    var itemlisting = document.createElement('div');
    var row = document.createElement("row");
    var firstCol12 = document.createElement("div");

    firstCol12.setAttribute("class", "col-md-12");

    Array.prototype.forEach.call(items, function (itemElem){
        var wrapper = document.createElement("div");
        wrapper.setAttribute("class", "list-group");

        var description = document.createElement("span");
        description.setAttribute("class", "itemDescription");
        description.textContent = itemElem.description;
        wrapper.appendChild(description);

        var dropdown = document.createElement("div");
        dropdown.setAttribute("class", "dropdown");

        var username = document.createElement("a");
        username.setAttribute("class", "dropdown-toggle displayName");
        username.setAttribute("data-toggle", "dropdown");
        username.textContent = itemElem.user.display_name;

        var infoWindow = document.createElement("div");
        infoWindow.setAttribute("class", "dropdown-menu userInfo");

        if(itemElem.user.phone_number != null)
            infoWindow.innerHTML = 'Phone: <strong>'+itemElem.user.phone_number+'</strong>';
        else
            infoWindow.innerHTML = 'No contact information.';

        dropdown.appendChild(username);
        dropdown.appendChild(infoWindow);
        wrapper.appendChild(dropdown);

        if(itemElem.image_url != null) {
            var image = document.createElement("img");
            image.setAttribute("src", baseImgUrl + "/" + itemElem.image_url);
            image.setAttribute("class", "itemImage");
            wrapper.appendChild(image);
        }

        var hr = document.createElement("hr");
        wrapper.appendChild(hr);

        firstCol12.appendChild(wrapper);

    });

    row.appendChild(firstCol12);
    itemlisting.appendChild(row);

    return itemlisting;
}

function closeCurrentMarker() {
    imageBlob = null;
    if(marker!=null && markerClosable) {
        marker.setMap(null);
        marker = null;
    }
}

function clearTheListing() {
    document.getElementById("mainListing").innerHTML = "";
    document.getElementById("numOfItems").innerText = "Bag";
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
