<?php
	// This page is for deleting a game record
	// This page is accessed through view_roster.php
	
	require '../includes/config.php';
	$page_title = 'digoro : Delete Game';
	include '../includes/header.html';
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

	if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) ) // From view schedule page
	{
		$id = $_GET['x'];

		// Create game object for use & pull latest data from database & initially set attributes
		$game = new Game();
		$game->setDB($db);
		$game->setGameID($id);
		$game->pullGameData();
		$game->checkAuth($userID);

	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted from delete_player page	
	{
		// Assign variable from delete_player.php FORM submission (hidden id field)
		$id = $_POST['z'];

		// Create game object for use & pull latest data from database & initially set attributes
		$game = new Game();
		$game->setDB($db);
		$game->setGameID($id);
		$game->pullGameData();
		$game->checkAuth($userID);

		if ($_POST['sure'] == 'Yes')
		{	// If form submitted is yes, delete the record
			$game->deleteGame($userID);
		}
		else
		{	// No confirmation of deletion.
			echo '<p>The game has NOT been deleted.</p>';
		}
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();		
	}	
		
	// Get attributes from game object
	$dateOB = $game->getGameAttribute('gdate');

	// Format date from database into more common format to display in page
	$gd = new DateTime($dateOB);
	$gdfrmt = $gd->format('m/d/Y');

	if ($dateOB != '')
	{	
		echo '<h3>Are you sure you want to delete this game on ' . $gdfrmt . ' from your schedule?</h3>';
			
		// Create the form:
		echo '<form action ="delete_game.php" method="post" id="DelGameForm">
			<input type="hidden" name="z" value="' . $id . '" />
			<input type="radio" name="sure" value="Yes" />Yes<br />
			<input type="radio" name="sure" value="No" checked="checked" />No<br />
			<input type="submit" name="submit" value="Delete" />
			</form>';
	}
	else 
	{	//Not a valid user ID.
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();
	}

	// Delete objects
	unset($game);
	unset($manager);
			
	// Close the connection:
	$db->close();
	unset($db);
					
	include '../includes/footer.html';
?>