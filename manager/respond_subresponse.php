<?php
	// respond_subresponse.php
	// This page allows a manager to respond a subresponse
		
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

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['SR-response'] == 'confirm' && !empty($_POST['z'])) {

		$subresponseID = $_POST['z'];
		$response = $_POST['SR-response'];
		
		// Validate comment
		if ($_POST['respond-SR-comment']) {
			$comments = $_POST['respond-SR-comment'];
		}
		else {
			$comments = '';
		}

		$subResponse = new SubResponse($subresponseID);
		$subResponse->setDB($db);
		$subResponse->confirmSubReqResp($subresponseID, $comments);	
		
			
	}
	
	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['SR-response'] == 'decline' && !empty($_POST['z'])) {

		$subresponseID = $_POST['z'];
		$response = $_POST['SR-response'];
		
		// Validate comment
		if ($_POST['respond-SR-comment']) {
			$comments = $_POST['respond-SR-comment'];
		}
		else {
			$comments = '';
		}

		$subResponse = new SubResponse();
		$subResponse->setSubReqID($subresponseID);
		$subResponse->setDB($db);
		$subResponse->declineSubReqResp($subresponseID, $comments);	

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

