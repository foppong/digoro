<?php
	// delete_acct.php
	// This page deletes a user's account
	
	require '../includes/config.php';
	$page_title = 'digoro : Delete Account';
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

	// Need the database connection:
	require_once MYSQL2;
	
	// Assign Database Resource to object
	$user->setDB($db);

	// Retrieve user ID
	$userID = $user->getUserAttribute('id_user');

	// Delete user from database
	$user->deleteUser($userID);

	// Close the connection:
	$db->close();
	unset($db);

	echo '<p>This account has been deleted successfully.</p>';

	include '../includes/ifooter.html'; 	
	
?>