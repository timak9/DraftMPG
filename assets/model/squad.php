<?php

class squad 
{
	var $database;
	
	// -- Function Name : __construct
    // -- Params :
    // -- Purpose : Starts the connection to the database.
    public function __construct()    
	{
        $this->database = new database();
	}
	
    public function makeSub()
    {
    //get Team and Players Id numbers
        $team = $_GET['squadid'];
        $pl1 = $_GET['pl1'];
        $pl2 = $_GET['pl2'];
        
    //update data holders
        $pl1Update;
        $pl2Update;
        
        $string = " blank";
        
    //load the full team squad first to find out in which position in the row each player is
        $result = $this->database->_loadSquad($team);
        $row = $result->fetch_assoc();
        
        //get check if players are in the goalie columns
                for($i=1; $i<=2; $i++)
                {
                    //set column name
                    $col = "g" . $i;
                    
                    //check if column value is equal to players id
                    if($row[$col] == $pl1)
                    {
                        //if it is equal to player 1's id, then initialize update holder data to set this column to player 2's id
                        $pl1Update = "" .$col. " = '". $pl2 . "'";
                        $string = $string . $pl1Update;
                    }
                    else if($row[$col] == $pl2)
                    {
                        //if it is equal to player 2's id, then initialize update holder data to set this column to player 1's id
                        $pl2Update = "" .$col. " = '". $pl1 . "'";
                        $string = $string . $pl2Update;
                    }
                    
                }
        
        //get check if players are in the starters columns
                for($i=1; $i<=10; $i++)
                {
                    //set column name
                    $col = "st" . $i;
                    
                    //check if column value is equal to players id
                    if($row[$col] == $pl1)
                    {
                        //if it is equal to player 1's id, then initialize update holder data to set this column to player 2's id
                        $pl1Update = "" .$col. " = '". $pl2 . "'";
                        $string = $string . $pl1Update;
                    }
                    else if($row[$col] == $pl2)
                    {
                        //if it is equal to player 2's id, then initialize update holder data to set this column to player 1's id
                        $pl2Update = "" .$col. " = '". $pl1 . "'";
                        $string = $string . $pl2Update;
                    }
                    
                }
        
        //get check if players are in the subs columns
                for($i=1; $i<=3; $i++)
                {
                    //set column name
                    $col = "s" . $i;
                    
                    //check if column value is equal to players id
                    if($row[$col] == $pl1)
                    {
                        //if it is equal to player 1's id, then initialize update holder data to set this column to player 2's id
                        $pl1Update = "" .$col. " = '". $pl2 . "'";
                        $string = $string . $pl1Update;
                    }
                    else if($row[$col] == $pl2)
                    {
                        //if it is equal to player 2's id, then initialize update holder data to set this column to player 1's id
                        $pl2Update = "" .$col. " = '". $pl1 . "'";
                        $string = $string . $pl2Update;
                    }
                    
                }
        
        //get check if players are in the reserves columns
                for($i=1; $i<=3; $i++)
                {
                    //set column name
                    $col = "r" . $i;
                    
                    //check if column value is equal to players id
                    if($row[$col] == $pl1)
                    {
                        //if it is equal to player 1's id, then initialize update holder data to set this column to player 2's id
                        $pl1Update = "" .$col. " = '". $pl2 . "'";
                        $string = $string . $pl1Update;
                    }
                    else if($row[$col] == $pl2)
                    {
                        //if it is equal to player 2's id, then initialize update holder data to set this column to player 1's id
                        $pl2Update = "" .$col. " = '". $pl1 . "'";
                        $string = $string . $pl2Update;
                    }
                    
                }
    
    //update database to save changes, pass in players id numbers
        $this->database->_makeSub($pl1Update, $pl2Update, $team);
    //call loadSquad function to reprint out the new line up
        $this->loadSquad($team);
        //echo $string;
    }
    
    //called to Load the page when squad.php view is opened
    public function loadSquad()
    {
        $id = $_GET['squadid'];
        
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($id);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueid = $teamsLeagueRow['leagueid'];
        
        //get gameweek based on date
        $date = date('y-m-d');
        $gwresult =  $this->database->_getGameweek($date);
        //get first row
        $gwrow = $gwresult->fetch_assoc();
        //set current gameweek
        $todaysGameweek = $gwrow['id'];
        
        //check if current gameweek has started yet, if not gw back 1 to allow for this game to remain in fixtures
        if($gwrow['status'] == 0)
        {
            $fixtureGW = $todaysGameweek - 1;
        }
        else
        {
            $fixtureGW = $todaysGameweek;
        }
        
        //get next gameweek opponent
        $opponentResult =  $this->database->_getManagersNextFixture($id, $fixtureGW);
        $opponentRow = $opponentResult->fetch_assoc();
        
        //query database for details
        $result = $this->database->_loadSquad($id);
        $row = $result->fetch_assoc();
        
        //add a decimal point to the value of player stored
        //get position for the point tobe put in
        $pos = strlen($row['squadvalue']) - 1;
        //add point to string in position
        $value = substr_replace($row['squadvalue'], '.', $pos, 0);

    //If the user IS the manager of the team
        if($row['managerId'] == $_SESSION['id'])
        {
            //print out squad page details
            echo'
                <div id="left60">
                <div id="pitchHeader">
                    <div class="halfWidth2">
                        <p>Squad Value: £'. $value .'0m </p><br>
                        <h3 title="' . $row['teamName'] . '">' . $row['teamName'] . '</h3><br>
                    </div>
                    <div class="halfWidth2">
                        <p>Next Opponent</p></br>'; 
            //check if the fixtures gameweek is the same as the next gameweek
                    if($opponentRow['gameweek']==$fixtureGW+1)
                    {
                        //print out opponent details
                        if($opponentRow['team1']==$id)
                        {
                            echo '<h3><a id="nextOpponentLink" href="index.php?viewsquad='.$opponentRow['team2'].'&leagueid='.$leagueid.'&squadid='.$id.'">'.$opponentRow['team2name'].'</a></h3>';
                        }
                        else
                        {
                            echo '<h3><a id="nextOpponentLink" href="index.php?viewsquad='.$opponentRow['team1'].'&leagueid='.$leagueid.'&squadid='.$id.'">'.$opponentRow['team1name'].'</a></h3>';
                        }
                    }
            //if its not then the useris playing against the league average
                    else
                    {
                        echo '<h3><a id="nextOpponentLink">League Average</a></h3>';
                    }
                echo'</div>
                </div>
                <div id="pitch">
                    <div class="position" id="gk">';
            //get Goalkeepers Information and print out goalkeepers on pitch
                    $goalie = $this->database->_getSinglePlayerDetails($row['g1'], $fixtureGW);
                    $goalieRow = $goalie->fetch_assoc();
            echo'
                        <div class="player" id="'. $goalieRow['position'] . $goalieRow['id'] .'">
                            <div class="badge"><img class="badgePic" src="assets/images/clubBadges/'. $goalieRow['teamId'] .'.png" alt="'. $goalieRow['teamName'] .' Badge"></div>
                            <div class="name" title="'. $goalieRow['webName'] .'" id="'. $goalieRow['id'] . '"><p>'. $goalieRow['webName'] .'</p></div>
                            <div class="vs" title="'. $goalieRow['nextFixture'] .'"><p>' . $goalieRow['nextFixture'] . '</p></div>
                        </div>

                    </div>

                    <div class="position" id="df">
            ';
                    //get Defenders Information and print out defence on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $defender = $this->database->_getSinglePlayerDetails($row[$id], $fixtureGW);
                        $defenderRow = $defender->fetch_assoc();
                        if($defenderRow['position'] == "Defender")
                        {
                        echo'
                            <div class="player" id="'. $defenderRow['position'] . $defenderRow['id'] . '">
                                <div class="badge"><img class="badgePic" src="assets/images/clubBadges/'. $defenderRow['teamId'] .'.png" alt="'. $defenderRow['teamName'] .' Badge"></div>
                                <div class="name" title="'. $defenderRow['webName'] .'" id="'. $defenderRow['id'] . '"><p>'. $defenderRow['webName'] .'</p></div>
                                <div class="vs" title="'. $defenderRow['nextFixture'] .'"><p>' . $defenderRow['nextFixture'] . '</p></div>
                            </div>
                        ';
                        }
                    }

            echo' 
                    </div>

                    <div class="position" id="mf">

            ';
                    //get Midfielders Information and print out midfield on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $midfielder = $this->database->_getSinglePlayerDetails($row[$id], $fixtureGW);
                        $midfielderRow = $midfielder->fetch_assoc();
                        if($midfielderRow['position'] == "Midfielder")
                        {
                        echo'
                            <div class="player" id="'. $midfielderRow['position'] . $midfielderRow['id'] . '">
                                <div class="badge"><img class="badgePic" src="assets/images/clubBadges/'. $midfielderRow['teamId'] .'.png" alt="'. $midfielderRow['teamName'] .' Badge"></div>
                                <div class="name" title="'. $midfielderRow['webName'] .'" id="'. $midfielderRow['id'] . '"><p>'. $midfielderRow['webName'] .'</p></div>
                                <div class="vs" title="'. $midfielderRow['nextFixture'] .'" ><p>' . $midfielderRow['nextFixture'] . '</p></div>
                            </div>
                        ';
                        }
                    }

            echo'   
                    </div>

                    <div class="position" id="str">
            ';
                    //get Midfielders Information and print out midfield on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $forward = $this->database->_getSinglePlayerDetails($row[$id], $fixtureGW);
                        $forwardRow = $forward->fetch_assoc();
                        if($forwardRow['position'] == "Forward")
                        {
                        echo'
                            <div class="player" id="'. $forwardRow['position'] . $forwardRow['id'] . '">
                                <div class="badge"><img class="badgePic" src="assets/images/clubBadges/'. $forwardRow['teamId'] .'.png" alt="'. $forwardRow['teamName'] .' Badge"></div>
                                <div class="name" title="'. $forwardRow['webName'] .'" id="'. $forwardRow['id'] . '"><p>'. $forwardRow['webName'] .'</p></div>
                                <div class="vs" title="'. $forwardRow['nextFixture'] .'"><p>' . $forwardRow['nextFixture'] . '</p></div>
                            </div>
                        ';
                        }
                    }

            echo'   
                    </div>

                </div>
            </div>
            <div id="right40">
                <div id="benchHeader">
                    <h1>Bench</h1>
                </div>
                <div id="bench">

            ';

                    //get sub goalie Information and print out on bench
                    $subGoalie = $this->database->_getSinglePlayerDetails($row['g2'], $fixtureGW);
                    $subGoalieRow = $subGoalie->fetch_assoc();

                    echo'
                        <div class="benchSeat">
                             <div class="sub" id="'. $subGoalieRow['position'] . $subGoalieRow['id'] .'">
                                <div class="badge"><img class="subBadgePic" src="assets/images/clubBadges/'. $subGoalieRow['teamId'] .'.png" alt="'. $subGoalieRow['teamName'] .' Badge"></div>
                                <div class="name" title="'. $subGoalieRow['webName'] .'" id="'. $subGoalieRow['id'] .'"><p>'. $subGoalieRow['webName'] .'</p></div>
                                <div class="vs" title="'. $subGoalieRow['nextFixture'] .'"><p>'. $subGoalieRow['nextFixture'] .'</p></div>
                            </div>
                        </div>
                    ';

                    //get subs Information and print out bench
                    for($i=1; $i<=3; $i++)
                    {
                        $id = "s" . $i;
                        $bench = $this->database->_getSinglePlayerDetails($row[$id], $fixtureGW);
                        $benchRow = $bench->fetch_assoc();

                    echo'
                        <div class="benchSeat">
                             <div class="sub" id="'. $benchRow['position'] . $benchRow['id'] . '">
                                <div class="badge"><img class="subBadgePic" src="assets/images/clubBadges/'. $benchRow['teamId'] .'.png" alt="'. $benchRow['teamName'] .' Badge"></div>
                                <div class="name" title="'. $benchRow['webName'] .'" id="'. $benchRow['id'] . '"><p>'. $benchRow['webName'] .'</p></div>
                                <div class="vs" title="'. $benchRow['nextFixture'] .'"><p>'. $benchRow['nextFixture'] .'</p></div>
                            </div>
                        </div>
                    ';
                    }       
            echo'
                </div>

                <div id="reserveHeader">
                    <h1>Reserves</h1>
                </div>
                <div id="reserves">
            ';

                    //get subs Information and print out bench
                    for($i=1; $i<=3; $i++)
                    {
                        $id = "r" . $i;
                        $reserver = $this->database->_getSinglePlayerDetails($row[$id], $fixtureGW);
                        $reserverRow = $reserver->fetch_assoc();

                    echo'
                        <div class="reserveSeat">
                         <div class="sub" id="'. $reserverRow['position'] . $reserverRow['id'] . '">
                            <div class="badge"><img class="subBadgePic" src="assets/images/clubBadges/'. $reserverRow['teamId'] .'.png" alt="'. $reserverRow['teamName'] .' Badge">
                             </div>
                            <div class="name" title="'. $reserverRow['webName'] .'" id="'. $reserverRow['id'] .'"><p>'. $reserverRow['webName'] .'</p></div>
                            <div class="vs"  title="'. $reserverRow['nextFixture'] .'"><p>'. $reserverRow['nextFixture'] .'</p></div>
                        </div>
                    </div>
                    ';
                    }

            echo'

                </div>

            </div>
            ';
        }
    //If the user IS NOT manager of the team
        else
        {
            echo "not your team";
        }
    }
    
    //called to Load the page when viewsquad.php view is opened
    public function viewSquad()
    {
        //set id of squad to view
        $id = $_GET['viewsquad'];
        
        //set variable that will be used to stop + button being shown when next gameweek up hasnt started
        $dontAllowUp = false;
        
        //check if gameweek has been passed, and set it if it has
        if (isset($_GET['gameweek']))
        {
            //get gameweek details
            $result =  $this->database->_getGameweekById($_GET['gameweek']);

            //get first row
            $row = $result->fetch_assoc();
            
            //check if gameweek has started
            if($row['status'] == 0)
            {
                //if it hasn't set to previous gameweek
                $gameweek = $row['id']-1;
                
                //update variable that will be used to stop + button being shown when next gameweek up hasnt started
                $dontAllowUp = true;
            }
            else
            {
                //if it has set it to current gameweek  
                $gameweek = $row['id'];
            }
        }
        //otherwise set it to current gameweek based on date
        else
        {
            //update variable that will be used to stop + button being shown when next gameweek up hasnt started
            $dontAllowUp = true;
            
            //get gameweek based on date
            $result =  $this->database->_getGameweek(date('y-m-d'));

            //get first row
            $row = $result->fetch_assoc();
            
            //check if gameweek has started
            if($row['status'] == 0)
            {
                //if it hasn't set to previous gameweek
                $gameweek = $row['id']-1;
                
                //update variable that will be used to stop + button being shown when next gameweek up hasnt started
                $dontAllowUp = true;
            }
            else
            {
                //if it has set it to current gameweek  
                $gameweek = $row['id'];
            }
        }
        
        //query database for details
        $result = $this->database-> _getGameweekSquad($id, $gameweek);
        
         $count=mysqli_num_rows($result);
        
        if($count!=0)
        {
        
        //var to hold points totals
        $totalPoints = 0; $benchPoints = 0;
        
        //get squads total points
        $pointsResult = $this->database-> _getGameweekSquad($id, $gameweek);
        $pointsRow = $pointsResult->fetch_assoc();
        //set players ids by posistions
        $g1 = $pointsRow['g1']; 
        $g2 = $pointsRow['g2'];
        $st1 = $pointsRow['st1'];
        $st2 = $pointsRow['st2'];
        $st3 = $pointsRow['st3'];
        $st4 = $pointsRow['st4'];
        $st5 = $pointsRow['st5'];
        $st6 = $pointsRow['st6'];
        $st7 = $pointsRow['st7'];
        $st8 = $pointsRow['st8'];
        $st9 = $pointsRow['st9'];
        $st10 = $pointsRow['st10'];
        $s1 = $pointsRow['s1'];
        $s2 = $pointsRow['s2'];
        $s3 = $pointsRow['s3'];
        
        //add a decimal point to the value of player stored
        //get position for the point tobe put in
        $pos = strlen($pointsRow['squadvalue']) - 1;
        //add point to string in position
        $squadValue = substr_replace($pointsRow['squadvalue'], '.', $pos, 0);


        //loop through and save total points
        for($i=1; $i<11; $i++) 
        {        
            //add points to correct var holder based on players position
            if($i == 1)
            {
                $goaliePos = "g".$i; $starterPos = "st".$i; $subPos = "s".$i;
                
                $goalie = $this->database->_getSinglePlayerGameweekPoints(${$goaliePos}, $gameweek);
                $goalieRow = $goalie->fetch_assoc();
                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gameweek);
                $starterRow = $starter->fetch_assoc();
                $sub = $this->database->_getSinglePlayerGameweekPoints(${$subPos}, $gameweek);
                $subRow = $sub->fetch_assoc();
                
                $goaliePoints = $goalieRow['total'];
                $starterPoints = $starterRow['total'];
                $subPoints = $subRow['total'];
                
                
                $totalPoints = $totalPoints + $goaliePoints;
                $totalPoints = $totalPoints + $starterPoints;
                $benchPoints = $benchPoints + $subPoints;
            }
            else if($i == 2)
            {
                $goaliePos = "g".$i; $starterPos = "st".$i; $subPos = "s".$i;
                
                $goalie = $this->database->_getSinglePlayerGameweekPoints(${$goaliePos}, $gameweek);
                $goalieRow = $goalie->fetch_assoc();
                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gameweek);
                $starterRow = $starter->fetch_assoc();
                $sub = $this->database->_getSinglePlayerGameweekPoints(${$subPos}, $gameweek);
                $subRow = $sub->fetch_assoc();
                
                $goaliePoints = $goalieRow['total'];
                $starterPoints = $starterRow['total'];
                $subPoints = $subRow['total'];
                
                
                $benchPoints = $benchPoints + $goaliePoints;
                $totalPoints = $totalPoints + $starterPoints;
                $benchPoints = $benchPoints + $subPoints;
            }
            else if($i== 3)
            {
                $starterPos = "st".$i; $subPos = "s".$i;
                
                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gameweek);
                $starterRow = $starter->fetch_assoc();
                $sub = $this->database->_getSinglePlayerGameweekPoints(${$subPos}, $gameweek);
                $subRow = $sub->fetch_assoc();
                
                $starterPoints = $starterRow['total'];
                $subPoints = $subRow['total'];
                
                $totalPoints = $totalPoints + $starterPoints;
                $benchPoints = $benchPoints + $subPoints;
            }
            else
            {
                $starterPos = "st".$i;
                
                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gameweek);
                $starterRow = $starter->fetch_assoc();
                
                $starterPoints = $starterRow['total'];
                
                $totalPoints = $totalPoints + $starterPoints;
            }
        }
            
            
        $row = $result->fetch_assoc();
            //print out squad page details
            echo'
                <div id="left60">
                <div id="pitchHeader">
                    <div class="thirdWidth2">
                        <p>Squad Value: £'. $squadValue .'0m </p><br>
                        <h3>' . $row['teamName'] . '</h3>
                    </div>
                    <div class="thirdWidth2">';
                    //display - button as long as gamewwek is 2 or more
                        if($gameweek > 1)
                        {
                            echo'<button class="gwChanger" id="'. ($gameweek-1) .'">-</button>';
                        }
                        echo'<p class="pxPad5" id="bigScreenGW">Gameweek <h3 id="bigScreenGW">'.$gameweek.' </h3></p>
                        <h3 class="pxPad5"  id="smallScreenGW">GW '.$gameweek.' </h3>';
                    //display + button as long as gamewwek is 37 or less
                        if($gameweek < 38 && $dontAllowUp == false)
                        {
                            echo'<button class="gwChanger" id="'. ($gameweek+1) .'">+</button>';
                        }
                        echo'
                        </br><h3>Points : '.$totalPoints.'</h3>
                    </div>
                    <div class="thirdWidth2">
                        <h3>Manager</h3></br> 
                        <p>' . $row['firstName'] . ' ' . $row['lastName'] . '</p>
                    </div>
                </div>
                <div id="pitch">
                    <div class="position" id="gk">';
            //get Goalkeepers Information and print out goalkeepers on pitch
                    $goalie = $this->database->_getSinglePlayerGameweekPoints($row['g1'], $gameweek);
                    $goalieRow = $goalie->fetch_assoc();
            echo'
                        <div class="player" id="'. $goalieRow['position'] . $goalieRow['id'] .'">
                            <div class="badge"><img class="badgePic" src="assets/images/clubBadges/'. $goalieRow['teamId'] .'.png" alt="'. $goalieRow['teamName'] .' Badge"></div>
                            <div class="name" title="'. $goalieRow['webName'] .'" id="'. $goalieRow['id'] . '"><p>'. $goalieRow['webName'] .'</p></div>
                            <div class="vs" title="'. $goalieRow['total'] .'"><p>'. $goalieRow['total'] .'</p></div>
                        </div>

                    </div>

                    <div class="position" id="df">
            ';
                    //get Defenders Information and print out defence on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $defender = $this->database->_getSinglePlayerGameweekPoints($row[$id], $gameweek);
                        $defenderRow = $defender->fetch_assoc();
                        if($defenderRow['position'] == "Defender")
                        {
                        echo'
                            <div class="player" id="'. $defenderRow['position'] . $defenderRow['id'] . '">
                                <div class="badge"><img class="badgePic" src="assets/images/clubBadges/'. $defenderRow['teamId'] .'.png" alt="'. $defenderRow['teamName'] .' Badge"></div>
                                <div class="name" title="'. $defenderRow['webName'] .'" id="'. $defenderRow['id'] . '"><p>'. $defenderRow['webName'] .'</p></div>
                                <div class="vs" title="'. $defenderRow['total'] .'"><p>'. $defenderRow['total'] .'</p></div>
                            </div>
                        ';
                        }
                    }

            echo' 
                    </div>

                    <div class="position" id="mf">

            ';
                    //get Midfielders Information and print out midfield on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $midfielder = $this->database->_getSinglePlayerGameweekPoints($row[$id], $gameweek);
                        $midfielderRow = $midfielder->fetch_assoc();
                        if($midfielderRow['position'] == "Midfielder")
                        {
                        echo'
                            <div class="player" id="'. $midfielderRow['position'] . $midfielderRow['id'] . '">
                                <div class="badge"><img class="badgePic" src="assets/images/clubBadges/'. $midfielderRow['teamId'] .'.png" alt="'. $midfielderRow['teamName'] .' Badge"></div>
                                <div class="name" title="'. $midfielderRow['webName'] .'" id="'. $midfielderRow['id'] . '"><p>'. $midfielderRow['webName'] .'</p></div>
                                <div class="vs" title="'. $midfielderRow['total'] .'"><p>'. $midfielderRow['total'] .'</p></div>
                            </div>
                        ';
                        }
                    }

            echo'   
                    </div>

                    <div class="position" id="str">
            ';
                    //get Midfielders Information and print out midfield on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $forward = $this->database->_getSinglePlayerGameweekPoints($row[$id], $gameweek);
                        $forwardRow = $forward->fetch_assoc();
                        if($forwardRow['position'] == "Forward")
                        {
                        echo'
                            <div class="player" id="'. $forwardRow['position'] . $forwardRow['id'] . '">
                                <div class="badge"><img class="badgePic" src="assets/images/clubBadges/'. $forwardRow['teamId'] .'.png" alt="'. $forwardRow['teamName'] .' Badge"></div>
                                <div class="name" title="'. $forwardRow['webName'] .'" id="'. $forwardRow['id'] . '"><p>'. $forwardRow['webName'] .'</p></div>
                                <div class="vs" title="'. $forwardRow['total'] .'"><p>'. $forwardRow['total'] .'</p></div>
                            </div>
                        ';
                        }
                    }

            echo'   
                    </div>

                </div>
            </div>
            <div id="right40">
                <div id="benchHeader">
                    <div class="halfWidth">
                        <h1>Bench</h1>
                    </div>
                    <div class="halfWidth">
                        <h3>Points: '. $benchPoints .'</h3>
                    </div>
                </div>
                <div id="bench">

            ';

                    //get sub goalie Information and print out on bench
                    $subGoalie = $this->database->_getSinglePlayerGameweekPoints($row['g2'], $gameweek);
                    $subGoalieRow = $subGoalie->fetch_assoc();

                    echo'
                        <div class="benchSeat">
                             <div class="sub" id="'. $subGoalieRow['position'] . $subGoalieRow['id'] .'">
                                <div class="badge"><img class="subBadgePic" src="assets/images/clubBadges/'. $subGoalieRow['teamId'] .'.png" alt="'. $subGoalieRow['teamName'] .' Badge"></div>
                                <div class="name" title="'. $subGoalieRow['webName'] .'" id="'. $subGoalieRow['id'] .'"><p>'. $subGoalieRow['webName'] .'</p></div>
                                <div class="vs" title="'. $subGoalieRow['total'] .'"><p>'. $subGoalieRow['total'] .'</p></div>
                            </div>
                        </div>
                    ';

                    //get subs Information and print out bench
                    for($i=1; $i<=3; $i++)
                    {
                        $id = "s" . $i;
                        $bench = $this->database->_getSinglePlayerGameweekPoints($row[$id], $gameweek);
                        $benchRow = $bench->fetch_assoc();

                    echo'
                        <div class="benchSeat">
                             <div class="sub" id="'. $benchRow['position'] . $benchRow['id'] . '">
                                <div class="badge"><img class="subBadgePic" src="assets/images/clubBadges/'. $benchRow['teamId'] .'.png" alt="'. $benchRow['teamName'] .' Badge"></div>
                                <div class="name" title="'. $benchRow['webName'] .'" id="'. $benchRow['id'] . '"><p>'. $benchRow['webName'] .'</p></div>
                                <div class="vs" title="'. $benchRow['total'] .'"><p>'. $benchRow['total'] .'</p></div>
                            </div>
                        </div>
                    ';
                    }       
            echo'
                </div>
                <div id="fixture">
                    <p>League Gameweek Fixtures</p>';
                //get gameweek opponent details
        
                //get gameweek opponent points total
                $this->getGWFixtures($gameweek);
            echo' 
                </div>
            </div>
            ';
        }
        else
        {
            echo'
                <div id="left60">
                <div id="pitchHeader">
                    <div class="thirdWidth">
                        <h2>No Team</h2>
                    </div>
                    <div class="thirdWidth2">';
                    //display - button as long as gamewwek is 2 or more
                        if($gameweek > 1)
                        {
                            echo'<button class="gwChanger" id="'. ($gameweek-1) .'">-</button>';
                        }
                        echo'<p class="pxPad5" id="bigScreenGW">Gameweek <h3 id="bigScreenGW">'.$gameweek.' </h3></p>
                        <h3 class="pxPad5"  id="smallScreenGW">GW '.$gameweek.' </h3>';
                    //display + button as long as gamewwek is 37 or less
                        if($gameweek < 38 && $dontAllowUp == false)
                        {
                            echo'<button class="gwChanger" id="'. ($gameweek+1) .'">+</button>';
                        }
                        echo'
                        </br><h3>League hadn\'t started</h3>
                    </div>
                    <div class="thirdWidth2">
                        <h3>Manager</h3></br> 
                        <p></p>
                    </div>
                </div>
                <div id="pitch">
                    
                </div>';
        }
    }
    
     //gets the fixtures/results for a league
    function getGWFixtures($gw)
    {
        //set league and id params
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($_GET['squadid']);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueid = $teamsLeagueRow['leagueid'];
        
        //search for league
        $result =  $this->database->_getGWFixtures($leagueid, $gw);
        
        $count=mysqli_num_rows($result);
        //if no fixtures are returned print message
        if($count<1)
        {
            echo '<p id="noFixturesText">No fixtures</p>';
        }
        //otherwise loop through and print out fixtures
        else
        {
            //var to hold league teams total points and to count number of teams in league
            $totalLeaguePoints = 0; $numTeams = 0;
            
            while($row = $result->fetch_assoc())
            {
                //add the two teams to number of teams in league
                $numTeams = $numTeams + 2;
                
                //print out  fixtures details
            echo'
                    <div class="fixture">';
                //if game status is 1 then gameweek has started so display score
                    if($row['status'] != 0)
                    {
                        //get the teams gameweek gameweek squads current points total for a said gameweek
                        //var to hold points totals
                        $team1TotalPoints = 0; $team2TotalPoints = 0;

                        //get team1 total points
                        $pointsResult = $this->database-> _getGameweekSquad($row['team1'], $gw);
                        $pointsRow = $pointsResult->fetch_assoc();
                        //set players ids by posistions
                        $g1 = $pointsRow['g1']; 
                        $g2 = $pointsRow['g2'];
                        $st1 = $pointsRow['st1'];
                        $st2 = $pointsRow['st2'];
                        $st3 = $pointsRow['st3'];
                        $st4 = $pointsRow['st4'];
                        $st5 = $pointsRow['st5'];
                        $st6 = $pointsRow['st6'];
                        $st7 = $pointsRow['st7'];
                        $st8 = $pointsRow['st8'];
                        $st9 = $pointsRow['st9'];
                        $st10 = $pointsRow['st10'];
                        $s1 = $pointsRow['s1'];
                        $s2 = $pointsRow['s2'];
                        $s3 = $pointsRow['s3'];
                        //loop through and save total points
                        for($i=1; $i<11; $i++) 
                        {    
                            //add points to correct var holder based on players position
                            if($i == 1)
                            {
                                $goaliePos = "g".$i; $starterPos = "st".$i;

                                $goalie = $this->database->_getSinglePlayerGameweekPoints(${$goaliePos}, $gw);
                                $goalieRow = $goalie->fetch_assoc();
                                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gw);
                                $starterRow = $starter->fetch_assoc();

                                $goaliePoints = $goalieRow['total'];
                                $starterPoints = $starterRow['total'];


                                $team1TotalPoints = $team1TotalPoints + $goaliePoints;
                                $team1TotalPoints = $team1TotalPoints + $starterPoints;
                            }
                            else
                            {
                                $starterPos = "st".$i;

                                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gw);
                                $starterRow = $starter->fetch_assoc();

                                $starterPoints = $starterRow['total'];

                                $team1TotalPoints = $team1TotalPoints + $starterPoints;
                            }
                        }
                        
                        //add team 1 points to league total points
                        $totalLeaguePoints = $totalLeaguePoints + $team1TotalPoints;
                        
                        //get team2 total points
                        $pointsResult = $this->database-> _getGameweekSquad($row['team2'], $gw);
                        $pointsRow = $pointsResult->fetch_assoc();
                        //set players ids by posistions
                        $g1 = $pointsRow['g1']; 
                        $g2 = $pointsRow['g2'];
                        $st1 = $pointsRow['st1'];
                        $st2 = $pointsRow['st2'];
                        $st3 = $pointsRow['st3'];
                        $st4 = $pointsRow['st4'];
                        $st5 = $pointsRow['st5'];
                        $st6 = $pointsRow['st6'];
                        $st7 = $pointsRow['st7'];
                        $st8 = $pointsRow['st8'];
                        $st9 = $pointsRow['st9'];
                        $st10 = $pointsRow['st10'];
                        $s1 = $pointsRow['s1'];
                        $s2 = $pointsRow['s2'];
                        $s3 = $pointsRow['s3'];
                        //loop through and save total points
                        for($i=1; $i<11; $i++) 
                        {    
                            //add points to correct var holder based on players position
                            if($i == 1)
                            {
                                $goaliePos = "g".$i; $starterPos = "st".$i;

                                $goalie = $this->database->_getSinglePlayerGameweekPoints(${$goaliePos}, $gw);
                                $goalieRow = $goalie->fetch_assoc();
                                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gw);
                                $starterRow = $starter->fetch_assoc();

                                $goaliePoints = $goalieRow['total'];
                                $starterPoints = $starterRow['total'];


                                $team2TotalPoints = $team2TotalPoints + $goaliePoints;
                                $team2TotalPoints = $team2TotalPoints + $starterPoints;
                            }
                            else
                            {
                                $starterPos = "st".$i;

                                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gw);
                                $starterRow = $starter->fetch_assoc();

                                $starterPoints = $starterRow['total'];

                                $team2TotalPoints = $team2TotalPoints + $starterPoints;
                            }
                        }
                        
                        //add team 1 points to league total points
                        $totalLeaguePoints = $totalLeaguePoints + $team2TotalPoints;
                        
                        echo'
                            <span class="fixtureText"> <a href="index.php?viewsquad='. $row['team1'] .'&squadid='. $_GET['squadid'] .'&gameweek='. $gw .'">'. $row['team1name'] .'</a></span> '. $team1TotalPoints .' 
                            - 
                            '. $team2TotalPoints .' <span class="fixtureText"><a href="index.php?viewsquad='. $row['team2'] .'&squadid='. $_GET['squadid'] .'&gameweek='. $gw .'">'. $row['team2name'] .'</a></span></div>
                        ';
                    }
                //otherwise don't display score
                    else
                    {
                        echo'
                            <span class="fixtureText">'. $row['team1name'] .'</span>
                            <a href="">Vs</a> 
                            <span class="fixtureText">'. $row['team2name'] .'</span></div>
                        ';
                    }
                
            
            }
                
                //search for league fixtures for selected gameweek for games vs league average
            $resultVsLA =  $this->database->_getGWFixturesVsLeagueAverage($leagueid, $gw);
            //count to see if there are any fixtures vs League Average
            $countVsLa=mysqli_num_rows($resultVsLA);
            
            // if there is print out fixtures details for games vs league average
            if($countVsLa == 1)
            {
               //add the team to number of teams in league
                $numTeams = $numTeams + 1;
                
                //var to hold teams total points
                $teamVsLaTotalPoints = 0;
                
                while($rowVsLA = $resultVsLA->fetch_assoc())
                {
                    //get team1 total points
                    $pointsResult = $this->database-> _getGameweekSquad($rowVsLA['team2'], $gw);
                    $pointsRow = $pointsResult->fetch_assoc();
                    //set players ids by posistions
                    $g1 = $pointsRow['g1']; 
                    $g2 = $pointsRow['g2'];
                    $st1 = $pointsRow['st1'];
                    $st2 = $pointsRow['st2'];
                    $st3 = $pointsRow['st3'];
                    $st4 = $pointsRow['st4'];
                    $st5 = $pointsRow['st5'];
                    $st6 = $pointsRow['st6'];
                    $st7 = $pointsRow['st7'];
                    $st8 = $pointsRow['st8'];
                    $st9 = $pointsRow['st9'];
                    $st10 = $pointsRow['st10'];
                    $s1 = $pointsRow['s1'];
                    $s2 = $pointsRow['s2'];
                    $s3 = $pointsRow['s3'];
                    //loop through and save total points
                    for($i=1; $i<11; $i++) 
                    {    
                        //add points to correct var holder based on players position
                        if($i == 1)
                        {
                            $goaliePos = "g".$i; $starterPos = "st".$i;
                            
                            $goalie = $this->database->_getSinglePlayerGameweekPoints(${$goaliePos}, $gw);
                            $goalieRow = $goalie->fetch_assoc();
                            $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gw);
                            $starterRow = $starter->fetch_assoc();
                            
                            $goaliePoints = $goalieRow['total'];
                            $starterPoints = $starterRow['total'];

                            $teamVsLaTotalPoints = $teamVsLaTotalPoints + $goaliePoints;
                            $teamVsLaTotalPoints = $teamVsLaTotalPoints + $starterPoints;
                        }
                        else
                        {
                            $starterPos = "st".$i;

                            $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gw);
                            $starterRow = $starter->fetch_assoc();
                            
                            $starterPoints = $starterRow['total'];

                            $teamVsLaTotalPoints = $teamVsLaTotalPoints + $starterPoints;
                        }
                    }
                    
                    //add team vs league average points to league total points
                    $totalLeaguePoints = $totalLeaguePoints + $teamVsLaTotalPoints;
                    
                    //work out the average points score for league teams
                    $leagueAverage = round($totalLeaguePoints / $numTeams);
                    
                echo'
                    <div class="fixture">';
                //if game status is 1 then gameweek has started so display score
                    if($rowVsLA['status'] != 0)
                    {
                        echo'
                            <span class="fixtureText">League Average</span> '. $leagueAverage .' 
                            -
                            '. $teamVsLaTotalPoints .' <span class="fixtureText"><a href="index.php?viewsquad='. $rowVsLA['team2'] .'&squadid='. $_GET['squadid'] .'&gameweek='. $gw .'">'. $rowVsLA['team2name'] .'</a></span></div>
                        ';
                    }
                //otherwise don't display score
                    else
                    {
                        echo'
                            <span class="fixtureText">League Average</span>
                            <a href="">Vs</a> 
                            <span class="fixtureText">'. $rowVsLA['team2name'] .'</span></div>
                        ';
                    }
                echo'</div>';
                }
                echo'</div>';
            }   
        }
    }
    
    public function loadSquadForTransfer($id)
    {
        //query database for details
        $result = $this->database->_loadSquad($id);
        $row = $result->fetch_assoc();
        
    //If the user IS the manager of the team
        if($row['managerId'] == $_SESSION['id'])
        {
           //print out team name and start of table
        echo '
            <h2>'. $row['teamName'] .'</h2>
            <table class="teamTable">
                <tr>
                    <th>Pos</th>
                    <th>Player</th>
                    <th>Team</th>
                    <th>TP</th>
                    <th>Trade</th>
                </tr>
            ';
            //get Goalkeepers Information and print out goalkeepers on pitch
                    $goalie = $this->database->_loadPlayerDetails($row['g1']);
                    $goalieRow = $goalie->fetch_assoc();
            echo'
                    <tr>
                        <td id="position'. $goalieRow['id'] .'">GK</td>
                        <td ><p class="playerName" id="'. $goalieRow['id'] .'">'. $goalieRow['webName'] .'</p></td>
                        <td title="'. $goalieRow['teamName'] .'">'. $goalieRow['shortName'] .'</td>
                        <td>'. $goalieRow['totalPoints'] .'</td>
                        <td><button class="tradeButton" id="trade'. $goalieRow['id'] .'" onclick="addToTradeOut(this.id)">Trade</button></td>
                    </tr>
            ';
                    //get Defenders Information and print out defence on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $defender = $this->database->_loadPlayerDetails($row[$id]);
                        $defenderRow = $defender->fetch_assoc();
                        if($defenderRow['position'] == "Defender")
                        {
                        echo'
                            <tr>
                                <td id="position'. $defenderRow['id'] .'">DF</td>
                                <td ><p class="playerName" id="'. $defenderRow['id'] .'">'. $defenderRow['webName'] .'</p></td>
                                <td title="'. $defenderRow['teamName'] .'">'. $defenderRow['shortName'] .'</td>
                                <td>'. $defenderRow['totalPoints'] .'</td>
                                <td><button class="tradeButton" id="trade'. $defenderRow['id'] .'" onclick="addToTradeOut(this.id)">Trade</button></td>
                            </tr>
                        ';
                        }
                    }

            
                    //get Midfielders Information and print out midfield on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $midfielder = $this->database->_loadPlayerDetails($row[$id]);
                        $midfielderRow = $midfielder->fetch_assoc();
                        if($midfielderRow['position'] == "Midfielder")
                        {
                        echo '
                            <tr>
                                <td id="position'. $midfielderRow['id'] .'">MF</td>
                                <td ><p class="playerName" id="'. $midfielderRow['id'] .'">'. $midfielderRow['webName'] .'</p></td>
                                <td title="'. $midfielderRow['teamName'] .'">'. $midfielderRow['shortName'] .'</td>
                                <td>'. $midfielderRow['totalPoints'] .'</td>
                                <td><button class="tradeButton" id="trade'. $midfielderRow['id'] .'" onclick="addToTradeOut(this.id)">Trade</button></td>
                            </tr>
                        ';
                        }
                    }

            
                    //get Midfielders Information and print out midfield on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $forward = $this->database->_loadPlayerDetails($row[$id]);
                        $forwardRow = $forward->fetch_assoc();
                        if($forwardRow['position'] == "Forward")
                        {
                        echo'
                            <tr>
                                <td id="position'. $forwardRow['id'] .'">FW</td>
                                <td ><p class="playerName" id="'. $forwardRow['id'] .'">'. $forwardRow['webName'] .'</p></td>
                                <td title="'. $forwardRow['teamName'] .'">'. $forwardRow['shortName'] .'</td>
                                <td>'. $forwardRow['totalPoints'] .'</td>
                                <td><button class="tradeButton" id="trade'. $forwardRow['id'] .'" onclick="addToTradeOut(this.id)">Trade</button></td>
                            </tr>
                        ';
                        }
                    }

                    //get sub goalie Information and print out on bench
                    $subGoalie = $this->database->_loadPlayerDetails($row['g2']);
                    $subGoalieRow = $subGoalie->fetch_assoc();

                    echo'
                       <tr>
                            <td id="position'. $subGoalieRow['id'] .'">GK</td>
                            <td ><p class="playerName" id="'. $subGoalieRow['id'] .'">'. $subGoalieRow['webName'] .'</p></td>
                            <td title="'. $subGoalieRow['teamName'] .'">'. $subGoalieRow['shortName'] .'</td>
                            <td>'. $subGoalieRow['totalPoints'] .'</td>
                            <td><button class="tradeButton" id="trade'. $subGoalieRow['id'] .'" onclick="addToTradeOut(this.id)">Trade</button></td>
                        </tr>
                    ';

                    //get subs Information and print out bench
                    for($i=1; $i<=3; $i++)
                    {
                        $id = "s" . $i;
                        $bench = $this->database->_loadPlayerDetails($row[$id]);
                        $benchRow = $bench->fetch_assoc();

                        //set shorthand for position
                        if($benchRow['position'] == "Goalkeeper")
                        {
                            $playerPosition = "GK";
                        }
                        else if($benchRow['position'] == "Defender")
                        {
                            $playerPosition = "DF";
                        }
                        else if($benchRow['position'] == "Midfielder")
                        {
                            $playerPosition = "MF";
                        }
                        else
                        {
                            $playerPosition = "FW";
                        }
                        
                    echo'
                        <tr>
                                <td id="position'. $benchRow['id'] .'">'. $playerPosition .'</td>
                                <td ><p class="playerName" id="'. $benchRow['id'] .'">'. $benchRow['webName'] .'</p></td>
                                <td title="'. $benchRow['teamName'] .'">'. $benchRow['shortName'] .'</td>
                                <td>'. $benchRow['totalPoints'] .'</td>
                                <td><button class="tradeButton" id="trade'. $benchRow['id'] .'" onclick="addToTradeOut(this.id)">Trade</button></td>
                            </tr>
                    ';
                    }       
            
                    //get subs Information and print out bench
                    for($i=1; $i<=3; $i++)
                    {
                        $id = "r" . $i;
                        $reserver = $this->database->_loadPlayerDetails($row[$id]);
                        $reserverRow = $reserver->fetch_assoc();
                        
                        //set shorthand for position
                        if($reserverRow['position'] == "Goalkeeper")
                        {
                            $playerPosition = "GK";
                        }
                        else if($reserverRow['position'] == "Defender")
                        {
                            $playerPosition = "DF";
                        }
                        else if($reserverRow['position'] == "Midfielder")
                        {
                            $playerPosition = "MF";
                        }
                        else
                        {
                            $playerPosition = "FW";
                        }

                    echo'
                        <tr>
                                <td id="position'. $reserverRow['id'] .'">'. $playerPosition .'</td>
                                <td ><p class="playerName" id="'. $reserverRow['id'] .'">'. $reserverRow['webName'] .'</p></td>
                                <td title="'. $reserverRow['teamName'] .'">'. $reserverRow['shortName'] .'</td>
                                <td>'. $reserverRow['totalPoints'] .'</td>
                                <td><button class="tradeButton" id="trade'. $reserverRow['id'] .'" onclick="addToTradeOut(this.id)">Trade</button></td>
                            </tr>
                    ';
                    }
            //close table
            echo '
                </table>
            ';

        }
    //If the user IS NOT manager of the team
        else
        {
            echo "not your team";
        }
    }
    
    public function loadOppositionSquadForTransfer()
    {
        $squadid = $_GET['squadid'];
        
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($squadid);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueid = $teamsLeagueRow['leagueid'];
        
    //get id of first opposition team (alphabeticly) in league
        $team1idResult = $this->database->_getFirstTeam($squadid, $leagueid);
        $team1idRow = $team1idResult->fetch_assoc();
        
        $team1id = $team1idRow['id'];
        
    //get list of all oppsotion teams and managers in league
        $allOppositionTeamsResult = $this->database->_loadOppositionTeamsAndManagers($leagueid, $squadid);
        
    //query database for details
        $result = $this->database->_loadSquad($team1id);
        $row = $result->fetch_assoc();
        
        //print out select of all teams in league
        
        echo '
                <select id="oppositionTeamSelect" onChange="changeOppositionTeam(this.value)">';
        
        while ($allOppositionTeamsRow = $allOppositionTeamsResult->fetch_assoc()) 
        {
            echo '
                  <option value="'. $allOppositionTeamsRow['id'] .'">'. $allOppositionTeamsRow['name'] .', '. $allOppositionTeamsRow['firstName'] .'  '. $allOppositionTeamsRow['lastName'] .'</option>
                  ';
        }
        
        
        echo'</select>';
        
           //print out team name and start of table <h2>'. $row['teamName'] .'</h2>
        echo '
            <table class="teamTable" id="oppositionTeamTable">
                <tr>
                    <th>Trade</th>
                    <th>Pos</th>
                    <th>Player</th>
                    <th>Team</th>
                    <th>TP</th>
                </tr>
            ';
            //get Goalkeepers Information and print out goalkeepers on pitch
                    $goalie = $this->database->_loadPlayerDetails($row['g1']);
                    $goalieRow = $goalie->fetch_assoc();
            echo'
                    <tr>
                        <td><button class="tradeButton" id="trade'. $goalieRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                        <td id="position'. $goalieRow['id'] .'">GK</td>
                        <td ><p class="playerName" id="'. $goalieRow['id'] .'">'. $goalieRow['webName'] .'</p></td>
                        <td title="'. $goalieRow['teamName'] .'">'. $goalieRow['shortName'] .'</td>
                        <td>'. $goalieRow['totalPoints'] .'</td>
                    </tr>
            ';
                    //get Defenders Information and print out defence on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $defender = $this->database->_loadPlayerDetails($row[$id]);
                        $defenderRow = $defender->fetch_assoc();
                        if($defenderRow['position'] == "Defender")
                        {
                        echo'
                            <tr>
                                <td><button class="tradeButton" id="trade'. $defenderRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                                <td id="position'. $defenderRow['id'] .'">DF</td>
                                <td ><p class="playerName" id="'. $defenderRow['id'] .'">'. $defenderRow['webName'] .'</p></td>
                                <td title="'. $defenderRow['teamName'] .'">'. $defenderRow['shortName'] .'</td>
                                <td>'. $defenderRow['totalPoints'] .'</td>
                            </tr>
                        ';
                        }
                    }

            
                    //get Midfielders Information and print out midfield on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $midfielder = $this->database->_loadPlayerDetails($row[$id]);
                        $midfielderRow = $midfielder->fetch_assoc();
                        if($midfielderRow['position'] == "Midfielder")
                        {
                        echo '
                            <tr>
                                <td><button class="tradeButton" id="trade'. $midfielderRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                                <td id="position'. $midfielderRow['id'] .'">MF</td>
                                <td ><p class="playerName" id="'. $midfielderRow['id'] .'">'. $midfielderRow['webName'] .'</p></td>
                                <td title="'. $midfielderRow['teamName'] .'">'. $midfielderRow['shortName'] .'</td>
                                <td>'. $midfielderRow['totalPoints'] .'</td>
                            </tr>
                        ';
                        }
                    }

            
                    //get Midfielders Information and print out midfield on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $forward = $this->database->_loadPlayerDetails($row[$id]);
                        $forwardRow = $forward->fetch_assoc();
                        if($forwardRow['position'] == "Forward")
                        {
                        echo'
                            <tr>
                                <td><button class="tradeButton" id="trade'. $forwardRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                                <td id="position'. $forwardRow['id'] .'">FW</td>
                                <td ><p class="playerName" id="'. $forwardRow['id'] .'">'. $forwardRow['webName'] .'</p></td>
                                <td title="'. $forwardRow['teamName'] .'">'. $forwardRow['shortName'] .'</td>
                                <td>'. $forwardRow['totalPoints'] .'</td>
                            </tr>
                        ';
                        }
                    }

                    //get sub goalie Information and print out on bench
                    $subGoalie = $this->database->_loadPlayerDetails($row['g2']);
                    $subGoalieRow = $subGoalie->fetch_assoc();

                    echo'
                       <tr>
                            <td><button class="tradeButton" id="trade'. $subGoalieRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                            <td id="position'. $subGoalieRow['id'] .'">GK</td>
                            <td ><p class="playerName" id="'. $subGoalieRow['id'] .'">'. $subGoalieRow['webName'] .'</p></td>
                            <td title="'. $subGoalieRow['teamName'] .'">'. $subGoalieRow['shortName'] .'</td>
                            <td>'. $subGoalieRow['totalPoints'] .'</td>
                        </tr>
                    ';

                    //get subs Information and print out bench
                    for($i=1; $i<=3; $i++)
                    {
                        $id = "s" . $i;
                        $bench = $this->database->_loadPlayerDetails($row[$id]);
                        $benchRow = $bench->fetch_assoc();

                        //set shorthand for position
                        if($benchRow['position'] == "Goalkeeper")
                        {
                            $playerPosition = "GK";
                        }
                        else if($benchRow['position'] == "Defender")
                        {
                            $playerPosition = "DF";
                        }
                        else if($benchRow['position'] == "Midfielder")
                        {
                            $playerPosition = "MF";
                        }
                        else
                        {
                            $playerPosition = "FW";
                        }
                        
                    echo'
                        <tr>
                                <td><button class="tradeButton" id="trade'. $benchRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                                <td id="position'. $benchRow['id'] .'">'. $playerPosition .'</td>
                                <td ><p class="playerName" id="'. $benchRow['id'] .'">'. $benchRow['webName'] .'</p></td>
                                <td title="'. $benchRow['teamName'] .'">'. $benchRow['shortName'] .'</td>
                                <td>'. $benchRow['totalPoints'] .'</td>
                            </tr>
                    ';
                    }       
            
                    //get subs Information and print out bench
                    for($i=1; $i<=3; $i++)
                    {
                        $id = "r" . $i;
                        $reserver = $this->database->_loadPlayerDetails($row[$id]);
                        $reserverRow = $reserver->fetch_assoc();
                        
                        //set shorthand for position
                        if($reserverRow['position'] == "Goalkeeper")
                        {
                            $playerPosition = "GK";
                        }
                        else if($reserverRow['position'] == "Defender")
                        {
                            $playerPosition = "DF";
                        }
                        else if($reserverRow['position'] == "Midfielder")
                        {
                            $playerPosition = "MF";
                        }
                        else
                        {
                            $playerPosition = "FW";
                        }

                    echo'
                            <tr>
                                <td><button class="tradeButton" id="trade'. $reserverRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                                <td id="position'. $reserverRow['id'] .'">'. $playerPosition .'</td>
                                <td ><p class="playerName" id="'. $reserverRow['id'] .'">'. $reserverRow['webName'] .'</p></td>
                                <td title="'. $reserverRow['teamName'] .'">'. $reserverRow['shortName'] .'</td>
                                <td>'. $reserverRow['totalPoints'] .'</td>
                            </tr>
                    ';
                    }
            //close table
            echo '
                </table>
            ';
    }
    
    public function loadOppositionSquadForTransferById()
    {
    //query database for details
        $result = $this->database->_loadSquad($_GET['teamId']);
        $row = $result->fetch_assoc();
        
            //get Goalkeepers Information and print out goalkeepers on pitch
                    $goalie = $this->database->_loadPlayerDetails($row['g1']);
                    $goalieRow = $goalie->fetch_assoc();
            echo'
                    <tr>
                        <td><button class="tradeButton" id="trade'. $goalieRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                        <td id="position'. $goalieRow['id'] .'">GK</td>
                        <td ><p class="playerName" id="'. $goalieRow['id'] .'">'. $goalieRow['webName'] .'</p></td>
                        <td title="'. $goalieRow['teamName'] .'">'. $goalieRow['shortName'] .'</td>
                        <td>'. $goalieRow['totalPoints'] .'</td>
                    </tr>
            ';
                    //get Defenders Information and print out defence on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $defender = $this->database->_loadPlayerDetails($row[$id]);
                        $defenderRow = $defender->fetch_assoc();
                        if($defenderRow['position'] == "Defender")
                        {
                        echo'
                            <tr>
                                <td><button class="tradeButton" id="trade'. $defenderRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                                <td id="position'. $defenderRow['id'] .'">DF</td>
                                <td ><p class="playerName" id="'. $defenderRow['id'] .'">'. $defenderRow['webName'] .'</p></td>
                                <td title="'. $defenderRow['teamName'] .'">'. $defenderRow['shortName'] .'</td>
                                <td>'. $defenderRow['totalPoints'] .'</td>
                            </tr>
                        ';
                        }
                    }

            
                    //get Midfielders Information and print out midfield on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $midfielder = $this->database->_loadPlayerDetails($row[$id]);
                        $midfielderRow = $midfielder->fetch_assoc();
                        if($midfielderRow['position'] == "Midfielder")
                        {
                        echo '
                            <tr>
                                <td><button class="tradeButton" id="trade'. $midfielderRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                                <td id="position'. $midfielderRow['id'] .'">MF</td>
                                <td ><p class="playerName" id="'. $midfielderRow['id'] .'">'. $midfielderRow['webName'] .'</p></td>
                                <td title="'. $midfielderRow['teamName'] .'">'. $midfielderRow['shortName'] .'</td>
                                <td>'. $midfielderRow['totalPoints'] .'</td>
                            </tr>
                        ';
                        }
                    }

            
                    //get Midfielders Information and print out midfield on pitch
                    for($i=1; $i<=10; $i++)
                    {
                        $id = "st" . $i;
                        $forward = $this->database->_loadPlayerDetails($row[$id]);
                        $forwardRow = $forward->fetch_assoc();
                        if($forwardRow['position'] == "Forward")
                        {
                        echo'
                            <tr>
                                <td><button class="tradeButton" id="trade'. $forwardRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                                <td id="position'. $forwardRow['id'] .'">FW</td>
                                <td ><p class="playerName" id="'. $forwardRow['id'] .'">'. $forwardRow['webName'] .'</p></td>
                                <td title="'. $forwardRow['teamName'] .'">'. $forwardRow['shortName'] .'</td>
                                <td>'. $forwardRow['totalPoints'] .'</td>
                            </tr>
                        ';
                        }
                    }

                    //get sub goalie Information and print out on bench
                    $subGoalie = $this->database->_loadPlayerDetails($row['g2']);
                    $subGoalieRow = $subGoalie->fetch_assoc();

                    echo'
                       <tr>
                            <td><button class="tradeButton" id="trade'. $subGoalieRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                            <td id="position'. $subGoalieRow['id'] .'">GK</td>
                            <td ><p class="playerName" id="'. $subGoalieRow['id'] .'">'. $subGoalieRow['webName'] .'</p></td>
                            <td title="'. $subGoalieRow['teamName'] .'">'. $subGoalieRow['shortName'] .'</td>
                            <td>'. $subGoalieRow['totalPoints'] .'</td>
                        </tr>
                    ';

                    //get subs Information and print out bench
                    for($i=1; $i<=3; $i++)
                    {
                        $id = "s" . $i;
                        $bench = $this->database->_loadPlayerDetails($row[$id]);
                        $benchRow = $bench->fetch_assoc();

                        //set shorthand for position
                        if($benchRow['position'] == "Goalkeeper")
                        {
                            $playerPosition = "GK";
                        }
                        else if($benchRow['position'] == "Defender")
                        {
                            $playerPosition = "DF";
                        }
                        else if($benchRow['position'] == "Midfielder")
                        {
                            $playerPosition = "MF";
                        }
                        else
                        {
                            $playerPosition = "FW";
                        }
                        
                    echo'
                        <tr>
                                <td><button class="tradeButton" id="trade'. $benchRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                                <td id="position'. $benchRow['id'] .'">'. $playerPosition .'</td>
                                <td ><p class="playerName" id="'. $benchRow['id'] .'">'. $benchRow['webName'] .'</p></td>
                                <td title="'. $benchRow['teamName'] .'">'. $benchRow['shortName'] .'</td>
                                <td>'. $benchRow['totalPoints'] .'</td>
                            </tr>
                    ';
                    }       
            
                    //get subs Information and print out bench
                    for($i=1; $i<=3; $i++)
                    {
                        $id = "r" . $i;
                        $reserver = $this->database->_loadPlayerDetails($row[$id]);
                        $reserverRow = $reserver->fetch_assoc();
                        
                        //set shorthand for position
                        if($reserverRow['position'] == "Goalkeeper")
                        {
                            $playerPosition = "GK";
                        }
                        else if($reserverRow['position'] == "Defender")
                        {
                            $playerPosition = "DF";
                        }
                        else if($reserverRow['position'] == "Midfielder")
                        {
                            $playerPosition = "MF";
                        }
                        else
                        {
                            $playerPosition = "FW";
                        }

                    echo'
                            <tr>
                                <td><button class="tradeButton" id="trade'. $reserverRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                                <td id="position'. $reserverRow['id'] .'">'. $playerPosition .'</td>
                                <td ><p class="playerName" id="'. $reserverRow['id'] .'">'. $reserverRow['webName'] .'</p></td>
                                <td title="'. $reserverRow['teamName'] .'">'. $reserverRow['shortName'] .'</td>
                                <td>'. $reserverRow['totalPoints'] .'</td>
                            </tr>
                    ';
                    }
    }
}
?>
