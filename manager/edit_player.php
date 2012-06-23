<?php
	// This page is for editing a player
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

	// Establish database connection
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z']))
	{
		$memberid = $_POST['z'];		

		// Create member object for use & pull latest data from database & initially set attributes
		$member = new Member();
		$member->setDB($db);
		$member->setMembID($memberid);
		$member->pullMemberData();
$member->isManager($userID);
		// Check if user is authroized to make edit
		if (!$member->isManager($userID))
		{
			echo 'You have to be the manager to make these changes.';
			exit();
		}

		// Trim all the incoming data:
		$trimmed = array_map('trim', $_POST);
		
		// Assume invalid values:
		$pos = $jnumb = FALSE;

		// Validate position input
		if (is_string($trimmed['position']))
		{
			$pos = $trimmed['position'];
		}
		else 
		{
			echo 'Please enter a position.';
			exit();
		}
		// Validate jersey number input
		if (filter_var($_POST['jersey_num'], FILTER_VALIDATE_INT) OR $_POST['jersey_num'] == NULL)
		{
			$jnumb = $_POST['jersey_num'];
		}
		else 
		{
			echo 'Please enter a numerical value.';
			exit();	
		}

		// Update database
		$member->editMember($pos, $jnumb);
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