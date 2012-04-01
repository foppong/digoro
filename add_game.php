<?php
	// add_game.php
	// This page allows a logged-in user to add a game to the schedule
		
	require 'includes/config.php';
	$page_title = 'digoro : Add Game';
	include 'includes/header.html';

	// Authorized Login Check
	// If not an administrator or manager, or no session value is present, redirect the user. Also validate the HTTP_USER_AGENT
	if (($_SESSION['role'] == 'P') OR !isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();
	}

	// Set up autoloading of classes
	function __autoload($class) {
		require_once('classes/' . $class . '.php');
	}


	// Assign team ID from session variable
	$idtm = $_SESSION['deftmID'];
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		require_once MYSQL;
		
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

		// Checks if team is selected and date format and entered time are valid before adding game to database.
		if ($idtm && $bdfrmat && $tm)
		{
			//$game = new Game($idtm, $bdfrmat, $tm, $db);
			
			// Make the query:
			$q = 'INSERT INTO schedules (id_team, date, time, opponent, venue, result) VALUES (?,?,?,?,?,?)';

			// Prepare the statement
			$stmt = $db->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('isssss', $idtm, $bdfrmat, $tm, $opp, $ven, $res);
			
			// Execute the query:
			$stmt->execute();
			
			// Print a message based upon result:
			if ($stmt->affected_rows == 1)
			{
				echo '<p>Your game was added succesfully.</p>';
			}
			else
			{
				echo '<p class="error">Your game was not added. Please contact the service administrator.</p>';
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);
			
			// Close the connection:
			$db->close();
			unset($db);
			
			include 'includes/footer.html';
			exit();			
		}
		else 
		{									
			echo '<p class="error">Please try again.</p>';
		}
	
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

<?php include 'includes/footer.html'; ?>
