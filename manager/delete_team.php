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

	// Site access level -> Manager
	$lvl = 'M'; 

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

	// Need the database connection:
	require_once MYSQL2;

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

		// Remove team instead of delete if User is not the manager
		if (!$team->isManager($userID))
		{
			
			$team->removeMember($userID);
			exit();
		}
		else {
			$team->deleteTeam();
		}

	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();
	}	

	// Delete objects
	unset($team);
	unset($manager);
				
	// Close the connection:
	$db->close();
	unset($db);

?>