<?php
	// This page is for deleting a team record
	// This page is accessed through myteams-m.php
	
	require '../includes/config.php';
	$page_title = 'digoro : Delete Team';
	include '../includes/header.html';
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
	
	if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) ) // From view teams page
	{
		$id = $_GET['x'];

		// Create team object for use & pull latest data from database & initially set attributes
		$team = new ManagerTeam();
		$team->setDB($db);
		$team->setTeamID($id);
		$team->pullTeamData();
	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted from delete_team page	
	{
		$id = $_POST['z'];

		// Create team object for use & pull latest data from database & initially set attributes
		$team = new ManagerTeam();
		$team->setDB($db);
		$team->setTeamID($id);
		$team->pullTeamData();

		if ($_POST['sure'] == 'Yes')
		{	// If form submitted is yes, delete the record

			$team->deleteTeam();		
			include '../includes/footer.html';
			exit();
		}
		else
		{	// No confirmation of deletion.
			echo '<p>The team has NOT been deleted.</p>';
		}
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();
	}	
	
	// Get team name attribute for page display purposes
	$teamname = $team->getTeamAttribute('tmname');

	if ($teamname != '') // Indicates valid user to page
	{		
		//Display the record being deleted:
		echo '<h3>Are you sure you want to delete Team ' . $teamname . ' from your profile?</h3>';
				
		// Create the form:
		echo '<form action ="delete_team.php" method="post" id="DelTeamForm">
			<input type="hidden" name="z" value="' . $id . '" />
			<input type="radio" name="sure" value="Yes" />Yes<br />
			<input type="radio" name="sure" value="No" checked="checked" />No<br />
			<input type="submit" name="submit" value="Delete" />
			</form>';		
	}
	else 
	{	//Not a valid user ID.
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();
	}		

	// Delete objects
	unset($team);
	unser($manager);
				
	// Close the connection:
	$db->close();
	unset($db);
					
	include '../includes/footer.html';
?>