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
		$typ = $gdfrmat = $evtm = $ven = $venadd = FALSE;

		// Validate event type is selected
		if ($_POST['add-event-sel-type'])
		{
			$typ = $_POST['add-event-sel-type'];
		}
		else 
		{
			echo 'Please select event type';
			exit();
		}

		// Validate event date
		if ($_POST['add-event-sel-date'])
		{
			$bd = new DateTime($_POST['add-event-sel-date']); // Convert js datepicker entry into format database accepts
			$gdfrmat = $bd->format('Y-m-d');
		}
		else 
		{
			echo 'Please enter a date';
			exit();
		}		
		
		// Validate event time
		if ($_POST['add-event-time'])
		{
			$evtm = $_POST['add-event-time'];
		}
		else 
		{
			echo 'Please enter a time';
			exit();
		}
	
		// Validate opponent is entered
		if ($_POST['add-event-opname'])
		{
			$opp = $_POST['add-event-opname'];
		}
		else 
		{
			$opp = '';
		}

		// Validate a venue is entered
		if ($_POST['add-event-vname'])
		{
			$ven = $_POST['add-event-vname'];
		}
		else 
		{
			echo 'Please enter a venue name';
			exit();
		}

		// Validate venue address is enetered
		if ($_POST['add-event-vadd'])
		{
			$venadd = $_POST['add-event-vadd'];
		}
		else 
		{
			echo 'Please enter a venue address';
			exit();
		}

		// Validate note
		if ($_POST['add-event-note'])
		{
			$note = $_POST['add-event-note'];
		}
		else 
		{
			$note = ''; 
		}

		// Checks if team is selected and date format and entered time are valid before adding event to team.
		if ($ctmID && $gdfrmat && $typ && $evtm && $ven && $venadd)
		{
			// Create event object for use & push event to database for specified team
			$event = new Event();
			$event->setDB($db);
			$event->createEvent($ctmID, $gdfrmat, $evtm, $opp, $ven, $venadd, $note, $typ);		
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
