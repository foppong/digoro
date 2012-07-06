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
		include '../includes/footer.html';
		exit();		
	}
	
	// Delete objects
	unset($member);
	unset($manager);

	// Close the connection:
	$db->close();
	unset($db);	 

?>
