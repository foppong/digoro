<?php
	// This page is for editing a subrequest
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
	
	// Establish database connection
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted	
	{
		$subReqid = $_POST['z'];

		// Create object for use & pull latest data from database & initially set attributes
		$subReq = new SubRequest();
		$subReq->setDB($db);
		$subReq->setSubReqID($subReqid);
		$subReq->pullSubReqData();
		
		// Check if user is authroized to make edit
		if (!$subReq->isManager($userID)) {
			echo 'You have to be the manager to edit.';
			exit();
		}

		$oldTeamID = $subReq->getSRAttribute('id_team');
		$oldEventID = $subReq->getSRAttribute('id_event');
		$oldSex = $subReq->getSRAttribute('sex_needed');
		$oldExp = $subReq->getSRAttribute('experience_needed');
		$oldReg = $subReq->getSRAttribute('id_region');

		// Validate team is selected
		if ($_POST['edit-SR-sel-teams'])
		{
			$tmID = $_POST['edit-SR-sel-teams'];
		}
		else 
		{
			$tmID = $oldTeamID;
		}
	
		// Validate game is selected
		if ($_POST['edit-SR-sel-events'])
		{
			$evntID = $_POST['edit-SR-sel-events'];
		}
		else 
		{
			$evntID = $oldEventID;
		}

		// Validate sex is selected
		if ($_POST['edit-SR-sel-sex'])
		{
			$sex = $_POST['edit-SR-sel-sex'];
		}
		else 
		{
			$sex = $oldSex;
		}

		// Validate experience is selected
		if ($_POST['edit-SR-sel-exp'])
		{
			$exp = $_POST['edit-SR-sel-exp'];
		}
		else 
		{
			$exp = $oldExp;
		}

		// Validate region is selected
		if ($_POST['edit-SR-sel-reg'])
		{
			$reg = $_POST['edit-SR-sel-reg'];
		}
		else 
		{
			$reg = $oldReg;
		}
		
		// If data is valid, edit subrequest
		if ($tmID && $evntID && $sex && $exp && $reg) {
			$subReq->editSubReq($tmID, $evntID, $sex, $exp, $reg);		
		}
		else {									
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
	unset($subReq);
	unset($manager);
			
	// Close the connection:
	$db->close();
	unset($db);

?>