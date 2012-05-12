<?php
	// add_game.php
	// This page allows a logged-in user to add a game to the schedule
		
	require '../includes/config.php';
	$page_title = 'digoro : Add Game';
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
	
	// Retrieve default team ID * THIS IS NOT CORRECT b/c we may not be working with the default team
	$idtm = $manager->getUserAttribute('dftmID');	
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		require_once MYSQL2;
		
		// Assume invalid values:
		$bdfrmat = $tm = FALSE;
		
		// Validate game date
		if ($_POST['date'])
		{
			$bd = new DateTime($_POST['date']); // Convert js datepicker entry into format database accepts
			$gdfrmat = $bd->format('Y-m-d');
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

		// Checks if team is selected and date format and entered time are valid before adding game to team.
		if ($idtm && $gdfrmat && $tm)
		{
			
			// Create game object for use & push game to database for specified team
			$game = new Game();
			$game->setDB($db);
			$game->createGame($idtm, $gdfrmat, $tm, $opp, $ven, $res);
			
			// Close the connection:
			$db->close();
			unset($db);
			
			include '../includes/footer.html';
			exit();			
		}
		else 
		{									
			echo '<p class="error">Please try again.</p>';
		}
	
	// Delete objects
	unset($game);
	unset($manager);

	// Close the connection:
	$db->close();
	unset($db);		
	}
?>

<h2>Add Game</h2>
<form action="add_game.php" method="post" id="AddGameForm">
	<fieldset>
	
	<div>
		<label for="date"><b>Select Game Date:</b></label>
		<input type="text" name="date" id="date" size="10" maxlength="10"
		value="<?php if (isset($_POST['date'])) echo $_POST['date']; ?>" />
	</div>

	<div>
		<label for="time"><b>Enter Game Time:</b></label>
		<input type="text" name="time" id="time" size="9" maxlength="9"
		value="<?php if (isset($_POST['time'])) echo $_POST['time']; ?>" />
		<small>Ex. 6:30 PM</small>
	</div>
	
	<div>
		<label for="opp"><b>Enter Opponent:</b></label>
		<input type="text" name="opp" id="opp" size="30" maxlength="45" 
		value="<?php if (isset($_POST['opp'])) echo $_POST['opp']; ?>" />
	</div>
	
	<div>
		<label for="ven"><b>Enter Venue:</b></label>
		<input type="text" name="ven" id="ven" size="30" maxlength="45" 
		value="<?php if (isset($_POST['ven'])) echo $_POST['ven']; ?>" />
	</div>
	
	<div>
		<label for="res"><b>Enter Results:</b></label>
		<input type="text" name="res" id="res" size="13" maxlength="13" 
		value="<?php if (isset($_POST['res'])) echo $_POST['res']; ?>" />
		<small>Ex. W 4-3</small>
	</div>
	
	<div align="center"><input type="submit" name="submit" value="Add Game" />
	</fieldset>
</form>

<?php include '../includes/footer.html'; ?>
