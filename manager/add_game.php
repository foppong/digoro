<?php
	// add_game.php
	// This page allows a logged-in user to add a game to the schedule

	ob_start();
	session_start();	
			
	require '../includes/config.php';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$manager = $_SESSION['userObj'];
		$userID = $manager->getUserID();
		$ctmID = $_SESSION['ctmID']; //Retrieve current team in session variable
	}
	else 
	{
		redirect_to('index.php');
	}

	// Establish database connection
	require_once MYSQL2;
	
	// Retrieve current team ID in session
	$ctmID = $_SESSION['ctmID'];

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{

		// Create team object for use & pull latest data from database & initially set attributes - used to add game
		$team = new Team();
		$team->setDB($db);
		$team->setTeamID($ctmID);
		$team->pullTeamData();

		// Check if user is authroized to make edit
		if (!$team->isManager($userID)) {
			echo 'You have to be the manager to add a member.';
			exit();
		}
		
		// Assume invalid values:
		$gdfrmat = $tm = FALSE;
		
		// Validate game date
		if ($_POST['dateAdd'])
		{
			$bd = new DateTime($_POST['dateAdd']); // Convert js datepicker entry into format database accepts
			$gdfrmat = $bd->format('Y-m-d');
		}
		else 
		{
			echo 'Please enter a date';
			exit();
		}		
		
		// Validate game time is entered
		if ($_POST['time'])
		{
			$tm = $_POST['time'];
		}
		else 
		{
			echo 'Please enter a time';
			exit();
		}
	
		// Validate opponent is entered
		if ($_POST['opp'])
		{
			$opp = $_POST['opp'];
		}
		else 
		{
			$opp = '';
		}

		// Validate a venue is entered
		if ($_POST['ven'])
		{
			$ven = $_POST['ven'];
		}
		else 
		{
			$ven = ''; 
		}

		// Validate a result is enetered
		if ($_POST['res'])
		{
			$res = $_POST['res'];
		}
		else 
		{
			$res = ''; 
		}

		// Validate a note is enetered
		if ($_POST['note'])
		{
			$note = $_POST['note'];
		}
		else 
		{
			$note = ''; 
		}

		// Checks if team is selected and date format and entered time are valid before adding game to team.
		if ($ctmID && $gdfrmat && $tm)
		{
			// Create game object for use & push game to database for specified team
			$game = new Game();
			$game->setDB($db);
			$game->createGame($ctmID, $gdfrmat, $tm, $opp, $ven, $res, $note);		
		}
		else 
		{									
			echo 'Please try again';
			exit();
		}
	}

	// Delete objects
	unset($game);
	unset($manager);

	// Close the connection:
	$db->close();
	unset($db);		

?>
