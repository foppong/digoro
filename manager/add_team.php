<?php
	// add_team.php
	// This page allows a logged-in user to add a team
		
	ob_start();
	session_start();
		
	require '../includes/config.php';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

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

	// Assign Database Resource to object
	$manager->setDB($db);

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// IN FUTURE CAN ADD LOGIC HERE FOR PAYING CUSTOMERS TO ADD TEAM - similar to checks 
		// i have now for managers to edit and add players/games
				
		// Assume invalid values:
		$tn = $sp = $ct = $st = $lg = FALSE;
				
		// Validate Team name
		if ($_POST["tname"]) {
			$tn = $_POST["tname"];
		}
		else {
			echo 'Please enter a Team name.';
			exit();
		}

		// Validate a sport is selected
		if ($_POST['sport']) {
			$sp = $_POST['sport'];
		}
		else {
			echo 'Please select your sport.';
			exit(); 
		}

		// Validate Team's homecity
		if ($_POST['city']) {
			$ct = $_POST['city'];
		}
		else {
			echo 'Please enter your teams homecity.';
			exit();
		}

		// Validate Team's state
		if ($_POST['state']) {
			$st = $_POST['state'];
		}
		else {
			echo 'Please enter your teams home state.';
			exit();
		}

		// Validate a league is selected
		if ($_POST['league']) {
			$lg = $_POST['league'];
		}
		else {
			echo 'Please select your league.';
			exit(); 
		}
		
		// Validate about team information
		if ($_POST['abouttm']) {
			$abtm = trim($_POST['abouttm']);
		}
		else {
			echo 'Please enter a brief description about your team.';
			exit();
		}

		// Checks if team name, userID, sport, team city, state, and league are valid before adding team to database.
		if ($lg && $userID && $sp && $tn && $ct && $st && $abtm) {
			// Create team object for use & create team for database
			$team = new Team();
			$team->setDB($db);
			$team->createTeam($lg, $sp, $userID, $tn, $ct, $st, $abtm);	
		}
		else {									
			echo 'Please try again.';
			exit();
		}	
	}
	else {
		// Accessed without posting to form
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

