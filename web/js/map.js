var marker = {marker: null, markerId: null, closable: true};
var map = null;
var shownMarkers = [];
// Constatnts
const BASE_URL = "http://localhost/gotit/web/app_dev.php";
const BASE_IMG_URL = "http://localhost/gotit/web/images";
const MAX_IMG_SIZE = 800;
const DEFAULT_ITEM_TYPE = "empty";

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

        var emptyMarker = createMarker(event.latLng.lat(), event.latLng.lng(), {}, DEFAULT_ITEM_TYPE);

        setCurrentMarker(emptyMarker, null, true);
        openModal();
    });

}

function getMarkers() {
    var endpoint = '/markers';

    sendRequest(endpoint, "GET", null, function(data, responseCode) {
        if (responseCode == 200) {
            var markers = JSON.parse(data);

            Array.prototype.forEach.call(markers, function (markerElem) {

                var label = null;
                if(markerElem.num_of_items > 1) {
                    label = {text:  String(markerElem.num_of_items), color: "white"};
                }

                shownMarkers.push({marker: setNonEmptyMarker(markerElem.marker_id,
                    markerElem.lat, markerElem.lng, label, markerElem.type.type_id)});
            });
        }
    });
}

function setNonEmptyMarker(markerId, lat, lng, label, type) {
    var itemMarker = createMarker(lat, lng, label, type);
    return itemMarker.addListener('click', function() {
        closeCurrentMarker();
        setCurrentMarker(itemMarker, markerId, false);
        getItems(markerId);
        openModal();
    });

}

function createMarker(lat, lng, label, type) {
    var newMarker = new google.maps.Marker({
        map: map,
        position: new google.maps.LatLng(
            parseFloat(lat),
            parseFloat(lng)
        ),
        icon: getMarkerIcon(type)
    });
    if(label!=null)
        newMarker.setLabel(label);
    return newMarker;
}

function setCurrentMarker(newMarker, markerId, closable) {
    marker.marker = newMarker;
    marker.markerId = markerId;
    marker.closable = closable;
}

function openModal() {
    $("#mainModal").modal('show');
}

function getItems(markerId) {
    var endpoint = '/items?markerId=' + markerId;
    sendRequest(endpoint, "GET", null, function(data, responseCode) {
        if (responseCode == 200) {
            data = JSON.parse(data);
            document.getElementById("mainListing").innerHTML = "";
            getItemsListing(data);
            var numOfItems = data.length;
            var itemsWord = "items";
            if(numOfItems == 1)
                itemsWord = "item";

            document.getElementById("numOfItems").innerText = data.length + " " + itemsWord;
        }
    });
}

function getItemsListing(items) {

    Array.prototype.forEach.call(items, function (itemElem){
        pushItemToList(itemElem);
    });

}

function pushItemToList (item) {
    resetItemForm();
    var mainListing = document.getElementById("mainListing");
    mainListing.insertBefore(getItemWrapper(item), mainListing.firstChild);
}

function getItemWrapper(itemElem) {
    var wrapper = document.createElement("div");
    wrapper.setAttribute("class", "list-group");

    var row1 = document.createElement("div");
    row1.setAttribute("class", "row");

    var row1col1 = document.createElement("div");
    row1col1.setAttribute("class", "col-md-1");
    var row1col2 = document.createElement("div");
    row1col2.setAttribute("class", "col-md-11");

    var icon = document.createElement("img");
    icon.setAttribute("class", "listingIcon");
    icon.setAttribute("src", getIcon(itemElem.type.type_id));

    var row1col2wrapper =document.createElement("div");
    var description = document.createElement("span");
    description.setAttribute("class", "itemDescription");
    description.innerHTML = itemElem.description;

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
    row1col2wrapper.appendChild(description);
    row1col2wrapper.appendChild(dropdown);

    row1col1.appendChild(icon);
    row1col2.appendChild(row1col2wrapper);
    row1.appendChild(row1col1);
    row1.appendChild(row1col2);
    wrapper.appendChild(row1);

    if(itemElem.image_url != null) {
        var image = document.createElement("img");
        image.setAttribute("src", BASE_IMG_URL + "/" + itemElem.image_url);
        image.setAttribute("class", "itemImage");
        wrapper.appendChild(image);
    }

    var hr = document.createElement("hr");
    wrapper.appendChild(hr);

    return wrapper;
}

function getMarkerIcon(type) {
    return {
        url: getIcon(type),
        scaledSize: new google.maps.Size(42, 42),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(21, 42),
        labelOrigin: new google.maps.Point(21, 16)
    };
}

function getIcon (type) {
    return BASE_IMG_URL + "/icons/ic-" + type.toLowerCase() + ".png";
}

function removeCurrentMarker() {
    if(marker.marker != null && marker.closable) {
        marker.marker.setMap(null);
        marker.marker = null;
        marker.markerId = null;
    }
}

function closeCurrentMarker() {
    imageBlob = null;
    removeCurrentMarker();
    clearTheListing();
    resetItemForm();
}

function clearTheListing() {
    document.getElementById("mainListing").innerHTML = "";
    document.getElementById("numOfItems").innerText = "New Bag";
}

