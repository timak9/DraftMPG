//set objects functions - This is started from the menu.js window.onload function
function loadThisPage() {
    //set onClick functions for all league options buttons
    $('[class="optionsButton"]:visible').each(function () {
        this.setAttribute("onClick", "openLeagueForm(this.id);");
    });
}

//Checks if form is fully filled out, submits form if it is and alerts if not
function checkCreateLeagueForm() {
    //get lengths of the input fields in form
    var createLeagueName = $("#createLeagueName").val().length;
    var createLeaguePassword = $("#createLeaguePassword").val().length;
    var createLeagueTeamName = $("#createLeagueTeamName").val().length;

    //if they are all longer than 0 they have all been filled in so submit form
    if (createLeagueName != 0 && createLeaguePassword != 0 && createLeagueTeamName != 0) {
        //data string holder
        var data = "";

        //get set up datastring from inputs
        data = data + "&leaguename=" + $("#createLeagueName").val();
        data = data + "&password=" + $("#createLeaguePassword").val();
        data = data + "&timeperpick=" + $("#createLeagueTimePerPick").val();
        data = data + "&teamname=" + $("#createLeagueTeamName").val();

        var xmlhttp;
        if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else { // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("createLeague").innerHTML = "Create League";
                document.getElementById("createLeague").setAttribute("onClick", "openLeagueForm(this.id);");
                $("#createLeagueForm").slideUp('slow');

                //send another xml request to get and reload the players leagues
                var xmlhttp2;

                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp2 = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp2 = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp2.onreadystatechange = function () {
                        if (xmlhttp2.readyState == 4 && xmlhttp2.status == 200) {
                            document.getElementById("myLeagues").innerHTML = xmlhttp2.responseText + '<div class="clear"></div>';
                        }
                    }
                    //make call to loadUsersLeagues function in model user.php
                xmlhttp2.open("GET", "index.php?loadDash=true", true);
                xmlhttp2.send();
            }
        }

        //make call to createLeague function in model league.php, with data created above
        xmlhttp.open("GET", "index.php?createLeague=true" + data, true);
        xmlhttp.send();
    }
    //if any are 0 highlight them and dont submit form, reset filled in inputs
    else {
        if (createLeagueName == 0) {
            document.getElementById("createLeagueName").style.backgroundColor = "#FF8080";
        } else {
            document.getElementById("createLeagueName").style.backgroundColor = "#FFFFFF";
        }
        if (createLeaguePassword == 0) {
            document.getElementById("createLeaguePassword").style.backgroundColor = "#FF8080";
        } else {
            document.getElementById("createLeaguePassword").style.backgroundColor = "#FFFFFF";
        }
        if (createLeagueDate == 0) {
            document.getElementById("createLeagueDate").style.backgroundColor = "#FF8080";
        } else {
            document.getElementById("createLeagueDate").style.backgroundColor = "#FFFFFF";
        }
        if (createLeagueTime == 0) {
            document.getElementById("createLeagueTime").style.backgroundColor = "#FF8080";
        } else {
            document.getElementById("createLeagueTime").style.backgroundColor = "#FFFFFF";
        }
        if (createLeagueTeamName == 0) {
            document.getElementById("createLeagueTeamName").style.backgroundColor = "#FF8080";
        } else {
            document.getElementById("createLeagueTeamName").style.backgroundColor = "#FFFFFF";
        }
        return false;
    }
}

//Checks if form is fully filled out, submits form if it is and alerts if not
function checkJoinLeagueForm() {
    //get lengths of the input fields in form
    var joinLeagueId = $("#joinLeagueId").val().length;
    var joinLeaguePassword = $("#joinLeaguePassword").val().length;
    var joinLeagueTeamName = $("#joinLeagueTeamName").val().length;

    //if they are all longer than 0 they have all been filled in so submit form
    if (joinLeagueId != 0 && joinLeaguePassword != 0 && joinLeagueTeamName != 0) {
        //data string holder
        var data = "";

        //get set up datastring from inputs
        data = data + "&leagueid=" + $("#joinLeagueId").val();
        data = data + "&password=" + $("#joinLeaguePassword").val();
        data = data + "&teamname=" + $("#joinLeagueTeamName").val();

        var xmlhttp;

        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    //trim response to remove and extra whitespace at beginning or end of returned string
                    var trimmed = xmlhttp.responseText.trim();
                    //alert if the leafue id and or password is wrong
                    if (trimmed == "League Id is invalid" || trimmed == "Wrong Id and/or password combination" || trimmed == "This league has already started" || trimmed == "You already have a team in this league") {
                        document.getElementById("warningLabel").innerHTML = xmlhttp.responseText;
                    }
                    //if the team is succesfully added clear warning label, hide form, reset button and reload the dashboard leagues
                    else {
                        //send another xml request to get and reload the players leagues
                        var xmlhttp2;

                        if (window.XMLHttpRequest) {
                            // code for IE7+, Firefox, Chrome, Opera, Safari
                            xmlhttp2 = new XMLHttpRequest();
                        } else {
                            // code for IE6, IE5
                            xmlhttp2 = new ActiveXObject("Microsoft.XMLHTTP");
                        }
                        xmlhttp2.onreadystatechange = function () {
                                if (xmlhttp2.readyState == 4 && xmlhttp2.status == 200) {
                                    document.getElementById("myLeagues").innerHTML = xmlhttp2.responseText + '<div class="clear"></div>';
                                }
                            }
                            //make call to loadUsersLeagues function in model user.php
                        xmlhttp2.open("GET", "index.php?loadDash=true", true);
                        xmlhttp2.send();

                        //reset the joinLeague form details
                        document.getElementById("warningLabel").innerHTML = "";
                        document.getElementById("joinLeague").innerHTML = "Join League";
                        document.getElementById("joinLeague").setAttribute("onClick", "openLeagueForm(this.id);");
                        $("#joinLeagueId").val("");
                        $("#joinLeaguePassword").val("");
                        $("#joinLeagueTeamName").val("");
                        $("#joinLeagueForm").slideUp('slow');
                    }
                }
            }
            //make call to joinLeague function in model league.php, with data created above
        xmlhttp.open("GET", "index.php?joinLeague=true" + data, true);
        xmlhttp.send();
    }
    //if any are 0 highlight them and dont submit form, reset filled in inputs
    else {
        if (joinLeagueId == 0) {
            document.getElementById("joinLeagueId").style.backgroundColor = "#FF8080";
        } else {
            document.getElementById("joinLeagueId").style.backgroundColor = "#FFFFFF";
        }
        if (joinLeaguePassword == 0) {
            document.getElementById("joinLeaguePassword").style.backgroundColor = "#FF8080";
        } else {
            document.getElementById("joinLeaguePassword").style.backgroundColor = "#FFFFFF";
        }
        if (joinLeagueTeamName == 0) {
            document.getElementById("joinLeagueTeamName").style.backgroundColor = "#FF8080";
        } else {
            document.getElementById("joinLeagueTeamName").style.backgroundColor = "#FFFFFF";
        }
        return false;
    }
}

//drop down league form 
function openLeagueForm(id) {
    //if the create league button is pressed
    if (id == "createLeague") {
        //slide up the join league form and drop down the create league form
        $("#joinLeagueForm").slideUp('slow', function () {
            $("#createLeagueForm").slideDown('slow');
        });

        //set the create league button text to Close and set the onclick to close the forms
        document.getElementById("createLeague").innerHTML = "Close";
        document.getElementById("createLeague").setAttribute("onClick", "closeLeagueForm();");
        //reset the join league button text to Join League and set the onclick to open the form
        document.getElementById("joinLeague").innerHTML = "Join League";
        document.getElementById("joinLeague").setAttribute("onClick", "openLeagueForm(this.id);");

        //clear join league form
        $("#joinLeagueId").val("");
        $("#joinLeaguePassword").val("");
        $("#joinLeagueTeamName").val("");
    }
    //if the join league button is pressed
    else {
        $("#createLeagueForm").slideUp('slow', function () {
            $("#joinLeagueForm").slideDown('slow');
        });

        //set the join league button text to Close and set the onclick to close the forms
        document.getElementById("joinLeague").innerHTML = "Close";
        document.getElementById("joinLeague").setAttribute("onClick", "closeLeagueForm();");
        //reset the create league button text to Create League and set the onclick to open the form
        document.getElementById("createLeague").innerHTML = "Create League";
        document.getElementById("createLeague").setAttribute("onClick", "openLeagueForm(this.id);");

        //clear create form 
        $("#createLeagueName").val("");
        $("#createLeaguePassword").val("");
        $("#createLeagueDate").val("");
        $("#createLeagueTime").val("");
        $("#createLeagueTimePerPick").val("60");
        $("#createLeagueTeamName").val("");
    }
}

//close up league form
function closeLeagueForm() {
    //slipe up both forms to hide
    $("#joinLeagueForm").slideUp('slow', function () {
        $("#createLeagueForm").slideUp('slow');
    });

    //reset the text and onclicks of both buttons
    document.getElementById("createLeague").innerHTML = "Create League";
    document.getElementById("createLeague").setAttribute("onClick", "openLeagueForm(this.id);");
    document.getElementById("joinLeague").innerHTML = "Join League";
    document.getElementById("joinLeague").setAttribute("onClick", "openLeagueForm(this.id);");

    //clear create form 
    $("#createLeagueName").val("");
    $("#createLeaguePassword").val("");
    $("#createLeagueDate").val("");
    $("#createLeagueTime").val("");
    $("#createLeagueTimePerPick").val("60");
    $("#createLeagueTeamName").val("");

    //clear join league form
    $("#joinLeagueId").val("");
    $("#joinLeaguePassword").val("");
    $("#joinLeagueTeamName").val("");
}
