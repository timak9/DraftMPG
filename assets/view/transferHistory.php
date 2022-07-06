<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="assets/images/layOutPics/favicon.ico">
    <link rel="stylesheet" type="text/css" href="assets/css/reset.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/menu.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/transferMenu.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/transferOpen.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/playerInfoPopUp.min.css" />
    <script src="assets/javascript/jqueryui/jquery-ui.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="assets/javascript/menu.min.js"></script>
    <script src="assets/javascript/transferOpen.min.js"></script>
    <script src="assets/javascript/transferMenu.min.js"></script>
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
                <div class="tpSelect" id=""><a href="index.php?transfer=true&squadid=<?php echo $_GET['squadid'];?>" class="blockLink">Free Agents</a></div>
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
                    <div class="tpSelected" id=""><a href="index.php?transfer=true&history=true&squadid=<?php echo $_GET['squadid'];?>" class="blockLink">Transfer History</a></div>
            </div>

            <div id="offersHolder">

                <?php $this->transfer->getleagueTransfers() ?>

            </div>

        </div>

</body>

</html>
