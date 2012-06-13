<?php
	// add_player.php
	// This page allows a logged-in user to add a player to a team
	
	require '../includes/config.php';
	$page_title = 'digoro : Add Player';
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
		$userID = $manager->getUserID();
		$ctmID = $_SESSION['ctmID']; //Retrieve current team in session variable
	}
	else 
	{
		redirect_to('index.php');
	}

	// Establish database connection
	require_once MYSQL2;

	// Assign Database Resource to object
	$manager->setDB($db);

	// Authorized Login Check
	if (!$manager->valid($lvl))
	{
		redirect_to('index.php');
	}

	// Create team object to help determine if manager can add a game to this team
	// NOTE: ISSUE HERE MIGHT BE IF I MOVE ADD GAME FEATURE TO HOME PAGE
	$team = new Team();
	$team->setDB($db);
	$team->setTeamID($ctmID);
	$team->pullTeamData();
	$team->checkAuth($userID);
		
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{

		// Trim all the incoming data:
		$trimmed = array_map('trim', $_POST);
		
		// Assume invalid values:
		$fn = $ln = $e = FALSE;

		// Validate firstname
		if (preg_match('/^[A-Z \'.-]{2,20}$/i', $trimmed['first_name']))
		{
			$fn = $trimmed['first_name'];
		}
		else 
		{
			echo '<p class="error"> Please enter valid first name.</p>';
		}
	
		// Validate lastname
		if (preg_match('/^[A-Z \'.-]{2,40}$/i', $trimmed['last_name']))
		{
			$ln = $trimmed['last_name'];
		}
		else 
		{
			echo '<p class="error"> Please enter valid last name.</p>';
		}
	
		// Validate email
		if (filter_var($trimmed['email'], FILTER_VALIDATE_EMAIL))
		{
			$e = $trimmed['email'];
		}
		else 
		{
			echo '<p class="error"> Please enter valid email address.</p>';
		}

		// Checks if name, email, and league are valid before proceeding.
		if ($ctmID && $fn && $ln && $e)
		{
			$member = new Member();
			$member->setDB($db);
			$member->createMember($e, $ctmID, $fn, $ln);

			// Close the connection:
			$db->close();
			unset($db);
				
			include '../includes/footer.html';
			exit();	

		}
		else 
		{									
			echo '<p class="error">Please try again.</p>';
		}
	}

	// Delete objects
	unset($member);
	unset($manager);

	// Close the connection:
	$db->close();
	unset($db);	

include '../includes/footer.html'; 

?>
