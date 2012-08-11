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

	// Establish database connection
	require_once MYSQL2;

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

	// Assign Database Resource to object
	$manager->setDB($db);

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted	
	{
		$teamid = $_POST['z'];

		// Create team object for use & pull latest data from database & initially set attributes
		$team = new Team();
		$team->setDB($db);
		$team->setTeamID($teamid);
		$team->pullTeamData();
		
		// Check if user is authroized to make edit
		if (!$team->isManager($userID))
		{
			echo 'You have to be the manager to make these changes.';
			exit();
		}

		// Collect current attributes
		$oldname = $team->getTeamAttribute("tmname");
		$oldabtm = $team->getTeamAttribute("about");

		// Validate team name
		if ($_POST['tname'])
		{
			$tname = $_POST['tname'];	
		}
		else 
		{
			$tname = $oldname;
		}		
	
		// Validate about team information
		if ($_POST['abouttm'])
		{
			$abtm = trim($_POST['abouttm']);
		}
		else 
		{
			$abtm = $oldabtm;
		}

		// Validate transfer decision
		if (isset($_POST['transfer'])) {
			$transfer = $_POST['transfer'];	
		}
		else {
			$transfer = FALSE;
		}

		// Validate email
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{
			$e = $_POST['email'];
		}
		else 
		{
			$e = FALSE;
		}
		
		// Check if user entered information is valid before continuing to edit game
		if ($tname) {
			$team->editTeam($tname, $abtm);
		}
		else
		{	// Errors in the user entered information
			echo 'Please try again';
			exit();
		}

		// Check if transfer option is set and correct
		if ($transfer == 'Yes' && $e) {
			$team->transferTeam($e);
		}

	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();		
	}

	// Delete objects
	unset($team);
	unset($manager);

	// Close the connection:
	$db->close();
	unset($db);

?>