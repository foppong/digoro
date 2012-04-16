<?php
	// delete_acct.php
	// This page deletes a user's account
	
	require 'includes/config.php';
	$page_title = 'digoro : Delete Account';
	include 'includes/iheader.html';

	// autoloading of classes
	function __autoload($class) {
		require_once('classes/' . $class . '.php');
	}

	// Create user object
	$user = new UserAuth();

	// Site access level -> General
	$lvl = 'G'; 

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

	require_once MYSQL;

	$userID = $_SESSION['userID'];
	$user = new UserAuth();
	$user->setDB($db);	
	$user->deleteUser($userID);

	// Close the connection:
	$db->close();
	unset($db);

	echo '<p>This account has been deleted successfully.</p>';

	include 'includes/ifooter.html'; 	
	
?>