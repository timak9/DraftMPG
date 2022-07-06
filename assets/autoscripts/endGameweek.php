<html>

<head>
    <title>DFF</title>
</head>

<body>

    <?php
        
        $user = "root";
        $password = "";
        $host = "127.0.0.1";
        $database = "draftfantasyfootball";
    
        $con = new mysqli ($host, $user, $password, $database );
    
		//remove php Maximum execution time limit
		set_time_limit(0);
		
        //get current date
        $date = date('y-m-d');
    
        //get current gameweek based on date
        $gwResults = mysqli_query ( $con, "SELECT * FROM gameweek where '$date' between startDate and endDate;" );
        $gwRow = $gwResults->fetch_assoc();
        $gameweek = $gwRow['id'];
    
        //update database to set gameweek status to 2 or finished
        mysqli_query ( $con,"UPDATE gameweek SET status = 2 WHERE id = $gameweek;" );
    
        //get all leagues
        $allLeaguesResults = mysqli_query ( $con, "SELECT * FROM fantasyleague;" );
    
        //loop through every league and update
        while ($allLeaguesRow = $allLeaguesResults->fetch_assoc())
        {
            //set league and id params
            $leagueid = $allLeaguesRow['id'];

            //search for league fixtures for gameweek
            $result = mysqli_query ( $con, "SELECT fantasymatch.*, one.name AS team1name, two.name AS team2name
                                                FROM fantasymatch	
                                                Inner JOIN managersteam AS one 
                                                    ON fantasymatch.team1 = one.id
                                                Inner JOIN managersteam AS two 
                                                    ON fantasymatch.team2 = two.id
                                                where fantasymatch.league = '$leagueid' and fantasymatch.status = 1 and fantasymatch.gameweek = '$gameweek';" );

            $count=mysqli_num_rows($result);
            //if no fixtures are returned print message
            if($count<1)
            {
                //no fixtures... do nothing
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

                    //calculate teams total scores 
                    //var to hold points totals
                    $team1TotalPoints = 0; $team2TotalPoints = 0;
                    
                    //get the team1s gameweek gameweek squads current points total for a said gameweek
                    $squadId = $row['team1'];                                                    
                    $pointsResult = mysqli_query ( $con, "SELECT gameweekteam.*, managersteam.manager as managerId, managersteam.name as teamName, user.id as managerId, user.firstName as firstName, user.lastName as lastName 
                                                FROM gameweekteam
                                                INNER JOIN managersteam ON managersteam.id = gameweekteam.teamId
                                                INNER JOIN user ON user.id = managersteam.manager
                                                where gameweek = $gameweek and teamId = $squadId;" );
                    
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

                                $goalie = mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = ${$goaliePos}
                                                AND points.gameweek = $gameweek;" );
                                $goalieRow = $goalie->fetch_assoc();
                                $starter = mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = ${$starterPos}
                                                AND points.gameweek = $gameweek;" );
                                $starterRow = $starter->fetch_assoc();

                                $goaliePoints = $goalieRow['total'];
                                $starterPoints = $starterRow['total'];


                                $team1TotalPoints = $team1TotalPoints + $goaliePoints;
                                $team1TotalPoints = $team1TotalPoints + $starterPoints;
                            }
                            else
                            {
                                $starterPos = "st".$i;

                                $starter = mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = ${$starterPos}
                                                AND points.gameweek = $gameweek;" );
                                $starterRow = $starter->fetch_assoc();

                                $starterPoints = $starterRow['total'];

                                $team1TotalPoints = $team1TotalPoints + $starterPoints;
                            }
                    }

                    //add team 1 points to league total points
                    $totalLeaguePoints = $totalLeaguePoints + $team1TotalPoints;
                    
                    //get the team2s gameweek gameweek squads current points total for a said gameweek
                    $squadId = $row['team2'];                                                     
                    $pointsResult = mysqli_query ( $con, "SELECT gameweekteam.*, managersteam.manager as managerId, managersteam.name as teamName, user.id as managerId, user.firstName as firstName, user.lastName as lastName 
                                                FROM gameweekteam
                                                INNER JOIN managersteam ON managersteam.id = gameweekteam.teamId
                                                INNER JOIN user ON user.id = managersteam.manager
                                                where gameweek = $gameweek and teamId = $squadId;" );
                    
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

                                $goalie = mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = ${$goaliePos}
                                                AND points.gameweek = $gameweek;" );
                                $goalieRow = $goalie->fetch_assoc();
                                $starter = mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = ${$starterPos}
                                                AND points.gameweek = $gameweek;" );
                                $starterRow = $starter->fetch_assoc();

                                $goaliePoints = $goalieRow['total'];
                                $starterPoints = $starterRow['total'];


                                $team2TotalPoints = $team2TotalPoints + $goaliePoints;
                                $team2TotalPoints = $team2TotalPoints + $starterPoints;
                            }
                            else
                            {
                                $starterPos = "st".$i;

                                $starter = mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = ${$starterPos}
                                                AND points.gameweek = $gameweek;" );
                                $starterRow = $starter->fetch_assoc();

                                $starterPoints = $starterRow['total'];

                                $team2TotalPoints = $team2TotalPoints + $starterPoints;
                            }
                    }

                    if($team1TotalPoints > $team2TotalPoints )
                    {
                        //if team 1 wins
                        //get game id and teams ids
                        $gameId = $row['id']; $team1id = $row['team1']; $team2id = $row['team2'];
                        //update game details
                        mysqli_query ( $con, "UPDATE fantasymatch SET team1score=$team1TotalPoints,team2score=$team2TotalPoints,status=2 WHERE id=$gameId;" );
                        //update team 1s details
                        mysqli_query ( $con, "UPDATE managersteam SET w= w + 1, totalPoints=totalPoints + 3,fantasyPoints= fantasyPoints + $team1TotalPoints WHERE id = $team1id" );
                        //update team 2s details
                        mysqli_query ( $con, "UPDATE managersteam SET l= l + 1, fantasyPoints= fantasyPoints + $team2TotalPoints WHERE id = $team2id" );
                    }
                    else if($team1TotalPoints < $team2TotalPoints )
                    {
                        //if team 2 wins
                        //get game id and teams ids
                        $gameId = $row['id']; $team1id = $row['team1']; $team2id = $row['team2'];
                        //update game details
                        mysqli_query ( $con, "UPDATE fantasymatch SET team1score=$team1TotalPoints,team2score=$team2TotalPoints,status=2 WHERE id=$gameId;" );
                        //update team 1s details
                        mysqli_query ( $con, "UPDATE managersteam SET l= l + 1, fantasyPoints= fantasyPoints + $team1TotalPoints WHERE id = $team1id" );
                        //update team 2s details
                        mysqli_query ( $con, "UPDATE managersteam SET w= w + 1, totalPoints=totalPoints + 3, fantasyPoints= fantasyPoints + $team2TotalPoints WHERE id = $team2id" );
                    }
                    else
                    {
                        //if team its a draw
                        //get game id and teams ids
                        $gameId = $row['id']; $team1id = $row['team1']; $team2id = $row['team2'];
                        //update game details
                        mysqli_query ( $con, "UPDATE fantasymatch SET team1score=$team1TotalPoints,team2score=$team2TotalPoints,status=2 WHERE id=$gameId;" );
                        //update team 1s details
                        mysqli_query ( $con, "UPDATE managersteam SET d= d + 1, totalPoints=totalPoints + 1, fantasyPoints= fantasyPoints + $team1TotalPoints WHERE id = $team1id" );
                        //update team 2s details
                        mysqli_query ( $con, "UPDATE managersteam SET d= d + 1, totalPoints=totalPoints + 1, fantasyPoints= fantasyPoints + $team2TotalPoints WHERE id = $team2id" );
                    }
                    
                    //add team 1 points to league total points
                    $totalLeaguePoints = $totalLeaguePoints + $team2TotalPoints;
                }   
            }
            
            //search for league fixtures for selected gameweek for games vs league average
            $resultVsLA = mysqli_query ( $con, "SELECT fantasymatch . * , two.name AS team2name
                                                FROM fantasymatch
                                                INNER JOIN managersteam AS two 
                                                    ON fantasymatch.team2 = two.id
                                                WHERE fantasymatch.league =  '$leagueid'
                                                AND fantasymatch.gameweek =  '$gameweek'  and fantasymatch.status = 1
                                                AND fantasymatch.team1 =  '-1';" );
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
                    $squadId = $rowVsLA['team2'];                                                     
                    $pointsResult = mysqli_query ( $con, "SELECT gameweekteam.*, managersteam.manager as managerId, managersteam.name as teamName, user.id as managerId, user.firstName as firstName, user.lastName as lastName 
                                                FROM gameweekteam
                                                INNER JOIN managersteam ON managersteam.id = gameweekteam.teamId
                                                INNER JOIN user ON user.id = managersteam.manager
                                                where gameweek = $gameweek and teamId = $squadId;" );
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
                            
                            $goalie = mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = ${$goaliePos}
                                                AND points.gameweek = $gameweek;" );
                                $goalieRow = $goalie->fetch_assoc();
                                $starter = mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = ${$starterPos}
                                                AND points.gameweek = $gameweek;" );
                                $starterRow = $starter->fetch_assoc();
                            
                            $goaliePoints = $goalieRow['total'];
                            $starterPoints = $starterRow['total'];

                            $teamVsLaTotalPoints = $teamVsLaTotalPoints + $goaliePoints;
                            $teamVsLaTotalPoints = $teamVsLaTotalPoints + $starterPoints;
                        }
                        else
                        {
                            $starterPos = "st".$i;

                                $starter = mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = ${$starterPos}
                                                AND points.gameweek = $gameweek;" );
                                $starterRow = $starter->fetch_assoc();
                            
                            $starterPoints = $starterRow['total'];

                            $teamVsLaTotalPoints = $teamVsLaTotalPoints + $starterPoints;
                        }
                    }
                    
                    //add team vs league average points to league total points
                    $totalLeaguePoints = $totalLeaguePoints + $teamVsLaTotalPoints;
                    
                    //work out the average points score for league teams
                    $leagueAverage = round($totalLeaguePoints / $numTeams);
                    
                    if($teamVsLaTotalPoints > $leagueAverage )
                    {
                        //if team vs league average wins
                        //get game id and teams ids
                        $gameId = $rowVsLA['id']; $teamId = $rowVsLA['team2'];
                        mysqli_query ( $con, "UPDATE fantasymatch SET team1score=$leagueAverage,team2score=$teamVsLaTotalPoints,status=2 WHERE id=$gameId;" );
                        //update team vs league average details
                        mysqli_query ( $con, "UPDATE managersteam SET w= w + 1, totalPoints=totalPoints + 3, fantasyPoints= fantasyPoints + $teamVsLaTotalPoints WHERE id = $teamId" );
                    }
                    else if($teamVsLaTotalPoints < $leagueAverage )
                    {
                        //if league average wins
                        //get game id and teams ids
                        $gameId = $rowVsLA['id']; $teamId = $rowVsLA['team2'];
                        mysqli_query ( $con, "UPDATE fantasymatch SET team1score=$leagueAverage,team2score=$teamVsLaTotalPoints,status=2 WHERE id=$gameId;" );
                        //update team vs league average details
                        mysqli_query ( $con, "UPDATE managersteam SET l= l + 1, fantasyPoints= fantasyPoints + $teamVsLaTotalPoints WHERE id = $teamId" );
                    }
                    else
                    {
                        //if team its a draw
                        //get game id and teams ids
                       $gameId = $rowVsLA['id']; $teamId = $rowVsLA['team2'];
                        mysqli_query ( $con, "UPDATE fantasymatch SET team1score=$leagueAverage,team2score=$teamVsLaTotalPoints,status=2 WHERE id=$gameId;" );
                        //update team vs league average details
                        mysqli_query ( $con, "UPDATE managersteam SET d= d + 1, totalPoints=totalPoints + 1, fantasyPoints= fantasyPoints + $teamVsLaTotalPoints WHERE id = $teamId" );
                    }
                }
            }
        }
            
            echo 'Job Done!';
		?>

</body>

</html>
