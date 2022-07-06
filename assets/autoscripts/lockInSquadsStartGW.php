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
    
        //get all current squads
        $result = mysqli_query ( $con, "SELECT * FROM currentsquad;" );
        
        //loop through squads and save to gameweek squad
        while ($row = $result->fetch_assoc()) 
        {
            //get squad details
            $teamId = $row['teamId'];
            $g1 = $row['g1'];              
            $st1 = $row['st1'];            
            $st2 = $row['st2'];               
            $st3 = $row['st3'];              
            $st4 = $row['st4'];            
            $st5 = $row['st5'];            
            $st6 = $row['st6'];            
            $st7 = $row['st7'];
            $st8 = $row['st8'];            
            $st9 = $row['st9'];            
            $st10 = $row['st10'];            
            $g2 = $row['g2'];           
            $s1 = $row['s1'];             
            $s2 = $row['s2'];             
            $s3 = $row['s3'];
                
            //write to db
            mysqli_query ( $con,"INSERT INTO gameweekteam(teamId, gameweek, g1, st1, st2, st3, st4, st5, st6, st7, st8, st9, st10, g2, s1, s2, s3) VALUES ( '$teamId', '$gameweek', '$g1', '$st1', '$st2', '$st3', '$st4', '$st5', '$st6', '$st7', '$st8', '$st9', '$st10', '$g2', '$s1', '$s2', '$s3' );" );
        }
    
        //update database to set gameweek status to 1 or ongoing
        mysqli_query ( $con,"UPDATE gameweek SET status = 1 WHERE id = $gameweek;" );
    
        //update database to set fantasy matches status to 1 or ongoing
        mysqli_query ( $con,"UPDATE fantasymatch SET status = 1 WHERE gameweek = $gameweek;" );
            echo 'Job Done!';
        
		?>

</body>

</html>
