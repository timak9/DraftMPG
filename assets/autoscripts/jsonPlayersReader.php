<html>

<head>
    <title>DFF</title>

</head>

<body>

    <?php
    //database connection
        $user = "root";
        $password = "";
        $host = "127.0.0.1";
        $database = "draftfantasyfootball";
        $con = new mysqli ($host, $user, $password, $database );

    //check if url exists
	   $url = 'https://fantasy.premierleague.com/drf/bootstrap-static';
	   $array = get_headers($url);
	   $string = $array[0];
	   //if it exists get the details
	   if(strpos($string,"200"))
	   {
            $jsonFile = file_get_contents($url);

            $jsonDecoded = json_decode($jsonFile, true);

        	$players = $jsonDecoded['elements'];
        	$teams = $jsonDecoded['teams'];

           for($i = 0; $i < count($players); $i++) {

                $id = $players[$i]['id'];
				$team_id = $players[$i]['team'];
				$first_name = $players[$i]['first_name'];
				$second_name = $players[$i]['second_name'];
				$value = $players[$i]['now_cost'];
				$webName = $players[$i]['web_name'];

				$news = $players[$i]['news'];

                if($players[$i]['element_type'] == 1)
                {
                    $position = 'Goalkeeper';
                }
                else if($players[$i]['element_type'] == 2)
                {
                    $position = 'Defender';
                }
                else if($players[$i]['element_type'] == 3)
                {
                    $position = 'Midfielder';
                }
                else
                {
                    $position = 'Forward';
                }


               //add Player to database
               mysqli_query ( $con,"INSERT INTO player (id, teamId, firstName, lastName, position, value, news, webName)
									VALUES ('$id', '$team_id', '$first_name', '$second_name', '$position', '$value',
									'$news', '$webName');" );


				echo '<p> ID: ' . $id . '  Team ID: ' . $team_id . '  Name: '  . $first_name . '  ' . $second_name . ' Pos: ' . $position .  '  £' . $value .  '  News: ' . $news . ' INSERTED!</p>';

            }

       }
       else
       {
           echo 'nope';
       }
?>

</body>

</html>
