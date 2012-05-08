<?php
	// This page is for editing a game
	// This page is accessed through view_sch.php
	
	require '../includes/config.php';
	$page_title = 'digoro : Edit Game';
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

	if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) ) // From view schedule page
	{
		$id = $_GET['x'];
		
		// Create game object for use & pull latest data from database & initially set attributes
		$game = new Game();
		$game->setDB($db);
		$game->setGameID($id);
		$game->pullGameData();

	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted from edit_player page	
	{
		$id = $_POST['z'];

		// Create game object for use & pull latest data from database & initially set attributes
		$game = new Game();
		$game->setDB($db);
		$game->setGameID($id);
		$game->pullGameData();

		// Assume invalid values:
		$bdfrmat = $tm = FALSE;
		
		// Validate game date
		if ($_POST['date'])
		{
			$bd = new DateTime($_POST['date']);
			$bdfrmat = $bd->format('Y-m-d');
		}
		else 
		{
			echo '<p class="error"> Please enter a date.</p>';
		}		
		
		// Validate game time is entered
		if ($_POST['time'])
		{
			$tm = $_POST['time'];
		}
		else 
		{
			echo '<p class="error"> Please enter a time.</p>';
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
		if ($bdfrmat && $tm)
		{
			$game->editGame($bdfrmat, $tm, $opp, $ven, $res, $id);
		}
		else
		{	// Errors in the user entered information
			echo '<p class="error">Please try again.</p>';
		}
	}
	else 
	{
echo "test point A";
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();		
	}

	// Always show the form...
	
	// Get attributes from game object
	$bdfrmatOB = $game->getGameAttribute('gdate');
	$tmOB = $game->getGameAttribute('gtime');
	$oppOB = $game->getGameAttribute('opponent');
	$venOB = $game->getGameAttribute('venue');
	$resOB = $game->getGameAttribute('result');

	if ($bdfrmatOB != '') // Valid user ID, show the form.	
	{		
		echo '<h2>Edit Game</h2>';
				
		// Create the form:
		echo '<form action ="edit_game.php" method="post" id="EditGameForm">
			<fieldset>
			<input type="hidden" name="z" value="' . $id . '" />
					
			<div>
				<label for="date"><b>Select Game Date:</b></label>
				<input type="text" name="date" id="date" size="10" maxlength="10"
				value="' . $bdfrmatOB . '" />
			</div>
				
			<div>
				<label for="time"><b>Enter Game Time:</b></label>
				<input type="text" name="time" id="time" size="9" maxlength="9"
				value="' . $tmOB . '" />
				<small>Ex. 6:30 PM</small>
			</div>
					
			<div>
				<label for="text"><b>Enter Opponent:</b></label>
				<input type="text" name="opp" id="opp" size="30" maxlength="45" 
				value="' . $oppOB . '" />
			</div>
					
			<div>
				<label for="text"><b>Enter Venue:</b></label>
				<input type="text" name="ven" id="ven" size="30" maxlength="45" 
				value="' . $venOB . '" />
			</div>
					
			<div>
				<label for="resP"><b>Enter Results:</b></label>
				<input type="text" name="res" id="res" size="13" maxlength="13" 
				value="' . $resOB . '" />
				<small>Ex. W 4-3</small>
			</div>	
				
			<input type="submit" name="submit" value="Save"/>
			</fieldset>
			</form><br />';
	}
	else 
	{	//Not a valid user ID, kill the script
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
					
	include '../includes/footer.html';
?>