<?php
	// add_player.php
	// This page allows a logged-in user to add a player to a team

	ob_start();
	session_start();
		
	require '../includes/config.php';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Validate user
	checkSessionObject();	
	
	// Check user role
	checkRole('m');

	// Establish database connection
	require_once MYSQL2;

	// Assign user object from session variable
	$user = $_SESSION['userObj'];
	$userID = $user->getUserID();

	// Retrieve current team ID in session
	$ctmID = $_SESSION['ctmID'];
		
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{

		// Create team object for use & pull latest data from database & initially set attributes - used to add member
		$team = new Team();
		$team->setDB($db);
		$team->setTeamID($ctmID);
		$team->pullTeamData();
		
		// Check if user is authroized to make edit
		if (!$team->isManager($userID)) {
			echo 'You have to be the manager to add a member.';
			exit();
		}
		
		// Assume invalid values:
		$fn = $ln = $e = FALSE;

		// Validate firstname
		if (preg_match('/^[A-Z \'.-]{2,20}$/i', $_POST['first_name']))
		{
			$fn = $_POST['first_name'];
		}
		else 
		{
			echo "Please enter a valid first name";	
			exit();
		}
	
		// Validate lastname
		if (preg_match('/^[A-Z \'.-]{2,40}$/i', $_POST['last_name']))
		{
			$ln = $_POST['last_name'];
		}
		else 
		{
			echo "Please enter a valid last name";
			exit();
		}
	
		// Validate email
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{
			$e = $_POST['email'];
		}
		else 
		{
			echo "Please enter a valid email";
			exit();
		}

		// Checks if name, email, and league are valid before proceeding.
		if ($ctmID && $fn && $ln && $e)
		{
			$member = new Member();
			$member->setDB($db);
			$member->createMember($e, $ctmID, $fn, $ln);
		}
		else 
		{									
			echo "Please try again";
			exit();
		}
	}
	else 
	{
		// Accsessed without posting to form
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();		
	}
	
	// Delete objects
	unset($member);
	unset($user);

	// Close the connection:
	$db->close();
	unset($db);	 

?>
