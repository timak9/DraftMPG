<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="assets/images/layOutPics/favicon.ico">
    <link rel="stylesheet" type="text/css" href="assets/css/reset.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/menu.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/dashboard.min.css" />
    <script src="assets/javascript/jqueryui/jquery-ui.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="assets/javascript/menu.min.js"></script>
    <script src="assets/javascript/dashboard.min.js"></script>

    <title>DFF | Dashboard |</title>

</head>

<body>

    <!-- Get nav bar -->
    <?php include('assets/view/nav.php'); ?>

        <div id="wrapper">
            <div id="LeagueOptions">
                <h2>My Leagues: </h2>
                <div class="optionsButton" id="createLeague">Create League</div>
                <div class="optionsButton" id="joinLeague">Join League</div>
                <?php if ($_SESSION['admin'] == 1) {echo '<a class="leagueHolderButton" href = "index.php?admin">Admin</a>';} ?>
            </div>


            <div id="createLeagueForm" class="leagueForm">
                <div class="form">
                    <label>League Name:</label>
                    <input type="text" id="createLeagueName" name="leaguename" placeholder="League Name" required>
                    <br>
                    <label>League Password:</label>
                    <input type="text" id="createLeaguePassword" name="password" placeholder="password" required>
                    <br>
                    <label>Time Per Draft Pick:</label>
                    <select id="createLeagueTimePerPick" name="timeperpick" required>
                        <option value="1">1 Second Quick Pick Draft</option>
                        <option value="30">30 secs</option>
                        <option value="60" selected>1 min</option>
                        <option value="120">2 mins</option>
                        <option value="300">5 mins</option>
                        <option value="600">10 mins</option>
                    </select>
                    <br>
                    <label>Team Name:</label>
                    <input type="text" id="createLeagueTeamName" name="teamname" placeholder="Team Name" required>
                    <br>
                    <div class="button" onclick="checkCreateLeagueForm()">Create League</div>
                </div>
            </div>

            <div id="joinLeagueForm" class="leagueForm">
                <div class="form">
                    <p id="warningLabel"></p>
                    </br>
                    <label>League Id:</label>
                    <input type="text" id="joinLeagueId" name="leagueid" placeholder="League Id" required>
                    <br>
                    <label>League Password:</label>
                    <input type="text" id="joinLeaguePassword" name="password" placeholder="Password" required>
                    <br>
                    <label>Team Name:</label>
                    <input type="text" id="joinLeagueTeamName" name="teamname" placeholder="Team Name" required>
                    <br>
                    <div class="button" onclick="checkJoinLeagueForm()">Join League</div>
                </div>
            </div>

            <div id="myLeagues">

                <?php $this->user->loadUsersLeagues() ?>

                    <div class="clear"></div>
            </div>

        </div>

</body>

</html>
