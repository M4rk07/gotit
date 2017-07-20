var imageBlob = null;

function setItemType(type) {
    document.getElementById("itemType").value = type;
    document.getElementById("dropdownIcon").setAttribute("src", getIcon(type));
}

function resetItemForm() {
    setItemType(DEFAULT_ITEM_TYPE);
    document.getElementById('itemDescription').value = "";
    document.getElementById("image").value = "";
}

function getItemFormData() {
    var latlng = marker.marker.getPosition();

    var description = document.getElementById('itemDescription').value;
    var type = document.getElementById("itemType").value;
    var lat = latlng.lat();
    var lng = latlng.lng();

    return {markerId: marker.markerId, description: description, lat: lat, lng: lng, type: type, imageBlob: imageBlob};
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
                if (width > MAX_IMG_SIZE) {
                    height *= MAX_IMG_SIZE / width;
                    width = MAX_IMG_SIZE;
                }
            } else {
                if (height > MAX_IMG_SIZE) {
                    width *= MAX_IMG_SIZE / height;
                    height = MAX_IMG_SIZE;
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

function hideItemForm() {
    document.getElementById('itemFormWrapper').style.display = "none";
}

function saveItemData() {
    var endpoint = '/items';
    var itemFormData = getItemFormData();

    var formData = new FormData();
    formData.append("description", itemFormData.description);
    formData.append("lat", itemFormData.lat);
    formData.append("lng", itemFormData.lng);
    formData.append("type", itemFormData.type);
    if(itemFormData.markerId != null)
        formData.append("markerId", itemFormData.markerId);
    if(itemFormData.imageBlob != null)
        formData.append("image", imageBlob);

    sendRequest(endpoint, "POST", formData, function(data, responseCode) {
        if (responseCode == 200) {
            var item = JSON.parse(data);
            pushItemToList(item);

            marker.marker.setIcon(getMarkerIcon(item.marker.num_of_items > 1 ? "plus1" : item.marker.type));
            marker.markerId = item.marker.marker_id;
            marker.closable = false;
            shownMarkers.push({marker: marker.marker, markerId: marker.markerId, type: item.marker.type});
        }
    });
}
