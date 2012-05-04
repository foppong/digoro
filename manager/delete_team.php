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
		$user = $_SESSION['userObj'];
	}
	else 
	{
		redirect_to('index.php');
	}

	// Need the database connection:
	require_once MYSQL2;

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		redirect_to('index.php');
	}
	
	// Check for a valid user ID, through GET or POST:
	if ( (isset($_GET['z'])) && (is_numeric($_GET['z'])) )
	{
		// Point A in Code Flow
		// Assign variable from view_roster.php using GET method
		$id = $_GET['z'];
	}
	elseif ( (isset($_POST['z'])) && (is_numeric($_POST['z'])) )
	{
		// Point C in Code Flow
		// Assign variable from delete_player.php FORM submission (hidden id field)
		$id = $_POST['z'];
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();
	}

	// Create team object with current team selection
	$team = new ManagerTeam();
	$team->setDB($db);
	$team->setTeamID($id);
	$team->pullTeamData();

	// Confirmation that form has been submitted:	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{	// Point D in Code Flow

		if ($_POST['sure'] == 'Yes')
		{	// If form submitted is yes, delete the record

			$team->deleteTeam();		
		}
		else
		{	// No confirmation of deletion.
			echo '<p>The team has NOT been deleted.</p>';
		}
	}
	else
	{	// Point B in Code Flow. Show the form

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

	} // End of the main submission conditional.

	unset($team);
				
	// Close the connection:
	$db->close();
	unset($db);
					
	include '../includes/footer.html';
?>