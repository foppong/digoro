<?php
	// logout.php
	// This page logs the user out of the site
	
	require '../includes/config.php';
	$page_title = 'digoro : Logout';
	include '../includes/iheader.html';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Site access level -> General
	$lvl = 'G'; 

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$user = $_SESSION['userObj'];
	}
	else 
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}

	// Log off user
	$user->logoff();

	echo '<h3>You are now logged out.</h3>';
	echo '<h4>Click <a href="../index.php">here</a> to return to the main login screen.</h4>';

	include '../includes/ifooter.html'; 	
	
?>