//set objects functions - This is started from the menu.js window.onload function
function loadThisPage() {
    //set onClick functions for all player names
    $('[class="playerName"]:visible').each(function () {
        this.setAttribute("onClick", "openPlayerInfo(this.id, event);");
    });

    $('[class="cancelOfferButton"]:visible').each(function () {
        this.setAttribute("onClick", "cancelTransfer(this.id);");
    });

    $('[class="acceptOfferButton"]:visible').each(function () {
        this.setAttribute("onClick", "acceptTransfer(this.id);");
    });
}

//Store GET details from url in an array
var GETARRAY = {};
if (document.location.toString().indexOf('?') !== -1) {
    var query = document.location
        .toString()
        // get the query string
        .replace(/^.*?\?/, '')
        // and remove any existing hash string (thanks, @vrijdenker)
        .replace(/#.*$/, '')
        .split('&');

    for (var i = 0, l = query.length; i < l; i++) {
        var aux = decodeURIComponent(query[i]).split('=');
        GETARRAY[aux[0]] = aux[1];
    }
}

//sends deletes transfer offer request to server
function cancelTransfer(id) {
    var xmlhttp;
    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            //print out table details
            document.getElementById("offersHolder").innerHTML = xmlhttp.responseText;
            //reset onclicks
            loadThisPage();
        }
    }


    document.getElementById("offersHolder").innerHTML = '<img id="loadingImage" src="assets/images/layOutPics/loading.gif">';

    //make call to cancelOffer() function in model transfer.php
    xmlhttp.open("GET", "index.php?cancelOffer=true&offerid=" + id + "&squadid=" + GETARRAY['squadid'], true);
    xmlhttp.send();
}

//sends accept transfer offer request to server
function acceptTransfer(id) {
    var xmlhttp;
    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.location.href = xmlhttp.responseText;
        }
    }


    document.getElementById("offersHolder").innerHTML = '<img id="loadingImage" src="assets/images/layOutPics/loading.gif">';

    //make call to cancelOffer() function in model transfer.php
    xmlhttp.open("GET", "index.php?acceptOffer=true&offerid=" + id + "&squadid=" + GETARRAY['squadid'] + "&leagueid=" + GETARRAY['leagueid'], true);
    xmlhttp.send();
}
