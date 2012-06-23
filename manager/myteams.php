<?php
	// myteams.php
	// This script retrieves all the team records associated with user.
	
	ob_start();
	session_start();

	require '../includes/config.php';
	include '../includes/php-functions.php';
	
	$page_title = 'digoro : My Teams';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Site access level -> General
	$lvl = 'G'; 

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$user = $_SESSION['userObj'];
		$userID = $user->getUserID();
	}
	else 
	{
		redirect_to('index.php');
	}

	// Need the database connection:	
	require_once MYSQL2;

	// Assign Database Resource to object
	$user->setDB($db);

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		redirect_to('index.php');
	}

	// Page header:
	echo '<h2>My Teams</h2>';

	//Series of code to set the default team
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// Retreieve team ID selection from user form submission	
		$teamID = $_POST['mt'];
		
		// Set the new global session variable to new team ID
		$_SESSION['ctmID'] = $teamID;

		$user->setDefaultTeam($teamID);
	}	
			
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $page_title; ?></title>
		<meta name="author" content="Frank" />
		<!-- External javascript call -->
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.js" charset="utf-8"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.js" charset="utf-8"></script>
		<script type="text/javascript" src="../js/myteams.js"></script>
		<!-- CSS Style Sheet -->
		<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" type="text/css" href="../css/styles.css" />
	</head>
	<body>
	
	<p class="status"></p>

	<form action="myteams.php" method="post">	
		<p id="teamP"><b>Select Your Default Team:</b>
		<select name="mt" id="menuteam"></select>		
		
		<div align="left"><input id="submit" type="submit" name="submit" value="Select" /></div>
	</form><br>

	<div id="EditTeamForm" title="Edit Team">	
		<form method="post">
			<label for="tname">New Team Name:</label><br />
			<input type="text" name="tname" id="tname" size="10" maxlength="45" /><br />

			<label for="abouttm">Team Information:</label><br />
			<textarea id="abouttm" name="abouttm" cols="30" rows="2"></textarea><br />
			<small>Enter something cool about your team.</small><br />

			<label for="transfer">Transfer Team?</label><br />
			<input type="radio" name="transfer" value="Yes" />Yes<br />
			<input type="radio" name="transfer" value="No" checked="checked" />No<br />
			
			<label for="email">If Yes, please enter new manager email address:</label>
			<input type="text" name="email" id="email" size="30" maxlength="60" />
		</form>
	</div>

	<div id="DelTeamForm" title="Delete Team">
		<form method="post">
			<p>Are you sure you want to remove this team? If you are the manager and wish to transfer
				team ownership, please cancel this selection and select the edit button.</p>
		</form>
	</div>

	<table id="MyTeams"></table>


<?php include '../includes/footer.html'; ?>