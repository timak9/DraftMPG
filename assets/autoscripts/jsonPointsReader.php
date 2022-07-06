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
		
		//loop through fantasy football json pages
		for ($playerId = 1; $playerId <= $limit; $playerId++)
		{
            echo '<p>'+$playerId+'</p></br>';
            
		//check if url exists
			$url = 'https://fantasy.premierleague.com/drf/element-summary/' . $playerId;
			$array = get_headers($url);
			$string = $array[0];
			//if it exists get the details
			if(strpos($string,"200")) 
			{
				$jsonFile = file_get_contents($url);
				$jsonDecoded = json_decode($jsonFile, true);
                
                //get size of fixture history all array
                $allSize = count($jsonDecoded['history']);
                
                //get current date
                $date = date('y-m-d');
                
                //get current gameweek based on date
                $gwResults = mysqli_query ( $con, "SELECT * FROM gameweek where '$date' between startDate and endDate;" );
                $gwRow = $gwResults->fetch_assoc();
                $todaysGameweek = $gwRow['id'];;
				
                //loop through all array
                for ($e = 0; $e < $allSize; $e++)
                {
                    //get required details from array
                    $date = $jsonDecoded['history'][$e]["kickoff_time_formatted"];
                    $playerGameweek = $jsonDecoded['history'][$e]["round"];

                    $opponent = $jsonDecoded['history'][$e]["opponent_team"];
                    $wasHome = $jsonDecoded['history'][$e]["was_home"];
                    $homeScore = $jsonDecoded['history'][$e]["team_h_score"];
                    $awayScore = $jsonDecoded['history'][$e]["team_a_score"];
                    if($wasHome == true)
                    {
                        $homeString = "(H) ".$homeScore."-".$awayScore;
                    }
                    else
                    {
                        $homeString = "(A) ".$awayScore."-".$homeScore;
                    }
                    $opponentNameResults = mysqli_query ( $con, "SELECT * FROM clubteam where id = '$opponent';" );
                    $opponentNameRow = $opponentNameResults->fetch_assoc();
                    $opponentName = $opponentNameRow['shortName'];
                    $opponentString = $opponentName." ".$homeString;

                    $minutesPlayed = $jsonDecoded['history'][$e]["minutes"];
                    $GoalsScored = $jsonDecoded['history'][$e]["goals_scored"];
                    $assists = $jsonDecoded['history'][$e]["assists"];
                    $cleansheets = $jsonDecoded['history'][$e]["clean_sheets"];
                    $goalsConceded = $jsonDecoded['history'][$e]["goals_conceded"];
                    $ownGoals = $jsonDecoded['history'][$e]["own_goals"];
                    $penaltiesSaved = $jsonDecoded['history'][$e]["penalties_saved"];
                    $penaltiesMissed = $jsonDecoded['history'][$e]["penalties_missed"];
                    $yellowCards = $jsonDecoded['history'][$e]["yellow_cards"];
                    $redCards = $jsonDecoded['history'][$e]["red_cards"];
                    $savesMade = $jsonDecoded['history'][$e]["saves"];
                    $bonus = $jsonDecoded['history'][$e]["bonus"];
                    $value = $jsonDecoded['history'][$e]["value"];
                    $points = $jsonDecoded['history'][$e]["total_points"];

                    //check to see if the match date is the same as todays date, if it is add to database
                    if($playerGameweek == $todaysGameweek)
                    {
                        //update players points details in db
                        mysqli_query ( $con,"UPDATE points SET gameweek='$playerGameweek', opponentResult='$opponentString', mp='$minutesPlayed', gs='$GoalsScored', a='$assists', cs='$cleansheets', gc='$goalsConceded', og='$ownGoals', ps='$penaltiesSaved', pm='$penaltiesMissed', yc='$yellowCards', rc='$redCards', s='$savesMade', b='$bonus', total='$points' WHERE playerId = $playerId and dateTime = '$date';" );
                    }  

                }
                
			} 
			else 
			//if it doesnt exist set limit to current "$playerId" value to stop loop
			{
				$limit = $playerId;
			}
		}
		      echo 'Job done!';
		?>

</body>

</html>
