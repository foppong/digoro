<?php
	// create_subrequest.php
	// This page allows a manager to create a subrequest
		
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

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		// Validate team is selected
		if ($_POST['create-SR-sel-teams'])
		{
			$tmID = $_POST['create-SR-sel-teams'];
		}
		else 
		{
			echo 'Please select a team.';
			exit();
		}
	
		// Validate game is selected
		if ($_POST['create-SR-sel-events'])
		{
			$evntID = $_POST['create-SR-sel-events'];
		}
		else 
		{
			echo 'Please select an event.';
			exit();
		}

		// Validate sex is selected
		if ($_POST['create-SR-sel-sex'])
		{
			$sex = $_POST['create-SR-sel-sex'];
		}
		else 
		{
			echo 'Please select a sex.';
			exit();
		}

		// Validate experience is selected
		if ($_POST['create-SR-sel-exp'])
		{
			$exp = $_POST['create-SR-sel-exp'];
		}
		else 
		{
			echo 'Please select the experience level desired.';
			exit(); 
		}

		// Validate region is selected
		if ($_POST['create-SR-sel-reg'])
		{
			$reg = $_POST['create-SR-sel-reg'];
		}
		else 
		{
			echo 'Please select a region.';
			exit(); 
		}

		// If data is valid, create subrequest
		if ($tmID && $evntID && $sex && $exp && $reg) {
			$subRequest = new SubRequest();
			$subRequest->setDB($db);
			$subRequest->createSubReq($userID, $tmID, $evntID, $sex, $exp, $reg);		
		}
		else {									
			echo 'Please try again';
			exit();
		}
	}
	else {
		// Accsessed without posting to form
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();		
	}

	// Delete objects
	unset($team);
	unset($manager);
		
	// Close the connection:
	$db->close();
	unset($db);	
?>

