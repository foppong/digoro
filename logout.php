<?php
	// logout.php
	// This page logs the user out of the SquadFill site
	
	require 'includes/config.php';
	$page_title = 'digoro : Logout';
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

	$user = new UserAuth();
	$user->logoff();

	include 'includes/ifooter.html'; 	
	
?>