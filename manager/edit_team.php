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

	// Site access level -> Manager
	$lvl = 'M'; 

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

	// Authorized Login Check
	if (!$manager->valid($lvl))
	{
		redirect_to('index.php');	
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted	
	{
		$id = $_POST['z'];

		// Create team object for use & pull latest data from database & initially set attributes
		$team = new Team();
		$team->setDB($db);
		$team->setTeamID($id);
		$team->pullTeamData();
		
		// Check if user is authroized to make edit
		if (!$team->isManager($userID))
		{
			echo 'You have to the manager to make this change.';
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
		
		// Check if user entered information is valid before continuing to edit game
		if ($tname)
		{
			$team->editTeam($tname, $abtm);
		}
		else
		{	// Errors in the user entered information
			echo 'Please try again';
			exit();
		}
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();		
	}

	//<div id="link"><a href=transfer_team.php?x=' . $id . '>Transfer Team</a>'; Put this with the myTeams page	
	
	// Delete objects
	unset($team);
	unset($manager);

	// Close the connection:
	$db->close();
	unset($db);

?>