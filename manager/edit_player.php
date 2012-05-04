<?php
	// This page is for editing a player
	// This page is accessed through view_roster.php
	
	require '../includes/config.php';
	$page_title = 'digoro : Edit Player';
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

	// Check for a valid user ID, through GET or POST:
	if ( (isset($_GET['z'])) && (is_numeric($_GET['z'])) )
	{
		// Point A in Code Flow
		// Assign variable from view_roster.php using GET method
		$id = $_GET['z'];
	}
	elseif ( (isset($_POST['z'])) && (is_numeric($_POST['z'])) )
	{
		// Point C in Code Flow
		// Assign variable from edit_player.php FORM submission (hidden id field)
		$id = $_POST['z'];
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();
	}

	// Establish database connection
	require_once MYSQL2;

	// Confirmation that form has been submitted:	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{	// Point D in Code Flow

		// Trim all the incoming data:
		$trimmed = array_map('trim', $_POST);
		
		// Assume invalid values:
		$pos = $jnumb = FALSE;
		
		// Validate position input
		if (is_string($trimmed['position']))
		{
			$pos = $trimmed['position'];
		}
		else 
		{
			echo '<p class="error">Please enter your position.</p>';
		}

		// Validate jersey number input
		if (filter_var($_POST['jersey_num'], FILTER_VALIDATE_INT))
		{
			$jnumb = $_POST['jersey_num'];
		}
		else 
		{
			echo '<p class="error">Please enter your jersey number.</p>';
		}
		

		// Check if user entered information is valid before continuing to edit player
		if ($pos && $jnumb)
		{
			// Update the user's info in the database
			$q = 'UPDATE players SET position=?, jersey_number=? 
				WHERE id_player=? LIMIT 1';

			// Prepare the statement
			$stmt = $db->prepare($q); 

			// Bind the inbound variables:
			$stmt->bind_param('ssi', $pos, $jnumb, $id);
				
			// Execute the query:
			$stmt->execute();

			if ($stmt->affected_rows == 1) // And update to the database was made
			{				
				echo '<p>The players profile has been edited.</p>';
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
	}	// End of submit conditional.

	// Point B in Code Flow
	// Always show the form...
	
	// Make the query to retreive user information:		
	$q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, p.position, p.jersey_number
		FROM players AS p INNER JOIN users AS u
		USING (id_user)
		WHERE p.id_player=?";

	// Prepare the statement:
	$stmt = $db->prepare($q);

	// Bind the inbound variable:
	$stmt->bind_param('i', $id);
		
	// Execute the query:
	$stmt->execute();		
		
	// Store results:
	$stmt->store_result();
	
	// Bind the outbound variable:
	$stmt->bind_result($nameOB, $posOB, $jnumbOB);	
		
	// Valid user ID, show the form.
	if ($stmt->num_rows == 1)
	{
		while ($stmt->fetch())
		{
			// Headliner
			echo '<h2>Edit ' . $nameOB . '\'s Player Profile</h2>';
			
			// Create the form:
			echo '<form action ="edit_player.php" method="post" id="EditPlayerForm">
				<fieldset>
				<input type="hidden" name="z" value="' . $id . '" />
				
				<div>
					<label for="position"><b>Position:</b></label>
					<input type="text" name="position" id="position" 
					size="20" maxlength="20" value="' . $posOB . '" />				
				</div>
				
				<div>
					<label for="jersey_num"><b>Jersey Number:</b></label>
					<input type="text" name="jersey_num" id="jersey_num" 
					size="4" maxlength="4" value="' . $jnumbOB . '" />
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