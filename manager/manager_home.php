<?php 
	// manager_homepage.php
	// This is the Manager Homepage
	require '../includes/config.php';
	$page_title = 'Welcome to digoro!';
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

	// Assign Database Resource to object
	$manager->setDB($db);

	// Authorized Login Check
	if (!$manager->valid($lvl))
	{
		redirect_to('index.php');
	}
	
	// Pull current user data from database and set object attributes
	$manager->pullUserData();
	
	// Assign updated user object session variable
	$_SESSION['userObj'] = $manager;

	// Get user ID
	$userID = $manager->getUserID();
	
	// Get user's default team ID
	$dftmID = $manager->getUserAttribute('dftmID');

	// Update team object session variable as user selects different teams
	if ( (isset($_POST['y'])) && (is_numeric($_POST['y'])) )
	{
		$_SESSION['ctmID'] = $_POST['y'];
		$ctmID = $_SESSION['ctmID'];
	
		// Create team object with current team selection
		$team = new ManagerTeam();
		$team->setDB($db);
		$team->setTeamID($ctmID);
		$team->pullTeamData();

		// Assign updated team object session variable
		//$_SESSION['teamObj'] = $team;

		// Get team name attribute for page display purposes
		$teamname = $team->getTeamAttribute('tmname');

		unset($team);
	}
	else 
	{
		// Create team object
		$team = new ManagerTeam();
		$team->setDB($db);
		$team->setTeamID($dftmID);
		$team->pullTeamData();
		
		// Assign default team ID to current team ID session variable
		$_SESSION['ctmID']  = $dftmID;
		
		// Get team name attribute for page display purposes
		$teamname = $team->getTeamAttribute('tmname');

		unset($team);	
	}
/*
	// Assign team object to session variable if doesn't exist
	if (!isset($_SESSION['teamObj']))
	{
		// Create team object
		$team = new ManagerTeam();
		$team->setDB($db);
		$team->setTeamID($dftmID);
		$team->pullTeamData();

		// Assign team object session variable
		$_SESSION['teamObj'] = $team;
		
		// Assign default team ID to current team ID session variable
		$_SESSION['ctmID']  = $dftmID;

		// Get team name attribute for page display purposes
		$teamname = $team->getTeamAttribute('tmname');
	}
	else
	{
		// Assign team object from session variable
		$team = $_SESSION['teamObj'];
	
		// Get team name attribute for page display purposes
		$teamname = $team->getTeamAttribute('tmname');
	}
*/		
	// Close the connection:
	$db->close();
	unset($db);	

?>

<div>
	<form action="manager_home.php" method="post" id="ViewRosterForm">	
		<p id="teamP"><b>View Team:</b>
		<select name="y" id="y"></select>
		<span class="errorMessage" id="teamPError">You must select your team.</span></p>		
		
		<div align="left"><input id="submit" type="submit" name="submit" value="Select" /></div>
	</form>
</div>

<h2><?php echo stripslashes($teamname); ?></h2>
<div id="tabmenu" class="ui-tabs">
	<ul>
		<li><a href="view_abteam.php"><span>About</span></a></li>
		<li><a href="view_roster.php"><span>Roster</span></a></li>
	    <li><a href="view_sch.php"><span>Schedule</span></a></li>
	    <li><a href="#"><span>SquadFill</span></a></li>
	    <li><a href="#"><span>Bulletin</span></a></li>
	</ul>
		<div id="view_abteam.php" class="ui-tabs-hide">About</div>
		<div id="view_roster.php" class="ui-tabs-hide">Roster</div>
		<div id="view_sch.php" class="ui-tabs-hide">Schedule</div>
		<div id="#" class="ui-tabs-hide">SquadFill</div>
		<div id="#" class="ui-tabs-hide">Bulletin</div>
</div><br />

<a href="add_player.php">Add Player</a><br />
<a href="add_team.php">Add Team</a><br />	

<?php include '../includes/footer.html'; ?>