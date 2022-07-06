<?php

class user 
{
	var $database;
	var $name;
	var $id;
	var $admin;
	
	// -- Function Name : __construct
    // -- Params :
    // -- Purpose : Starts the connection to the database.
    public function __construct()    
	{
        $this->database = new database();
	}
	
	public function login()
    {
        if (isset($_POST['usernameLogIn']) && isset($_POST['passwordLogIn'])  )
        {
            // username and password sent from form
            $username =$_POST['usernameLogIn'];
            $password= $_POST['passwordLogIn'];
            
            $result =  $this->database->_login($username);
            $count=mysqli_num_rows($result);

            if($count==1)
            {
                // If result matched $myusername and $mypassword, table row must be 1 row
                while ($row = $result->fetch_assoc()) {
                    
                    //function doesnt exist on versions older than 5.6 of php so this is a fallback
                    if(!function_exists('hash_equals'))
                    {
                        function hash_equals($str1, $str2)
                        {
                            if(strlen($str1) != strlen($str2))
                            {
                                return false;
                            }
                            else
                            {
                                $res = $str1 ^ $str2;
                                $ret = 0;
                                for($i = strlen($res) - 1; $i >= 0; $i--)
                                {
                                    $ret |= ord($res[$i]);
                                }
                                return !$ret;
                            }
                        }
                    }

                    if ( hash_equals($row['password'], crypt($password, $row['salt'])) ) 
                    {
                        $this->id     = $row['id'];
                        $this->admin     = $row['admin'];
                        $this->name = $row['firstName']. " " .$row['lastName'];
                        $this->sessionCookies();
                        header("location: " . "http://" . $_SERVER['SERVER_NAME'] ."/index.php?dash=true");
                    }
                    else
                    {
                        header("location: " . "http://" . $_SERVER['SERVER_NAME'] ."/index.php?wrongpassword");
                    }
                    
                }
                
            } 
            else 
            {
                header("location: " . "http://" . $_SERVER['SERVER_NAME'] ."/index.php?wrongpassword");
            }

        }

    }
	
	// -- Function Name : logout
    // -- Params :
    // -- Purpose : Logs the user out and clears all the sessions and cookies.
    public
    function logout()
    {
        $_SESSION = array();
        // Unset all of the session variables.

        if (ini_get("session.use_cookies")) {
            // If it's desired to kill the session, also delete the session cookie.
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }

        session_destroy();
        // Finally, destroy the session.
        header("location: " . "http://" . $_SERVER['SERVER_NAME'] ."/index.php");
    }
    
    // -- Function Name : registration
    // -- Params :
    // -- Purpose : Registers the user to the database and strips them to unuse the sql injects.
    public
    function register(){

        if (isset($_POST['username']) && isset($_POST['password'])  )
        {
            // username and password sent from form
            $username =$_POST['username'];
            
            //check if username already exists
            $result =  $this->database->_login($username);
            $count=mysqli_num_rows($result);

            if($count==1)
            {
                echo 'Username already exists';
            }
            else
            {
                $password = $_POST['password'];

                // A higher "cost" is more secure but consumes more processing power
                $cost = 10;

                // Create a random salt
                $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');

                // Prefix information about the hash so PHP knows how to verify it later.
                // "$2a$" Means we're using the Blowfish algorithm. The following two digits are the cost parameter.
                $salt = sprintf("$2a$%02d$", $cost) . $salt;

                // Hash the password with the salt
                $hash = crypt($password, $salt);

                //get other user details
                $fName = $_POST['fName'];
                $lName= $_POST['lName'];
                $username = $_POST['username'];
                $email = $_POST['email'];

                //send the details to be added to database and return added row
                $result =  $this->database->_register($fName, $lName, $username, $email, $hash, $salt);
                $count2=mysqli_num_rows($result);

                //log user into account
                if($count2==1)
                {
                    while ($row = $result->fetch_assoc()) 
                    {
                        $this->id     = $row['id'];
                        $this->name = $row['firstName']. " " .$row['lastName'];
                        $this->sessionCookies();
                        header("location: " . "http://" . $_SERVER['SERVER_NAME'] ."/index.php?dash=true");
                    }
                }
            }
        }

    }
    
    //Loads and display users leagues and teams details in dashboard 
    public function loadUsersLeagues()
    {
        //get gameweek based on date
        $date = date('y-m-d');
        $gwresult =  $this->database->_getGameweek($date);
        //get first row
        $gwrow = $gwresult->fetch_assoc();
        
        //load the users league details
        $result = $this->database->_loadUsersLeagues($_SESSION['id']);
        $count=mysqli_num_rows($result);
        
        //display results
        if($count>0)
        {
            while ($row = $result->fetch_assoc()) 
            {
                //if league IS already started
                if($row['draftStatus'] == 2)
                {
                echo '    
                    <div class="leagueHolder">
                        <div class="leagueHolderHeader">'. $row['leagueName'] .'</div>
                        <p>'. $row['name'] .'</p>
                        <p>W - L - D</p>
                        <p>'. $row['w'] .' - '. $row['l'] .' - '. $row['d'] .'</p>
                        <div class="leagueHolderButton"><a class="blockLink" href="index.php?table=true&squadid='. $row['id'].'">League Table</a></div>
                        <div class="leagueHolderButton">';
                        
                        //check if current gameweek status is ongoing, print out points button if it is  
                        if ($gwrow['status'] == 1)
                        {
                            echo '<a class="blockLink" href="index.php?viewsquad='. $row['id'].'&squadid='. $row['id'].'">Points</a>';
                        }
                        //otherwise print out manage team button
                        else
                        {
                            echo '<a class="blockLink" href="index.php?squad=true&squadid='. $row['id'].'">Manage Team</a>';
                        }
                    echo '
                        </div>
                        <div class="leagueHolderButton"><a class="blockLink" href="index.php?transfer=true&squadid='. $row['id'].'">Transfer Market</a>';
                    
                    //get number of open transfer offers for current team
                    $openOffersResult = $this->database->_getTeamsTransferRequests($row['id']);
                    $openOffersRow = $openOffersResult->fetch_assoc();
                    //if there is at least one open offer display notification
                    if($openOffersRow['numOffers'] == 1)
                    {
                        echo '<div class="dashOpenOffers" title="You have ' .$openOffersRow['numOffers'] .' open transfer offer"><a href = "index.php?transfer=true&open=true&squadid='.$row['id'].'">'. $openOffersRow['numOffers'] .'</a></div>';
                    }
                    else if($openOffersRow['numOffers'] > 1)
                    {
                        echo '<div class="dashOpenOffers" title="You have ' .$openOffersRow['numOffers'] .' open transfer offers"><a href = "index.php?transfer=true&open=true&squadid='.$row['id'].'">'. $openOffersRow['numOffers'] .'</a></div>';
                    }
                
                echo '    </div>

                    </div>
                ';  
                }
                //if league IS NOT already started 
                else
                {
                //format date
                    $dt = new DateTime($row['draftDate']);
				    $day = $dt->format('d-m-y');
                    
                    
                echo '    
                    <div class="leagueHolder">
                        <div class="leagueHolderHeader">'. $row['leagueName'] .'</div>
                        <p>'. $row['name'] .'</p>
                        <p>Admin Manually Starts Draft</p>
                        <p>League Id = '. $row['leagueId'] .' & Password = '. $row['leaguePassword'] .'</p>
                        <div class="leagueHolderButton"><a class="blockLink" href="index.php?draft=true&squadid='. $row['id'].'">Draft Page</a></div>

                    </div>
                ';   
                }
            
            }
        }
        else
        {
            echo '<h2 class="noTeamYet">Hello '. $_SESSION['name'] .', You have no teams yet. Create or join a league above to get started!</h2>';
        }
    }
    
    //Loads and display users leagues dropdown and teams details in dashboard 
    public function loadUsersLeaguesDropDownOptions()
    {
        //load the users league details
        $result = $this->database->_loadUsersLeagues($_SESSION['id']);
        $count=mysqli_num_rows($result);
        
        //display results
        if($count>0)
        {
            while ($row = $result->fetch_assoc()) 
            {
            //if league IS already started display option for it
                if($row['status'] != 0)
                {
                    //select current league
                    if($row['id'] == $_GET['squadid'])
                    {
                    echo '
                        <option value="" selected>'. $row['name'] .', '. $row['leagueName'] .'</option>
                    ';   
                    }
                    else
                    {
                    echo '  
                        <option value="index.php?squad=true&squadid='. $row['id'].'">'. $row['name'] .', '. $row['leagueName'] .'</option>
                    '; 
                    } 
                }
            }
        }
    }
    
    
    // -- Function Name : sessionCookies
    // -- Params :
    // -- Purpose : Sets the session cookies.
    public function sessionCookies()
	{
        $_SESSION["id"] = $this->id;
        $_SESSION["name"] = $this->name;
        $_SESSION["admin"] = $this->admin;
    }
    
}
?>
