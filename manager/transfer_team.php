<?php
	// This page is for transferring team ownership
	
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
		$memberUserID = FALSE;

		// Validate transfer member
		if ($_POST['transfermember']) {
			$memberUserID = $_POST['transfermember'];
		}
		else {
			echo 'Please select a member to transfer team ownership to';
			exit();
		}
		
		// Check if user entered information is valid before continuing to edit game
		if ($memberUserID) {
			$team->transferTeam($memberUserID, $teamid);
		}
		else {
			echo 'Transfer not made';
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
	unset($manager);

	// Close the connection:
	$db->close();
	unset($db);

?>