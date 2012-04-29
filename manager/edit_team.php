<?php
	// This page is for editing a team
	// This page is accessed through myteams-m.php
	
	require '../includes/config.php';
	$page_title = 'digoro : Edit Team';
	include '../includes/header.html';

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
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}

	// Establish database connection
	require_once MYSQL2;

	// Assign Database Resource to object
	$manager->setDB($db);

	// Authorized Login Check
	if (!$manager->valid($lvl))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}

	// Check for a valid game sch ID, through GET or POST:
	if ( (isset($_GET['z'])) && (is_numeric($_GET['z'])) )
	{
		// Point A in Code Flow
		// Assign variable from myteams-m.php using GET method
		$id = $_GET['z'];
	}
	elseif ( (isset($_POST['z'])) && (is_numeric($_POST['z'])) )
	{
		// Point C in Code Flow
		// Assign variable from edit_team.php FORM submission (hidden id field)
		$id = $_POST['z'];
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();
	}

	$team = new ManagerTeam();
	$team->setDB($db);
	$team->setTeamID($id);
	$team->pullTeamData();

	// Confirmation that form has been submitted:	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{	// Point D in Code Flow

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

			$team->editTeam($tname, $abtm);		
		}
		else
		{	// Errors in the user entered information
			echo '<p class="error">Please try again.</p>';
		}
 
	}	// End of submit conditional.

	// Point B in Code Flow
	// Always show the form...
	
	// Get team name attribute
	$teamname = $team->getTeamAttribute('tmname');
	$about = $team->getTeamAttribute('about');

	if ($teamname != '' && $about != '') // Valid user ID, show the form.	
	{
		// Headliner
		echo '<h2>Edit Team</h2>';
				
		// Create the form:
		echo '<form action ="edit_team.php" method="post" id="EditTeamForm">
			<fieldset>
			<input type="hidden" name="z" value="' . $id . '" />
	
			<div>
				<label for="tname"><b>New Team Name:</b></label>
				<input type="text" name="tname" id="tname" size="10" maxlength="45"
				value="' . $teamname . '" />
			</div>
					
			<div>
				<label for="abouttm"><b>Team Information:</b></p>
				<textarea id="abouttm" name="abouttm" cols="30" rows="2">"' . $about . '"</textarea><br />
				<small>Enter something cool about your team.</small>
			</div>
					
			<input type="submit" name="submit" value="Save"/>
			</fieldset>
			</form><br />';	
	}
	else 
	{	//Not a valid user ID, kill the script
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();
	}	
/*	
	// Make the query to retreive team information from teams table in database:		
	$q = "SELECT team_name, about
		FROM teams
		WHERE id_team=?";

	// Prepare the statement:
	$stmt = $db->prepare($q);

	// Bind the inbound variable:
	$stmt->bind_param('i', $id);
		
	// Execute the query:
	$stmt->execute();		
		
	// Store results:
	$stmt->store_result();
	
	// Bind the outbound variable:
	$stmt->bind_result($tnameOB, $abtmOB);	
	
	// Valid user ID, show the form.
	if ($stmt->num_rows == 1)
	{
		while ($stmt->fetch())
		{
			$abtmSP = stripcslashes($abtmOB);

			// Headliner
			echo '<h2>Edit Team</h2>';
			
			// Create the form:
			echo '<form action ="edit_team.php" method="post" id="EditTeamForm">
				<fieldset>
				<input type="hidden" name="z" value="' . $id . '" />
				
				<div>
					<label for="tname"><b>New Team Name:</b></label>
					<input type="text" name="tname" id="tname" size="10" maxlength="45"
					value="' . $tnameOB . '" />
				</div>
				
				<div>
					<label for="abouttm"><b>Team Information:</b></p>
					<textarea id="abouttm" name="abouttm" cols="30" rows="2">"' . $abtmSP . '"</textarea><br />
					<small>Enter something cool about your team.</small>
				<div>
				
				<input type="submit" name="submit" value="Save"/>
				</fieldset>
				</form><br />';
		}
	}
	else 
	{	//Not a valid user ID, kill the script
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();
	}
		
	// Close the statement:
	$stmt->close();
	unset($stmt);
*/			
	// Close the connection:
	$db->close();
	unset($db);
					
	include '../includes/footer.html';
?>