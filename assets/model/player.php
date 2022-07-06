<?php

class player 
{
	var $database;
	
	// -- Function Name : __construct
    // -- Params :
    // -- Purpose : Starts the connection to the database.
    public function __construct()    
	{
        $this->database = new database();
	}
	
    //function to load player details for player information pop up
    public function loadPlayerDetails()
    {    
    //get Players Id number
        $plId = $_GET['plId'];
        //to get managers team and league details
        $teamsLeagueResult = $this->database->_getTeamsLeague($_GET['squadid']);
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
        
    //load the full team squad first to find out in which position in the row each player is
        $result = $this->database->_loadPlayerDetails($plId);
        $row = $result->fetch_assoc();
        
    //check if current gameweek has started yet, if not gw back 1 to allow for this game to remain in fixtures
        if($gwrow['status'] == 0)
        {
            $fixtureGW = $todaysGameweek - 1;
        }
        else
        {
            $fixtureGW = $todaysGameweek;
        }
    //load the players full history list
        $historyResult = $this->database->_getPlayersHistory($plId);
    //load the players full fixture list
        $fixtureResult = $this->database->_getPlayersFixtures($plId, $fixtureGW);
    //get players owner
        $ownerResult = $this->database->_getPlayersOwner($plId, $leagueid);
        $ownerRow = $ownerResult->fetch_assoc();
        
        
        if($ownerRow['owner'] == "")
        {
            $owner = "Free Agent";
        }
        else
        {
            $owner = $ownerRow['owner'];
        }
        
    //add a decimal point to the value of player stored
        //get position for the point tobe put in
        $pos = strlen($row['value']) - 1;
        //add point to string in position
        $value = substr_replace($row['value'], '.', $pos, 0);
        
    //get minutes per game value
        if($row['gamesPlayed'] > 0)
        {
            $minsPerGame = $row['minutesPlayed'] / $row['gamesPlayed'];
            $savesMade = $row['savesMade'];
            $penaltiesSaved = $row['penaltiesSaved'];
            $goalsScored = $row['goalsScored'];
            $assists = $row['assists'];
            $redCards = $row['redCards'];
            $yellowCards = $row['yellowCards'];
            $cleanSheets = $row['cleanSheets'];
        }
        else 
        {
            $minsPerGame = 0;
            $savesMade = 0;
            $penaltiesSaved = 0;
            $goalsScored = 0;
            $assists = 0;
            $redCards = 0;
            $yellowCards = 0;
            $cleanSheets = 0;
        }
        
        //round to full number
        $minsPerGame = round($minsPerGame);
       
    echo'
        <div id="playerInformationHeader">
            <div id="playerInformationClubBadge"><img class="subBadgePic" src="assets/images/clubBadges/'. $row['teamId'] .'.png" alt="Team name Badge"></div>
            <div id="playerInformationName">'. $row['firstName'] .' '. $row['lastName'] .'</div>
            <div id="playerInformationCloseButton">X</div>
        </div>
        
        <div id="playerInformationOptions">
            <div id="playerInformationOptionsTabsHolder">
                <div class="playerInformationOptionsTabSelected" id="Overview">Overview</div>
                <div class="playerInformationOptionsTab" id="History">History</div>
                <div class="playerInformationOptionsTab" id="Fixtures">Fixtures</div>
            </div>
        </div>
        <div class="playerDetailsHolder" id="overviewHolder">
            <p class="whiteText">'. $row['news'] .'</p>
            <div id="playerImage">';
            //check if players image is saved
                if(file_exists('assets/images/playerImages/'. $row['id'] .'.jpg'))
                {
                    echo '<img class="badgePic2" src="assets/images/playerImages/'. $row['id'] .'.jpg" alt="Players Picture">';
                }
            //otherwise print out default image
                else
                {
                    echo '<img class="badgePic2" src="assets/images/playerImages/fallback.png" alt="Players Picture">';
                }
                
    echo'   </div>
            <div id="playerInfo">
                <div class="playerInfoRow"><p>'. $row['teamName'] .'</p></div>
                <div class="playerInfoRow"><p>'. $row['position'] .'</p></div>
                <div class="playerInfoRow"><p>Value: £'. $value .'0m</p></div>
                <div class="playerInfoRow"><p>Total Points: '. $row['totalPoints'] .'</p></div>
                <div class="playerInfoRow"><p>'. $owner .'</p></div>
            </div>
            <div id="detailsHolder">
                
                <div class="quickStats">
                    <div class="quickStatsName"><p>Games</p></div>
                    <div class="quickStatsValue"><p>'. $row['gamesPlayed'] .'</p></div>
                </div>
                <div class="quickStats">
                    <div class="quickStatsName"><p>Mins/Game</p></div>
                    <div class="quickStatsValue"><p>'. $minsPerGame .'</p></div>
                </div>
        ';
        //different stats for goalkeeper or outfielder
        if($row['position'] == "Goalkeeper" )
        {
            echo'
                <div class="quickStats">
                    <div class="quickStatsName"><p>Saves Made</p></div>
                    <div class="quickStatsValue"><p>'. $savesMade .'</p></div>
                </div>
                <div class="quickStats">
                    <div class="quickStatsName" title="Penalties Saves"><p>Pen Saves</p></div>
                    <div class="quickStatsValue"><p>'. $penaltiesSaved .'</p></div>
                </div>
            ';
        }
        else
        {
            echo'
                <div class="quickStats">
                    <div class="quickStatsName"><p>Goals</p></div>
                    <div class="quickStatsValue"><p>'. $goalsScored .'</p></div>
                </div>
                <div class="quickStats">
                    <div class="quickStatsName"><p>Assists</p></div>
                    <div class="quickStatsValue"><p>'. $assists .'</p></div>
                </div>
            ';
        }
        echo'
                <div class="quickStats">
                    <div class="quickStatsName"><p>Bookings</p></div>
                    <div class="quickStatsValue"><p>R'. $redCards .' Y'. $yellowCards .'</p></div>
                </div>
                <div class="quickStats">
                    <div class="quickStatsName"><p>Clean Sheets</p></div>
                    <div class="quickStatsValue"><p>'. $cleanSheets .'</p></div>
                </div>
                
                <div class="clear"></div>
                <div class="gameDetails">
                    <table>
                      <tr>
                        <th>GW</th>
                        <th>Opposition</th> 
                        <th>Points</th>
                      </tr>';
                //get last 3 fixtures, loop through and print out
                      $last3Result = $this->database->_getLast3PlayersHistory($plId);
                      while ($last3Row = $last3Result->fetch_assoc()) 
                        {
                            echo '
                            <tr>
                                <td>'. $last3Row["gameweek"] .'</td>
                                <td>'. $last3Row["opponentResult"] .'</td> 
                                <td>'. $last3Row["total"] .'</td>
                            </tr> 
                            ';
                        }
        echo '
                    </table>
                </div>
                <div class="gameDetails">
                     <table>
                      <tr>
                        <th>GW</th>
                        <th>Opposition</th> 
                        <th>Date</th>
                      </tr>';
                //get next 3 fixtures, loop through and print out
                      $last3Result = $this->database->_getNext3PlayersFixtures($plId, $fixtureGW);
                      while ($last3Row = $last3Result->fetch_assoc()) 
                        {
                            echo '
                            <tr>
                                <td>'. $last3Row["gameweek"] .'</td>
                                <td>'. $last3Row["opponent_short"] .'</td>
                                <td>'. $last3Row["date"] .'</td>
                            </tr> 
                            ';
                        }
        echo '
                    </table>
                </div>
                <div class="clear"></div>
            </div>
        </div>
                <div class="playerDetailsHolder" id="historyHolder">
                    <table class="HistoryTable">
                      <tr>
                        <th>Date</th>
                        <th title="Game Week">gw</th>
                        <th>Opposition</th>
                        <th title="Minutes Played">mp</th>
                        <th title="Goals Scored">gs</th>
                        <th title="Assists">a</th>
                        <th title="Clean Sheets">cs</th>
                        <th title="Goals Conceded">gc</th>
                        <th title="Own Goals">og</th>
                        <th title="Penalties Saved">ps</th>
                        <th title="Penalties Missed">pm</th>
                        <th title="Yellow Cards">yc</th>
                        <th title="Red Cards">rc</th>
                        <th title="Saves">s</th>
                        <th title="Bonus">b</th>
                        <th title="Total Points">total</th>
                      </tr>
    ';
        //loop through and print out players history
                while ($historyRow = $historyResult->fetch_assoc()) 
                {
				    echo '
                      <tr>
                        <td>'. $historyRow['dateTime'] .'</td>
                        <td>'. $historyRow['gameweek'] .'</td>
                        <td>'. $historyRow['opponentResult'] .'</td> 
                        <td>'. $historyRow['mp'] .'</td>
                        <td>'. $historyRow['gs'] .'</td>
                        <td>'. $historyRow['a'] .'</td>
                        <td>'. $historyRow['cs'] .'</td>
                        <td>'. $historyRow['gc'] .'</td>
                        <td>'. $historyRow['og'] .'</td>
                        <td>'. $historyRow['ps'] .'</td>
                        <td>'. $historyRow['pm'] .'</td>
                        <td>'. $historyRow['yc'] .'</td>
                        <td>'. $historyRow['rc'] .'</td>
                        <td>'. $historyRow['s'] .'</td>
                        <td>'. $historyRow['b'] .'</td>
                        <td>'. $historyRow['total'] .'</td>
                      </tr>
                      ';
				}
    echo'         
                    </table>       
                </div>
                <div class="playerDetailsHolder" id="fixturesHolder">
                    <table class="HistoryTable">
                      <tr>
                        <th title="Game Week">Gameweek</th>
                        <th>Date</th>
                        <th>Opposition</th>
                      </tr>
    ';
        //loop through and print out players history
                while ($fixtureRow = $fixtureResult->fetch_assoc()) 
                {
				    echo '
                      <tr>
                        <td>'. $fixtureRow['gameweek'] .'</td>
                        <td>'. $fixtureRow['date'] .'</td>
                        <td>'. $fixtureRow['opponent'] .'</td> 
                      </tr>
                      ';
				}
    echo'         
                    </table>  
                </div>
                <div class="clear"></div>
            </div>
            
        </div>
        ';
        
    }
    
}

?>
