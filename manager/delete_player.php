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

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted	
	{
		// Assign variable from FORM submission (hidden id field)	
		$memberid = $_POST['z'];

		// Create member object for use & pull latest data from database & initially set attributes
		$member = new Member();
		$member->setDB($db);
		$member->setMembID($memberid);
		$member->pullMemberData();

		// Check if user is authroized to make edit
		if (!$member->isManager($userID)) {
			echo 'You have to be the manager to delete a member.';
			exit();
		}

		$member->deleteMember();
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();		
	}

	// Delete objects
	unset($member);
	unset($manager);
				
	// Close the connection:
	$db->close();
	unset($db);
?>