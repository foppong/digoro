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

		$subReqid = $_POST['z'];

		// Create object for use & pull latest data from database & initially set attributes
		$subReq = new SubRequest();
		$subReq->setDB($db);
		$subReq->setSubReqID($subReqid);
		$subReq->pullSubReqData();

		$evntID = $subReq->getSRAttribute('id_event');

		// Validate comment enetered
		if ($_POST['respond-SR-comment'])
		{
			$com = $_POST['respond-SR-comment'];
		}
		else 
		{
			$com = '';
		}

		// If data is valid, create subrequest response
		if ($subReqid) {
			$subResponse = new SubResponse();
			$subResponse->setSubReqID($subReqid);
			$subResponse->setDB($db);
			$subResponse->createSubReqResp($userID, $evntID, $com);	
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

