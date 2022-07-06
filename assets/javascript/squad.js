//Global variable that will hold the id number of a selected player to be swapped i.e first player chosen in a substitution
var idHolder = 0;

//set objects functions - This is started from the menu.js window.onload function
function loadThisPage() {
    //set onclick functions on all players
    //get all goalkeepers
    $('*[id*="Goalkeeper"]:visible').each(function () {
        this.setAttribute("onClick", "chooseSub(this.id);");
    });
    //get all defenders
    $('*[id*="Defender"]:visible').each(function () {
        this.setAttribute("onClick", "chooseSub(this.id);");
    });
    //get all midfielders
    $('*[id*="Midfielder"]:visible').each(function () {
        this.setAttribute("onClick", "chooseSub(this.id);");
    });
    //get all goalkeepers
    $('*[id*="Forward"]:visible').each(function () {
        this.setAttribute("onClick", "chooseSub(this.id);");
    });

    //set onClick functions for all player names
    $('[class="name"]:visible').each(function () {
        this.setAttribute("onClick", "openPlayerInfo(this.id, event);");
    });
}

function chooseSub(position) {
    //position being passed in is the div id of the selected "player" class. The id is created joining the players poisition and id

    //count how many players are starting in each position
    var startingGoalkeepers = $('#gk .player').length;
    var startingDefenders = $('#df .player').length;
    var startingMidfielders = $('#mf .player').length;
    var startingForwards = $('#str .player').length;

    //get first letter of id passed in
    var firstLeter = position.substring(0, 1);

    //depending on what the first letter is, get the substring of position

    //Goalkeepers Transfer Settings
    if (firstLeter == "G") {
        var id = position.substring(10);
        //Set global variable idHolder = to the selected players id
        idHolder = id;
        var position = position.substring(0, 10);
        //get all goalkeepers, allow swaps
        $('*[id*="Goalkeeper"]:visible').each(function () {
            this.style.backgroundColor = "orange";
            this.style.opacity = "0.7";
            this.setAttribute("onClick", "makeSub(this.id, idHolder);");
        });


        //get all defenders, dont allow swaps
        $('*[id*="Defender"]:visible').each(function () {
            this.style.backgroundColor = "none";
            this.style.opacity = "1";
            this.setAttribute("onClick", "none;");
        });

        //get all midfielders, dont allow swaps
        $('*[id*="Midfielder"]:visible').each(function () {
            this.style.backgroundColor = "none";
            this.style.opacity = "1";
            this.setAttribute("onClick", "none;");
        });

        //get all goalkeepers, dont allow swaps
        $('*[id*="Forward"]:visible').each(function () {
            this.style.backgroundColor = "none";
            this.style.opacity = "1";
            this.setAttribute("onClick", "none;");
        });

    }
    //Defenders Transfer Settings
    else if (firstLeter == "D") {
        //Get squad status of selected player ie. starter(pitch), sub(bench) or reserve(reserves)
        var squadStatus = document.getElementById("" + position).parentNode.parentNode.id;

        //seperate the string passed in to get player position and player id
        var id = position.substring(8);
        //Set global variable idHolder = to the selected players id
        idHolder = id;
        var position = position.substring(0, 8);

        //if a reserve player is selected they can only be swapped with players of same position
        if (squadStatus == "reserves") {
            //get all other defenders, allow to be swapped with
            $('*[id*="Defender"]:visible').each(function () {
                this.style.backgroundColor = "orange";
                this.style.opacity = "0.7";
                this.setAttribute("onClick", "makeSub(this.id, idHolder);");
            });

            //get all goalkeepers, dont allow swaps
            $('*[id*="Goalkeeper"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all midfielders, dont allow swaps
            $('*[id*="Midfielder"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all forwards, dont allow swaps
            $('*[id*="Forward"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

        }
        //if a sub player is selected they can be swapped with any outfields players on the bench, reserves with the same position or outfielders starting depending on how this.id, idHolderny players are in the position
        else if (squadStatus == "bench") {
            //get all other defenders, allow swaps
            $('*[id*="Defender"]:visible').each(function () {
                this.style.backgroundColor = "orange";
                this.style.opacity = "0.7";
                this.setAttribute("onClick", "makeSub(this.id, idHolder);");
            });

            //get all goalkeepers, dont allow swaps
            $('*[id*="Goalkeeper"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all midfielders, allow swaps as long as they are not reserves and there are more than 2 starting midfielders
            $('*[id*="Midfielder"]:visible').each(function () {
                //make sure they are not reserves
                if (startingMidfielders != 2 && this.parentNode.parentNode.id != "reserves") {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                } else {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }

            });

            //get all forwards,allow swaps as long as they are not reserves and there are more than 1 starting forward
            $('*[id*="Forward"]:visible').each(function () {
                //make sure there is more than on forward starting and they are not in the reserves
                if (startingForwards != 1 && this.parentNode.parentNode.id != "reserves") {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                //otherwise don't allow starters to be swapped
                else {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }

                //if the player is also on the bench always allow swap to be made
                if (this.parentNode.parentNode.id == squadStatus) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
            });
        }
        //if a starting player is selected they can be swapped with any outfields players on the bench depending on how many players are in the position and reserves with the same position
        else if (squadStatus == "pitch") {
            //get any player with the same position
            $('*[id*="Defender"]:visible').each(function () {
                //allow players on bench or reserves to be swapped with
                if (this.parentNode.parentNode.id != squadStatus) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                //dont allow players on th
                else if (this.parentNode.parentNode.id == squadStatus) {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
            });

            //get all goalkeepers, dont allow swaps
            $('*[id*="Goalkeeper"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all midfielders, dont allow swaps
            $('*[id*="Midfielder"]:visible').each(function () {
                //allow players on bench to be swapped as long as there is more than 3 defenders on the field, but not reserves
                if (this.parentNode.parentNode.id != squadStatus && this.parentNode.parentNode.id != "reserves" && startingDefenders > 3) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                else {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
                //dont allow players on the field to be swapped or players in reserves
                if (this.parentNode.parentNode.id == squadStatus || this.parentNode.parentNode.id == "reserves") {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
            });

            //get all forwards, dont allow swaps
            $('*[id*="Forward"]:visible').each(function () {
                //allow players on bench to be swapped as long as there is more than 3 defenders on the field, but not reserves
                if (this.parentNode.parentNode.id != squadStatus && this.parentNode.parentNode.id != "reserves" && startingDefenders > 3) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                else {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
                //dont allow players on the field to be swapped or players in reserves
                if (this.parentNode.parentNode.id == squadStatus || this.parentNode.parentNode.id == "reserves") {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
            });

        }
    }
    //Midfielders Transfer Settings
    else if (firstLeter == "M") {
        //Get squad status of selected player ie. starter(pitch), sub(bench) or reserve(reserves)
        var squadStatus = document.getElementById("" + position).parentNode.parentNode.id;

        //seperate the string passed in to get player position and player id
        var id = position.substring(10);
        //Set global variable idHolder = to the selected players id
        idHolder = id;
        var position = position.substring(0, 10);

        //if a reserve player is selected they can only be swapped with players of same position
        if (squadStatus == "reserves") {

            //get all goalkeepers, dont allow swaps
            $('*[id*="Goalkeeper"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all other defenders, dont allow swaps
            $('*[id*="Defender"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all midfielders, allow to be swapped with
            $('*[id*="Midfielder"]:visible').each(function () {
                this.style.backgroundColor = "orange";
                this.style.opacity = "0.7";
                this.setAttribute("onClick", "makeSub(this.id, idHolder);");
            });

            //get all forwards, dont allow swaps
            $('*[id*="Forward"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });
        }
        //if a sub player is selected they can be swapped with any outfields players on the bench, reserves with the same position or outfielders starting depending on how many players are in the position
        else if (squadStatus == "bench") {
            //get all other midfielders, allow swaps
            $('*[id*="Midfielder"]:visible').each(function () {
                this.style.backgroundColor = "orange";
                this.style.opacity = "0.7";
                this.setAttribute("onClick", "makeSub(this.id, idHolder);");
            });

            //get all goalkeepers, dont allow swaps
            $('*[id*="Goalkeeper"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all defenders, allow swaps as long as they are not reserves and there are more than 3 starting midfielders
            $('*[id*="Defender"]:visible').each(function () {
                //make sure they are not reserves
                if (startingDefenders > 3 && this.parentNode.parentNode.id != "reserves") {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                } else {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }

                //if the player is also on the bench always allow swap to be made
                if (this.parentNode.parentNode.id == squadStatus) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }

            });

            //get all forwards,allow swaps as long as they are not reserves and there are more than 1 starting forward
            $('*[id*="Forward"]:visible').each(function () {
                //make sure there is more than on forward starting and they are not in the reserves
                if (startingForwards > 1 && this.parentNode.parentNode.id != "reserves") {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                //otherwise don't allow starters to be swapped
                else {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }

                //if the player is also on the bench always allow swap to be made
                if (this.parentNode.parentNode.id == squadStatus) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
            });
        }
        //if a starting player is selected they can be swapped with any outfields players on the bench depending on how many players are in the position and reserves with the same position
        else if (squadStatus == "pitch") {
            //get any player with the same position
            $('*[id*="Midfielder"]:visible').each(function () {
                //allow players on bench or reserves to be swapped with
                if (this.parentNode.parentNode.id != squadStatus) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                //dont allow starting players to be swapped
                else if (this.parentNode.parentNode.id == squadStatus) {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
            });

            //get all goalkeepers, dont allow swaps
            $('*[id*="Goalkeeper"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all defenders
            $('*[id*="Defender"]:visible').each(function () {
                //allow players on bench to be swapped as long as there is more than 2 midfielders on the field, but not reserves
                if (this.parentNode.parentNode.id != squadStatus && this.parentNode.parentNode.id != "reserves" && startingMidfielders > 2) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                //dont allow players on the field to be swapped or players in reserves
                if (this.parentNode.parentNode.id == squadStatus || this.parentNode.parentNode.id == "reserves") {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
            });

            //get all forwards
            $('*[id*="Forward"]:visible').each(function () {
                //allow players on bench to be swapped as long as there is more than 2 midfielders on the field, but not reserves
                if (this.parentNode.parentNode.id != squadStatus && this.parentNode.parentNode.id != "reserves" && startingMidfielders > 2) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                //dont allow players on the field to be swapped or players in reserves
                else if (this.parentNode.parentNode.id == squadStatus || this.parentNode.parentNode.id == "reserves") {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
            });

        }
    }
    //Forwards Transfer Settings
    else {
        //Get squad status of selected player ie. starter(pitch), sub(bench) or reserve(reserves)
        var squadStatus = document.getElementById("" + position).parentNode.parentNode.id;

        //seperate the string passed in to get player position and player id
        var id = position.substring(7);
        //Set global variable idHolder = to the selected players id
        idHolder = id;
        var position = position.substring(0, 7);

        //if a reserve player is selected they can only be swapped with players of same position
        if (squadStatus == "reserves") {

            //get all goalkeepers, dont allow swaps
            $('*[id*="Goalkeeper"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all other defenders, dont allow swaps
            $('*[id*="Defender"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all midfielders, dont allow swaps
            $('*[id*="Midfielder"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all forwards, allow to be swapped with
            $('*[id*="Forward"]:visible').each(function () {
                this.style.backgroundColor = "orange";
                this.style.opacity = "0.7";
                this.setAttribute("onClick", "makeSub(this.id, idHolder);");
            });
        }
        //if a sub player is selected they can be swapped with any outfields players on the bench, reserves with the same position or outfielders starting depending on how many players are in the position
        else if (squadStatus == "bench") {
            //get all other forwards, allow swaps
            $('*[id*="Forward"]:visible').each(function () {
                this.style.backgroundColor = "orange";
                this.style.opacity = "0.7";
                this.setAttribute("onClick", "makeSub(this.id, idHolder);");
            });

            //get all goalkeepers, dont allow swaps
            $('*[id*="Goalkeeper"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all defenders, allow swaps as long as they are not reserves and there are more than 1 starting forward
            $('*[id*="Defender"]:visible').each(function () {
                //make sure they are not reserves
                if (startingDefenders > 3 && this.parentNode.parentNode.id != "reserves") {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                } else {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }

                //if the player is also on the bench always allow swap to be made
                if (this.parentNode.parentNode.id == squadStatus) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }

            });

            //get all midfielders,allow swaps as long as they are not reserves and there are more than 1 starting forward
            $('*[id*="Midfielder"]:visible').each(function () {
                //make sure there is more than on forward starting and they are not in the reserves
                if (startingMidfielders > 2 && this.parentNode.parentNode.id != "reserves") {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                //otherwise don't allow starters to be swapped
                else {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }

                //if the player is also on the bench always allow swap to be made
                if (this.parentNode.parentNode.id == squadStatus) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
            });
        }
        //if a starting player is selected they can be swapped with any outfields players on the bench depending on how many players are in the position and reserves with the same position
        else if (squadStatus == "pitch") {
            //get any player with the same position
            $('*[id*="Forward"]:visible').each(function () {
                //allow players on bench or reserves to be swapped with
                if (this.parentNode.parentNode.id != squadStatus) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                //dont allow starting players to be swapped
                else if (this.parentNode.parentNode.id == squadStatus) {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
            });

            //get all goalkeepers, dont allow swaps
            $('*[id*="Goalkeeper"]:visible').each(function () {
                this.style.backgroundColor = "none";
                this.style.opacity = "1";
                this.setAttribute("onClick", "none;");
            });

            //get all defenders
            $('*[id*="Defender"]:visible').each(function () {
                //allow players on bench to be swapped as long as there is more than 1 forward on the field, but not reserves
                if (this.parentNode.parentNode.id != squadStatus && this.parentNode.parentNode.id != "reserves" && startingForwards > 1) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                else {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
                //dont allow players on the field to be swapped or players in reserves
                if (this.parentNode.parentNode.id == squadStatus || this.parentNode.parentNode.id == "reserves") {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
            });

            //get all midfielders
            $('*[id*="Midfielder"]:visible').each(function () {
                //allow players on bench to be swapped as long as there is more than 1 forward on the field, but not reserves
                if (this.parentNode.parentNode.id != squadStatus && this.parentNode.parentNode.id != "reserves" && startingForwards > 1) {
                    this.style.backgroundColor = "orange";
                    this.style.opacity = "0.7";
                    this.setAttribute("onClick", "makeSub(this.id, idHolder);");
                }
                else {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
                //dont allow players on the field to be swapped or players in reserves
                if (this.parentNode.parentNode.id == squadStatus || this.parentNode.parentNode.id == "reserves") {
                    this.style.backgroundColor = "none";
                    this.style.opacity = "1";
                    this.setAttribute("onClick", "none;");
                }
            });

        }
    }


    //set the background colour and cancelSub on click function for selected player
    $('#' + position + id).css('background-color', 'indigo');
    $('#' + position + id).css('opacity', '0.8');
    document.getElementById("" + position + id).setAttribute("onClick", "cancelSub();");
}

function cancelSub() {
    //Reset onclick functions, colours and opacity on all players
    //get all goalkeepers, allow swaps
    $('*[id*="Goalkeeper"]:visible').each(function () {
        this.style.backgroundColor = "inherit";
        this.style.opacity = "1";
        this.setAttribute("onClick", "chooseSub(this.id);");
    });
    //get all defenders, dont allow swaps
    $('*[id*="Defender"]:visible').each(function () {
        this.style.backgroundColor = "inherit";
        this.style.opacity = "1";
        this.setAttribute("onClick", "chooseSub(this.id);");
    });
    //get all midfielders, dont allow swaps
    $('*[id*="Midfielder"]:visible').each(function () {
        this.style.backgroundColor = "inherit";
        this.style.opacity = "1";
        this.setAttribute("onClick", "chooseSub(this.id);");
    });
    //get all goalkeepers, dont allow swaps
    $('*[id*="Forward"]:visible').each(function () {
        this.style.backgroundColor = "inherit";
        this.style.opacity = "1";
        this.setAttribute("onClick", "chooseSub(this.id);");
    });
}

function makeSub(playerOut, playerIn) {
    //get Squad Id from GET in url
    var val = "squadid";
    var squadid = "Not found",
        tmp = [];
    var items = location.search.substr(1).split("&");
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split("=");
        if (tmp[0] === val) squadid = decodeURIComponent(tmp[1]);
    }

    //getFirst letter of player in id so we know how to seperate the id into position and player id number
    var firstLeter = playerOut.substring(0, 1);

    //Depending on the first letter we know how many long the position name is and where the players id number starts, then set playersIn id to the correct number
    if (firstLeter == "G") {
        playerOut = playerOut.substring(10);
    } else if (firstLeter == "D") {
        playerOut = playerOut.substring(8);
    } else if (firstLeter == "M") {
        playerOut = playerOut.substring(10);
    } else {
        playerOut = playerOut.substring(7);
    }

    var xmlhttp;

    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            //Print out the updated squad list
            document.getElementById("wrapper").innerHTML = xmlhttp.responseText;
            //call loadThisPage function to add onClick functions to players
            loadThisPage();
        }
    }

    //make call to makeSub function in model squad.php, including both players id numbers
    xmlhttp.open("GET", "index.php?makeSub=true&pl1=" + playerOut + "&pl2=" + playerIn + "&squadid=" + squadid, true);
    xmlhttp.send();
}

//Remove player functions
function removePlayersOnClick() {
    //set onclick functions on all players
    //get all goalkeepers
    $('*[id*="Goalkeeper"]:visible').each(function () {
        this.style.backgroundColor = "inherit";
        this.style.opacity = "1";
        this.setAttribute("onClick", "none;");
    });
    //get all defenders
    $('*[id*="Defender"]:visible').each(function () {
        this.style.backgroundColor = "inherit";
        this.style.opacity = "1";
        this.setAttribute("onClick", "none;");
    });
    //get all midfielders
    $('*[id*="Midfielder"]:visible').each(function () {
        this.style.backgroundColor = "inherit";
        this.style.opacity = "1";
        this.setAttribute("onClick", "none;");
    });
    //get all goalkeepers
    $('*[id*="Forward"]:visible').each(function () {
        this.style.backgroundColor = "inherit";
        this.style.opacity = "1";
        this.setAttribute("onClick", "none;");
    });

    //remove onClick functions for all player names
    $('[class="name"]:visible').each(function () {
        this.setAttribute("onClick", "none;");
    });
}
