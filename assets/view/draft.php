<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="assets/images/layOutPics/favicon.ico">
    <link rel="stylesheet" type="text/css" href="assets/css/reset.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/draft.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/playerInfoPopUp.min.css" />
    <script src="assets/javascript/jqueryui/jquery-ui.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js"></script>
    <script src="assets/javascript/draft.min.js"></script>
    <script src="assets/javascript/playerInfoPopUp.min.js"></script>

    <title>DFF | The Draft |</title>

</head>

<body>

    <div id="playerInformationWrapper">

    </div>

    <div id="selectionAlertBox">
        <p>Confirm selection of Name</p>
        <button>Ok</button>
        <button>Cancel</button>
    </div>

    <div id="wrapper">

        <div id="left">
            <div id="theTimer" class="topContent">Time Remaining : 00:00</div>
            <div id="pickOrder" class="mainContent">
                <div class="draftHeader">
                    <p id="pickText">Pick Order</p>
                </div>
                <div class="draftBody" id="pickTableHolder">
                    <table class="pickTable">
                        <tr>
                            <th>#</th>
                            <th>Team</th>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div id="mid">
            <div id="whatYouNeed" class="topContent">
                <div class="draftHeader">
                    <p id="wynText">What You Need</p>
                </div>
                <div id="WYNbody">
                    <div class="WYNpos">GK</div>
                    <div class="WYNpos">DF</div>
                    <div class="WYNpos">MF</div>
                    <div class="WYNpos">FW</div>
                    <div class="WYNpos" id="gkCount">0</div>
                    <div class="WYNpos" id="dfCount">0</div>
                    <div class="WYNpos" id="mfCount">0</div>
                    <div class="WYNpos" id="fwCount">0</div>
                </div>
            </div>
            <div id="thePlayers">
                <div class="draftHeader">Available Players</div>
                <div id="searchFreeAgents">
                    <?php $this->premTeam->getPremTeamsSelect(); ?>
                        <select id="selectPosition">
                            <option value="Any">Position</option>
                            <option value="Goalkeeper">GK</option>
                            <option value="Defender">DF</option>
                            <option value="Midfielder">MF</option>
                            <option value="Forward">FW</option>
                        </select>
                        <input id="selectName" type="text" placeholder="Search Players">
                </div>
                <table class="teamTable" id="freeAgentsTable">
                    <tr>
                        <th>Pos</th>
                        <th>Player</th>
                        <th>Team</th>
                        <th>TP</th>
                        <th>Select</th>
                    </tr>
                    <?php $this->draft->getAvailablePlayers() ?>
                </table>
            </div>
        </div>

        <div id="right">
            <div class="topContent" id="teamNameLeagueName"></div>
            <div id="pickHistory" class="mainContent">
                <div class="draftHeader">Pick History</div>
                <div class="draftBody" id="historyTableHolder">
                    <table class="pickTable">
                        <tr class="roundColumn">
                            <td>#</td>
                            <td>Team</td>
                            <td>Player</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
