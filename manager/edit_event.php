<?php
	// This page is for editing a event
	// This page is accessed through view_sch.php
	
	ob_start();
	session_start();	
		
	require '../includes/config.php';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Validate user
	checkSessionObject();	
	
	// Check user role
	checkRole('m');

	// Assign user object from session variable
	$user = $_SESSION['userObj'];
	$userID = $user->getUserID();
	
	// Establish database connection
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['z'])) // Confirmation that form has been submitted	
	{
		$eventid = $_POST['z'];

		// Create event object for use & pull latest data from database & initially set attributes
		$event = new Event();
		$event->setDB($db);
		$event->setEventID($eventid);
		
		// Check if user is authroized to make edit
		if (!$event->isManager($userID, $eventid)) {
			echo 'You have to be the manager to edit a event.';
			exit();
		}

		// Validate event type is selected
		if ($_POST['edit-event-sel-type']) {
			$typ = $_POST['edit-event-sel-type'];
		}
		else {
			echo 'Please select event type';
			exit();
		}
						
		// Validate event date
		if ($_POST['edit-event-sel-date']) {
			$bd = new DateTime($_POST['edit-event-sel-date']); // Convert js datepicker entry into format database accepts
			$gdfrmat = $bd->format('Y-m-d');
		}
		else {
			echo 'Please enter a date';
			exit();
		}			
		
		// Validate event time
		if ($_POST['edit-event-time']) {
			$evtm = $_POST['edit-event-time'];
		}
		else {
			echo 'Please enter a time';
			exit();
		}

		// Validate opponent is entered
		if ($_POST['edit-event-opname']) {
			$opp = $_POST['edit-event-opname'];
		}
		else {
			$opp = '';
		}

		// Validate a venue is entered
		if ($_POST['edit-event-vname']) {
			$ven = $_POST['edit-event-vname'];
		}
		else {
			echo 'Please enter a venue name';
			exit();
		}

		// Validate venue address is enetered
		if ($_POST['edit-event-vadd']) {
			$venadd = $_POST['edit-event-vadd'];
		}
		else {
			echo 'Please enter a venue address';
			exit();
		}

		// Validate note
		if ($_POST['edit-event-note']) {
			$note = $_POST['edit-event-note'];
		}
		else 
		{
			$note = ''; 
		}

		// Validate a result is selected
		if ($_POST['edit-event-res']) {
			$res = $_POST['edit-event-res'];
		}
		else {
			$res = ''; 
		}

	
		// Check if user entered information is valid before continuing to edit event
		if ($gdfrmat && $typ && $evtm && $ven && $venadd) {
			$event->editEvent($userID, $gdfrmat, $evtm, $opp, $ven, $venadd, $res, $note, $typ);
		}
		else {	// Errors in the user entered information
			echo 'Please try again';
			exit();
		}
	}
	else 
	{	// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();		
	}
		
	// Delete objects
	unset($event);
	unset($user);
			
	// Close the connection:
	$db->close();
	unset($db);

?>