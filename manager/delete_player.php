<?php
	// This page is for deleting a player record
	// This page is accessed through view_roster.php
	
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
	}
	else 
	{
		redirect_to('index.php');
	}

	// Authorized Login Check
	if (!$manager->valid($lvl))
	{
		redirect_to('index.php');
	}

	// Need the database connection:
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted from delete_player page	
	{
		$id = $_POST['z'];

		// Create member object for use & pull latest data from database & initially set attributes
		$member = new Member();
		$member->setDB($db);
		$member->setMembID($id);
		$member->pullMemberData();
		$member->checkAuth($userID);
		$member->deleteMember($userID);
	}
	else 
	{
		// No valid ID, kill the script.
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