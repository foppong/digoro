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

		// Check if user is authroized to make edit
		if (!$member->isManager($userID)) {
			echo 'You have to be the manager to edit a member.';
			exit();
		}

		$oldpos = $member->getMemberAttribute('position');
		$oldjnumb = $member->getMemberAttribute('jersey_numb');

		// Trim all the incoming data:
		$trimmed = array_map('trim', $_POST);

		// Validate position input
		if (is_string($trimmed['position']) && !empty($trimmed['position'])) {
			$pos = $trimmed['position'];
		}
		else {
			$pos = $oldpos;
		}
		
		// Validate jersey number input
		if (filter_var($_POST['jersey_num'], FILTER_VALIDATE_INT)) {
			$jnumb = $_POST['jersey_num'];
		}
		else {
			$jnumb = $oldjnumb;
		}

		if ($pos && $jnumb) {
			// Update database
			$member->editMember($pos, $jnumb);			
		}
		else { // Errors in the user entered information
			echo 'Please try again';
			exit();	
		}
	}
	else {
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