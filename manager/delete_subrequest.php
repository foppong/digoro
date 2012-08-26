<?php
	// This page is for deleting a subrequest record
	// This page is accessed through find_subs_view.php
	
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

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['z'])) // Confirmation that form has been submitted	
	{
		// Assign variable from FORM submission (hidden id field)	
		$subReqid = $_POST['z'];

		// Create event object for use & pull latest data from database & initially set attributes
		$subReq = new SubRequest();
		$subReq->setDB($db);
		$subReq->setSubReqID($subReqid);
		$subReq->pullSubReqData(); // Need to pull data for isManager fn

		// Check if user is authroized to make edit
		if (!$subReq->isManager($userID)) {
			echo 'You have to be the manager to delete a subrequest.';
			exit();
		}

		$subReq->deleteSubReq($subReqid);
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();		
	}	

	// Delete objects
	unset($subReq);
	unset($manager);
			
	// Close the connection:
	$db->close();
	unset($db);
?>