function enableDisableSearchIcon (element, type) {
    if(element.style.getPropertyValue("filter") != "none") {
        element.style.filter = "none";
        element.style.webkitFilter = "none";
    }
    else {
        element.style.filter = "grayscale(100%)";
        element.style.webkitFilter = "grayscale(100%)";
    }
}
