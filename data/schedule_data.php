<?php
	/* schedule_data.php
	* For managers: This script retrieves all the records from the schedule table.
	* 
	*/
	
	ob_start();
	session_start();
			
	require '../includes/config.php';
	include '../includes/php-functions.php';

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
		redirect_to('index.php');
	}

	// Need the database connection:	
	require_once MYSQL2;
	
	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		redirect_to('index.php');
	}


	// Retrieve current team ID from session variable
	$tm = $_SESSION['ctmID'];

	// Make the Query:
	$q = "SELECT id_game, DATE_FORMAT(date, '%a: %b %e, %Y'), time, opponent, venue, result
		FROM games
		WHERE id_team=?
		ORDER BY date ASC";
		
	// Prepare the statement:
	$stmt = $db->prepare($q);
	
	// Bind the inbound variable:
	$stmt->bind_param('i', $tm);
		
	// Execute the query:
	$stmt->execute();		
			
	// Store results:
	$stmt->store_result();
		
	// Bind the outbound variable:
	$stmt->bind_result($idOB, $dateOB, $timeOB, $oppOB, $venOB, $resOB);
		
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{		
		// Fetch and print all records...
		while ($stmt->fetch())
		{		
			$json[] = array(
			'Date' => $dateOB,
			'Time' => $timeOB,
			'Opponent' => stripslashes($oppOB),
			'Venue' => stripslashes($venOB),
			'Result' => $resOB,	
			'Edit' => '<a href=edit_game.php?x=' . $idOB . '>Edit</a>',
			'Delete' => '<a href=delete_game.php?x=' . $idOB . '>Delete</a>');
		}	// End of WHILE loop
			
		// Send the JSON data:
		echo json_encode($json);

	}
	else 
	{	// No games or events scheduled
		
		$json[] = array(
			'<p class="error">You have no games scheduled.
			<a href="add_game.php">Click Here</a> to a game or event.<br /></p><br /><br />');
			
		// Send the JSON data:
		echo json_encode($json);
	}	

	// Close the statement:
	$stmt->close();
	unset($stmt);			

	// Delete objects
	unset($gdt);
	unset($user);

	// Close the connection:
	$db->close();
	unset($db);

?>

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
