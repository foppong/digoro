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

	// Validate user
	checkSessionObject();	
	
	// Check user role
	checkRole('m');

	// Assign user object from session variable
	$user = $_SESSION['userObj'];
	$userID = $user->getUserID();

	// Establish database connection
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['z']))
	{
		$memberid = $_POST['z'];		

		// Create member object for use & pull latest data from database & initially set attributes
		$member = new Member();
		$member->setDB($db);
		$member->setMembID($memberid);

		// Check if user is authroized to make edit
		if (!$member->isManager($userID, $memberid)) {
			echo 'You have to be the manager to edit a member.';
			exit();
		}

		// Assume invalid values:
		$fn = $ln = $sex = $ppos = FALSE;

		// Trim all the incoming data:
		$trimmed = array_map('trim', $_POST);

		// Validate firstname
		if (preg_match('/^[A-Z \'.-]{2,20}$/i', $_POST['edit-member-fname']))
		{
			$fn = $_POST['edit-member-fname'];
		}
		else 
		{
			echo "Please enter a valid first name";	
			exit();
		}

		// Validate lastname
		if (preg_match('/^[A-Z \'.-]{2,40}$/i', $_POST['edit-member-lname']))
		{
			$ln = $_POST['edit-member-lname'];
		}
		else 
		{
			echo "Please enter a valid last name";
			exit();
		}

		// Validate sex is selected
		if ($_POST['edit-member-sel-sex'])
		{
			$sex = $_POST['edit-member-sel-sex'];
		}
		else 
		{
			echo 'Please select a sex.';
			exit();
		}

		// Validate primary position is entered
		if ($_POST['edit-member-ppos'])
		{
			$ppos = $_POST['edit-member-ppos'];
		}
		else 
		{
			echo "Please enter a primary position";
			exit();
		}

		// Validate secondary position
		if ($_POST['edit-member-spos'])
		{
			$spos = $_POST['edit-member-spos'];
		}
		else 
		{
			$spos = '';
		}
		
		// Validate jersey number input
		if (filter_var($_POST['edit-member-jernum'], FILTER_VALIDATE_INT)) {
			$jnumb = $_POST['edit-member-jernum'];
		}
		else {
			$jnumb = 0;
		}

		if ($fn && $ln && $sex && $ppos) {
			// Update database
			$member->editMember($memberid, $fn, $ln, $sex, $ppos, $spos, $jnumb);			
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
	unset($user);
			
	// Close the connection:
	$db->close();
	unset($db);
					
?>