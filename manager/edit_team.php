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

if (isset($_POST['abouttm'])){
    echo $_POST['abouttm'] . "<br />";
	echo $_POST['tname'] . "<br />";
	echo $_POST['z'] . "<br />";   
}else{
    echo 'False <br />';
}

	// Confirmation that form has been submitted:	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{	// Point D in Code Flow

		// Assume invalid values:
		$tname = FALSE;
		
		// Validate team name
		if ($_POST['tname'])
		{
			$tname = htmlspecialchars($_POST['tname']);
echo $tname . "<br />";			
		}
		else 
		{
			echo '<p class="error"> Please enter a team name.</p>';
		}		
		
		// Validate about team information
		if ($_POST['abouttm'])
		{
			$abtm = htmlspecialchars(trim($_POST['abouttm']));
echo $abtm . "<br />";	
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
echo $teamname . "<br />";
echo $about . "<br />";
echo $id; 
	if ($teamname != '' && $about != '') // Valid user ID, show the form.	
	{
		// Headliner
		echo '<h2>Edit Team</h2>';
				
		// Create the form:
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
					<input type="hidden" name="id" id="id">
					<input type="submit" value="update" id="update" />
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

	// Close the connection:
	$db->close();
	unset($db);
					
	include '../includes/footer.html';
?>