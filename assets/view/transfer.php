<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="assets/images/layOutPics/favicon.ico">
    <link rel="stylesheet" type="text/css" href="assets/css/reset.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/menu.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/transfer.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/playerInfoPopUp.min.css" />
    <script src="assets/javascript/jqueryui/jquery-ui.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="assets/javascript/menu.min.js"></script>
    <script src="assets/javascript/transfer.min.js"></script>
    <script src="assets/javascript/playerInfoPopUp.min.js"></script>

    <title>DFF | Transfer Market |</title>

</head>

<body>

    <!-- Get nav bar -->
    <?php include('assets/view/nav.php'); ?>

        <div id="playerInformationWrapper">

        </div>

        <div id="wrapper">
            <!-- Transfer Page Selectors -->
            <div id="tpSelectorsHolder">
                <div class="tpSelected" id=""><a href="index.php?transfer=true&squadid=<?php echo $_GET['squadid'];?>" class="blockLink">Free Agents</a></div>
                <div class="tpSelect" id=""><a href="index.php?transfer=true&offer=true&squadid=<?php echo $_GET['squadid'];?>" class="blockLink">Offer Trade</a></div>

                <?php
                //get number of open transfer offers for current team
                    $openOffersCount = $this->transfer->getTeamsTransferRequests();
                    //if there is at least one open offer display notification
                    if($openOffersCount == 1)
                    {
                        echo '<div class="tpSelect" id="openOffer"><a href="index.php?transfer=true&open=true&squadid='.$_GET['squadid'].'" class="blockLink">'.$openOffersCount.' Open Offer</a></div>';
                    }
                    else if($openOffersCount > 1)
                    {
                        echo '<div class="tpSelect" id="openOffer"><a href="index.php?transfer=true&open=true&squadid='.$_GET['squadid'].'" class="blockLink">'.$openOffersCount.' Open Offers</a></div>';
                    }
                    else
                    {
                        echo '<div class="tpSelect" id=""><a href="index.php?transfer=true&open=true&squadid='.$_GET['squadid'].'" class="blockLink">Open Offers</a></div>';
                    }
            ?>

                    <div class="tpSelect" id=""><a href="index.php?transfer=true&history=true&squadid=<?php echo $_GET['squadid'];?>" class="blockLink">Transfer History</a></div>
            </div>

            <div id="myTeamHolder">
                <?php $this->squad->loadSquadForTransfer($_GET['squadid']) ?>
            </div>

            <div id="insAndOutsHolder">
                <p class="tradeAlerts" id="freeAgentTradeAlert">Alert Here</p>
                <div class="iaoHolder">
                    <div class="iaoHeader" id="ins">Players In</div>
                    <table class="teamTable" id="playersInTable">
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="iaoHolder">
                    <div class="iaoHeader" id="ins">Players Out</div>
                    <table class="teamTable" id="playersOutTable">
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <button class="makeTradeButton" id="freeAgentTradeButton">Make Trade</button>
            </div>

            <div id="freeAgentHolder">
                <h2>Free Agents</h2>
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
                        <th>Trade</th>
                        <th>Pos</th>
                        <th>Player</th>
                        <th>Team</th>
                        <th>TP</th>
                    </tr>
                    <?php $this->league->getFreeAgents() ?>
                </table>
            </div>

        </div>

</body>

</html>
