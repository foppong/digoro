<?php
	// add_event.php
	// This page allows a logged-in user to add a event to the schedule

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

		// Create team object for use & pull latest data from database & initially set attributes - used to add event
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
		
		// Validate event date
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
		
		// Validate event time is entered
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

		// Checks if team is selected and date format and entered time are valid before adding event to team.
		if ($ctmID && $gdfrmat && $tm)
		{
			// Create event object for use & push event to database for specified team
			$event = new Event();
			$event->setDB($db);
			$event->createEvent($ctmID, $gdfrmat, $tm, $opp, $ven, $res, $note);		
		}
		else 
		{									
			echo 'Please try again';
			exit();
		}
	}
	else 
	{
		// Accsessed without posting to form
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();		
	}

	// Delete objects
	unset($event);
	unset($manager);

	// Close the connection:
	$db->close();
	unset($db);		

?>
