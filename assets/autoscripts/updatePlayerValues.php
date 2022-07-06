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
				$value = $players[$i]['now_cost'];
				$news = $players[$i]['news'];


               //Update Player in database
               mysqli_query ( $con,"UPDATE player SET value='$value', news='$news' WHERE id = $id;" );

				echo '<p> Updated ID: ' . $id . ' Value: £' . $value .  '  News: ' . $news . ' INSERTED!</p>';

            }

       }
       else
       {
           echo 'nope';
       }
		?>

</body>

</html>
