<?php
	// This page is for deleting a team record
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

	// Need the database connection:
	require_once MYSQL2;
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['z'])) // Confirmation that form has been submitted	
	{
		$teamid = $_POST['z'];

		// Create team object for use & pull latest data from database & initially set attributes
		$team = new Team();
		$team->setDB($db);

		// Remove team instead of delete if User is not the manager
		if (!$team->isManager($userID, $teamid))
		{		
			$team->removeMember($userID);
			exit();
		}
		else {
			$team->deleteTeam($teamid);
			// Redirect user to home page after delete
			$url = BASE_URL . 'manager/my_teams.php';
			header("Location: $url");
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
	unset($user);
				
	// Close the connection:
	$db->close();
	unset($db);

?>