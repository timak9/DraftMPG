//variable to hold users current top scroll position when openPlayerInfo is selected
var topScroll = 0;

//Get the squad id from url
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


function openPlayerInfo(playerId, event) {
    //cancel the onclick function for outter divs
    event.cancelBubble = true;
    if (event.stopPropagation) event.stopPropagation();

    var xmlhttp;

    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                //Print out the updated player details
                document.getElementById("playerInformationWrapper").innerHTML = xmlhttp.responseText;
                //set player information close buttons onclick function
                document.getElementById("playerInformationCloseButton").setAttribute("onClick", "closePlayerInfo()")
                    //set onClick function for player pop details options
                document.getElementById("Overview").setAttribute("onClick", "")
                document.getElementById("History").setAttribute("onClick", "changeInfoDisplay(this.id)")
                document.getElementById("Fixtures").setAttribute("onClick", "changeInfoDisplay(this.id)")
                    //reset classes for player pop details options
                document.getElementById('Overview').setAttribute("class", "playerInformationOptionsTabSelected");
                document.getElementById('History').setAttribute("class", "playerInformationOptionsTab");
                document.getElementById('Fixtures').setAttribute("class", "playerInformationOptionsTab");
                //reset the player info output displays
                document.getElementById("overviewHolder").style.display = "block";
                document.getElementById("historyHolder").style.display = "none";
                document.getElementById("fixturesHolder").style.display = "none";
            }
        }
        //store current scroll position
    topScroll = $('body').scrollTop();;
    //move user to top of page
    scroll(0, 0);
    //fade out main body
    document.getElementById("wrapper").style.opacity = "0.2";
    //display loading pic in playerInformationWrapper
    document.getElementById("playerInformationWrapper").innerHTML = '<p id="loadingText">Loading Player Details...</p><div class="clear"></div><img id="loadingPic" src="assets/images/layOutPics/loading.gif">';
    //display the player information div
    document.getElementById("playerInformationWrapper").style.display = "block";

    //make call to loadPlayerDetails function in model player.php, including players id numbers
    xmlhttp.open("GET", "index.php?loadPlayerDetails=true&plId=" + playerId + "&squadid=" + GETARRAY['squadid'], true);
    xmlhttp.send();
}


function closePlayerInfo() {
    //move user back to original scroll position
    scroll(0, topScroll);
    //reset main body to full opacity
    document.getElementById("wrapper").style.opacity = "1";
    //hide player information div
    document.getElementById("playerInformationWrapper").style.display = "none";
}


function changeInfoDisplay(id) {
    if (id == "Overview") {
        //set onClick function for player pop details options
        document.getElementById("Overview").setAttribute("onClick", "");
        document.getElementById("History").setAttribute("onClick", "changeInfoDisplay(this.id)");
        document.getElementById("Fixtures").setAttribute("onClick", "changeInfoDisplay(this.id)");
        //set classes for player pop details options
        document.getElementById('Overview').setAttribute("class", "playerInformationOptionsTabSelected");
        document.getElementById('History').setAttribute("class", "playerInformationOptionsTab");
        document.getElementById('Fixtures').setAttribute("class", "playerInformationOptionsTab");
        //set the player info output displays
        document.getElementById("overviewHolder").style.display = "block";
        document.getElementById("historyHolder").style.display = "none";
        document.getElementById("fixturesHolder").style.display = "none";
    } else if (id == "History") {
        //set onClick function for player pop details options
        document.getElementById("Overview").setAttribute("onClick", "changeInfoDisplay(this.id)");
        document.getElementById("History").setAttribute("onClick", "");
        document.getElementById("Fixtures").setAttribute("onClick", "changeInfoDisplay(this.id)");
        //set classes for player pop details options
        document.getElementById('Overview').setAttribute("class", "playerInformationOptionsTab");
        document.getElementById('History').setAttribute("class", "playerInformationOptionsTabSelected");
        document.getElementById('Fixtures').setAttribute("class", "playerInformationOptionsTab");
        //set the player info output displays
        document.getElementById("overviewHolder").style.display = "none";
        document.getElementById("historyHolder").style.display = "block";
        document.getElementById("fixturesHolder").style.display = "none";
    } else if (id == "Fixtures") {
        //set onClick function for player pop details options
        document.getElementById("Overview").setAttribute("onClick", "changeInfoDisplay(this.id)");
        document.getElementById("History").setAttribute("onClick", "changeInfoDisplay(this.id)");
        document.getElementById("Fixtures").setAttribute("onClick", "");
        //set classes for player pop details options
        document.getElementById('Overview').setAttribute("class", "playerInformationOptionsTab");
        document.getElementById('History').setAttribute("class", "playerInformationOptionsTab");
        document.getElementById('Fixtures').setAttribute("class", "playerInformationOptionsTabSelected");
        //set the player info output displays
        document.getElementById("overviewHolder").style.display = "none";
        document.getElementById("historyHolder").style.display = "none";
        document.getElementById("fixturesHolder").style.display = "block";
    }
}
