<?php

class transfer 
{
	var $database;
	// -- Function Name : __construct
    // -- Params :
    // -- Purpose : Starts the connection to the database.
    public function __construct()    
	{
        $this->database = new database();
	}
    
    //trades players from a users team with free agents
    function freeAgentsTrade()
    {
        //set squad and league id
        $teamId = $_GET['squadid'];
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($teamId);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueId = $teamsLeagueRow['leagueid'];
        
        //set variable to count player numbers
        $playerNumber = 1;
        
        //loop through gets passed in
        foreach($_GET as $k => $v) 
        {
            //variables to hold player ina and player out ids
            $playerInId = "";
            $playerOutId = "";
            
            //variable to hold outgoimg players position in squad
            $playerOutPosition = "";
            
            //loop through all playersOut, set player out id
            if(strpos($k, 'playerOut') === 0) 
            {
                //set player out id
                $playerOutId = $v;
                
                //set playerIns id
                $playerInId = $_GET['playerIn'.$playerNumber];

                //add 1 to playerNumber to get next player on next iteration
                $playerNumber++;

                //find what position the playerOut is in the current squad
                //load the full team squad first to find out in which position in the row each player is
                $result = $this->database->_loadSquad($teamId);
                $row = $result->fetch_assoc();

                //get check if players are in the goalie columns
                        for($i=1; $i<=2; $i++)
                        {
                            //set column name
                            $col = "g" . $i;

                            //check if column value is equal to players id
                            if($row[$col] == $playerOutId)
                            {
                                $playerOutPosition = $col;
                            }
                        }

                //get check if players are in the starters columns
                        for($i=1; $i<=10; $i++)
                        {
                            //set column name
                            $col = "st" . $i;

                            //check if column value is equal to players id
                            if($row[$col] == $playerOutId)
                            {
                                $playerOutPosition = $col;
                            }                    
                        }

                //get check if players are in the subs columns
                        for($i=1; $i<=3; $i++)
                        {
                            //set column name
                            $col = "s" . $i;

                            //check if column value is equal to players id
                            if($row[$col] == $playerOutId)
                            {
                                $playerOutPosition = $col;
                            }                    
                        }

                //get check if players are in the reserves columns
                        for($i=1; $i<=3; $i++)
                        {
                            //set column name
                            $col = "r" . $i;

                            //check if column value is equal to players id
                            if($row[$col] == $playerOutId)
                            {
                                $playerOutPosition = $col;
                            }                    
                        }
                
                //delete any open transfer offers involving traded players in the league
                //first find any offers involving same players in league
                $otherOffersResult1 = $this->database->_findOtherOffersWithPlayer($playerOutId, -1, $leagueId);
                while ($otherOffersRow1 = $otherOffersResult1->fetch_assoc()) 
                {
                    //remove any offers found from open offer database
                    $this->database->_cancelOffer($otherOffersRow1['offerId']);
                }

                //update database to swap players positions
                $this->database->_tradeInPlayer($playerOutPosition, $playerInId, $teamId);
            }
        }
        
        //return redirect string to squad page
        echo "index.php?squad=true&leagueid=". $leagueId . "&squadid=". $teamId; 
    }
    
    //trades players from a users team with free agents
    function offerTrade()
    {
        //set squad and league id
        $teamId = $_GET['squadid'];
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($teamId);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueId = $teamsLeagueRow['leagueid'];
        $opTeam = $_GET['opTeam'];
        
        //arrays to hold player in and player out ids
        $playersInIds = array();
        $playersOutIds = array();
        
        //set variable to count player numbers
        $playerNumber = 1;
        
        //loop through gets passed in
        foreach($_GET as $k => $v) 
        {   
            //loop through all playersOut, set player out id
            if(strpos($k, 'playerOut') === 0) 
            {
                //set player out id
                $playerOutId = $v;
                
                //set playerIns id
                $playerInId = $_GET['playerIn'.$playerNumber];
                
                //add players into arrays
                array_push($playersInIds, $playerInId);
                array_push($playersOutIds, $v);

                //add 1 to playerNumber to get next player on next iteration
                $playerNumber++;
            }
        }
        
        //add offer to database
        $offerId = $this->database->_addNewOffer($leagueId, $teamId, $opTeam, date('Y-m-d'));
        
        //add players to offer in database
        for ($i = 0; $i < count($playersOutIds); ++$i) 
        {
            $this->database->_addPlayerToOffer($offerId, $playersOutIds[$i], $playersInIds[$i]);
        }
        
        //return redirect string to squad page
        echo "index.php?transfer=true&open=true&leagueid=". $leagueId . "&squadid=". $teamId;
    }
    
    //gets list of open transfers involving a team
    function getOpenOffers()
    {
        $teamId = $_GET['squadid'];
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($_GET['squadid']);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueid = $teamsLeagueRow['leagueid'];
        
        //If the user IS the manager of the team
        if($teamsLeagueRow['managerid'] == $_SESSION['id'])
        {
            //players in offer value holders
            $playersOfferedValue = 0;
            $playersRequestedValue = 0;

            //search for open offers
            $result =  $this->database->_getOpenOffers($teamId);
            //count number of rows returned to see if any open offers exist
            $count=mysqli_num_rows($result);
            //if there is print them out
            if($count > 0)
            {
                //loop through and print out offer details
                while ($row = $result->fetch_assoc()) 
                {
                    //get players offered in trade
                    $offeredPlayersResult =  $this->database->_getPlayersInOffer($row['id']);
                    $requestedPlayersResult =  $this->database->_getPlayersInOffer($row['id']);

                echo'
                    <div class="singleOffer">
                        <div class="offerColumn">
                            <h3>Players Offered</h3>';

                    //print out players offered in transfer offer
                    while ($offeredPlayersRow = $offeredPlayersResult->fetch_assoc()) 
                    {     
                        echo '<p class="playerName" id="'. $offeredPlayersRow['player1'] .'">'. $offeredPlayersRow['P1FN'] .' '. $offeredPlayersRow['P1LN'] .'</p>';
                        $playersOfferedValue += $offeredPlayersRow['P1Value'];
                    }
                    //add a decimal point to the value of player stored
                        //get position for the point tobe put in
                        $pos = strlen($playersOfferedValue) - 1;
                        //add point to string in position
                        $playersOfferedValue = substr_replace($playersOfferedValue, '.', $pos, 0);

                echo '<br>Total Value: £'.$playersOfferedValue.'0m  </div>
                        <div class="offerColumn"><h3>Players Requested</h3>';

                    //reset value
                    $playersOfferedValue = 0;

                    //print out players requested in transfer offer
                    while ($requestedPlayersRow = $requestedPlayersResult->fetch_assoc()) 
                    {     
                        echo '<p class="playerName" id="'. $requestedPlayersRow['player2'] .'">'. $requestedPlayersRow['P2FN'] .' '. $requestedPlayersRow['P2LN'] .'</p>';
                        $playersRequestedValue += $requestedPlayersRow['P2Value'];
                    }

                    //add a decimal point to the value of player stored
                        //get position for the point tobe put in
                        $pos = strlen($playersRequestedValue) - 1;
                        //add point to string in position
                        $playersRequestedValue = substr_replace($playersRequestedValue, '.', $pos, 0);

                echo '<br>Total Value: £'.$playersRequestedValue.'0m  </div>';

                    //reset value
                    $playersRequestedValue = 0;

                    //write different offered to/By info based on who the offer was made by
                    if($row['team1Manager'] == $_SESSION['id'])
                    {
                        echo'<div class="offerColumn"><h3>Offered To</h3><p>'. $row['team2Name'] .'</p></div>';
                    }
                    else
                    {
                        echo'<div class="offerColumn"><h3>Offered By</h3><p>'. $row['team1Name'] .'</p></div>';
                    }
                    
                    //format date
                    $dt = new DateTime($row['date']);
				    $day = $dt->format('d-m-y');
                    
                    echo'
                        <div class="offerColumn"><h3>Date Offered</h3><p>'. $day .'</p></div>
                        ';
                    //write different offered to/By info based on who the offer was made by
                    if($row['team1Manager'] == $_SESSION['id'])
                    {
                        echo'<div class="actionColumn">
                                <div class="cancelOfferButton" id="'. $row['id'] .'">Cancel</div>
                            </div>';
                    }
                    else
                    {
                        echo'<div class="actionColumn">
                                <div class="acceptOfferButton" id="'. $row['id'] .'">Accept</div>
                                <div class="cancelOfferButton" id="'. $row['id'] .'">Decline</div>
                            </div>';
                    }
                    echo'
                        <div class="clear"></div>
                    </div>
                ';
                }
            }
            else
            //tell user they have no open offers
            {
                echo '<h2 class="noOffersYet">You currently have no open transfer negotiations</h2>';
            }
        }
        else
        //if user is NOT manager of this team
        {
            echo '<p>Not your team</p>';
        }
    }
    
    //cancels a transfer offer
    function cancelOffer()
    {
        //delete offer details from database
        $this->database->_cancelOffer($_GET['offerid']);
        
        //call function to return reprint of update open offers list
        $this->getOpenOffers($_GET['squadid']);
    }
    
    //when a transfer offer is accepted this puts the offer through
    function confirmTrade()
    {
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($_GET['squadid']);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueid = $teamsLeagueRow['leagueid'];
        
        //get trade details
        $tradeResult = $this->database->_getTrade($_GET['offerid']);
        $tradeRow = $tradeResult->fetch_assoc();
        
        //set teams ids
        $team1 = $tradeRow['team1'];
        $team2 = $tradeRow['team2'];
        
        //get details of players in trade offer
        $offerPlayersResult =  $this->database->_getPlayersInOffer($tradeRow['id']);
        
        //loop through and swap players teams
        while ($offerPlayersRow = $offerPlayersResult->fetch_assoc()) 
        {     
            //get team1 squad
            $team1SquadResult = $this->database->_loadSquad($team1);
            $team1SquadRow = $team1SquadResult->fetch_assoc();
            
            //find what position player1 is in team1 squad
        //get check if players are in the goalie columns
            for($i=1; $i<=2; $i++)
            {
                //set column name
                $col1 = "g" . $i;
                
                //check if column value is equal to players id
                if($team1SquadRow[$col1] == $offerPlayersRow['player1'])
                {
                    $playerOutPosition1 = $col1;
                }
            }

        //get check if players are in the starters columns
            for($i=1; $i<=10; $i++)
            {
                //set column name
                $col1 = "st" . $i;

                //check if column value is equal to players id
                if($team1SquadRow[$col1] == $offerPlayersRow['player1'])
                {
                    $playerOutPosition1 = $col1;
                }                    
            }

        //get check if players are in the subs columns
            for($i=1; $i<=3; $i++)
            {
                //set column name
                $col1 = "s" . $i;

                //check if column value is equal to players id
                if($team1SquadRow[$col1] == $offerPlayersRow['player1'])
                {
                    $playerOutPosition1 = $col1;
                }                    
            }

        //get check if players are in the reserves columns
            for($i=1; $i<=3; $i++)
            {
                //set column name
                $col1 = "r" . $i;
                        
                //check if column value is equal to players id
                if($team1SquadRow[$col1] == $offerPlayersRow['player1'])
                {
                    $playerOutPosition1 = $col1;
                }                    
            }
            
            
            //get team2 squad
            $team2SquadResult = $this->database->_loadSquad($team2);
            $team2SquadRow = $team2SquadResult->fetch_assoc();
            
            //find what position player2 is in team2 squad
        //get check if players are in the goalie columns
            for($i=1; $i<=2; $i++)
            {
                //set column name
                $col2 = "g" . $i;
                
                //check if column value is equal to players id
                if($team2SquadRow[$col2] == $offerPlayersRow['player2'])
                {
                    $playerOutPosition2 = $col2;
                }
            }

        //get check if players are in the starters columns
            for($i=1; $i<=10; $i++)
            {
                //set column name
                $col2 = "st" . $i;

                //check if column value is equal to players id
                if($team2SquadRow[$col2] == $offerPlayersRow['player2'])
                {
                    $playerOutPosition2 = $col2;
                }                    
            }

        //get check if players are in the subs columns
            for($i=1; $i<=3; $i++)
            {
                //set column name
                $col2 = "s" . $i;

                //check if column value is equal to players id
                if($team2SquadRow[$col2] == $offerPlayersRow['player2'])
                {
                    $playerOutPosition2 = $col2;
                }                    
            }

        //get check if players are in the reserves columns
            for($i=1; $i<=3; $i++)
            {
                //set column name
                $col2 = "r" . $i;
                        
                //check if column value is equal to players id
                if($team2SquadRow[$col2] == $offerPlayersRow['player2'])
                {
                    $playerOutPosition2 = $col2;
                }                    
            }
            
        //update database to swap players teams into correct positions
            $this->database->_tradeInPlayer($playerOutPosition1, $offerPlayersRow['player2'], $team1);
            $this->database->_tradeInPlayer($playerOutPosition2, $offerPlayersRow['player1'], $team2);
            
        //delete any other open transfer offers involving traded players in the league
            //first find any offers involving same players in league
            $otherOffersResult1 = $this->database->_findOtherOffersWithPlayer($offerPlayersRow['player1'], $_GET['offerid'], $leagueid);
            while ($otherOffersRow1 = $otherOffersResult1->fetch_assoc()) 
            {
                //remove any offers found from open offer database
                $this->database->_cancelOffer($otherOffersRow1['offerId']);
            }
            $otherOffersResult2 = $this->database->_findOtherOffersWithPlayer($offerPlayersRow['player2'], $_GET['offerid'], $leagueid);
            while ($otherOffersRow2 = $otherOffersResult2->fetch_assoc()) 
            {
                //remove any offers found from open offer database
                $this->database->_cancelOffer($otherOffersRow2['offerId']);
            }
        }
        
        //add transfer to history
        $this->addHistory($_GET['offerid']);
        
        //return redirect string to squad page
        echo "index.php?squad=true&leagueid=". $leagueid . "&squadid=". $_GET['squadid']; 
    }
    
    //commits a confirmed trade to history
    function addHistory($offerId)
    {   
        //get trade details
        $tradeResult = $this->database->_getTrade($offerId);
        $tradeRow = $tradeResult->fetch_assoc();
        
        //add offer to database
        $historyId = $this->database->_addNewHistory($tradeRow['leagueId'], $tradeRow['team1'], $tradeRow['team2'], date('Y-m-d'));
        
        //get details of players in trade offer
        $offerPlayersResult =  $this->database->_getPlayersInOffer($offerId);
        
        //add players to history in database
        while($offerPlayersRow = $offerPlayersResult->fetch_assoc()) 
        {
            $this->database->_addPlayerToHistory($historyId, $offerPlayersRow['player1'], $offerPlayersRow['player2']);
        }
        
        //remove offer from open offer database
        $this->database->_cancelOffer($_GET['offerid']);
    }
    
    //get a list of transfer history for league
    function getleagueTransfers()
    {
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($_GET['squadid']);
        $teamsLeagueRow = $teamsLeagueResult->fetch_assoc();
        //set the League id to the league id of the managers team
        $leagueId = $teamsLeagueRow['leagueid'];
        
        //search for league transfer history
        $result =  $this->database->_getleagueTransfers($leagueId);
        //count number of rows returned to see if any open offers exist
        $count=mysqli_num_rows($result);
        //if there is print them out
        if($count > 0)
        {
            //loop through and print out offer details
            while ($row = $result->fetch_assoc()) 
            {
                //players in offer value holders
                $playersOfferedValue = 0;
                $playersRequestedValue = 0;

                //get players involved in trade
                $offeredPlayersResult =  $this->database->_getPlayersInHistory($row['id']);
                $requestedPlayersResult =  $this->database->_getPlayersInHistory($row['id']);
                
                //format date
                $dt = new DateTime($row['date']);
				$day = $dt->format('d-m-y');

            echo'
                <div class="singleOffer">
                    <div class="offerColumn"><h3>Date</h3><p>'. $day .'</p></div>
                    <div class="offerColumn"><h3>Offered By</h3><p>'. $row['team1Name'] .'</p></div>
                    <div class="offerColumn">
                        <h3>Players Offered</h3>';

                //print out players offered in transfer offer
                while ($offeredPlayersRow = $offeredPlayersResult->fetch_assoc()) 
                {     
                    echo '<p class="playerName" id="'. $offeredPlayersRow['player1'] .'">'. $offeredPlayersRow['P1FN'] .' '. $offeredPlayersRow['P1LN'] .'</p>';
                    $playersOfferedValue += $offeredPlayersRow['P1Value'];
                }
                    //add a decimal point to the value of player stored
                        //get position for the point tobe put in
                        $pos = strlen($playersOfferedValue) - 1;
                        //add point to string in position
                        $playersOfferedValue = substr_replace($playersOfferedValue, '.', $pos, 0);

                echo '<br>Total Value: £'.$playersOfferedValue.'0m  </div>
                    <div class="offerColumn"><h3>Players Requested</h3>';
                //reset value
                $playersOfferedValue = 0;

                //print out players requested in transfer offer
                while ($requestedPlayersRow = $requestedPlayersResult->fetch_assoc()) 
                {     
                    echo '<p class="playerName" id="'. $requestedPlayersRow['player2'] .'">'. $requestedPlayersRow['P2FN'] .' '. $requestedPlayersRow['P2LN'] .'</p>';
                    $playersRequestedValue += $requestedPlayersRow['P2Value'];
                }
                    //add a decimal point to the value of player stored
                        //get position for the point tobe put in
                        $pos = strlen($playersRequestedValue) - 1;
                        //add point to string in position
                        $playersRequestedValue = substr_replace($playersRequestedValue, '.', $pos, 0);
            echo '<br>Total Value: £'.$playersRequestedValue.'0m  </div>
                    <div class="offerColumn"><h3>Accepted By</h3><p>'. $row['team2Name'] .'</p></div>
                    <div class="clear"></div>
                </div>
            ';
                //reset value
                $playersRequestedValue = 0;
            }
        }
        else
        //tell user they have no open offers
        {
            echo '<h2 class="noOffersYet">There have been no transfers in this league yet</h2>';
        }
    }
    
    //returns a count of number of open transfer offers a team has
    function getTeamsTransferRequests()
    {
        $openOffersResult = $this->database->_getTeamsTransferRequests($_GET['squadid']);
        $openOffersRow = $openOffersResult->fetch_assoc();
        
        return $openOffersRow['numOffers'];
    }
}
?>
