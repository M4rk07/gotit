var lastElement = null;
var searchType = null;
var withOthers = false;

function enableDisableSearchIcon (element, type) {
    if(element.style.getPropertyValue("filter") != "none") {
        if(lastElement != null && type != "other") {
            enableGrayscale(lastElement);
        }
        disableGrayscale(element);
        if(type=="other")
            withOthers = true;
        else {
            lastElement = element;
            searchType = type;
        }
    }
    else {
        enableGrayscale(element);
        if(type=="other")
            withOthers = false;
        else
            searchType = null;
    }
}

function enableGrayscale (element) {
    element.style.filter = "grayscale(100%)";
    element.style.webkitFilter = "grayscale(100%)";
}

function disableGrayscale (element) {
    element.style.filter = "none";
    element.style.webkitFilter = "none";
}
