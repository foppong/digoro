<?php
	// create_subresponse.php
	// This page allows a user to respond to a subrequest
		
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

		$subResponseID = $_POST['z'];

		// Validate comment enetered
		if ($_POST['SR-response-comment'])
		{
			$com = $_POST['SR-response-comment'];
		}
		else 
		{
			echo 'Please provide a reason for cancelling';
			exit();
		}

		// If data is valid, cancel subrequest response
		if ($subResponseID) {
			$subResponse = new SubResponse();
			$subResponse->setSRRespID($subResponseID);
			$subResponse->setDB($db);
			$subResponse->cancelSubResponse($userID, $com);	
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

