<?php

class premTeam 
{
	var $database;
	// -- Function Name : __construct
    // -- Params :
    // -- Purpose : Starts the connection to the database.
    public function __construct()    
	{
        $this->database = new database();
	}
    
    //prints out a select of prem teams names with ids as value
    public function getPremTeamsSelect()
    {
        //search for prem teams
        $result =  $this->database->_getPremTeams();
        
        echo '
                <select id="selectPremTeam">
                  <option value="Any">Any Team</option>';
        
        while ($row = $result->fetch_assoc()) 
        {
            echo '
                  <option value="'. $row['id'] .'">'. $row['fullName'] .'</option>
                  ';
        }
        
        
        echo'</select>';
    }
}
?>