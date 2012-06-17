<?php
	// This page is for editing a game
	// This page is accessed through view_sch.php
	
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

	// Establish database connection
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted from edit_player page	
	{
		$id = $_POST['z'];

		// Create game object for use & pull latest data from database & initially set attributes
		$game = new Game();
		$game->setDB($db);
		$game->setGameID($id);
		$game->pullGameData();
		$game->checkAuth($userID);

		// Assume invalid values:
		$gdfrmat = $tm = FALSE;
		
		// Validate game date
		if ($_POST['date'])
		{
			$bd = new DateTime($_POST['date']); // Convert js datepicker entry into format database accepts
			$gdfrmat = $bd->format('Y-m-d');
		}
		else 
		{
			echo 'Please enter a date';
			exit();
		}		
		
		// Validate game time is entered
		if ($_POST['time'])
		{
			$tm = $_POST['time'];
		}
		else 
		{
			echo 'Please enter a time';
			exit();
		}
	
		// Validate opponent is entered
		if ($_POST['opp'])
		{
			$opp = $_POST['opp'];
		}
		else 
		{
			$opp = '';
		}

		// Validate a venue is entered
		if ($_POST['ven'])
		{
			$ven = $_POST['ven'];
		}
		else 
		{
			$ven = ''; 
		}

		// Validate a note is entered
		if ($_POST['note'])
		{
			$note = $_POST['note'];
		}
		else 
		{
			$note = ''; 
		}

		// Validate a result is selected
		if ($_POST['res'])
		{
			$res = $_POST['res'];
		}
		else 
		{
			$res = ''; 
		}
		
		// Check if user entered information is valid before continuing to edit game
		if ($gdfrmat && $tm)
		{
			$game->editGame($userID, $gdfrmat, $tm, $opp, $ven, $res, $note);
		}
		else
		{	// Errors in the user entered information
			echo 'Please try again';
			exit();
		}
	}
	else 
	{	// No valid ID, kill the script.
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