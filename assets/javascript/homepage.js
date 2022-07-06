//On page load run load page function to set objects functions
window.onload = loadPage;

//set objects functions
function loadPage() {
    document.getElementById("orRegister").setAttribute("onClick", "switchBetween();");
    document.getElementById("orLogIn").setAttribute("onClick", "switchBack();");
    document.getElementById("pass1").setAttribute("onKeyUp", "checkPass();");
    document.getElementById("pass2").setAttribute("onKeyUp", "checkPass();");

}

function switchBetween() {
    $("#logIn").slideUp('slow', function () {
        $("#register").slideDown('slow');
    });
    $('#logInForm').animate({
        'margin-top': '-=50px'
    });
}

function switchBack() {
    $("#register").slideUp('slow', function () {
        $("#logIn").slideDown('slow');
    });
    $('#logInForm').animate({
        'margin-top': '+=50px'
    });
}

function checkPass() {
    var pass1 = document.getElementById('pass1');
    var pass2 = document.getElementById('pass2');

    if (pass2.value == "") {
        pass2.style.backgroundColor = "#ffffff";
        confirmButton.style.display = "none";
        notWorkingButton.style.display = "block";
        return false;
    } else if (pass1.value == pass2.value) {
        pass2.style.backgroundColor = "#ffffff";
        confirmButton.style.display = "block";
        notWorkingButton.style.display = "none";
        return true;
    } else {
        pass2.style.backgroundColor = "#ff6666";
        confirmButton.style.display = "none";
        notWorkingButton.style.display = "block";
        return false;
    }
}
