<?php

class draft 
{
	var $database;
	// -- Function Name : __construct
    // -- Params :
    // -- Purpose : Starts the connection to the database.
    public function __construct()    
	{
        $this->database = new database();
	}
    
    //prints out league and team name
    public function getTeamLeagueName()
    {
        //to get managers team and league details
        $teamResult = $this->database->_getTeamLeagueDetails($_GET['squadid']);
        $teamRow = $teamResult->fetch_assoc();
        
        if($_SESSION['id'] == $teamRow['leagueadmin'])
        {
            $isAdmin = 'Yes';
        }
        else
        {
            $isAdmin = 'No';
        }
        
        //put details into array
 $array=array("teamName"=>$teamRow['teamName'],"leagueName"=>$teamRow['leagueName'],"selectionTime"=>$teamRow['selectionTime'],"draftStatus"=>$teamRow['draftStatus'],"leagueAdmin"=>$isAdmin);
        
        echo json_encode($array);
    }
    
    //gets the order of teams selecting during the draft
    public function getDraftOrder()
    {
        $squadId = $_GET['squadid'];
        //to get team league detail
        $teamsLeagueResult = $this->database->_getTeamsLeague($squadId);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueid = $teamsLeagueRow['leagueid'];
        
        //get draft order
        $draftOrderResult = $this->database->getDraftOrder($leagueid);
        //count number of rows returned
        $count=mysqli_num_rows($draftOrderResult);
        
        //array to hold details being returned
        $draftOrderArray = array();
        //placeHolder variablefor what pick number it is
        $pickNumber = 1;
        
        //loop through results and add to array
        while($draftOrderRow = $draftOrderResult->fetch_assoc())
        {
            $draftOrderArray[$pickNumber] = $draftOrderRow['teamName'];
            $pickNumber++;
        }
        
        echo json_encode($draftOrderArray);
    }
    
    //starts draft
    public function startDraft()
    {
        $squadId = $_GET['squadid'];

        //set draft id
        $draft = $this->database->getDraftDetails($squadId);
        $draftRow = $draft->fetch_assoc();
        $draftId = $draftRow['id'];
        $leagueId = $draftRow['leagueId'];
        
        $this->database->_startDraft($draftId);
        $this->database->_startLeague($leagueId);   
    }
    
    //gets the order of teams selecting during the draft
    public function getDrafthistory()
    {
        $squadId = $_GET['squadid'];

        //set draft id
        $draft = $this->database->getDraftDetails($squadId);
        $draftRow = $draft->fetch_assoc();
        $draftId = $draftRow['id'];
        
        $history = $this->database->getDraftPicks($draftId);
        
        //array to hold details being returned
        $draftHistoryArray = array();
        //placeHolder variablefor what pick number it is
        $pickNumber = 1;
        
        
        //loop through results and add to array
        while($historyRow = $history->fetch_assoc())
        {
            array_push($draftHistoryArray,array($historyRow['teamName'], $historyRow['playerName']));
            $pickNumber++;
        }
        
        echo json_encode($draftHistoryArray);
    }
    
    
    //move draft pick to next pick
    public function moveToNextPick($leagueId, $squadId)
    {
        //get draft details
        $draft = $this->database->getDraftDetails($squadId);
        $draftRow = $draft->fetch_assoc();
        $draftId = $draftRow['id'];       
        $lastPickNo = $draftRow['pickNumber'];     
        
        //get draft order
        $draftOrderResult = $this->database->getDraftOrder($leagueId);
        //count number of rows returned
        $totalPicks=mysqli_num_rows($draftOrderResult);
        
        //array to hold details being returned
        $draftOrderArray = array();
        //placeHolder variablefor what pick number it is
        $pickNumber = 1;
        
        //loop through results and add to array
        while($draftOrderRow = $draftOrderResult->fetch_assoc())
        {
            $draftOrderArray[$pickNumber] = $draftOrderRow['teamId'];
            $pickNumber++;
        }
        
        //get total number of picks in draft
        $finalPick = $totalPicks * 18;
        
        //place holder for number of pick
        $pickNumber = 1;
        
        //loop through rounds of picks
        for($round=1; $round<=18; $round++)
            {
                //for odd numbered round picks go from start of array to end
                if($round % 2 != 0)
                {
                    for($pick=1; $pick<=$totalPicks; $pick++)
                    {
                        //if last pick was final pick then end draft
                        if($lastPickNo == $finalPick)
                        {
                            //end draft
                            $this->database->_endDraft($draftId);
                        }
                        else
                        {
                            //if pick number is the next number up from last pick then update database
                            if($pickNumber == $lastPickNo+1)
                            {
                                $this->database->updateDraftPick($draftId, $draftOrderArray[$pick], $pickNumber);
                            }
                        }
                        $pickNumber++;
                    }
                }
                //for even numbered round picks go from end of array to start
                else
                {
                    for($pick=$totalPicks; $pick>=1; $pick--)
                    {
                        //if last pick was final pick then end draft
                        if($lastPickNo == $finalPick)
                        {
                            //end draft
                            $this->database->_endDraft($draftId);
                        }
                        else
                        {
                            //if pick number is the next number up from last pick then update database
                            if($pickNumber == $lastPickNo+1)
                            {
                                $this->database->updateDraftPick($draftId, $draftOrderArray[$pick], $pickNumber);
                            }
                        }
                        $pickNumber++;
                    }
                }
            }
    }
    
    //get count of how many players current squad has per position
    public function countPlayersPerPos()
    {
        $currentSquadResult = $this->database->_loadSquad($_GET['squadid']);
        $teamRow = $currentSquadResult->fetch_assoc();
        
        //place holders for count of players required per position
        $gks=2; $dfs=6; $mfs=6; $fws=4;
        
        //count goalkeepers
        for($i=1; $i<=2; $i++)
        {
            $id = "g" . $i;
            
            if($teamRow[$id] != 0)
            {
                $gks--;
            }
        }
        
        //loop through starting players and count positions
        for($i=1; $i<=10; $i++)
        {
            $id = "st" . $i;
            
            if($teamRow[$id] != 0)
            {
                //get players details
                $player = $this->database->_getSinglePlayerDetails($teamRow[$id], 1);
                $playerRow = $player->fetch_assoc();
                
                //check what players position is and adjust positions count accordingly
                if($playerRow['position'] == 'Defender')
                {
                    $dfs--;
                }
                else if ($playerRow['position'] == 'Midfielder')
                {
                    $mfs--;
                }
                else if ($playerRow['position'] == 'Forward')
                {
                    $fws--;
                }
            }
        }
        
        //loop through subs and count positions
        for($i=1; $i<=3; $i++)
        {
            $id = "s" . $i;
            
            if($teamRow[$id] != 0)
            {
                //get players details
                $player = $this->database->_getSinglePlayerDetails($teamRow[$id], 1);
                $playerRow = $player->fetch_assoc();
                
                //check what players position is and adjust positions count accordingly
                if($playerRow['position'] == 'Defender')
                {
                    $dfs--;
                }
                else if ($playerRow['position'] == 'Midfielder')
                {
                    $mfs--;
                }
                else
                {
                    $fws--;
                }
            }
        }
        
        //loop through reserves and count positions
        for($i=1; $i<=3; $i++)
        {
            $id = "r" . $i;
            
            if($teamRow[$id] != 0)
            {
                //get players details
                $player = $this->database->_getSinglePlayerDetails($teamRow[$id], 1);
                $playerRow = $player->fetch_assoc();
                
                //check what players position is and adjust positions count accordingly
                if($playerRow['position'] == 'Defender')
                {
                    $dfs--;
                }
                else if ($playerRow['position'] == 'Midfielder')
                {
                    $mfs--;
                }
                else
                {
                    $fws--;
                }
            }
        }
        
        //put position counts into array
        $posCountArray=array("Goalkeepers"=>$gks,"Defenders"=>$dfs,"Midfielders"=>$mfs,"Forwards"=>$fws);
        
        echo json_encode($posCountArray);
    }
    
    public function addPlayerToSquad()
    {
        $passedInPickNumber = $_GET['picknumber'];

        //set ids
        $playerId = $_GET['playerid']; $squadId = $_GET['squadid'];
        //get draft details
        $draft = $this->database->getDraftDetails($squadId);
        $draftRow = $draft->fetch_assoc();
        $draftId = $draftRow['id'];
        $pickTeam = $draftRow['teamsPick'];
        $pickNo = $draftRow['pickNumber'];
        
        if($pickNo == $passedInPickNumber)
        {
            //to get team league detail
            $teamsLeagueResult = $this->database->_getTeamsLeague($squadId);
            $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
            //set the League id to the league id of the managers team
            $leagueId = $teamsLeagueRow['leagueid'];
            //set draft id
            $draft = $this->database->getDraftDetails($squadId);
            $draftRow = $draft->fetch_assoc();
            $draftId = $draftRow['id'];
            //get players details
            $player = $this->database->_getSinglePlayerDetails($playerId, 1);
            $playerRow = $player->fetch_assoc();

            //get players position
            $playerPosition = $playerRow['position'];

            //get number of players in per position in squad
            $currentSquadResult = $this->database->_loadSquad($squadId);
            $teamRow = $currentSquadResult->fetch_assoc();
            //place holders for count of players required per position
            $gks=0; $dfs=0; $mfs=0; $fws=0;

            //count goalkeepers
            for($i=1; $i<=2; $i++)
            {
                $id = "g" . $i;

                if($teamRow[$id] != 0)
                {
                    $gks++;
                }
            }

            //loop through starting players and count positions
            for($i=1; $i<=10; $i++)
            {
                $id = "st" . $i;

                if($teamRow[$id] != 0)
                {
                    //get players details
                    $player = $this->database->_getSinglePlayerDetails($teamRow[$id], 1);
                    $playerRow = $player->fetch_assoc();

                    //check what players position is and adjust positions count accordingly
                    if($playerRow['position'] == 'Defender')
                    {
                        $dfs++;
                    }
                    else if ($playerRow['position'] == 'Midfielder')
                    {
                        $mfs++;
                    }
                    else if ($playerRow['position'] == 'Forward')
                    {
                        $fws++;
                    }
                }
            }

            //loop through subs and count positions
            for($i=1; $i<=3; $i++)
            {
                $id = "s" . $i;

                if($teamRow[$id] != 0)
                {
                    //get players details
                    $player = $this->database->_getSinglePlayerDetails($teamRow[$id], 1);
                    $playerRow = $player->fetch_assoc();

                    //check what players position is and adjust positions count accordingly
                    if($playerRow['position'] == 'Defender')
                    {
                        $dfs++;
                    }
                    else if ($playerRow['position'] == 'Midfielder')
                    {
                        $mfs++;
                    }
                    else
                    {
                        $fws++;
                    }
                }
            }

            //loop through reserves and count positions
            for($i=1; $i<=3; $i++)
            {
                $id = "r" . $i;

                if($teamRow[$id] != 0)
                {
                    //get players details
                    $player = $this->database->_getSinglePlayerDetails($teamRow[$id], 1);
                    $playerRow = $player->fetch_assoc();

                    //check what players position is and adjust positions count accordingly
                    if($playerRow['position'] == 'Defender')
                    {
                        $dfs++;
                    }
                    else if ($playerRow['position'] == 'Midfielder')
                    {
                        $mfs++;
                    }
                    else
                    {
                        $fws++;
                    }
                }
            }

            //placeHolder to hold squad position player will be added to
            $squadPosition = "";

            //add player to current squad based on position and how many players are already in squad by position
            if($playerPosition == "Goalkeeper")
            {
                if($gks == 0)
                {
                    $squadPosition = "g1";
                }
                else
                {
                    $squadPosition = "g2";
                }
            }
            else if($playerPosition == "Defender")
            {
                if($dfs == 0)
                {
                    $squadPosition = "st1";
                }
                else if($dfs == 1)
                {
                    $squadPosition = "st2";
                }
                else if($dfs == 2)
                {
                    $squadPosition = "st3";
                }
                else if($dfs == 3)
                {
                    $squadPosition = "st4";
                }
                else if($dfs == 4)
                {
                    $squadPosition = "s1";
                }
                else if($dfs == 5)
                {
                    $squadPosition = "r1";
                }
            }
            else if($playerPosition == "Midfielder")
            {
                if($mfs == 0)
                {
                    $squadPosition = "st5";
                }
                else if($mfs == 1)
                {
                    $squadPosition = "st6";
                }
                else if($mfs == 2)
                {
                    $squadPosition = "st7";
                }
                else if($mfs == 3)
                {
                    $squadPosition = "st8";
                }
                else if($mfs == 4)
                {
                    $squadPosition = "s2";
                }
                else if($mfs == 5)
                {
                    $squadPosition = "r2";
                }
            }
            else
            {
                if($fws == 0)
                {
                    $squadPosition = "st9";
                }
                else if($fws == 1)
                {
                    $squadPosition = "st10";
                }
                else if($fws == 2)
                {
                    $squadPosition = "s3";
                }
                else if($fws == 3)
                {
                    $squadPosition = "r3";
                }
            }
            //add player to squad list in database
            $this->database->addDraftSelection($squadId, $playerId, $squadPosition);
            //add selection to draft history in dp
            $this->database->addDraftSelectionHistory($squadId, $playerId, $draftId);
            //move on to next draft pick
            $this->moveToNextPick($leagueId, $squadId);
        }
        else
        {
            //do nothing
        }
    }
    
    //getdraft pick details
    public function checkDraftPick()
    {
        $squadId = $_GET['squadid'];
        //get draft details
        $draft = $this->database->getDraftDetails($squadId);
        $draftRow = $draft->fetch_assoc();
        $draftId = $draftRow['id'];     
        $pickTeam = $draftRow['teamsPick'];     
        $pickNo = $draftRow['pickNumber'];      
        $status = $draftRow['status'];     

        $draftPickArray=array("pickNo"=>$pickNo,"pickTeam"=>$pickTeam,"status"=>$status);
        
        echo json_encode($draftPickArray);
    }
    
    //selects random player when a players draft time runs out
    public function selectRandomPlayer()
    {
        //set number of players to pick from
        $num_rec_per_page=15;
        $start_from = 0;
        
        $passedInPickNumber = $_GET['picknumber'];
        
        //to get managers team and league details
        $squadId = $_GET['squadid'];
        $teamsLeagueResult = $this->database->_getTeamsLeague($squadId);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        $leagueid = $teamsLeagueRow['leagueid'];
        
        //get draft details
        $draft = $this->database->getDraftDetails($squadId);
        $draftRow = $draft->fetch_assoc();
        $draftId = $draftRow['id'];     
        $pickTeam = $draftRow['teamsPick'];     
        $pickNo = $draftRow['pickNumber']; 
        
        if($pickNo == $passedInPickNumber)
        {
            //move to next pick in draft
            $this -> moveToNextPick($leagueid, $squadId);

            //get number of players in per position in squad
            $currentSquadResult = $this->database->_loadSquad($squadId);
            $teamRow = $currentSquadResult->fetch_assoc();
            //place holders for count of players required per position
            $gks=0; $dfs=0; $mfs=0; $fws=0;

            //count goalkeepers
            for($i=1; $i<=2; $i++)
            {
                $id = "g" . $i;

                if($teamRow[$id] != 0)
                {
                    $gks++;
                }
            }

            //loop through starting players and count positions
            for($i=1; $i<=10; $i++)
            {
                $id = "st" . $i;

                if($teamRow[$id] != 0)
                {
                    //get players details
                    $player = $this->database->_getSinglePlayerDetails($teamRow[$id], 1);
                    $playerRow = $player->fetch_assoc();

                    //check what players position is and adjust positions count accordingly
                    if($playerRow['position'] == 'Defender')
                    {
                        $dfs++;
                    }
                    else if ($playerRow['position'] == 'Midfielder')
                    {
                        $mfs++;
                    }
                    else if ($playerRow['position'] == 'Forward')
                    {
                        $fws++;
                    }
                }
            }

            //loop through subs and count positions
            for($i=1; $i<=3; $i++)
            {
                $id = "s" . $i;

                if($teamRow[$id] != 0)
                {
                    //get players details
                    $player = $this->database->_getSinglePlayerDetails($teamRow[$id], 1);
                    $playerRow = $player->fetch_assoc();

                    //check what players position is and adjust positions count accordingly
                    if($playerRow['position'] == 'Defender')
                    {
                        $dfs++;
                    }
                    else if ($playerRow['position'] == 'Midfielder')
                    {
                        $mfs++;
                    }
                    else
                    {
                        $fws++;
                    }
                }
            }

            //loop through reserves and count positions
            for($i=1; $i<=3; $i++)
            {
                $id = "r" . $i;

                if($teamRow[$id] != 0)
                {
                    //get players details
                    $player = $this->database->_getSinglePlayerDetails($teamRow[$id], 1);
                    $playerRow = $player->fetch_assoc();

                    //check what players position is and adjust positions count accordingly
                    if($playerRow['position'] == 'Defender')
                    {
                        $dfs++;
                    }
                    else if ($playerRow['position'] == 'Midfielder')
                    {
                        $mfs++;
                    }
                    else
                    {
                        $fws++;
                    }
                }
            }

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

            //set search options
            $orderByString = "order by value desc, totalPoints desc";
            $premTeamString = "";
            $nameString = ""; 
            $positionString = ""; 

            //array to hold potential positions
            $positionsAvailable = array();

            //if positions aren't full add to positions available array
            if($gks != 2)
            {
                array_push($positionsAvailable,"Goalkeeper");
            }

            if($dfs != 6)
            {
                array_push($positionsAvailable,"Defender");
            }

            if($mfs != 6)
            {
                array_push($positionsAvailable,"Midfielder");
            }

            if($fws != 4)
            {
                array_push($positionsAvailable,"Forward");
            }

            $arrayLength = sizeof($positionsAvailable) - 1;

            //pick random number to select position from array
            $pickThisPosition = rand(0,$arrayLength);

            //set position string for query
            $positionString = "and position = '". $positionsAvailable[$pickThisPosition] ."'"; 

            //generate random number to select random player from returned results
            $pickThisPlayer = rand(0,14);
            //placeholder for what number player the loop is on
            $loopNumber = 0;

            //get all free agents in the league, option to filter by position
            $freeAgentResult =  $this->database->_getLeagueFreeAgents($notThesePlayers, $positionString, $premTeamString, $nameString, $orderByString, $start_from, $num_rec_per_page);

            while ($freeAgentRow = $freeAgentResult->fetch_assoc()) 
            {
                if($loopNumber == $pickThisPlayer)
                {
                    //get players if
                    $playerId = $freeAgentRow['id'];
                    //get players position
                    $playerPosition = $freeAgentRow['position'];

                    //placeHolder to hold squad position player will be added to
                    $squadPosition = "";

                    //add player to current squad based on position and how many players are already in squad by position
                    if($playerPosition == "Goalkeeper")
                    {
                        if($gks == 0)
                        {
                            $squadPosition = "g1";
                        }
                        else
                        {
                            $squadPosition = "g2";
                        }
                    }
                    else if($playerPosition == "Defender")
                    {
                        if($dfs == 0)
                        {
                            $squadPosition = "st1";
                        }
                        else if($dfs == 1)
                        {
                            $squadPosition = "st2";
                        }
                        else if($dfs == 2)
                        {
                            $squadPosition = "st3";
                        }
                        else if($dfs == 3)
                        {
                            $squadPosition = "st4";
                        }
                        else if($dfs == 4)
                        {
                            $squadPosition = "s1";
                        }
                        else if($dfs == 5)
                        {
                            $squadPosition = "r1";
                        }
                    }
                    else if($playerPosition == "Midfielder")
                    {
                        if($mfs == 0)
                        {
                            $squadPosition = "st5";
                        }
                        else if($mfs == 1)
                        {
                            $squadPosition = "st6";
                        }
                        else if($mfs == 2)
                        {
                            $squadPosition = "st7";
                        }
                        else if($mfs == 3)
                        {
                            $squadPosition = "st8";
                        }
                        else if($mfs == 4)
                        {
                            $squadPosition = "s2";
                        }
                        else if($mfs == 5)
                        {
                            $squadPosition = "r2";
                        }
                    }
                    else
                    {
                        if($fws == 0)
                        {
                            $squadPosition = "st9";
                        }
                        else if($fws == 1)
                        {
                            $squadPosition = "st10";
                        }
                        else if($fws == 2)
                        {
                            $squadPosition = "s3";
                        }
                        else if($fws == 3)
                        {
                            $squadPosition = "r3";
                        }
                    }

                    //add player to squad list in database
                    $this->database->addDraftSelection($squadId, $playerId, $squadPosition);
                    //add selection to draft history in dp
                    $this->database->addDraftSelectionHistory($squadId, $playerId, $draftId);
                }
                $loopNumber++;
            }
        }
        else
        {
            //do nothing
        }
    }
    
    //gets all the free agents in the league
    function getAvailablePlayers()
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
                            <td id="position'. $freeAgentRow['id'] .'">'. $playerPosition .'</td>
                            <td><p class="playerName" id="'. $freeAgentRow['id'] .'">'. $freeAgentRow['webName'] .'</p></td>
                            <td title="'. $freeAgentRow['teamName'] .'">'. $freeAgentRow['shortName'] .'</td>
                            <td>'. $freeAgentRow['totalPoints'] .'</td>
                            <td><button class="tradeButton" id="select'. $freeAgentRow['id'] .'" onclick="selectPlayer(this.id)">Select</button></td>
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
}
?>
