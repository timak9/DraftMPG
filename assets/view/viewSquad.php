<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="assets/images/layOutPics/favicon.ico">
    <link rel="stylesheet" type="text/css" href="assets/css/reset.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/squad.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/viewsquad.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/menu.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/playerInfoPopUp.min.css" />
    <script src="assets/javascript/jqueryui/jquery-ui.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="assets/javascript/menu.min.js"></script>
    <script src="assets/javascript/viewsquad.min.js"></script>
    <script src="assets/javascript/playerInfoPopUp.min.js"></script>

    <title>DFF | Points |</title>

</head>

<body>

    <?php include('assets/view/nav.php'); ?>

        <div id="playerInformationWrapper">

        </div>

        <div id="wrapper">

            <div id="changeGwWrapper">
                <?php $this->squad->viewSquad() ?>
            </div>
        </div>

</body>

</html>
