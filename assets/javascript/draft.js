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

//On page load run load page function to set objects functions
window.onload = loadThisPage;

//set database check timer interval
var myVar = setInterval(checkPick, 2000);

//variable to hold weather or not user isleague admin
var leagueAdmin = 'No';

//holds page of free agent lists player is looking at
var currentFreeAgentPage = 1;

//placeHolder for draft status
var draftStatus = 0;

//place holders for countdown timer
var selectionTime = 0;
var countDownTime = 0;

//holds the squad id of team curently picking and draft pick number
var teamCurrentlyPicking = 0;
var draftPickNumber = 0;

//place holders for number of players required per position
var gks = 0;
var dfs = 0;
var mfs = 0;
var fws = 0;

function loadThisPage() {
    getDraftDetails();

    checkPick();

    document.getElementById('selectPremTeam').setAttribute("onChange", "changePage(1)");
    document.getElementById('selectPosition').setAttribute("onChange", "changePage(1)");
    document.getElementById('selectName').setAttribute("onKeyUp", "changePage(1)");

    //set onClick functions for all player names
    $('[class="playerName"]:visible').each(function () {
        this.setAttribute("onClick", "openPlayerInfo(this.id, event);");
    });

    //check if its the users turn to pick, if not hide selectbuttons
    if (teamCurrentlyPicking == GETARRAY['squadid']) {
        $('.tradeButton').show();
    } else {
        $('.tradeButton').hide();
    }
}


var countDown = setInterval(countDownTimer, 1000);

function countDownTimer() {
    var theDiv = document.getElementById("theTimer");

    countDownTime++;
    var timeLeft = selectionTime - countDownTime;

    var minutes = Math.floor(timeLeft / 60);

    var seconds = timeLeft - minutes * 60;

    var finalTime = str_pad_left(minutes, '0', 2) + ':' + str_pad_left(seconds, '0', 2);

    theDiv.innerHTML = "Time Remaining : " + finalTime;

    if (timeLeft == 0) {
        var theDiv = document.getElementById('selectionAlertBox');
        theDiv.innerHTML = "";
        theDiv.style.display = "none";
        document.getElementById('wrapper').style.opacity = '1';
        clearInterval(countDown);
        $.ajax({
            url: 'index.php?selectrandomplayer=true&squadid=' + teamCurrentlyPicking + '&picknumber=' + draftPickNumber,
            cache: false,
            success: function (data) {
                checkPick();
            },
        });
        countDownTime = 0;
        countDown = setInterval(countDownTimer, 1000);

    }
}

function str_pad_left(string, pad, length) {
    return (new Array(length + 1).join(pad) + string).slice(-length);
}

function checkPick() {
    $.ajax({
        url: 'index.php?checkdraftpick=true&squadid=' + GETARRAY['squadid'],
        cache: false,
        success: function (data) {
            var parsedData = JSON.parse(data);

            //get draft details
            var pickNumber = parsedData['pickNo'];
            var teamPicking = parsedData['pickTeam'];
            var theDraftStatus = parsedData['status'];

            //if draft is on going
            if (theDraftStatus == 1) {
                //check if the team picking has changed since last check
                //if it has then reload draft page with new details
                if (teamPicking != teamCurrentlyPicking || draftPickNumber != pickNumber) {
                    //reset timer
                    clearInterval(countDown);
                    countDownTime = 0;
                    countDown = setInterval(countDownTimer, 1000);

                    //update details of last pick
                    teamCurrentlyPicking = teamPicking;
                    draftPickNumber = pickNumber;

                    //update draft details on the page
                    changePage(currentFreeAgentPage);
                    loadThisPage();

                    document.getElementById('pickText').innerHTML = "Pick Order - Pick #" + pickNumber + " in progress";

                    //check if its the users turn to pick
                        //if it is allow them to select players
                    if (teamPicking == GETARRAY['squadid']) {
                        $('.tradeButton').show();
                    }
                    //if not don't allow themto select player
                    else {
                        $('.tradeButton').hide();
                    }
                }
            }
            //if draft hasnt started
            else if (theDraftStatus == 0) {
                teamCurrentlyPicking = 0;
                clearInterval(countDown);

                if (leagueAdmin == 'Yes') {
                    var theDiv = document.getElementById("theTimer");
                    theDiv.innerHTML = "<button class='draftButton' onclick='startDraft()'>Start Draft</button>";
                } else {
                    var theDiv = document.getElementById("theTimer");
                    theDiv.innerHTML = "Draft hasn't started yet";
                }
            }
            //if draft is finished
            else {
                teamCurrentlyPicking = 0;
                clearInterval(countDown);

                var theDiv = document.getElementById("theTimer");
                theDiv.innerHTML = "<div class='draftButton'><a href='index.php?squad=true&squadid=" + GETARRAY['squadid'] + "'>Draft Finished - View Squad</a></div>";
            }
        },
    });
}


//loads the draft details on page load
function getDraftDetails() {
    //get details time per draft selection and team and league name
    $.ajax({
        url: 'index.php?getteamleaguedetails=true&squadid=' + GETARRAY['squadid'],
        cache: false,
        success: function (data) {
            //get json obect of details and parse it
            var parsedData = JSON.parse(data);

            //get league admin
            leagueAdmin = parsedData['leagueAdmin'];
            //set draft status time
            draftStatus = parsedData['draftStatus'];
            //set selection time
            selectionTime = parsedData['selectionTime'];
            //output team and league name
            document.getElementById("wynText").innerHTML = parsedData['teamName'] + ' - What you need';
            document.getElementById('teamNameLeagueName').innerHTML = "<div class='draftButton'><a href='index.php?dash=true'>Exit</a></div>";
        },
    });

    //get count of players required per position
    $.ajax({
        url: 'index.php?getplayersperposition=true&squadid=' + GETARRAY['squadid'],
        cache: false,
        success: function (data) {
            //get json obect of details and parse it
            var parsedData = JSON.parse(data);

            //update required players per position count and output
            document.getElementById('gkCount').innerHTML = parsedData['Goalkeepers'];
            gks = parsedData['Goalkeepers'];
            document.getElementById('dfCount').innerHTML = parsedData['Defenders'];
            dfs = parsedData['Defenders'];
            document.getElementById('mfCount').innerHTML = parsedData['Midfielders'];
            mfs = parsedData['Midfielders'];
            document.getElementById('fwCount').innerHTML = parsedData['Forwards'];
            fws = parsedData['Forwards'];
        },
    });

    //get draft order
    $.ajax({
        url: 'index.php?getdraftorder=true&squadid=' + GETARRAY['squadid'],
        cache: false,
        success: function (data) {
            //get json obect of details and parse it
            var parsedData = JSON.parse(data);
            //count total number of pics
            var totalPicks = Object.keys(parsedData).length;

            var tableHtml = '<table class="pickTable">';

            var pickNumber = 1;

            //loop through picks 18 times and print out pick order
            for (var round = 1; round <= 18; round++) {
                tableHtml += '<tr class="roundColumn"><td colspan="2">Round: ' + round + '</td></tr><tr class="headingColumn"><td>#</td><td>Team</td></tr>';
                if (round % 2 != 0) {
                    for (var pick = 1; pick <= totalPicks; pick++) {
                        tableHtml += '<tr><td>' + pickNumber + '</td><td>' + parsedData[pick] + '</td></tr>';
                        pickNumber++;
                    }
                } else {

                    for (var pick = totalPicks; pick >= 1; pick--) {
                        tableHtml += '<tr><td>' + pickNumber + '</td><td>' + parsedData[pick] + '</td></tr>';
                        pickNumber++;
                    }
                }
            }
            tableHtml += '</table>';

            document.getElementById('pickTableHolder').innerHTML = tableHtml;
        },
    });


    //get draft history
    $.ajax({
        url: 'index.php?getdrafthistory=true&squadid=' + GETARRAY['squadid'],
        cache: false,
        success: function (data) {
            //get json obect of details and parse it
            var parsedData = JSON.parse(data);

            var numberOfPicks = parsedData.length;

            var tableHtml = '<table class="pickTable"><tr class="roundColumn"><td>#</td><td>Team</td><td>Player</td></tr>';

            for (var round = numberOfPicks - 1; round >= 0; round--) {
                var pickNo = round + 1;
                tableHtml += "<tr><td>" + pickNo + "<td>" + parsedData[round][0] + "</td><td>" + parsedData[round][1] + "</td></tr>";
            }

            tableHtml += "</table>";

            document.getElementById('historyTableHolder').innerHTML = tableHtml;
        },
    });
}

//alerts user to confirm selection
function selectPlayer(id) {
    //extract players id from id passed in ("select"+players id)
    var playerId = id.substr(6);
    //get players position
    var position = document.getElementById('position' + playerId).innerHTML;

    var theDiv = document.getElementById('selectionAlertBox');

    if (position == 'GK' && gks == 0) {
        var content = '<p>You already have 2 Goalkeepers</p> <button onclick="cancelSelection()">Ok</button>';
    } else if (position == 'DF' && dfs == 0) {
        var content = '<p>You already have 6 Defenders</p> <button onclick="cancelSelection()">Ok</button>';
    } else if (position == 'MF' && mfs == 0) {
        var content = '<p>You already have 6 Midfielders</p> <button onclick="cancelSelection()">Ok</button>';
    } else if (position == 'FW' && fws == 0) {
        var content = '<p>You already have 4 Forwards</p> <button onclick="cancelSelection()">Ok</button>';
    } else {
        //get players name
        var playerName = document.getElementById(playerId).innerHTML;

        var content = '<p>Confirm Selection of ' + playerName + '</p> <button onclick="addPlayerToSquad(' + playerId + ')">Confirm</button><button onclick="cancelSelection()">Cancel</button>';
    }

    theDiv.innerHTML = content;

    document.getElementById('wrapper').style.opacity = '0.25';

    theDiv.style.display = "block";

}

//puts selection through
function addPlayerToSquad(playerId) {

    var theDiv = document.getElementById('selectionAlertBox');
    theDiv.innerHTML = "";
    document.getElementById('wrapper').style.opacity = '1';
    theDiv.style.display = "none";
    if (teamCurrentlyPicking == GETARRAY['squadid']) {
        $.ajax({
            url: 'index.php?addplayertosquad=true&squadid=' + GETARRAY['squadid'] + '&playerid=' + playerId + '&picknumber=' + draftPickNumber,
            cache: false,
            success: function (data) {
                loadThisPage();
                changePage(1);
            },
        });
    } else {
        alert("No Cheating!");
    }
}

function cancelSelection() {
    var theDiv = document.getElementById('selectionAlertBox');

    theDiv.innerHTML = "";

    document.getElementById('wrapper').style.opacity = '1';

    theDiv.style.display = "none";
}

function startDraft() {
    //starts the draft and league
    $.ajax({
        url: 'index.php?startdraft=true&squadid=' + GETARRAY['squadid'],
        cache: false,
        success: function (data) {
            loadThisPage();
            changePage(1);
        },
    });
    //generates the fixtures for the league
    $.ajax({
        url: 'index.php?generatefixtures=true&squadid=' + GETARRAY['squadid'],
        cache: false,
        success: function (data) {
            loadThisPage();
            changePage(1);
        },
    });
}

//changes page on free agents result list, (pagification)
function changePage(id) {
    currentFreeAgentPage = id;

    //set the header details for table
    var startOfTable = "<tr><th>Pos</th><th>Player</th><th>Team</th><th>TP</th><th>Select</th></tr>";

    //get search details
    var pos = document.getElementById('selectPosition').value;
    var team = document.getElementById('selectPremTeam').value;
    if (document.getElementById('selectName').value == "") {
        var name = "Any";
    } else {
        var name = document.getElementById('selectName').value;
    }


    var xmlhttp;
    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            //print out table details
            document.getElementById("freeAgentsTable").innerHTML = startOfTable + xmlhttp.responseText;
            //reset onclicks
            loadThisPage();
        }
    }

    //make call to getFreeAgents() function in model league.php, with data created above
    xmlhttp.open("GET", "index.php?getavailableplayers=true&page=" + id + "&position=" + pos + "&premTeam=" + team + "&name=" + name + "&squadid=" + GETARRAY['squadid'], true);
    xmlhttp.send();
}
