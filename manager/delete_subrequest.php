<?php
	// This page is for deleting a subrequest record
	// This page is accessed through find_subs_view.php

	require_once('../includes/bootstrap.php');
	require_once('../includes/php-functions.php');

	// Validate user
	checkSessionObject();

	// Check user role
	checkRole('m');

	// Assign user object from session variable
	$user = $_SESSION['userObj'];
	$userID = $user->getUserID();

	if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['z'])) { // Confirmation that form has been submitted
		// Assign variable from FORM submission (hidden id field)
		$subReqid = $_POST['z'];

		// Create event object for use & pull latest data from database & initially set attributes
		$subReq = new SubRequest();
		$subReq->setSubReqID($subReqid);
		$subReq->pullSubReqData(); // Need to pull data for isManager fn

		// Check if user is authroized to make edit
		if(!$subReq->isManager($userID)) {
			echo 'You have to be the manager to delete a subrequest.';
			exit();
		}

		$subReq->deleteSubReq($subReqid);
	}
	else {
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();
	}

	// Delete objects
	unset($subReq);
	unset($user);