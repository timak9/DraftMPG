<?php 


echo'
    
   <div id="PageHeader">
        <div id="Logo"><a class="blockLink" href="index.php?dash"></a></div>
        <div id="Menu">';
//for dashboard dont display menu options
        if(!isset($_GET['dash']))
        {
            echo'
                <ul id="MenuList">
                    <li ';  
            //set selected li based on what page the user is on
                        if(isset($_GET['squad'])){echo 'class="selected"';}
                    echo'>
                        <a href = "index.php?squad=true&squadid='.$_GET['squadid'].'">Squad</a>
                    </li>
                    <li ';  
            //set selected li based on what page the user is on
                        if(isset($_GET['table'])){echo 'class="selected"';}
                    echo'>
                        <a href = "index.php?table=true&squadid='.$_GET['squadid'].'" >Table</a>
                    </li>
                    <li ';  
            //set selected li based on what page the user is on
                        if(isset($_GET['transfer'])){echo 'class="selected"';}
                    echo'>
                        <a href = "index.php?transfer=true&squadid='.$_GET['squadid'].'">Transfers</a>
                        ';
                    
                    //get number of open transfer offers for current team
                    $openOffersCount = $this->transfer->getTeamsTransferRequests();
                    //if there is at least one open offer display notification
                    if($openOffersCount == 1 && !isset($_GET['transfer']))
                    {
                        echo '<div class="openOffers" title="You have ' .$openOffersCount .' open transfer offer"><a href = "index.php?transfer=true&open=true&squadid='.$_GET['squadid'].'">' .$openOffersCount .'</a></div>';
                    }
                    else if($openOffersCount > 1 && !isset($_GET['transfer']))
                    {
                        echo '<div class="openOffers" title="You have ' .$openOffersCount .' open transfer offers"><a href = "index.php?transfer=true&open=true&squadid='.$_GET['squadid'].'">' .$openOffersCount .'</a></div>';
                    }
                
                echo '
                    </li>
                    <li ';  
            //set selected li based on what page the user is on
                        if(isset($_GET['viewsquad'])){echo 'class="selected"';}
                    echo'>
                        <a href = "index.php?viewsquad='.$_GET['squadid'].'&squadid='.$_GET['squadid'].'">Points</a>
                    </li>
                </ul>
            ';
        }
echo'    </div>
        <div id="LogOut">';
        //load players league select and display on all pages other than dashboard
        if(!isset($_GET['dash']))
        {
            echo'<select id="SelectTeam" onchange="location = this.options[this.selectedIndex].value;">';
                $this->user->loadUsersLeaguesDropDownOptions();
            echo'</select>';
        }
echo'
            <div class="logoutButton"><a href="index.php?logout=true">Log Out</a></div>
        </div>
        <div id="MobileMenu">
           <div id="MobileMenuPic"></div>
        </div>
        <div class="clear"></div>
        <div id="DropDownMenu">';
        //load players league select and display on all pages other than dashboard
        if(!isset($_GET['dash']))
        {
            echo'<select id="MobileSelectTeam" onchange="location = this.options[this.selectedIndex].value;">';
                $this->user->loadUsersLeaguesDropDownOptions();
            echo'</select>';
        }
echo'
            <div class="logoutButton"><a href="index.php?logout=true">Log Out</a></div>
            ';
//for dashboard dont display menu options
        if(!isset($_GET['dash']))
        {
            echo'
            <ul id="MobileMenuList">
                <li ';  
            //set selected li based on what page the user is on
                if(isset($_GET['squad'])){echo 'class="selected"';}
                echo'>
                    <a href = "index.php?squad=true&squadid='.$_GET['squadid'].'">Squad</a>
                </li>
                <li ';  
            //set selected li based on what page the user is on
                    if(isset($_GET['table'])){echo 'class="selected"';}
                echo'>
                    <a href = "index.php?table=true&squadid='.$_GET['squadid'].'" >Table</a>
                </li>
                <li ';  
            //set selected li based on what page the user is on
                    if(isset($_GET['transfer'])){echo 'class="selected"';}
                echo'>
                    <a href = "index.php?transfer=true&squadid='.$_GET['squadid'].'">Transfers</a>
                    ';
                    
                    //get number of open transfer offers for current team
                    $openOffersCount = $this->transfer->getTeamsTransferRequests();
                    //if there is at least one open offer display notification
                    if($openOffersCount == 1 && !isset($_GET['transfer']))
                    {
                        echo '<div class="openOffers" title="You have ' .$openOffersCount .' open transfer offer"><a href = "index.php?transfer=true&open=true&squadid='.$_GET['squadid'].'">' .$openOffersCount .'</a></div>';
                    }
                    else if($openOffersCount > 1 && !isset($_GET['transfer']))
                    {
                        echo '<div class="openOffers" title="You have ' .$openOffersCount .' open transfer offers"><a href = "index.php?transfer=true&open=true&squadid='.$_GET['squadid'].'">' .$openOffersCount .'</a></div>';
                    }
                
                echo '
                </li>
                <li ';  
            //set selected li based on what page the user is on
                    if(isset($_GET['viewsquad'])){echo 'class="selected"';}
                echo'>
                    <a href = "index.php?viewsquad='.$_GET['squadid'].'&squadid='.$_GET['squadid'].'">Points</a>
                </li>
            </ul>
            ';
        }
echo'
        </div>
        <div class="clear"></div>
    </div>
    
';


?>