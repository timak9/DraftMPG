//On page load run load page function to set objects functions
window.onload = loadPage;

//set objects functions
function loadPage() {
    document.getElementById("MobileMenuPic").setAttribute("onClick", "dropDownMenu();");

    //window.onload function for any other scripts
    loadThisPage();
}

//on screen resize hide mobile menu
window.onresize = function (event) {
    document.getElementById("MobileMenuPic").style.backgroundImage = "url('assets/images/layOutPics/menu.png')";
    document.getElementById("DropDownMenu").style.display = "none";
    document.getElementById("wrapper").style.marginTop = "80px";
};

//mobile menu slide up and slide down
function dropDownMenu() {
    if (document.getElementById("DropDownMenu").style.display != "block") {
        $("#DropDownMenu").slideDown();
        document.getElementById("MobileMenuPic").style.backgroundImage = "url('assets/images/layOutPics/closeMenu.png')";
        $('#wrapper').animate({
            'margin-top': '+=100px'
        });
    } else {
        $("#DropDownMenu").slideUp();
        document.getElementById("MobileMenuPic").style.backgroundImage = "url('assets/images/layOutPics/menu.png')";
        $('#wrapper').animate({
            'margin-top': '-=100px'
        });
    }
}
