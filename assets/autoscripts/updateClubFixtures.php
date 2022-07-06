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
		
		//set var to highest number of players in loop
		$limit = 1000;
    
        //clear current club matches
        mysqli_query ( $con,"TRUNCATE clubmatch;" ); 
    
        echo 'cleared Table';
        
        //var to hold id of last team
        $lastTeam = 0; 
    
		//loop through fantasy football json pages
		for ($x = 1; $x <= $limit; $x++)
		{
		//check if url exists
			$url = 'https://fantasy.premierleague.com/drf/element-summary/' . $x;
			$array = get_headers($url);
			$string = $array[0];
			//if it exists get the details
			if(strpos($string,"200")) 
			{
				$jsonFile = file_get_contents($url);
			
				$jsonDecoded = json_decode($jsonFile, true);

                $isHome = $jsonDecoded['fixtures'][0]["is_home"];
                if($isHome == true)
                {
                    $team_id = $jsonDecoded['fixtures'][0]["team_h"];
                }
                else
                {
                    $team_id = $jsonDecoded['fixtures'][0]["team_a"];
                }

                
                echo '<p>updating for ' .$team_id. '</p>';
                
                //get size of fixture history all array
                $allSize = count($jsonDecoded['fixtures']);
                
                //if the team being checked is different to the last team checked loop through fixtures
                if($lastTeam != $team_id)
                {
                    //loop through all array
                    for ($e = 0; $e < $allSize; $e++)
                    {
                        echo '<p>---------Game ' .$e. '</p>';
                        //get required details from array
                        $date = $jsonDecoded['fixtures'][$e]['kickoff_time_formatted'];
                        $gameweek = $jsonDecoded['fixtures'][$e]['event'];
                        $isHome = $jsonDecoded['fixtures'][$e]["is_home"];
                        if($isHome == true)
                        {
                            $opponent = $jsonDecoded['fixtures'][$e]['opponent_name']. ' (H)';
                            $opponentShort = $jsonDecoded['fixtures'][$e]['opponent_short_name']. ' (H)';
                        }
                        else
                        {
                            $opponent = $jsonDecoded['fixtures'][$e]['opponent_name']. ' (A)';
                            $opponentShort = $jsonDecoded['fixtures'][$e]['opponent_short_name']. ' (A)';
                        }

                        //add Game to database
                    mysqli_query ( $con,"INSERT INTO clubmatch (teamId, gameweek, date, opponent, opponent_short)
                                        VALUES ('$team_id', '$gameweek', '$date', '$opponent', '$opponentShort');" );
                    }   
                }
                
                //set last team equal to team of player just slooked at
                $lastTeam = $team_id;
			} 
			else 
			//if it doesnt exist set limit to current "$x" value to stop loop
			{
				$limit = $x;
			}
		
		}
		
		?>

</body>

</html>
