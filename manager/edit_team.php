<?php
	// This page is for editing a team
	// This page is accessed through myteams-m.php
	
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
		$teamid = $_POST['z'];

		// Create team object for use & pull latest data from database & initially set attributes
		$team = new Team();
		$team->setDB($db);
		
		// Check if user is authroized to make edit
		if (!$team->isManager($userID, $teamid))
		{
			echo 'You have to be the manager to make these changes.';
			exit();
		}

		// Assume invalid values:
		$sp = $tn = $sex = $reg = $lvl = FALSE;
				
		// Validate a sport is selected
		if ($_POST['edit-team-sel-sport']) {
			$sp = $_POST['edit-team-sel-sport'];
		}
		else {
			echo 'Please select a sport.';
			exit(); 
		}

		// Validate Team name entered
		if ($_POST["edit-team-name"]) {
			$tn = $_POST["edit-team-name"];
		}
		else {
			echo 'Please enter a Team name.';
			exit();
		}

		// Validate Team sex selected
		if ($_POST['edit-team-sel-sex']) {
			$sex = $_POST['edit-team-sel-sex'];
		}
		else {
			echo 'Please enter your teams sex.';
			exit();
		}

		// Validate Team region selected
		if ($_POST['edit-team-sel-region']) {
			$reg = $_POST['edit-team-sel-region'];
		}
		else {
			echo 'Please enter your teams region.';
			exit();
		}

		// Validate Team level of play selected
		if ($_POST['edit-team-sel-level-play']) {
			$lvl = $_POST['edit-team-sel-level-play'];
		}
		else {
			echo 'Please enter your teams level of play.';
			exit();
		}

		// Validate email address
		if (filter_var($_POST['edit-team-email'], FILTER_VALIDATE_EMAIL)) {
			$e = $_POST['edit-team-email'];
		}
		else {
			$e = '';
		}
		
		// Validate about team information
		if ($_POST['edit-team-abouttm']) {
			$abtm = trim($_POST['edit-team-abouttm']);
		}
		else {
			$abtm = '';
		}
		
		// Check if user entered information is valid before continuing to edit game
		if ($userID && $sp && $tn && $sex && $reg && $lvl) {
			$team->editTeam($sp, $tn, $abtm, $lvl, $reg, $sex, $e, $teamid);
		}
		else {	// Errors in the user entered information
			echo 'Please try again';
			exit();
		}
	}
	else {
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();		
	}

	// Delete objects
	unset($team);
	unset($user);

	// Close the connection:
	$db->close();
	unset($db);

?>