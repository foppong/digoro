<?php
	// This page is for deleting a game record
	// This page is accessed through view_roster.php
	
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

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted	
	{
		// Assign variable from FORM submission (hidden id field)	
		$gameid = $_POST['z'];

		// Create game object for use & pull latest data from database & initially set attributes
		$game = new Game();
		$game->setDB($db);
		$game->setGameID($gameid);
		$game->pullGameData();

		// Check if user is authroized to make edit
		if (!$game->isManager($userID)) {
			echo 'You have to be the manager to delete a game.';
			exit();
		}

		$game->deleteGame($userID);
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();		
	}	

	// Delete objects
	unset($game);
	unset($manager);
			
	// Close the connection:
	$db->close();
	unset($db);
?>