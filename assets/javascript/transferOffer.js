//set objects functions - This is started from the menu.js window.onload function
function loadThisPage() {
    //set onClick functions for all player names
    $('[class="playerName"]:visible').each(function () {
        this.setAttribute("onClick", "openPlayerInfo(this.id, event);");
    });

    document.getElementById('offerTradeButton').setAttribute("onclick", "makeTradeOffer()");
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

//Global Arrays to hold ids of all players in and players going out
var playersInArray = [],
    playersOutArray = [];

//Global Arrays to hold ids of players in and players going out by position
var gksInArray = [],
    dfsInArray = [],
    mfsInArray = [],
    fwsInArray = [];
var gksOutArray = [],
    dfsOutArray = [],
    mfsOutArray = [],
    fwsOutArray = [];

//Global Variables to hold numbers of players in and out per position
var gkIn = 0,
    gkOut = 0,
    dfIn = 0,
    dfOut = 0,
    mfIn = 0,
    mfOut = 0,
    fwIn = 0,
    fwOut = 0;

//add player to trade in to transfer
function addToTradeIn(id) {
    //extract players id from id pased in ("trade"+players id)
    var playerId = id.substr(5);

    // Get Table
    var tableRef = document.getElementById('playersInTable').getElementsByTagName('tbody')[0];

    //Check if player is already in trade
    var elementExists = document.getElementById("row" + playerId);
    if (elementExists) {
        //if its already here do nothing
    } else
    //else add player to transfer
    {
        //add player to all players array
        playersInArray.push(playerId);

        //get players position
        var playerPosition = document.getElementById('position' + playerId).innerHTML;

        //adjust global variable of playersIn position
        if (playerPosition == "GK") {
            //add player to position array
            gksInArray.push(playerId);
            gkIn++;
        } else if (playerPosition == "DF") {
            //add player to position array
            dfsInArray.push(playerId);
            dfIn++;
        } else if (playerPosition == "MF") {
            //add player to position array
            mfsInArray.push(playerId);
            mfIn++;
        } else if (playerPosition == "FW") {
            //add player to position array
            fwsInArray.push(playerId);
            fwIn++;
        }

        // Insert a row in the table at the last row and give it an id of (row + players id)
        var newRow = tableRef.insertRow(tableRef.rows.length);
        newRow.setAttribute("id", "row" + playerId);

        // Insert cells in the row
        var newCell1 = newRow.insertCell(0);
        var newCell2 = newRow.insertCell(1);
        var newCell3 = newRow.insertCell(2);

        // create text node for players position 
        var newText1 = document.createTextNode(playerPosition);

        //create p node with correct class, id and onclick function 
        var p = document.createElement("P");
        var pText = document.createTextNode(document.getElementById(playerId).innerHTML);
        p.appendChild(pText);
        p.setAttribute("class", "playerName");
        p.setAttribute("id", playerId);
        p.setAttribute("onclick", "openPlayerInfo(this.id, event);");

        //create button node with correct details
        var btn = document.createElement("BUTTON");
        var btnText = document.createTextNode("X");
        btn.appendChild(btnText);
        btn.setAttribute("class", "tradeButton");
        btn.setAttribute("id", "button" + playerId);
        btn.setAttribute("onclick", "removeFromTable(this.id);");

        //append created nodes to the correct cells
        newCell1.appendChild(newText1);
        newCell2.appendChild(p);
        newCell3.appendChild(btn);
    }
}

//add player to trade out transfer
function addToTradeOut(id) {
    //extract players id from id pased in ("trade"+players id)
    var playerId = id.substr(5);

    // Get Table
    var tableRef = document.getElementById('playersOutTable').getElementsByTagName('tbody')[0];


    //Check if playeris already in trade
    var elementExists = document.getElementById("row" + playerId);
    if (elementExists) {
        //if its already here do nothing
    } else
    //else add player to transfer
    {
        //add player to all players array
        playersOutArray.push(playerId);

        //get players position
        var playerPosition = document.getElementById('position' + playerId).innerHTML;

        //adjust global variable of players Out position
        if (playerPosition == "GK") {
            //add player to position array
            gksOutArray.push(playerId);
            gkOut++;
        } else if (playerPosition == "DF") {
            //add player to position array
            dfsOutArray.push(playerId);
            dfOut++;
        } else if (playerPosition == "MF") {
            //add player to position array
            mfsOutArray.push(playerId);
            mfOut++;
        } else if (playerPosition == "FW") {
            //add player to position array
            fwsOutArray.push(playerId);
            fwOut++;
        }

        // Insert a row in the table at the last row and give it an id of (row + players id)
        var newRow = tableRef.insertRow(tableRef.rows.length);
        newRow.setAttribute("id", "row" + playerId);

        // Insert cells in the row
        var newCell1 = newRow.insertCell(0);
        var newCell2 = newRow.insertCell(1);
        var newCell3 = newRow.insertCell(2);

        // create text node for players position 
        var newText1 = document.createTextNode(document.getElementById('position' + playerId).innerHTML);

        //create p node with correct class, id and onclick function 
        var p = document.createElement("P");
        var pText = document.createTextNode(document.getElementById(playerId).innerHTML);
        p.appendChild(pText);
        p.setAttribute("class", "playerName");
        p.setAttribute("id", playerId);
        p.setAttribute("onclick", "openPlayerInfo(this.id, event);");

        //create button node with correct details
        var btn = document.createElement("BUTTON");
        var btnText = document.createTextNode("X");
        btn.appendChild(btnText);
        btn.setAttribute("class", "tradeButton");
        btn.setAttribute("id", "button" + playerId);
        btn.setAttribute("onclick", "removeFromTable(this.id);");

        //append created nodes to the correct cells
        newCell1.appendChild(newText1);
        newCell2.appendChild(p);
        newCell3.appendChild(btn);
    }
}

//remove a player from the transfer offer table
function removeFromTable(id) {
    //extract players id from id pased in ("button"+players id)
    var playerId = id.substr(6);

    //check if index is in players In array and remove if it is
    var index1 = playersInArray.indexOf(playerId);
    if (index1 > -1) {
        //remove player from all players array
        playersInArray.splice(index1, 1);

        //get players position
        var playerPosition = document.getElementById('position' + playerId).innerHTML;

        //adjust global variable of playersIn position
        if (playerPosition == "GK") {
            //get players index in position array
            var posIndex = gksInArray.indexOf(playerId);

            //remove player from array
            gksInArray.splice(posIndex, 1);

            gkIn--;
        } else if (playerPosition == "DF") {
            //get players index in position array
            var posIndex = dfsInArray.indexOf(playerId);

            //remove player from array
            dfsInArray.splice(posIndex, 1);

            dfIn--;
        } else if (playerPosition == "MF") {
            //get players index in position array
            var posIndex = mfsInArray.indexOf(playerId);

            //remove player from array
            mfsInArray.splice(posIndex, 1);

            mfIn--;
        } else if (playerPosition == "FW") {
            //get players index in position array
            var posIndex = fwsInArray.indexOf(playerId);

            //remove player from array
            fwsInArray.splice(posIndex, 1);

            fwIn--;
        }
    }

    //check if index is in players Out array and remove if it is
    var index2 = playersOutArray.indexOf(playerId);
    if (index2 > -1) {
        //remove player from all players array
        playersOutArray.splice(index2, 1);

        //get players position
        var playerPosition = document.getElementById('position' + playerId).innerHTML;

        //adjust global variable of players Out position
        if (playerPosition == "GK") {
            //get players index in position array
            var posIndex = gksOutArray.indexOf(playerId);

            //remove player from array
            gksOutArray.splice(posIndex, 1);

            gkOut--;
        } else if (playerPosition == "DF") {
            //get players index in position array
            var posIndex = dfsOutArray.indexOf(playerId);

            //remove player from array
            dfsOutArray.splice(posIndex, 1);

            dfOut--;
        } else if (playerPosition == "MF") {
            //get players index in position array
            var posIndex = mfsOutArray.indexOf(playerId);

            //remove player from array
            mfsOutArray.splice(posIndex, 1);

            mfOut--;
        } else if (playerPosition == "FW") {
            //get players index in position array
            var posIndex = fwsOutArray.indexOf(playerId);

            //remove player from array
            fwsOutArray.splice(posIndex, 1);

            fwOut--;
        }
    }

    //remove players row from correct trade offer table
    //extract players id from id pased in ("button"+players id)
    var playerId = id.substr(6);
    //get row
    var row = document.getElementById("row" + playerId);
    //removerow
    row.parentNode.removeChild(row);
}

//changes the opposition team
function changeOppositionTeam(id) {
    //set the header details for table
    var startOfTable = "<tr><th>Trade</th><th>Pos</th><th>Player</th><th>Team</th><th>TP</th></tr>";

    var xmlhttp;
    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            //print out table details
            document.getElementById("oppositionTeamTable").innerHTML = startOfTable + xmlhttp.responseText;
            //reset onclicks
            loadThisPage();
        }
    }


    document.getElementById("oppositionTeamTable").innerHTML = '<p id="">Loading Squad...</p></div><img id="" src="assets/images/layOutPics/loading.gif">';

    //Reset players in details
    gkIn = 0, dfIn = 0, mfIn = 0, fwIn = 0;
    gksInArray = [], dfsInArray = [], mfsInArray = [], fwsInArray = [];

    //clear players in table
    $("#playersInTable tr").remove();

    //make call to getFreeAgents() function in model league.php, with data created above
    xmlhttp.open("GET", "index.php?getOppositionTeam=true&teamId=" + id, true);
    xmlhttp.send();
}

//makes the trade offer to other team
function makeTradeOffer() {
    //variable to hold opposition teams id
    var select = document.getElementById("oppositionTeamSelect");
    var opTeam = select.options[select.selectedIndex].value;

    //make sure there is players in trade
    if (playersInArray.length == 0 && playersOutArray.length == 0) {
        document.getElementById('freeAgentTradeAlert').innerHTML = "Add players to transfer";
        document.getElementById('freeAgentTradeAlert').style.display = "block";
    }
    //check if there is the same amout of players in and players out per position
    else if (gkIn == gkOut && dfIn == dfOut && mfIn == mfOut && fwIn == fwOut) {
        //variabes to hold the strings for players in and out in transfer
        var transferPlayersIn = "",
            transferPlayersOut = "";

        //variables to count the number of players in and out in transfer(used for numbering players in string)
        var playersInNum = 1,
            playersOutNum = 1;

        //loop through players In per position and create players In String
        var gksInLength = gksInArray.length;
        for (var i = 0; i < gksInLength; i++) {
            transferPlayersIn = transferPlayersIn + "&playerIn" + playersInNum + "=" + gksInArray[i];
            playersInNum++;
        }
        var dfsInLength = dfsInArray.length;
        for (var i = 0; i < dfsInLength; i++) {
            transferPlayersIn = transferPlayersIn + "&playerIn" + playersInNum + "=" + dfsInArray[i];
            playersInNum++;
        }
        var mfsInLength = mfsInArray.length;
        for (var i = 0; i < mfsInLength; i++) {
            transferPlayersIn = transferPlayersIn + "&playerIn" + playersInNum + "=" + mfsInArray[i];
            playersInNum++;
        }
        var fwsInLength = fwsInArray.length;
        for (var i = 0; i < fwsInLength; i++) {
            transferPlayersIn = transferPlayersIn + "&playerIn" + playersInNum + "=" + fwsInArray[i];
            playersInNum++;
        }

        //loop through players Out per position and create players In String
        var gksOutLength = gksOutArray.length;
        for (var i = 0; i < gksOutLength; i++) {
            transferPlayersOut = transferPlayersOut + "&playerOut" + playersOutNum + "=" + gksOutArray[i];
            playersOutNum++;
        }
        var dfsOutLength = dfsOutArray.length;
        for (var i = 0; i < dfsOutLength; i++) {
            transferPlayersOut = transferPlayersOut + "&playerOut" + playersOutNum + "=" + dfsOutArray[i];
            playersOutNum++;
        }
        var mfsOutLength = mfsOutArray.length;
        for (var i = 0; i < mfsOutLength; i++) {
            transferPlayersOut = transferPlayersOut + "&playerOut" + playersOutNum + "=" + mfsOutArray[i];
            playersOutNum++;
        }
        var fwsOutLength = fwsOutArray.length;
        for (var i = 0; i < fwsOutLength; i++) {
            transferPlayersOut = transferPlayersOut + "&playerOut" + playersOutNum + "=" + fwsOutArray[i];
            playersOutNum++;
        }
        document.getElementById('freeAgentTradeAlert').style.display = "none";

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

        //make call to getFreeAgents() function in model league.php, with data created above
        xmlhttp.open("GET", "index.php?offerTrade=true" + transferPlayersIn + transferPlayersOut + "&squadid=" + GETARRAY['squadid'] + "&opTeam=" + opTeam, true);
        xmlhttp.send();


    } else
    //alert that trade needs equal amounts of players per position to go through
    {
        document.getElementById('freeAgentTradeAlert').innerHTML = "Must have equal players per position";
        document.getElementById('freeAgentTradeAlert').style.display = "block";
    }
}
