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
    
        //get all gameweek squads for given week
        $allSquadsResults = mysqli_query ( $con, "SELECT * FROM gameweekteam where gameweek = $gameweek;" );
    
        while ($allSquadsRow = $allSquadsResults->fetch_assoc())
        {
            //set squads id
            $squadId = $allSquadsRow['teamId'];
            
            //test goalkeepers to see if they should be swapped
            //set goalkeeper 1s id
            $gk1 = $allSquadsRow['g1'];
            
            //get starting goalkeepers details 
            $goalie =mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total, SUM(points.mp) AS mins
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = $gk1
                                                AND points.gameweek = $gameweek;" );
            $goalieRow = $goalie->fetch_assoc();
            //check if goallkeeper played
            if($goalieRow['mins'] == 0)
            {
                //if they didn't
                //set goalkeeper 2s id
                $gk2 = $allSquadsRow['g2'];
                
                //get sub goalies details
                $goalie2 =mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total, SUM(points.mp) AS mins
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id = $gk2
                                                AND points.gameweek = $gameweek;" );
                $goalieRow2 = $goalie2->fetch_assoc();
                //check if sub goalie played
                if($goalieRow2['mins'] != 0)
                {
                    //if they did update the gameweek squad to bring on sub
                    mysqli_query ( $con, "UPDATE gameweekteam SET g1=$gk2, g2=$gk1 WHERE teamId = $squadId and gameweek = $gameweek;" );
                }
            }
            
            //set variables to store number of starters per position
            $dfs = 0; $mfs = 0; $fws = 0;
            
            //loop through starting players and count players per position
            for($i=1; $i<=10; $i++)
            {            
                $id = "st" . $i;
                $playerId = $allSquadsRow[$id];
                $player = mysqli_query ( $con, "SELECT player.*, clubteam.fullName as teamName
                                                FROM player
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                WHERE player.id ='$playerId';" );
                $playerRow = $player->fetch_assoc();
                if($playerRow['position'] == "Defender")
                {
                    $dfs++;
                }
                else if($playerRow['position'] == "Midfielder")
                {
                    $mfs++;
                }
                else if($playerRow['position'] == "Forward")
                {
                    $fws++;
                }
            }
            
            //loop through players and adjust subs
            for($i=1; $i<=10; $i++)
            {
                //load up new squad list for every iteration
                $thisSquadsResults = mysqli_query ( $con, "SELECT * FROM gameweekteam where gameweek = $gameweek and teamId = $squadId;" );
                $thisSquadsRow = $thisSquadsResults->fetch_assoc();
                
                $stId = "st" . $i;
                //set players id
                $starterId = $thisSquadsRow[$stId];
                //get starting players details 
                $starter =mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total, SUM(points.mp) AS mins
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                        WHERE player.id = $starterId
                                                        AND points.gameweek = $gameweek;" );
                $starterRow = $starter->fetch_assoc();
                //if player is a defender
                if($starterRow['position'] == "Defender")
                {
                    //check if player played
                    if($starterRow['mins'] == 0)
                    {
                        //if they didn't
                        //loop through subs and make sub if possible
                        for($b=1; $b<=3; $b++)
                        {
                            $sId = "s" . $b;
                            //set sub id
                            $subId = $thisSquadsRow[$sId];
                            
                            //get sub players details 
                            $sub =mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total, SUM(points.mp) AS mins
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                            WHERE player.id = $subId
                                                            AND points.gameweek = $gameweek;" );
                            $subRow = $sub->fetch_assoc();
                            //if there is only 3 defenders on team (the minimum) then the sub coming on must be a defender
                            if($dfs == 3)
                            {
                                //if sub played and their position is defender sub player in
                                if($subRow['mins'] != 0 && $subRow['position'] == "Defender")
                                {
                                    //if they did update the gameweek squad to bring on sub
                                    mysqli_query ( $con, "UPDATE gameweekteam SET $stId=$subId, $sId=$starterId WHERE teamId = $squadId and gameweek = $gameweek;" );
                                    //set loop variable to max to exit loop
                                    $b =100;
                                }
                            }
                            //otherwise any position sub can come on
                            else
                            {
                                //if sub played sub player in
                                if($subRow['mins'] != 0)
                                {
                                    //take one off starting defenders count for player subbed off
                                    $dfs--;
                                    
                                    //adjust player position variables depending on position of player brought on
                                    if($subRow['position'] == "Defender")
                                    {
                                        $dfs++;
                                    }
                                    else if($subRow['position'] == "Midfielder")
                                    {
                                        $mfs++;
                                    }
                                    else if($subRow['position'] == "Forward")
                                    {
                                        $fws++;
                                    }
                                    
                                    
                                    
                                    //if they did update the gameweek squad to bring on sub
                                    mysqli_query ( $con, "UPDATE gameweekteam SET $stId=$subId, $sId=$starterId WHERE teamId = $squadId and gameweek = $gameweek;" );
                                    //set loop variable to max to exit loop
                                    $b =100;
                                }   
                            }
                        }
                    }
                }
                else if($starterRow['position'] == "Midfielder")
                {
                    //check if player played
                    if($starterRow['mins'] == 0)
                    {
                        //if they didn't
                        //loop through subs and make sub if possible
                        for($b=1; $b<=3; $b++)
                        {
                            $sId = "s" . $b;
                            //set sub id
                            $subId = $thisSquadsRow[$sId];
                            
                            //get sub players details 
                            $sub =mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total, SUM(points.mp) AS mins
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                            WHERE player.id = $subId
                                                            AND points.gameweek = $gameweek;" );
                            $subRow = $sub->fetch_assoc();
                            //if there is only 2 midfielders on team (the minimum) then the sub coming on must be a midfielder
                            if($mfs == 2)
                            {
                                //if sub played and their position is midfielder sub player in
                                if($subRow['mins'] != 0 && $subRow['position'] == "Midfielder")
                                {
                                    //if they did update the gameweek squad to bring on sub
                                    mysqli_query ( $con, "UPDATE gameweekteam SET $stId=$subId, $sId=$starterId WHERE teamId = $squadId and gameweek = $gameweek;" );
                                    //set loop variable to max to exit loop
                                    $b =100;
                                }
                            }
                            //otherwise any position sub can come on
                            else
                            {
                                //if sub played sub player in
                                if($subRow['mins'] != 0)
                                {
                                    //take one off starting midfielders count for player subbed off
                                    $mfs--;
                                    
                                    //adjust player position variables depending on position of player brought on
                                    if($subRow['position'] == "Defender")
                                    {
                                        $dfs++;
                                    }
                                    else if($subRow['position'] == "Midfielder")
                                    {
                                        $mfs++;
                                    }
                                    else if($subRow['position'] == "Forward")
                                    {
                                        $fws++;
                                    }
                                    
                                    
                                    
                                    //if they did update the gameweek squad to bring on sub
                                    mysqli_query ( $con, "UPDATE gameweekteam SET $stId=$subId, $sId=$starterId WHERE teamId = $squadId and gameweek = $gameweek;" );
                                    //set loop variable to max to exit loop
                                    $b =100;
                                }   
                            }
                        }
                    }
                }
                else if($starterRow['position'] == "Forward")
                {
                    //check if player played
                    if($starterRow['mins'] == 0)
                    {
                        //if they didn't
                        //loop through subs and make sub if possible
                        for($b=1; $b<=3; $b++)
                        {
                            $sId = "s" . $b;
                            //set sub id
                            $subId = $thisSquadsRow[$sId];
                            
                            //get sub players details 
                            $sub =mysqli_query ( $con, "SELECT player.*, clubteam.fullName AS teamName, SUM(points.total) AS total, SUM(points.mp) AS mins
                                                FROM player
                                                INNER JOIN points ON points.playerId = player.id
                                                INNER JOIN clubteam ON player.teamId = clubteam.id
                                                            WHERE player.id = $subId
                                                            AND points.gameweek = $gameweek;" );
                            $subRow = $sub->fetch_assoc();
                            //if there is only 1 Forward on team (the minimum) then the sub coming on must be a Forward
                            if($fws == 1)
                            {
                                //if sub played and their position is Forward sub player in
                                if($subRow['mins'] != 0 && $subRow['position'] == "Forward")
                                {
                                    //if they did update the gameweek squad to bring on sub
                                    mysqli_query ( $con, "UPDATE gameweekteam SET $stId=$subId, $sId=$starterId WHERE teamId = $squadId and gameweek = $gameweek;" );
                                    //set loop variable to max to exit loop
                                    $b =100;
                                }
                            }
                            //otherwise any position sub can come on
                            else
                            {
                                //if sub played sub player in
                                if($subRow['mins'] != 0)
                                {
                                    //take one off starting Forwards count for player subbed off
                                    $fws--;
                                    
                                    //adjust player position variables depending on position of player brought on
                                    if($subRow['position'] == "Defender")
                                    {
                                        $dfs++;
                                    }
                                    else if($subRow['position'] == "Midfielder")
                                    {
                                        $mfs++;
                                    }
                                    else if($subRow['position'] == "Forward")
                                    {
                                        $fws++;
                                    }
                                    
                                    
                                    
                                    //if they did update the gameweek squad to bring on sub
                                    mysqli_query ( $con, "UPDATE gameweekteam SET $stId=$subId, $sId=$starterId WHERE teamId = $squadId and gameweek = $gameweek;" );
                                    //set loop variable to max to exit loop
                                    $b =100;
                                }   
                            }
                        }
                    }
                }
            }
        }
        
            echo 'Job Done!';
		?>

</body>

</html>
