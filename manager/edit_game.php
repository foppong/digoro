<?php
	// This page is for editing a game
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
		$gameid = $_POST['z'];

		// Create game object for use & pull latest data from database & initially set attributes
		$game = new Game();
		$game->setDB($db);
		$game->setGameID($gameid);
		$game->pullGameData();
		
		// Check if user is authroized to make edit
		if (!$game->isManager($userID)) {
			echo 'You have to be the manager to edit a game.';
			exit();
		}

		$oldDate = $game->getGameAttribute('gdate');
		$oldTime = $game->getGameAttribute('gtime');
		$oldOpp = $game->getGameAttribute('opponent');
		$oldVen = $game->getGameAttribute('venue');
		$oldRes = $game->getGameAttribute('result');
		$oldNote = $game->getGameAttribute('note');
						
		// Validate game date
		if ($_POST['dateEdit']) {
			$bd = new DateTime($_POST['dateEdit']); // Convert js datepicker entry into format database accepts
			$gdfrmat = $bd->format('Y-m-d');
		}
		else {
			$gdfrmat = $oldDate;
		}		
		
		// Validate game time is entered
		if (is_string($_POST['time'] && !empty($_POST['time']))) {
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
	
		// Check if user entered information is valid before continuing to edit game
		if ($gdfrmat && $tm)
		{
			$game->editGame($userID, $gdfrmat, $tm, $opp, $ven, $res, $note);
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
	unset($game);
	unset($manager);
			
	// Close the connection:
	$db->close();
	unset($db);

?>