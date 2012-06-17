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

	// Site access level -> Manager
	$lvl = 'M'; 

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

	// Authorized Login Check
	if (!$manager->valid($lvl))
	{
		redirect_to('index.php');
	}

	// Need the database connection:
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted from delete_player page	
	{
		// Assign variable from delete_player.php FORM submission (hidden id field)
		$id = $_POST['z'];

		// Create game object for use & pull latest data from database & initially set attributes
		$game = new Game();
		$game->setDB($db);
		$game->setGameID($id);
		$game->pullGameData();
		$game->checkAuth($userID);
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();		
	}	

	// Delete objects
	unset($game);
	unset($manager);
			
	// Close the connection:
	$db->close();
	unset($db);
?>