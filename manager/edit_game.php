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
		$user = $_SESSION['userObj'];
	}
	else 
	{
		redirect_to('index.php');
	}

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		redirect_to('index.php');
	}

	// Establish database connection
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['x'])) // Confirmation that form has been submitted from schedule page
	{
		$id = $_POST['x'];
	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted from edit_player page	
	{
		$id = $_POST['z'];

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
			// Update the user's info in the players' table in database
			$q = 'UPDATE schedules SET date=?, time=?, opponent=?, venue=?, result=?
				WHERE id_sch=? LIMIT 1';

			// Prepare the statement
			$stmt = $db->prepare($q); 

			// Bind the inbound variables:
			$stmt->bind_param('sssssi', $bdfrmat, $tm, $opp, $ven, $res, $id);
				
			// Execute the query:
			$stmt->execute();

			if ($stmt->affected_rows == 1) // And update to the database was made
			{				
				echo '<p>The game has been edited.</p>';
			}
			else 
			{	// Either did not run ok or no updates were made
				echo '<p>No changes were made.</p>';
			}
		}
		else
		{	// Errors in the user entered information
			echo '<p class="error">Please try again.</p>';
		}
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();		
	}

	// Always show the form...
	
	// Make the query to retreive game information from schedules table in database:		
	$q = "SELECT date, time, opponent, venue, result
		FROM schedules
		WHERE id_sch=? LIMIT 1";

	// Prepare the statement:
	$stmt = $db->prepare($q);

	// Bind the inbound variable:
	$stmt->bind_param('i', $id);
		
	// Execute the query:
	$stmt->execute();		
		
	// Store results:
	$stmt->store_result();
	
	// Bind the outbound variable:
	$stmt->bind_result($bdfrmatOB, $tmOB, $oppOB, $venOB, $resOB);	
		
	// Valid user ID, show the form.
	if ($stmt->num_rows == 1)
	{
		while ($stmt->fetch())
		{
			// Headliner
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
	}
	else 
	{	//Not a valid user ID, kill the script
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();
	}
		
	// Close the statement:
	$stmt->close();
	unset($stmt);
			
	// Close the connection:
	$db->close();
	unset($db);
					
	include '../includes/footer.html';
?>