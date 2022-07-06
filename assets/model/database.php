<?php
class database 
{
    var $user = "hxmwdejw_fifamassa";
    var $password = "T!mothy90108991";
    var $host = "localhost";
    var $database = "hxmwdejw_fifamassa";
    var $con;

    // -- Function Name : __construct
    // -- Params :
    // -- Purpose : Doonects to the database
    public function __construct() 
	{
        if (! isset ( $_SESSION )) 
        {
            session_start ();
        }

        $this->con = new mysqli ($this->host, $this->user, $this->password, $this->database );

        if (mysqli_connect_errno ()) 
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error ();
        }
    }
    
    // -- Function Name : register
    // -- Params : $fName, $lName, $username, $email, $password, $salt
    // -- Purpose : Register the user to the database.
    public function _register($fName, $lName, $username, $email, $password, $salt) 
    {
        mysqli_query ( $this->con, "INSERT INTO user (firstName,lastName,username,email,password,salt)
                                              VALUES ('$fName','$lName','$username','$email','$password','$salt')" );
        
        $id = mysqli_insert_id($this->con);
        
        $results = mysqli_query ( $this->con, "SELECT * FROM user WHERE id='$id';" );
        return $results;
    }
	
    
    // -- Function Name : login
    // -- Params : $email, $password
    // -- Purpose : Checks if username exists in the database.
    public function _login($username) 
    {
        $results = mysqli_query ( $this->con, "SELECT * FROM user WHERE username='$username';" );
        return $results;
    }
    
    
    // -- Purpose : get team, manager and league of a given squad
    public function _getTeamLeagueDetails($teamId) 
    {
        $results = mysqli_query ( $this->con, "SELECT managersteam.manager as managerId, managersteam.id as teamId, managersteam.name as teamName, managersteam.colour1 as col1, managersteam.colour2 as col2, managersteam.jersey as jersey,
                                                user.id as managerId, user.firstName as firstName, user.lastName as lastName,
                                                fantasyleague.name as leagueName, fantasyleague.draftSelectionTime as selectionTime, fantasyleague.admin AS leagueadmin,
                                                draft.status as draftStatus
                                                FROM managersteam
                                                INNER JOIN user ON user.id = managersteam.manager
                                                INNER JOIN fantasyleague ON managersteam.league = fantasyleague.id
                                                INNER JOIN draft on fantasyleague.id = draft.leagueId
                                                WHERE managersteam.id ='$teamId';" );
        return $results;
    }
    
    // -- Purpose : Load up the current squad of a managers team, aswell as player, team and manager details
    public function _loadSquad($teamId) 
    {
        $results = mysqli_query ( $this->con, "SELECT managersteam.manager as managerId, managersteam.id as teamId, managersteam.name as teamName, managersteam.colour1 as col1, managersteam.colour2 as col2, managersteam.jersey as jersey,
                                                currentsquad.*, sum(player.value) as squadvalue,
                                                user.id as managerId, user.firstName as firstName, user.lastName as lastName  
                                                FROM managersteam
                                                INNER JOIN currentsquad ON managersteam.id = currentsquad.teamId
                                                INNER JOIN user ON user.id = managersteam.manager
                                                INNER join player
                                                    on player.id = currentsquad.st1 or player.id = currentsquad.st2 or player.id = currentsquad.st3 or player.id = currentsquad.st4 or player.id = currentsquad.st5 or player.id = currentsquad.st6 or player.id = currentsquad.st7 or player.id = currentsquad.st8 or player.id = currentsquad.st9 or player.id = currentsquad.st10 or player.id = currentsquad.g1 or player.id = currentsquad.g2 or player.id = currentsquad.s1 or player.id = currentsquad.s2 or player.id = currentsquad.s3 or player.id = currentsquad.r1 or player.id = currentsquad.r2 or player.id = currentsquad.r3
                                                WHERE managersteam.id ='$teamId';" );
        return $results;
    }
    
    // -- Purpose : get the squad value of a team
    public function _getSquadValue($teamId)
    {
        $results = mysqli_query ( $this->con, "SELECT sum(player.value) as squadvalue
                                                FROM managersteam
                                                INNER JOIN currentsquad ON managersteam.id = currentsquad.teamId
                                                INNER JOIN user ON user.id = managersteam.manager
                                                INNER join player
                                                    on player.id = currentsquad.st1 or player.id = currentsquad.st2 or player.id = currentsquad.st3 or player.id = currentsquad.st4 or player.id = currentsquad.st5 or player.id = currentsquad.st6 or player.id = currentsquad.st7 or player.id = currentsquad.st8 or player.id = currentsquad.st9 or player.id = currentsquad.st10 or player.id = currentsquad.g1 or player.id = currentsquad.g2 or player.id = currentsquad.s1 or player.id = currentsquad.s2 or player.id = currentsquad.s3 or player.id = currentsquad.r1 or player.id = currentsquad.r2 or player.id = currentsquad.r3
                                                WHERE managersteam.id ='$teamId';" );
        return $results;
    }



    // -- Purpose : get details of a single player, including team name
    public function _getSinglePlayerDetails($playerId, $gameweek) 
    {
        $results = mysqli_query ( $this->con, "SELECT player.* , clubteam.fullName AS teamName, clubmatch.opponent AS nextFixture
                                                FROM player
                                                INNER JOIN clubteam ON clubteam.id = player.teamId
                                                INNER JOIN clubmatch ON clubmatch.teamId = clubteam.id
                                                AND clubmatch.gameweek > $gameweek
                                                WHERE player.id = $playerId
                                                ORDER BY clubmatch.gameweek
                                                LIMIT 1;" );
        return $results;
    }
    
    // -- Purpose : get the gameweek squad for a given team on a given gameweek
    public function _getGameweekSquad($teamId, $gameweek) 
    {
        $results = mysqli_query ( $this->con, "SELECT gameweekteam.*, managersteam.manager as managerId, managersteam.name as teamName, user.id as managerId, user.firstName as firstName, user.lastName as lastName, sum(player.value) as squadvalue
                                                FROM gameweekteam
                                                INNER JOIN managersteam ON managersteam.id = gameweekteam.teamId
                                                INNER JOIN user ON user.id = managersteam.manager
                                                INNER join player
                                                    on player.id = gameweekteam.st1 or player.id = gameweekteam.st2 or player.id = gameweekteam.st3 or player.id = gameweekteam.st4 or player.id = gameweekteam.st5 or player.id = gameweekteam.st6 or player.id = gameweekteam.st7 or player.id = gameweekteam.st8 or player.id = gameweekteam.st9 or player.id = gameweekteam.st10 or player.id = gameweekteam.g1 or player.id = gameweekteam.g2 or player.id = gameweekteam.s1 or player.id = gameweekteam.s2 or player.id = gameweekteam.s3
                                                where gameweek = $gameweek and gameweekteam.teamId = $teamId;" );
        return $results;
    }
    
    // -- Purpose : get details of a players gameweek points and player details
    public function _getSinglePlayerGameweekPoints($playerId, $gameweek) 
    {
        $results = mysqli_query ( $this->con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = $playerId
                                                AND points.gameweek = $gameweek;" );
        return $results;
    }
    
    // -- Purpose : get a teams gameweek squad and player points
    /*public function _getGameweekPoints($squadId, $gameweek) 
    {
        $results = mysqli_query ( $this->con, "SELECT gameweekteam.teamId, gameweekteam.g1, g1.total as g1Points, gameweekteam.st1, st1.total as st1Points, gameweekteam.st2, st2.total as st2Points, gameweekteam.st3, st3.total as st3Points, gameweekteam.st4, st4.total as st4Points, gameweekteam.st5, st5.total as st5Points, gameweekteam.st6, st6.total as st6Points, gameweekteam.st7, st7.total as st7Points, gameweekteam.st8, st8.total as st8Points, gameweekteam.st9, st9.total as st9Points, gameweekteam.st10, st10.total as st10Points, gameweekteam.g2, g2.total as g2Points, gameweekteam.s1, s1.total as s1Points, gameweekteam.s2, s2.total as s2Points, gameweekteam.s3, s3.total as s3Points 
FROM gameweekteam  inner join points as g1 on g1.playerId = gameweekteam.g1 inner join points as st1 on st1.playerId = gameweekteam.st1 inner join points as st2 on st2.playerId = gameweekteam.st2 inner join points as st3 on st3.playerId = gameweekteam.st3 inner join points as st4 on st4.playerId = gameweekteam.st4 inner join points as st5 on st5.playerId = gameweekteam.st5 inner join points as st6 on st6.playerId = gameweekteam.st6 inner join points as st7 on st7.playerId = gameweekteam.st7 inner join points as st8 on st8.playerId = gameweekteam.st8 inner join points as st9 on st9.playerId = gameweekteam.st9 inner join points as st10 on st10.playerId = gameweekteam.st10 inner join points as g2 on g2.playerId = gameweekteam.g2 inner join points as s1 on s1.playerId = gameweekteam.s1 inner join points as s2 on s2.playerId = gameweekteam.s2 inner join points as s3 on s3.playerId = gameweekteam.s3
WHERE g1.gameweek = $gameweek and st1.gameweek = $gameweek and st2.gameweek = $gameweek and st3.gameweek = $gameweek and st4.gameweek = $gameweek and st5.gameweek = $gameweek and st6.gameweek = $gameweek and st7.gameweek = $gameweek and st8.gameweek = $gameweek and st9.gameweek = $gameweek and st10.gameweek = $gameweek and g2.gameweek = $gameweek and s1.gameweek = $gameweek and s2.gameweek = $gameweek and s3.gameweek = $gameweek and gameweekteam.gameweek = $gameweek and gameweekteam.teamId = $squadId;" );
        return $results;
    }*/
    
    // -- Purpose : Updates a squad when substitute is made, ids of players swapped are passed in
    public function _makeSub($pl1, $pl2, $team) 
    {
        mysqli_query ( $this->con, "UPDATE currentsquad SET $pl1, $pl2 WHERE teamId =  $team;" );
    }
	
    //gets all players information required for player info pop up box, gets games that the player has played in
    public function _loadPlayerDetails($plId)
    {
        $results = mysqli_query ( $this->con, "SELECT player.*, clubteam.fullName as teamName, clubteam.shortName as shortName, SUM(points.total) as totalPoints, SUM(points.gs) as goalsScored, SUM(points.a) as assists, SUM(points.cs) as cleanSheets, SUM(points.yc) as yellowCards, SUM(points.rc) as redCards, SUM(points.s) as savesMade, SUM(points.ps) as penaltiesSaved, SUM(points.mp) as minutesPlayed, COUNT(points.mp) AS gamesPlayed
                                                FROM player 
                                                Inner join points
                                                    on points.playerId = player.id
                                                Inner join clubteam
                                                    on clubteam.id = player.teamId
                                                where player.id = $plId and points.mp > 0;" );
        return $results;
    }
    
    //gets all players history required for player info pop up box, gets games that the player has played in
    public function _getPlayersHistory($plId)
    {
        $results = mysqli_query ( $this->con, "SELECT player.*, clubteam.fullName as teamName, points.*
                                                FROM player 
                                                Inner join points
                                                    on points.playerId = player.id
                                                Inner join clubteam
                                                    on clubteam.id = player.teamId
                                                where player.id = $plId
                                                order by points.gameweek desc;" );
        return $results;
    }
    
    //gets previous 3 games details for player
    public function _getLast3PlayersHistory($plId)
    {
        $results = mysqli_query ( $this->con, "SELECT points.total, points.opponentResult, points.gameweek
                                                FROM player 
                                                Inner join points
                                                    on points.playerId = player.id
                                                where player.id = $plId
                                                order by points.gameweek desc
												limit 3;" );
        return $results;
    }
    
    //gets next 3 games details for player
    public function _getNext3PlayersFixtures($plId, $gameweek)
    {
        $results = mysqli_query ( $this->con, "SELECT clubmatch.gameweek, clubmatch.opponent, clubmatch.opponent_short, clubmatch.date
                                                FROM player 
                                                Inner join clubteam
                                                    on clubteam.id = player.teamId
                                                Inner join clubmatch
                                                    on clubmatch.teamId = clubteam.id and clubmatch.gameweek > $gameweek
                                                where player.id = $plId
                                                order by clubmatch.gameweek
												limit 3;" );
        return $results;
    }
    
    //gets all future games details for player
    public function _getPlayersFixtures($plId, $gameweek)
    {
        $results = mysqli_query ( $this->con, "SELECT clubmatch.gameweek, clubmatch.opponent, clubmatch.date
                                                FROM player 
                                                Inner join clubteam
                                                    on clubteam.id = player.teamId
                                                Inner join clubmatch
                                                    on clubmatch.teamId = clubteam.id
                                                where player.id = $plId and clubmatch.gameweek > $gameweek
                                                order by clubmatch.gameweek;" );
        return $results;
    }
    
    //managersteam next fixture
    public function _getManagersNextFixture($id, $gw)
    {
        $results = mysqli_query ( $this->con, "SELECT fantasymatch.*, one.name AS team1name, two.name AS team2name FROM managersteam
                                                Inner join fantasymatch
                                                    on managersteam.id = fantasymatch.team2 or managersteam.id = fantasymatch.team1
                                                Inner JOIN managersteam AS one 
                                                    ON fantasymatch.team1 = one.id
                                                Inner JOIN managersteam AS two 
                                                    ON fantasymatch.team2 = two.id
                                                where managersteam.id = $id and gameweek > $gw;" );
        return $results;
    }
    
    //returns a list of all the users leagues and teams
    public function _loadUsersLeagues($id)
    {
        $results = mysqli_query ( $this->con, "SELECT managersteam.*, fantasyleague.name as leagueName, fantasyleague.draftDate as draftDate, fantasyleague.draftTime as draftTime, fantasyleague.draftSelectionTime as draftSelectionTime, fantasyleague.status as status, fantasyleague.id as leagueId, fantasyleague.password as leaguePassword, draft.status as draftStatus
                                                FROM managersteam 
                                                INNER JOIN fantasyleague
                                                    ON managersteam.league = fantasyleague.id
                                                INNER JOIN draft
                                                    ON draft.leagueId = fantasyleague.id
                                                WHERE manager = $id;" );
        return $results;
    }
    
    //returns a list of all the leagues teams
    public function _loadLeagueTable($id)
    {
        $results = mysqli_query ( $this->con, "SELECT managersteam. * , fantasyleague.name AS leagueName, fantasyleague.status AS 
STATUS , fantasyleague.id AS leagueId, COUNT( fantasymatch.status ) AS gamesPlayed, sum(player.value) as squadvalue
                                                FROM managersteam
                                                INNER JOIN currentsquad ON managersteam.id = currentsquad.teamId
                                                INNER join player
                                                    on player.id = currentsquad.st1 or player.id = currentsquad.st2 or player.id = currentsquad.st3 or player.id = currentsquad.st4 or player.id = currentsquad.st5 or player.id = currentsquad.st6 or player.id = currentsquad.st7 or player.id = currentsquad.st8 or player.id = currentsquad.st9 or player.id = currentsquad.st10 or player.id = currentsquad.g1 or player.id = currentsquad.g2 or player.id = currentsquad.s1 or player.id = currentsquad.s2 or player.id = currentsquad.s3 or player.id = currentsquad.r1 or player.id = currentsquad.r2 or player.id = currentsquad.r3
                                                INNER JOIN fantasyleague ON managersteam.league = fantasyleague.id
                                                LEFT JOIN fantasymatch ON managersteam.id = fantasymatch.team1 AND fantasymatch.status = 2
                                                OR managersteam.id = fantasymatch.team2 AND fantasymatch.status = 2
                                                WHERE fantasyleague.id = $id
                                                GROUP BY managersteam.id
                                                ORDER BY totalPoints desc, fantasyPoints desc, w desc, name;" );
        return $results;
    }
    

    //returns a list of all the leagues teams
    public function _loadLeagueTableNoValue($id)
    {
        $results = mysqli_query ( $this->con, "SELECT managersteam. * , fantasyleague.name AS leagueName, fantasyleague.status AS
STATUS , fantasyleague.id AS leagueId, COUNT( fantasymatch.status ) AS gamesPlayed
                                                FROM managersteam
                                                INNER JOIN fantasyleague ON managersteam.league = fantasyleague.id
                                                LEFT JOIN fantasymatch ON managersteam.id = fantasymatch.team1 AND fantasymatch.status = 2
                                                OR managersteam.id = fantasymatch.team2 AND fantasymatch.status = 2
                                                WHERE fantasyleague.id = $id
                                                GROUP BY managersteam.id
                                                ORDER BY totalPoints desc, fantasyPoints desc, w desc, name;" );
        return $results;
    }

    //Create new league in database and return id
    public function _createLeague($leaguename, $password, $timeperpick,$admin)
    {
        mysqli_query ( $this->con, "INSERT INTO fantasyleague (name,password,draftSelectionTime,admin)
                                              VALUES ('$leaguename','$password','$timeperpick','$admin')" );
        
        $id = mysqli_insert_id($this->con);
        
        return $id;
    }
    
    //creates a new team and adds them to league  and returns teams id
     public function _addNewTeam($leagueId, $teamName, $manager)
    {
        mysqli_query ( $this->con, "INSERT INTO managersteam (manager,league,name)
                                              VALUES ('$manager','$leagueId','$teamName')" );
        
        $id = mysqli_insert_id($this->con);
        
        return $id;
    }
    
    //adds a blank squad to database
     public function _addBlankSquad($squadId)
    {
        mysqli_query ( $this->con, "INSERT INTO currentsquad(teamId) VALUES ('$squadId')" );
    }
    
    //sets up draft details
    public function _addDraft($leagueId, $teamId)
    {
        mysqli_query ( $this->con, "INSERT INTO draft(leagueId, teamsPick, pickNumber, status) 
                                    VALUES ('$leagueId','$teamId','1','0')" );
        
        $id = mysqli_insert_id($this->con);
        
        return $id;
    }
    
    //starts draft
     public function _startDraft($draftId)
    {
        mysqli_query ( $this->con, "UPDATE draft SET status = 1 WHERE id = $draftId" );
    }
    
    //ends draft
     public function _endDraft($draftId)
    {
        mysqli_query ( $this->con, "UPDATE draft SET status = 2 WHERE id = $draftId" );
    }
    
    //starts league
     public function _startLeague($leagueId)
    {
        mysqli_query ( $this->con, "UPDATE fantasyleague SET status = 1 WHERE id = $leagueId" );
    }
    
    //adds team to draft order
    public function _addDraftOrder($draftId, $teamId)
    {
        mysqli_query ( $this->con, "INSERT INTO draftorder(draftId, teamId) 
                                    VALUES ('$draftId','$teamId')" );
    }
    
    //search for league by league id
    public function _findLeague($id)
    {
        $results = mysqli_query ( $this->con, "SELECT *
                                                FROM fantasyleague 
                                                WHERE id = $id;" );
        return $results;
    }
 
    
    //check if a player is already in a league
    public function _isPlayerInLeague($leagueid, $managerid)
    {
        $results = mysqli_query ( $this->con, "SELECT fantasyleague . * , managersteam.league, managersteam.manager as  managersId
                                                FROM fantasyleague
                                                INNER JOIN managersteam 
                                                    ON managersteam.league = fantasyleague.id
                                                WHERE fantasyleague.id = $leagueid AND managersteam.manager = $managerid;" );
        return $results;
    }
    
    /* NEVER USED
    //get fixtures/results for league on a given gameweek based on date excluding games vs league average
    public function _getFixtures($id, $date)
    {
        $results = mysqli_query ( $this->con, "SELECT gameweek.id AS gameweekNumber, gameweek.startDate, gameweek.startDate, fantasymatch.*, one.name AS team1name, two.name AS team2name
                                                FROM gameweek 
                                                INNER join fantasymatch
                                                    on gameweek.id = fantasymatch.gameweek	
                                                INNER JOIN managersteam AS one 
                                                    ON fantasymatch.team1 = one.id
                                                INNER JOIN managersteam AS two 
                                                    ON fantasymatch.team2 = two.id
                                                where fantasymatch.league = '$id' and '$date' between startDate and endDate;" );
        return $results;
    }
    
    //get fixtures/results for league on a given gameweek based on date vs league average
    public function _getFixturesVsLeagueAverage($id, $date)
    {
        $results = mysqli_query ( $this->con, "SELECT gameweek.id AS gameweekNumber, gameweek.startDate, gameweek.startDate, fantasymatch.*,  two.name AS team2name
                                                FROM gameweek 
                                                INNER join fantasymatch
                                                    on gameweek.id = fantasymatch.gameweek
                                                INNER JOIN managersteam AS two 
                                                    ON fantasymatch.team2 = two.id
                                                where fantasymatch.league = '$id' 
                                                AND fantasymatch.team1 =  '-1'
                                                AND '$date' between startDate and endDate;" );
        return $results;
    }*/
    
    //get gameweek based on date
    public function _getGameweek($date)
    {
        $results = mysqli_query ( $this->con, "SELECT *
                                                FROM gameweek
                                                where '$date' between startDate and endDate;" );
        return $results;
    }
    
    //get gameweek details
    public function _getGameweekById($id)
    {
        $results = mysqli_query ( $this->con, "SELECT *
                                                FROM gameweek
                                                where id = '$id';" );
        return $results;
    }
    
    //get fixtures/results for league on a gameweek based on gameweek number excluding games vs league average
    public function _getGWFixtures($id, $gw)
    {
        $results = mysqli_query ( $this->con, "SELECT fantasymatch.*, one.name AS team1name, two.name AS team2name
                                                FROM fantasymatch	
                                                Inner JOIN managersteam AS one 
                                                    ON fantasymatch.team1 = one.id
                                                Inner JOIN managersteam AS two 
                                                    ON fantasymatch.team2 = two.id
                                                where fantasymatch.league = '$id' and fantasymatch.gameweek = '$gw';" );
        return $results;
    }
    
     //get fixtures/results for league on a gameweek based on gameweek number vs league average
    public function _getGWFixturesVsLeagueAverage($id, $gw)
    {
        $results = mysqli_query ( $this->con, "SELECT fantasymatch . * , two.name AS team2name
                                                FROM fantasymatch
                                                INNER JOIN managersteam AS two 
                                                    ON fantasymatch.team2 = two.id
                                                WHERE fantasymatch.league =  '$id'
                                                AND fantasymatch.gameweek =  '$gw'
                                                AND fantasymatch.team1 =  '-1';" );
        return $results;
    }
    
    //get the owner of a given player
    public function _getPlayersOwner($playerid, $leagueid)
    {
        $results = mysqli_query ( $this->con, "SELECT user.firstName, user.lastName, managersteam.name as owner, player.*, clubteam.fullName as teamName
                                                FROM fantasyleague
                                                left join managersteam
                                                    on fantasyleague.id = managersteam.league
                                                inner join user
                                                    on user.id = managersteam.manager
                                                inner join currentsquad
                                                    on currentsquad.teamId = managersteam.id
                                                Inner join player
                                                    on player.id = currentsquad.st1 or player.id = currentsquad.st2 or player.id = currentsquad.st3 or player.id = currentsquad.st4 or player.id = currentsquad.st5 or player.id = currentsquad.st6 or player.id = currentsquad.st7 or player.id = currentsquad.st8 or player.id = currentsquad.st9 or player.id = currentsquad.st10 or player.id = currentsquad.g1 or player.id = currentsquad.g2 or player.id = currentsquad.s1 or player.id = currentsquad.s2 or player.id = currentsquad.s3 or player.id = currentsquad.r1 or player.id = currentsquad.r2 or player.id = currentsquad.r3
                                                INNER join clubteam
                                                    on clubteam.id = player.teamId
                                                WHERE fantasyleague.id = $leagueid and player.id= $playerid;" );
        return $results;
    }
    
    //get list of all players in a given league
    public function _getLeaguePlayers($leagueid)
    {
        $results = mysqli_query ( $this->con, "SELECT managersteam.name as owner, player.*, clubteam.fullName as teamName
                                                FROM fantasyleague
                                                inner join managersteam
                                                    on fantasyleague.id = managersteam.league
                                                inner join currentsquad
                                                    on currentsquad.teamId = managersteam.id
                                                left join player
                                                    on player.id = currentsquad.st1 or player.id = currentsquad.st2 or player.id = currentsquad.st3 or player.id = currentsquad.st4 or player.id = currentsquad.st5 or player.id = currentsquad.st6 or player.id = currentsquad.st7 or player.id = currentsquad.st8 or player.id = currentsquad.st9 or player.id = currentsquad.st10 or player.id = currentsquad.g1 or player.id = currentsquad.g3 or player.id = currentsquad.g2 or player.id = currentsquad.s1 or player.id = currentsquad.s2 or player.id = currentsquad.s3 or player.id = currentsquad.r1 or player.id = currentsquad.r2 or player.id = currentsquad.r3 or player.id = currentsquad.r4 or player.id = currentsquad.r5 or player.id = currentsquad.r6 or player.id = currentsquad.r7 or player.id = currentsquad.r8 or player.id = currentsquad.r9 or player.id = currentsquad.r10 or player.id = currentsquad.r11 or player.id = currentsquad.r12 or player.id = currentsquad.r13 or player.id = currentsquad.r14 or player.id = currentsquad.r15
                                                INNER join clubteam
                                                    on clubteam.id = player.teamId
                                                WHERE fantasyleague.id = $leagueid;" );
        return $results;
    }
    
    //get list of free agents in a given league, a string of all players in the league are passed in
    public function _getLeagueFreeAgents($notThesePlayers, $positionString, $premTeamString, $nameString, $orderByString, $start_from, $num_rec_per_page)
    {
        $results = mysqli_query ( $this->con, "SELECT player.*, clubteam.fullName as teamName, clubteam.shortName as shortName, SUM(points.total) as totalPoints, SUM(points.gs) as goalsScored, SUM(points.a) as assists, SUM(points.cs) as cleanSheets, SUM(points.yc) as yellowCards, SUM(points.rc) as redCards, SUM(points.s) as savesMade, SUM(points.ps) as penaltiesSaved, SUM(points.mp) as minutesPlayed
                                                FROM player 
                                                Inner join points
                                                    on points.playerId = player.id
                                                Inner join clubteam
                                                    on clubteam.id = player.teamId 
                                                WHERE $notThesePlayers $positionString $premTeamString $nameString
                                                group by player.id
                                                $orderByString
                                                Limit $start_from, $num_rec_per_page;" );
        return $results;
    }

    
    //get details of all premier league teams
    public function _getPremTeams()
    {
        $results = mysqli_query ( $this->con, "Select * from clubteam where leagueId = 1 Order By fullName;");
        return $results;
    }
    
    //trade in a free agent to squad
    public function _tradeInPlayer($playerOutPosition, $playerInId, $teamId)
    {
        mysqli_query ( $this->con, "UPDATE currentsquad SET $playerOutPosition = $playerInId WHERE teamId =  $teamId;" );
    }
    
    //load first team in league
    public function _getFirstTeam($teamId, $leagueId)
    {
        $results = mysqli_query ( $this->con, "SELECT * FROM managersteam where league = $leagueId and id != $teamId order by name limit 0, 1;" );
        return $results;
    }
    
    //returns a list of all leagues opposition teams
    public function _loadOppositionTeamsAndManagers($leagueid, $teamId)
    {
        $results = mysqli_query ( $this->con, "SELECT user.firstName, user.lastName, managersteam. * , fantasyleague.name AS leagueName, fantasyleague.status AS 
STATUS , fantasyleague.id AS leagueId
                                                FROM managersteam
                                                INNER JOIN fantasyleague 
                                                    ON managersteam.league = fantasyleague.id
                                                inner join user
                                                    on user.id = managersteam.manager
                                                WHERE fantasyleague.id = $leagueid  and managersteam.id != $teamId
                                                ORDER BY managersteam.name;" );
        return $results;
    }
    
    //adds a new transfer offer to database and returns auto generated id
    public function _addNewOffer($leagueId, $team1, $team2, $date)
    {
        mysqli_query ( $this->con, "INSERT INTO transferoffer (leagueId,team1,team2,date)
                                              VALUES ('$leagueId','$team1','$team2','$date');" );
        
        $id = mysqli_insert_id($this->con);
        
        return $id;
    }
    
    //adds a players to offer in database
    public function _addPlayerToOffer($offerId, $player1, $player2)
    {
        mysqli_query ( $this->con, "INSERT INTO offeredplayers (offerId,player1,player2)
                                              VALUES ('$offerId','$player1','$player2');" );
    }
    
    //adds a new transfer offer to database and returns auto generated id
    public function _addNewHistory($leagueId, $team1, $team2, $date)
    {
        mysqli_query ( $this->con, "INSERT INTO transferhistory (leagueId,team1,team2,date)
                                              VALUES ('$leagueId','$team1','$team2','$date');" );
        
        $id = mysqli_insert_id($this->con);
        
        return $id;
    }
    
    //adds a players to offer in database
    public function _addPlayerToHistory($offerId, $player1, $player2)
    {
        mysqli_query ( $this->con, "INSERT INTO transferedplayers (historyId,player1,player2)
                                              VALUES ('$offerId','$player1','$player2');" );
    }
    
    //get open transfer offers involving a team
    public function _getOpenOffers($teamId)
    {
        $results = mysqli_query ( $this->con, "SELECT transferoffer.* , team1.name AS team1Name, team1.manager AS team1Manager, team2.name AS team2Name
                                                FROM  transferoffer 
                                                INNER JOIN managersteam AS team1 ON transferoffer.team1 = team1.id
                                                INNER JOIN managersteam AS team2 ON transferoffer.team2 = team2.id
                                                where team1 = $teamId or team2 = $teamId
                                                order by transferoffer.date desc, transferoffer.id desc;" );
        return $results;
    }
    
    //get details of single trade
    public function _getTrade($offerId)
    {
        $results = mysqli_query ( $this->con, "SELECT *
                                                FROM  transferoffer 
                                                where id = $offerId;" );
        return $results;
    }
    
    //get players involved in transfer offer
    public function _getPlayersInOffer($offerId)
    {
        $results = mysqli_query ( $this->con, "SELECT offeredplayers.*, player1.firstName as P1FN, player1.value as P1Value, player1.lastName as P1LN, player2.firstName as P2FN, player2.lastName as P2LN, player2.value as P2Value
                                                FROM  offeredplayers 
                                                INNER JOIN player AS player1 ON offeredplayers.player1 = player1.id
                                                INNER JOIN player AS player2 ON offeredplayers.player2 = player2.id
                                                where offerId = $offerId;" );
        return $results;
    }
    
    //delets a transfer offer, and players offered from database 
    public function _cancelOffer($offerId)
    {
        mysqli_query ( $this->con, "DELETE FROM transferoffer WHERE id = $offerId;");
        mysqli_query ( $this->con, "DELETE FROM offeredplayers WHERE offerId = $offerId;");
    }
    
    //finds other transfer offers in the league that involve a player in a confirmed transfer 
    public function _findOtherOffersWithPlayer($playerId, $offerId, $leagueId)
    {
        $results = mysqli_query ( $this->con, "SELECT offeredplayers.*, transferoffer.leagueId FROM offeredplayers 
                                    inner join transferoffer on offeredplayers.offerId = transferoffer.id
                                    WHERE player1 = $playerId and offerId !=$offerId and transferoffer.leagueId = $leagueId
                                    or player2 = $playerId and offerId != $offerId and transferoffer.leagueId = $leagueId;");
        return $results;
    }
    
    //get open transfer offers involving a team
    public function _getleagueTransfers($leagueId)
    {
        $results = mysqli_query ( $this->con, "SELECT transferhistory.* , team1.name AS team1Name, team1.manager AS team1Manager, team2.name AS team2Name
                                                FROM  transferhistory 
                                                INNER JOIN managersteam AS team1 ON transferhistory.team1 = team1.id
                                                INNER JOIN managersteam AS team2 ON transferhistory.team2 = team2.id
                                                where leagueId = $leagueId
                                                order by transferhistory.date desc, transferhistory.id desc;" );
        return $results;
    }
    
    //get players involved in transfer offer
    public function _getPlayersInHistory($historyId)
    {
        $results = mysqli_query ( $this->con, "SELECT transferedplayers.*, player1.value as P1Value, player1.firstName as P1FN, player1.lastName as P1LN, player2.firstName as P2FN, player2.lastName as P2LN, player2.value as P2Value
                                                FROM  transferedplayers 
                                                INNER JOIN player AS player1 ON transferedplayers.player1 = player1.id
                                                INNER JOIN player AS player2 ON transferedplayers.player2 = player2.id
                                                where historyId = $historyId;" );
        return $results;
    }
    
    public function _addNewFantasyFixture($gameweek, $leagueId, $team1, $team2)
    {
         mysqli_query ( $this->con, "INSERT INTO fantasymatch (gameweek,league,team1,team2)
                                              VALUES ('$gameweek','$leagueId','$team1','$team2')" );
    }
    
    //gets the league id and managers id of a managers team
    public function _getTeamsLeague($teamId)
    {
        $results = mysqli_query ($this->con, "SELECT user.id AS managerid, managersteam.league AS teamleague, managersteam.manager AS teamsmanager, fantasyleague.id AS leagueid, fantasyleague.admin AS leagueadmin
                                                FROM user
                                                INNER JOIN managersteam ON user.id = managersteam.manager
                                                INNER JOIN fantasyleague ON fantasyleague.id = managersteam.league
                                                WHERE managersteam.id = $teamId;");
        return $results;
    }
    
    //getcount of number of transer request a team has got
    public function _getTeamsTransferRequests($teamId)
    {
        $results = mysqli_query ($this->con, "SELECT COUNT( id ) AS numOffers
                                                FROM  transferoffer 
                                                WHERE  team2 =$teamId;");
        return $results;
    }
    
    //get draft order for specific league/draft
    public function getDraftOrder($leagueId)
    {
        $results = mysqli_query ($this->con, "SELECT draft.pickNumber AS nextPick, draftorder . * , managersteam.name AS teamName
                                                FROM  draftorder 
                                                INNER JOIN draft ON draft.id = draftorder.draftId
                                                INNER JOIN managersteam ON draftorder.teamid = managersteam.id
                                                WHERE  draft.leagueId = $leagueId;");
        return $results;
    }
    
    //adds a player selected in draft into scurrent squad
    public function addDraftSelection($squadId, $playerId, $squadPosition)
    {
        mysqli_query ($this->con, "UPDATE currentsquad SET $squadPosition = $playerId WHERE teamId = $squadId;");
    }
    
    //get draft id based on squad id
    public function getDraftDetails($squadId)
    {
        $results = mysqli_query ($this->con, "SELECT draft.* FROM draft
                                                inner join fantasyleague
                                                on fantasyleague.id = draft.leagueId
                                                inner join managersteam
                                                on managersteam.league = fantasyleague.id
                                                where managersteam.id = $squadId;");
        return $results;
    }
    
    //adds draft pick to history
    public function addDraftSelectionHistory($squadId, $playerId, $draftId)
    {
        mysqli_query ($this->con, "INSERT INTO draftpicks(draftId, teamId, playerId) VALUES ('$draftId', '$squadId', '$playerId');");
    }
    
    //get draft picks history
    public function getDraftPicks($draftId)
    {
        $results = mysqli_query ($this->con, "SELECT managersteam.name as teamName, player.webName as playerName FROM draftpicks
                                                inner join player
                                                on player.id = draftpicks.playerId
                                                inner join managersteam
                                                on managersteam.id = draftpicks.teamId
                                                WHERE draftpicks.draftid = $draftId
                                                order by draftpicks.id ");
        return $results;
    }
    
    //updates draft pick details
    public function updateDraftPick($draftId, $teamId, $pickNumber)
    {
        mysqli_query ($this->con, "UPDATE draft SET teamsPick = $teamId , pickNumber = $pickNumber WHERE id = $draftId;");
    }
}
?>
