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

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$manager = $_SESSION['userObj'];
		$userID = $manager->getUserID();
	}
	else 
	{
		redirect_to('index.php');
	}
	
	// Establish database connection
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted	
	{
		$eventid = $_POST['z'];

		// Create event object for use & pull latest data from database & initially set attributes
		$event = new Event();
		$event->setDB($db);
		$event->setEventID($eventid);
		$event->pullEventData();
		
		// Check if user is authroized to make edit
		if (!$event->isManager($userID)) {
			echo 'You have to be the manager to edit a event.';
			exit();
		}

		$oldDate = $event->getEventAttribute('gdate');
		$oldTime = $event->getEventAttribute('gtime');
		$oldOpp = $event->getEventAttribute('opponent');
		$oldVen = $event->getEventAttribute('venue');
		$oldRes = $event->getEventAttribute('result');
		$oldNote = $event->getEventAttribute('note');
						
		// Validate event date
		if ($_POST['dateEdit']) {
			$bd = new DateTime($_POST['dateEdit']); // Convert js datepicker entry into format database accepts
			$gdfrmat = $bd->format('Y-m-d');
		}
		else {
			$gdfrmat = $oldDate;
		}		
		
		// Validate event time is entered
		if (!empty($_POST['time'])) {
			$tm = $_POST['time'];
		}
		else {
			$tm = $oldTime;
		}

		// Validate opponent is entered
		if (is_string($_POST['opp']) && !empty($_POST['opp'])) {
			$opp = $_POST['opp'];
		}
		else {
			$opp = $oldOpp;
		}

		// Validate a venue is entered
		if (is_string($_POST['ven']) && !empty($_POST['ven'])) {
			$ven = $_POST['ven'];
		}
		else {
			$ven = $oldVen; 
		}

		// Validate a result is selected
		if (is_string($_POST['res']) && !empty($_POST['res'])) {
			$res = $_POST['res'];
		}
		else {
			$res = $oldRes; 
		}

		// Validate a note is entered
		if (is_string($_POST['note']) && !empty($_POST['note'])) {
			$note = $_POST['note'];
		}
		else {
			$note = $oldNote; 
		}	
	
		// Check if user entered information is valid before continuing to edit event
		if ($gdfrmat && $tm)
		{
			$event->editEvent($userID, $gdfrmat, $tm, $opp, $ven, $res, $note);
		}
		else
		{	// Errors in the user entered information
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
	unset($manager);
			
	// Close the connection:
	$db->close();
	unset($db);

?>