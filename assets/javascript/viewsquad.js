//set objects functions - This is started from the menu.js window.onload function
function loadThisPage() {
    //set onClick functions for all player names
    $('[class="name"]:visible').each(function () {
        this.setAttribute("onClick", "openPlayerInfo(this.id, event);");
    });

    $('[class="gwChanger"]:visible').each(function () {
        this.setAttribute("onClick", "changeGamew(this.id);");
    });
}

//Get the league id from url
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

//changes the gameweek thats being displayed
function changeGamew(gw) {
    var xmlhttp;

    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById("changeGwWrapper").innerHTML = xmlhttp.responseText;
            loadThisPage()
        }
    }


    //make call to loadPlayerDetails function in model player.php, including players id numbers
    xmlhttp.open("GET", "index.php?changeviewsquad=true&viewsquad=" + GETARRAY['viewsquad'] + "&leagueid=" + GETARRAY['leagueid'] + "&squadid=" + GETARRAY['squadid'] + "&gameweek=" + gw, true);
    xmlhttp.send();
}
