<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="assets/images/layOutPics/favicon.ico">
    <link rel="stylesheet" type="text/css" href="assets/css/reset.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/home.min.css" />
    <script src="assets/javascript/jqueryui/jquery-ui.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="assets/javascript/homepage.min.js"></script>

    <title>Draft Fantasy Football | Log In |</title>

</head>

<body>
    <div id="wrapper">

        <div id="infoBox">
            <h1>Welcome To</h1>
            <h2>Draft Fantasy Football</h2>
            <p>make your team your own</p>
        </div>

        <div id="logInForm">
            <div id="logIn">
                <form name="login" class="forms" action="<?php $this->user->login();?>" method="post">
                    <h2>Log In</h2>
                    <h3 id="red">Wrong Username and/or Password</h3>
                    <input type="text" class="input" name="usernameLogIn" placeholder="Username" required>
                    <br>
                    <input type="password" class="input" name="passwordLogIn" placeholder="Password" required>
                    <br>
                    <input type="submit" class="button2" value="Log in">
                </form>
                <p class="orRegister" id="orRegister">Or register</p>
            </div>
            <div id="register">
                <h2>Enter details</h2>
                <form name="Register" class="forms" action="<?php $this->user->register();?>" method="post">
                    <input type="text" class="input" name="fName" placeholder="First Name" required>
                    <br>
                    <input type="text" class="input" name="lName" placeholder="Last Name" required>
                    <br>
                    <input type="text" class="input" name="username" placeholder="Username" required>
                    <br>
                    <input type="email" class="input" name="email" placeholder="Email" minlength=6 required>
                    <br>
                    <input id="pass1" type="password" name="password" class="input" placeholder="Password" required>
                    <br>
                    <input id="pass2" type="password" class="input" onkeyup="checkPass(); return false;" placeholder="Re-enter Password" required>
                    <br>
                    <div id="notWorkingButton">Sign Up</div>
                    <input id="confirmButton" type="submit" class="button2" value="Sign Up">
                </form>
                <p class="orRegister" id="orLogIn">Or log in</p>
            </div>
        </div>
        <div class="clear"></div>
    </div>


</body>

</html>
