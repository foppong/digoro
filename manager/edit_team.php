<?php
	// This page is for editing a team
	// This page is accessed through myteams-m.php
	
	require '../includes/config.php';
	$page_title = 'digoro : Edit Team';
	include '../includes/header.html';
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

	if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) ) // From view teams page
	{
		$id = $_GET['x'];
		
		// Create team object for use & pull latest data from database & initially set attributes
		$team = new ManagerTeam($id);
		$team->setDB($db);
		//$team->setTeamID($id);
		$team->pullTeamData();
		$team->checkAuth($userID);
	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted from edit_team page	
	{
		$id = $_POST['z'];

		// Create team object for use & pull latest data from database & initially set attributes
		$team = new ManagerTeam($id);
		$team->setDB($db);
		//$team->setTeamID($id);
		$team->pullTeamData();
		$team->checkAuth($userID);

		// Assume invalid values:
		$tname = FALSE;
		
		// Validate team name
		if ($_POST['tname'])
		{
			$tname = $_POST['tname'];
		
		}
		else 
		{
			echo '<p class="error"> Please enter a team name.</p>';
		}		
		
		// Validate about team information
		if ($_POST['abouttm'])
		{
			$abtm = trim($_POST['abouttm']);

		}
		else 
		{
			$abtm = '';
		}
	
		// Check if user entered information is valid before continuing to edit game
		if ($tname)
		{
			if($team->editTeam($tname, $abtm) == True)
			{
				echo '<p>Team was successfully updated</p>';
			}
			else 
			{
				echo '<p>No changes were made</p>';
			}
		}
		else
		{	// Errors in the user entered information
			echo '<p class="error">Please try again.</p>';
		}
 
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();		
	}

	// Get attributes from team object
	$teamname = $team->getTeamAttribute('tmname');
	$about = $team->getTeamAttribute('about');

	if ($teamname != '') // Valid user ID, show the form.	
	{
		// Headliner
		echo '<h2>Edit Team</h2>';
				
		// Create the form (AJAX is used here to post to database)
		echo '
		<div id="EditTeam"></div>
		<div id="Team">
			<fieldset id="TeamDetails">
				<legend>Edit Team</legend>
				<form method="post" id="information">
				<p id="status"></p>
				<input type="hidden" name="z" value="' . $id . '" />				
				<p>
					<label for="tname">New Team Name:</label><br/>
					<input type="text" name="tname" id="tname" size="10" maxlength="45" value="' . $teamname . '" />
				</p>
				<p>
					<label for="abouttm">Team Information:</label><br/>
					<textarea id="abouttm" name="abouttm" cols="30" rows="2">"' . $about . '"</textarea><br />
					<small>Enter something cool about your team.</small>
				</p>
				<p>
					<input type="button" value="update" id="update" />
				</p>
			</form>
			</fieldset>
		</div>';				
	}
	else 
	{	//Not a valid user ID, kill the script
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
					
	include '../includes/footer.html';
?>