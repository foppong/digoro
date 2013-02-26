<?php
	// This page is for logging in a user
	// This page is accessed through the login page
	
	ob_start();
	session_start();	
		
	require '../includes/config.php';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// If session value is present, redirect the user. Also validate the HTTP_USER_AGENT	
	if (isset($_SESSION['agent']) AND ($_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']))) {
	
		$url = BASE_URL . 'manager/home.php';
		header("Location: $url");
		exit();			
	}
	
	// Establish database connection
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Validate email address
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$e = $_POST['email'];
		}
		else {
			$e = FALSE;
			echo '<p class="error"> Please enter valid email address!</p>';
		}
		
		// Validate password
		if (!empty($_POST['pass'])) {
			$p = $_POST['pass'];
		}
		else {
			$p = FALSE;
			echo '<p class="error">You forgot to enter your password!</p>';
		}

		// Check if email and password entered are valid before proceeding to login procedure.
		if ($e && $p) {
			// Create user object & login user 
			$user = new UserAuth();
			$user->setDB($db);	
			$user->login($e, $p);
			unset($user);
		}
	}
	
	// Delete objects
	unset($user);
			
	// Close the connection:
	$db->close();
	unset($db);

?>