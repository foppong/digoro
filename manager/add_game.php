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
		$userID = $manager->getUserID();
		$ctmID = $_SESSION['ctmID']; //Retrieve current team in session variable
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
	
	// Retrieve current team ID in session
	$ctmID = $_SESSION['ctmID'];

	// Create team object to help determine if manager can add a game to this team
	// NOTE: ISSUE HERE MIGHT BE IF I MOVE ADD GAME FEATURE TO HOME PAGE
	$team = new Team();
	$team->setDB($db);
	$team->setTeamID($ctmID);
	$team->pullTeamData();
	$team->checkAuth($userID);

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		
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

		// Validate a result is enetered
		if ($_POST['res'])
		{
			$res = $_POST['res'];
		}
		else 
		{
			$res = ''; 
		}

		// Validate a note is enetered
		if ($_POST['note'])
		{
			$note = $_POST['note'];
		}
		else 
		{
			$note = ''; 
		}

		// Checks if team is selected and date format and entered time are valid before adding game to team.
		if ($ctmID && $gdfrmat && $tm)
		{

			// Create game object for use & push game to database for specified team
			$game = new Game();
			$game->setDB($db);
			$game->createGame($ctmID, $gdfrmat, $tm, $opp, $ven, $res, $note);
	
			$json[] = array('<p>Game was successfully added.</p><br />');

			// Send the JSON data:
			echo json_encode($json);

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
<p id="status"></p>
<div id="AddGameForm" title="Add New Game">
	
	<form method="post">
	<fieldset>

		<label for="date">Select Game Date:</label>
		<input type="text" name="date" id="date" tabindex="-1" size="10" maxlength="10" class="text ui-widget-content ui-corner-all" 
		value="<?php if (isset($_POST['date'])) echo $_POST['date']; ?>" />

		<label for="time">Enter Game Time:</label>
		<input type="text" name="time" id="time" size="9" maxlength="9" class="text ui-widget-content ui-corner-all" 
		value="<?php if (isset($_POST['time'])) echo $_POST['time']; ?>" />

		<label for="opp">Enter Opponent:</label>
		<input type="text" name="opp" id="opp" size="30" maxlength="45" class="text ui-widget-content ui-corner-all" 
		value="<?php if (isset($_POST['opp'])) echo $_POST['opp']; ?>" />

		<label for="ven">Enter Venue:</label>
		<input type="text" name="ven" id="ven" size="30" maxlength="45" class="text ui-widget-content ui-corner-all" 
		value="<?php if (isset($_POST['ven'])) echo $_POST['ven']; ?>" />

		<label for="note">Enter Game Notes:</label>
		<textarea id="note" name="note" cols="30" rows="2" class="text ui-widget-content ui-corner-all"> 
		<?php if (isset($_POST['note'])) echo $_POST['note']; ?></textarea><br />
		<small>Enter any notes about the game.</small><br />

		<label for="res">Enter Results:</label>
		<input type="text" name="res" id="res" size="13" maxlength="13" class="text ui-widget-content ui-corner-all" 
		value="<?php if (isset($_POST['res'])) echo $_POST['res']; ?>" />
		<small>Ex. W 4-3</small>
		
	</fieldset>
	</form>
</div>

<button id="add-game">Add Game</button>

<?php include '../includes/footer.html'; ?>
