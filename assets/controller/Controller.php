<?php

include_once("assets/model/draft.php");
include_once("assets/model/league.php");
include_once("assets/model/player.php");
include_once("assets/model/premTeam.php");
include_once("assets/model/squad.php");
include_once("assets/model/transfer.php");
include_once("assets/model/user.php");
include_once("assets/model/database.php");

class Controller {
    
    var $draft;
    var $league;
    var $player;
    var $premTeam;
    var $squad;
    var $transfer;
    var $user;
    var $database;
	
    //contructor
    public
    function __construct()      
	{
       $this->draft = new draft();    
       $this->league = new league();
       $this->player = new player();
       $this->premTeam = new premTeam();
       $this->squad = new squad();
       $this->transfer = new transfer();
       $this->user = new user();
    }

    public
    function invoke()
	{
		if(isset($_SESSION['id']))
		{
            if (isset($_GET['admin']))
            {
				if($_SESSION['admin'] == 1)
                {
                    include('assets/view/admin.php');
                }
                else
                {
                    echo 'why are you here? <a href = "index.php?dash">Go Back</a>';
                }
            }
            else if (isset($_GET['squad']))
            {
				include('assets/view/squad.php');
            } 
            else if (isset($_GET['changeviewsquad']))
            {
				$this->squad->viewSquad();
            } 
            else if (isset($_GET['viewsquad']))
            {
				include('assets/view/viewSquad.php');
            } 
            else if (isset($_GET['logout']))        
            {
                $this->user->logout();
            } 
            else if (isset($_GET['dash']))        
            {
                include('assets/view/dashboard.php');
            }
            else if (isset($_GET['transfer']) && isset($_GET['offer']))        
            {
                include('assets/view/transferOffer.php');
            }
            else if (isset($_GET['transfer']) && isset($_GET['open']))        
            {
                include('assets/view/transferOpen.php');
            }
            else if (isset($_GET['transfer']) && isset($_GET['history']))        
            {
                include('assets/view/transferHistory.php');
            }
            else if (isset($_GET['transfer']))        
            {
                include('assets/view/transfer.php');
            }
            else if (isset($_GET['offerTrade']))        
            {
                $this->transfer->offerTrade();
            }
            else if (isset($_GET['cancelOffer']))        
            {
                $this->transfer->cancelOffer();
            }
            else if (isset($_GET['acceptOffer']))        
            {
                $this->transfer->confirmTrade();
            }
            else if (isset($_GET['getOppositionTeam']))        
            {
                $this->squad->loadOppositionSquadForTransferById();
            }
            else if (isset($_GET['table']))        
            {
                include('assets/view/table.php');
            }
            else if (isset($_GET['makeSub']))        
            {
                $this->squad->makeSub();
            }
            else if (isset($_GET['getFreeAgents']))        
            {
                $this->league->getFreeAgents();
            }
            else if (isset($_GET['freeAgentsTrade']))        
            {
                $this->transfer->freeAgentsTrade();
            }
            else if (isset($_GET['loadPlayerDetails']))        
            {
                $this->player->loadPlayerDetails();
            }
            else if (isset($_GET['createLeague']))        
            {
                $this->league->createLeague();
            }
            else if (isset($_GET['joinLeague']))        
            {
                $this->league->joinLeague();
            }
            else if (isset($_GET['generatefixtures']))        
            {
                $this->league->generateFixtures();
            }
            else if (isset($_GET['loadDash']))        
            {
                $this->user->loadUsersLeagues();
            }
            else if (isset($_GET['changegw']))        
            {
                $this->league->getGWFixtures();
            }
            else if (isset($_GET['draft']))        
            {
                include('assets/view/draft.php');
            }
            else if (isset($_GET['checkdraftpick']))        
            {
                $this->draft->checkDraftPick();
            }
            else if (isset($_GET['getavailableplayers']))        
            {
                $this->draft->getAvailablePlayers();
            }
            else if (isset($_GET['getplayersperposition']))        
            {
                $this->draft->countPlayersPerPos();
            }
            else if (isset($_GET['getteamleaguedetails']))        
            {
                $this->draft->getTeamLeagueName();
            }
            else if (isset($_GET['getdraftorder']))        
            {
                $this->draft->getDraftOrder();
            }
            else if (isset($_GET['getdrafthistory']))        
            {
                $this->draft->getDraftHistory();
            }
            else if (isset($_GET['addplayertosquad']))        
            {
                $this->draft->addPlayerToSquad();
            }
            else if (isset($_GET['selectrandomplayer']))        
            {
                $this->draft->selectRandomPlayer();
            }
            else if (isset($_GET['startdraft']))        
            {
                $this->draft->startDraft();
            }
            else if (isset($_GET['test']))        
            {
                include('assets/view/test.php');
            }
			else
            {
			     include('assets/view/dashboard.php');
            }
		}
		else 
		{
            if (isset($_GET['wrongpassword']))
            {
				include('assets/view/homewp.php');
            } 
            else
            {
                include('assets/view/home.php');  
            }
		}
			
    }

}
?>
