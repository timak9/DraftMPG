//set objects functions - This is started from the menu.js window.onload function
function loadThisPage() {
    //set onClick functions for previous gameweek button
    $('[class="gwButtonL"]:visible').each(function () {
        this.setAttribute("onClick", "changeGWL(this.id);");
    });
    //set onClick functions for next gameweek button
    $('[class="gwButtonR"]:visible').each(function () {
        this.setAttribute("onClick", "changeGWR(this.id);");
    });
}

//changes the gameweek fixtures/results when previous button is pressed
function changeGWL(id) {
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

    //set previous gameweeks number
    var lastweek = id - 1;
    //set previous gameweeks number
    var nextweek = lastweek + 2;

    //change id for previous button
    $('[class="gwButtonL"]:visible').each(function () {
        this.setAttribute("id", lastweek);
    });
    //change id for next button
    $('[class="gwButtonR"]:visible').each(function () {
        this.setAttribute("id", nextweek);
    });


    var xmlhttp;

    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            //Print out the returned fixture list
            document.getElementById("changeFixturesWrapper").innerHTML = xmlhttp.responseText;

        }
    }

    //make call to getGWFixtures function in model league.php
    xmlhttp.open("GET", "index.php?changegw=true&gw=" + id + "&squadid=" + GETARRAY['squadid'], true);
    xmlhttp.send();

    //set gameweek number text
    document.getElementById("gwNumber").innerHTML = "Gameweek " + id;

    //display or hide the previous and next buttons depending on gameweek id (so users cant select gameweeks out side the 38 gameweek range)
    if (id == 1) {
        elements = document.getElementsByClassName('gwButtonL');
        for (var i = 0; i < elements.length; i++) {
            elements[i].style.visibility = "hidden";
        }
    } else {
        elements = document.getElementsByClassName('gwButtonL');
        for (var i = 0; i < elements.length; i++) {
            elements[i].style.visibility = "visible";
        }
    }
    if (id == 38) {
        elements = document.getElementsByClassName('gwButtonR');
        for (var i = 0; i < elements.length; i++) {
            elements[i].style.visibility = "hidden";
        }
    } else {
        elements = document.getElementsByClassName('gwButtonR');
        for (var i = 0; i < elements.length; i++) {
            elements[i].style.visibility = "visible";
        }
    }
}


//changes the gameweek fixtures/results when previous button is pressed
function changeGWR(id) {
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

    //set previous gameweeks number
    var lastweek = id - 1;
    //set previous gameweeks number
    var nextweek = lastweek + 2;

    //change id for previous button
    $('[class="gwButtonL"]:visible').each(function () {
        this.setAttribute("id", lastweek);
    });
    //change id for next button
    $('[class="gwButtonR"]:visible').each(function () {
        this.setAttribute("id", nextweek);
    });


    var xmlhttp;

    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            //Print out the returned fixture list
            document.getElementById("changeFixturesWrapper").innerHTML = xmlhttp.responseText;

        }
    }

    //make call to loadPlayerDetails function in model player.php, including players id numbers
    xmlhttp.open("GET", "index.php?changegw=true&gw=" + id + "&squadid=" + GETARRAY['squadid'], true);
    xmlhttp.send();

    //set gameweek number text
    document.getElementById("gwNumber").innerHTML = "Gameweek " + id;

    //display or hide the previous and next buttons depending on gameweek id (so users cant select gameweeks out side the 38 gameweek range)
    if (id == 1) {
        elements = document.getElementsByClassName('gwButtonL');
        for (var i = 0; i < elements.length; i++) {
            elements[i].style.visibility = "hidden";
        }
    } else {
        elements = document.getElementsByClassName('gwButtonL');
        for (var i = 0; i < elements.length; i++) {
            elements[i].style.visibility = "visible";
        }
    }
    if (id == 38) {
        elements = document.getElementsByClassName('gwButtonR');
        for (var i = 0; i < elements.length; i++) {
            elements[i].style.visibility = "hidden";
        }
    } else {
        elements = document.getElementsByClassName('gwButtonR');
        for (var i = 0; i < elements.length; i++) {
            elements[i].style.visibility = "visible";
        }
    }
}
