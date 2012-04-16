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

	// Authorized Login Check
	// If no session value is present, redirect the user. Also validate the HTTP_USER_AGENT	
	if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
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