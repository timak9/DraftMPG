<?php

class league 
{
	var $database;
	// -- Function Name : __construct
    // -- Params :
    // -- Purpose : Starts the connection to the database.
    public function __construct()    
	{
        $this->database = new database();
	}
    //creates a new league and adds the team to league 
    public function createLeague()
    {
        //get league details from form
        $leaguename = $_GET['leaguename'];
        $password = $_GET['password'];
        $timeperpick = $_GET['timeperpick'];
        
        //admin will be user creating league
        $admin = $_SESSION['id'];
        
        //create league in database and return leagues id
        $result =  $this->database->_createLeague($leaguename, $password, $timeperpick,$admin);
        
        //get team name from form
        $teamname = $_GET['teamname'];
        
        //add team to database and league and get squad id
        $theSquadId = $this->database->_addNewTeam($result, $teamname, $admin);
        //add blank squad to database
        $this->database->_addBlankSquad($theSquadId);
        //add draft details and get draft id
        $draftId = $this->database->_addDraft($result, $theSquadId);
        //add to team to draft order
        $this->database->_addDraftOrder($draftId, $theSquadId);
    }
    
    //adds a new team to an existing league
    public function joinLeague()
    {
        //get league details from form
        $leagueid = $_GET['leagueid'];
        $password = $_GET['password'];
        $teamname = $_GET['teamname'];
        //manager will be user joining league
        $manager = $_SESSION['id'];
        
        //search for league
        $result =  $this->database->_findLeague($leagueid);
        $row = $result->fetch_assoc();
        
        //check if league wit this id exists
        $count=mysqli_num_rows($result);
        //if it exists
        if($count==1)
        {
            //if the id and password match
            if($row["password"] == $password)
            {
                //check league status if 0 - league hasnt started and they can be added
                if($row["status"] == 0)
                {
                    //query database to find out if the manager already has a team in the league
                    $result2 =  $this->database->_isPlayerInLeague($leagueid, $manager);
                    $row2 = $result2->fetch_assoc();
                    //count to see how many rows are returned
                    $count2=mysqli_num_rows($result2);
                    
                    //if they already have a team entered in league
                    if($count2==1)
                    {
                        echo 'You already have a team in this league';
                    }
                    //otherwise add team to database
                    else
                    {
                        //add team to database and get squad id
                        $theSquadId = $this->database->_addNewTeam($leagueid, $teamname, $manager);
                        //add blank squad to database
                        $this->database->_addBlankSquad($theSquadId);
                        
                        //get draftId
                        $draft = $this->database->getDraftDetails($theSquadId);
                        $draftRow = $draft->fetch_assoc();
                        $draftId = $draftRow['id'];
                        
                        //add to team to draft order
                        $this->database->_addDraftOrder($draftId, $theSquadId);
                    }
                }
                //otherwise the league has already started and they can't join
                else
                {
                    echo 'This league has already started';
                }
            }
            //if password is wrong
            else
            {
               echo 'Wrong Id and/or password combination';
            }
            
        }
        //if it doesn't
        else
        {
            echo 'League Id is invalid';
        }
    }
    
    
    //loads league table league
    public function loadLeagueTable()
    {   
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($_GET['squadid']);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueid = $teamsLeagueRow['leagueid'];
        
        //search for league
        $result =  $this->database->_loadLeagueTable($leagueid);
        
        //get first row
        $row = $result->fetch_assoc();
    
        //print out league name and table header
        echo '
            <h2>'. $row['leagueName'] .'</h2>
            <table>
                <tr>
                    <th title="Position">Pos</th>
                    <th title="Team Name" class="teamName">Team</th> 
                    <th title="Squad Value">Value</th>
                    <th title="Games Played">GP</th>
                    <th title="Wins">W</th>
                    <th title="Loses">L</th>
                    <th title="Draws">D</th>
                    <th title="Fantasy Points">FP</th>
                    <th title="League Points">LP</th>
                </tr>
        ';
        
        //print out the first row
            //find out if this team in this position is the logged in users team and set the rows class accordingly
            if($row['manager'] == $_SESSION['id'])
            {
                echo '<tr class="usersTeam">';
            }
            else
            {
                echo '<tr>';
            }
        //continue printing out the first row
        //add a decimal point to the value of player stored
            //get position for the point tobe put in
            $pos = strlen($row['squadvalue']) - 1;
            //add point to string in position
            $value = substr_replace($row['squadvalue'], '.', $pos, 0);
        echo '            
                    <td>1</td>
                    <td><a href="index.php?viewsquad='. $row['id'] .'&squadid='. $_GET['squadid'] .'">'. $row['name'] .'</a></td> 
                    <td>£'. $value .'0</td>
                    <td>'. $row['gamesPlayed'] .'</td>
                    <td>'. $row['w'] .'</td>
                    <td>'. $row['l'] .'</td>
                    <td>'. $row['d'] .'</td>
                    <td>'. $row['fantasyPoints'] .'</td>
                    <td>'. $row['totalPoints'] .'</td>
                 </tr>
                ';
        //print out the rest of the rows 
            //set var to hold league position number
            $position = 2;
            while ($row = $result->fetch_assoc()) 
            {
                
                //find out if this team in this position is the logged in users team and set the rows class accordingly
                if($row['manager'] == $_SESSION['id'])
                {
                    echo '<tr class="usersTeam">';
                }
                else
                {
                    echo '<tr>';
                }
                //add a decimal point to the value of player stored
                    //get position for the point tobe put in
                    $pos = strlen($row['squadvalue']) - 1;
                    //add point to string in position
                    $value = substr_replace($row['squadvalue'], '.', $pos, 0);
            echo'
                    <td>'. $position .'</td>
                    <td><a href="index.php?viewsquad='. $row['id'] .'&squadid='. $_GET['squadid'] .'">'. $row['name'] .'</a></td> 
                    <td>£'. $value .'0</td>
                    <td>'. $row['gamesPlayed'] .'</td>
                    <td>'. $row['w'] .'</td>
                    <td>'. $row['l'] .'</td>
                    <td>'. $row['d'] .'</td>
                    <td>'. $row['fantasyPoints'] .'</td>
                    <td>'. $row['totalPoints'] .'</td>
                 </tr>
            ';
                //add 1 to league position holder
                $position++;
            }
                    
        echo '
            </table>
        
        ';
    }
    
    //gets the fixtures/results for a league
    function getFixtures($date)
    {
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($_GET['squadid']);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueid = $teamsLeagueRow['leagueid'];
        
        //get gameweek based on date
        $result =  $this->database->_getGameweek($date);
        
        //get first row
        $row = $result->fetch_assoc();
        
        //set current gameweek
        $gameweek = $row['id'];
        //get previous gameweek number
        $lastWeek = $row['id'] - 1;
        //get next gameweek number
        $nextWeek = $row['id'] + 1;
    
        //print out  fixtures details
        echo'
            <div id="fixturesWrapper">
                <div id="gwSelector">
                <p class="gwButtonL" id="'. $lastWeek.'">Previous</p>
                <p id="gwNumber">Gameweek '. $gameweek .'</p>
                <p class="gwButtonR" id="'. $nextWeek.'">Next</p>
            </div>
            <div id="changeFixturesWrapper">
        ';
        
        
        //search for league fixtures for selected gameweek excluding games vs league average
        $result2 =  $this->database->_getGWFixtures($leagueid, $gameweek);
        
        $count=mysqli_num_rows($result2);
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
            
            //print out  fixtures details for games excluding games vs league average
            while($row2 = $result2->fetch_assoc())
            {
                //add the two teams to number of teams in league
                $numTeams = $numTeams + 2;
                
            echo'
                    <div class="fixture">';
                //if game status is 1 then gameweek has started so display score
                    if($row2['status'] != 0)
                    {
                        //get the teams gameweek gameweek squads current points total for a said gameweek
                        //var to hold points totals
                        $team1TotalPoints = 0; $team2TotalPoints = 0;

                        //get team1 total points
                        $pointsResult = $this->database-> _getGameweekSquad($row2['team1'], $gameweek);
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

                                $goalie = $this->database->_getSinglePlayerGameweekPoints(${$goaliePos}, $gameweek);
                                $goalieRow = $goalie->fetch_assoc();
                                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gameweek);
                                $starterRow = $starter->fetch_assoc();

                                $goaliePoints = $goalieRow['total'];
                                $starterPoints = $starterRow['total'];


                                $team1TotalPoints = $team1TotalPoints + $goaliePoints;
                                $team1TotalPoints = $team1TotalPoints + $starterPoints;
                            }
                            else
                            {
                                $starterPos = "st".$i;

                                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gameweek);
                                $starterRow = $starter->fetch_assoc();

                                $starterPoints = $starterRow['total'];

                                $team1TotalPoints = $team1TotalPoints + $starterPoints;
                            }
                        }
                        
                        //add team 1 points to league total points
                        $totalLeaguePoints = $totalLeaguePoints + $team1TotalPoints;
                        
                        //get team2 total points
                        $pointsResult = $this->database-> _getGameweekSquad($row2['team2'], $gameweek);
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

                                $goalie = $this->database->_getSinglePlayerGameweekPoints(${$goaliePos}, $gameweek);
                                $goalieRow = $goalie->fetch_assoc();
                                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gameweek);
                                $starterRow = $starter->fetch_assoc();

                                $goaliePoints = $goalieRow['total'];
                                $starterPoints = $starterRow['total'];


                                $team2TotalPoints = $team2TotalPoints + $goaliePoints;
                                $team2TotalPoints = $team2TotalPoints + $starterPoints;
                            }
                            else
                            {
                                $starterPos = "st".$i;

                                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gameweek);
                                $starterRow = $starter->fetch_assoc();

                                $starterPoints = $starterRow['total'];

                                $team2TotalPoints = $team2TotalPoints + $starterPoints;
                            }
                        }
                        
                        //add team 1 points to league total points
                        $totalLeaguePoints = $totalLeaguePoints + $team2TotalPoints;
                        
                        
                        echo'
                            <span class="fixtureText"> <a href="index.php?viewsquad='. $row2['team1'] .'&squadid='. $_GET['squadid'] .'&gameweek='. $gameweek .'">'. $row2['team1name'] .'</a></span> '. $team1TotalPoints .' 
                            - 
                            '. $team2TotalPoints .' <span class="fixtureText"><a href="index.php?viewsquad='. $row2['team2'] .'&squadid='. $_GET['squadid'] .'&gameweek='. $gameweek .'">'. $row2['team2name'] .'</a></span></div>
                        ';
                    }
                //otherwise don't display score
                    else
                    {
                        echo'
                            <span class="fixtureText">'. $row2['team1name'] .'</span>
                            <a href="">Vs</a> 
                            <span class="fixtureText">'. $row2['team2name'] .'</span></div>
                        ';
                    }
            }

            //search for league fixtures for selected gameweek for games vs league average
            $resultVsLA =  $this->database->_getGWFixturesVsLeagueAverage($leagueid, $row['id']);
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
                    if($rowVsLA['status'] != 0)
                    {
                        //get team1 total points
                        $pointsResult = $this->database-> _getGameweekSquad($rowVsLA['team2'], $gameweek);
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

                                $goalie = $this->database->_getSinglePlayerGameweekPoints(${$goaliePos}, $gameweek);
                                $goalieRow = $goalie->fetch_assoc();
                                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gameweek);
                                $starterRow = $starter->fetch_assoc();

                                $goaliePoints = $goalieRow['total'];
                                $starterPoints = $starterRow['total'];

                                $teamVsLaTotalPoints = $teamVsLaTotalPoints + $goaliePoints;
                                $teamVsLaTotalPoints = $teamVsLaTotalPoints + $starterPoints;
                            }
                            else
                            {
                                $starterPos = "st".$i;

                                $starter = $this->database->_getSinglePlayerGameweekPoints(${$starterPos}, $gameweek);
                                $starterRow = $starter->fetch_assoc();

                                $starterPoints = $starterRow['total'];

                                $teamVsLaTotalPoints = $teamVsLaTotalPoints + $starterPoints;
                            }
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
                            '. $teamVsLaTotalPoints .' <span class="fixtureText"><a href="index.php?viewsquad='. $rowVsLA['team2'] .'&squadid='. $_GET['squadid'] .'&gameweek='. $gameweek .'">'. $rowVsLA['team2name'] .'</a></span></div>
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
            }

            echo'
                </div>
            '; 
        }
    }
    
    //gets the fixtures/results for a league
    function getGWFixtures()
    {
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($_GET['squadid']);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueid = $teamsLeagueRow['leagueid'];
        
        $gw = $_GET['gw'];
        
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
                    if($rowVsLA['status'] != 0)
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
    
    //gets all the free agents in the league
    function getFreeAgents()
    {
        //set number of records per page
        $num_rec_per_page=16;
        
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($_GET['squadid']);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        
        //If the user IS the manager of the team then set the league to the teams league and load up the free agents, otherwise dont
        if($teamsLeagueRow['managerid'] == $_SESSION['id'])
        {
            $leagueid = $teamsLeagueRow['leagueid'];
        
        
            //set position string
            if (isset($_GET["position"]) && $_GET["position"] != "Any") 
            { 
                $positionString = "and position = '". $_GET["position"] ."'"; 
            } 
            else 
            { 
                $positionString = ""; 
            };

            //set team string
            if (isset($_GET["premTeam"]) && $_GET["premTeam"] != "Any") 
            { 
                $premTeamString = "and teamId = '". $_GET["premTeam"] ."'"; 
            } 
            else 
            { 
                $premTeamString = ""; 
            };

            //set name string
            if (isset($_GET["name"]) && $_GET["name"] != "Any") 
            { 
                $nameString = "and webName LIKE '%". $_GET["name"] ."%'"; 
            } 
            else 
            { 
                $nameString = ""; 
            };

            //set order by string
            if (isset($_GET["orderBy"])) 
            { 
                $orderByString = "order by ". $_GET["orderBy"] .", totalPoints desc";
            } 
            else 
            { 
                $orderByString = "order by totalPoints desc, value desc";
            };

            //get pagination details
            if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
            $start_from = ($page-1) * $num_rec_per_page; 


            //get all players that ARE in teams in the league
            $result =  $this->database->_getLeaguePlayers($leagueid);

            //get string of all players id currently in league to be passed
            $notThesePlayers = "";

            //get first player
            $row = $result->fetch_assoc();
            $notThesePlayers = $notThesePlayers. "player.id != '". $row['id'] ."'";

            //get the rest of the players
            while($row = $result->fetch_assoc())
                {
                    $notThesePlayers = $notThesePlayers. "and player.id != '". $row['id'] ."'";
                }   

            //get all free agents in the league, option to filter by position
            $freeAgentResult =  $this->database->_getLeagueFreeAgents($notThesePlayers, $positionString, $premTeamString, $nameString, $orderByString, $start_from, $num_rec_per_page);

            //loop through these players and print out
            while($freeAgentRow = $freeAgentResult->fetch_assoc())
                {
                    //set shorthand for position
                    if($freeAgentRow['position'] == "Goalkeeper")
                    {
                        $playerPosition = "GK";
                    }
                    else if($freeAgentRow['position'] == "Defender")
                    {
                        $playerPosition = "DF";
                    }
                    else if($freeAgentRow['position'] == "Midfielder")
                    {
                        $playerPosition = "MF";
                    }
                    else
                    {
                        $playerPosition = "FW";
                    }
                    echo '
                        <tr>
                            <td><button class="tradeButton" id="trade'. $freeAgentRow['id'] .'" onclick="addToTradeIn(this.id)">Trade</button></td>
                            <td id="position'. $freeAgentRow['id'] .'">'. $playerPosition .'</td>
                            <td><p class="playerName" id="'. $freeAgentRow['id'] .'">'. $freeAgentRow['webName'] .'</p></td>
                            <td title="'. $freeAgentRow['teamName'] .'">'. $freeAgentRow['shortName'] .'</td>
                            <td>'. $freeAgentRow['totalPoints'] .'</td>
                        </tr>
                    ';
                }  

            $rs_result = $this->database->_getLeagueFreeAgents($notThesePlayers, $positionString, $premTeamString, $nameString, $orderByString, 0, 5000); 
            $total_records = $count=mysqli_num_rows($rs_result);  //count number of records
            $total_pages = ceil($total_records / $num_rec_per_page); 

            $pageDown = $page - 1;
            $pageUp = $page + 1;

            //print out pagination selector details
                echo'    
                    <tr>
                        <td colspan="5">
                            <div id="pageNumbers">';
                                //dont display if on page one
                                if($page != 1)
                                {
                                echo '
                                    <button class="pageButton" id="1" onclick="changePage(this.id)"><<</button>
                                    <button class="pageButton" id="'. $pageDown .'" onclick="changePage(this.id)"><</button>
                                    ';
                                 }
                                echo'<div>'. $page .' of '. $total_pages .'</div>';
                                if($page != $total_pages)
                                {
                                echo '
                                    <button class="pageButton" id="'. $pageUp .'" onclick="changePage(this.id)">></button>
                                    <button class="pageButton" id="'. $total_pages .'" onclick="changePage(this.id)">>></button>
                                    ';
                                 }

                            echo'</div>
                        </td>
                    </tr>
                ';
        }
        else
        {
            echo "Not your League";
        }
    }
    
    //generates the fixture list for fantasy league
    function generateFixtures()
    {
        $gameWeeks = 38;
        
        //get league id
        $teamsLeagueResult = $this->database->_getTeamsLeague($_GET['squadid']);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueId = $teamsLeagueRow['leagueid'];
        
        //get gameweek based on date
        $date = date('y-m-d');
        $gwresult =  $this->database->_getGameweek($date);
        //get first row
        $gwrow = $gwresult->fetch_assoc();
        //set current gameweek
        $todaysGameweek = $gwrow['id'];
        
        //check if current gameweek has started yet, if it has set first gameweek fixtures to next gameweek
        if($gwrow['status'] == 0)
        {
            $currentGameweek = $todaysGameweek;
        }
        else
        {
            $currentGameweek = $todaysGameweek + 1;
        }
        
        //array to hold team ids
        $teams = array();
        
        //get all teams in league
        $result =  $this->database->_loadLeagueTableNoValue($leagueId);
        
        //add teams to arrays
        while($row = $result->fetch_assoc())
        {
            array_push($teams, $row['id']);
        } 
        
        //check if theres an odd number of teams, if there is add a "league average" team to beginning of array
        if(count($teams) % 2 != 0)
        {
            array_unshift($teams, -1);
        }
        
        //loop through all remaining gameweeks and generate ficture list for each week
        for($i=$currentGameweek; $i<=$gameWeeks; $i++)
        {   
            //var holder for 2nd team
            $awayTeam = count($teams)-1;
            
            //loop through first half of team array
            for($a=0; $a<count($teams)/2; $a++)
            {
                //if first team is -1 its the league average team.. always have league average as home team
                if($teams[$a] == -1)
                {
                    //add fixture write to database
                    $this->database->_addNewFantasyFixture($i, $leagueId, $teams[$a], $teams[$awayTeam]);
                }
                //alternate home and away teams every second week
                else if($i % 2 != 0)
                {
                    //add fixture write to database
                    $this->database->_addNewFantasyFixture($i, $leagueId, $teams[$a], $teams[$awayTeam]);
                }
                else
                {
                    //add fixture write to database
                    $this->database->_addNewFantasyFixture($i, $leagueId, $teams[$awayTeam], $teams[$a]);
                }
                
                //
                if($awayTeam == count($teams)/2)
                {
                    //reset awayTeam variable
                    $awayTeam = count($teams)-1;
                }
                else
                {
                    $awayTeam--;
                }
            }
            
            //get last element in array and put in temp holder
            $lastEl = $teams[count($teams)-1];
            //get the 1st element in array and put in temp holder
            $firstEl = $teams[0];
            //remove this element for now
            $teams = array_diff($teams, array($firstEl));
                
            //remove last element from array
            array_pop($teams);
            //insert previous last element to beginning of array
            array_unshift($teams , $lastEl);
            
            //reinstate the original first element
            array_unshift($teams , $firstEl);
        }
    }
    
}
?>
