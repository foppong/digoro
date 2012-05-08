<?php
	// This page is for deleting a player record
	// This page is accessed through view_roster.php
	
	require '../includes/config.php';
	$page_title = 'digoro : Delete Player';
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

	// Need the database connection:
	require_once MYSQL2;

	if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) ) // From view roster page
	{
		$id = $_GET['x'];
	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted from delete_player page	
	{
		$id = $_POST['z'];

		if ($_POST['sure'] == 'Yes')
		{	// If form submitted is yes, delete the record
		
			// Make the query	
			$q = "DELETE FROM players WHERE id_player=? LIMIT 1";

			// Prepare the statement:
			$stmt = $db->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('i', $id);

			// Execute the query:
			$stmt->execute();
			
			// If the query ran ok.
			if ($stmt->affected_rows == 1) 
			{	
				// Print a message
				echo '<p>The player has been deleted successfully.</p>';
				include '../includes/footer.html';
				exit();				
			}
			else 
			{	// If the query did not run ok.
				echo '<p class="error">The player could not be deleted due to a system errror.</p>';
				exit();
			}
		}
		else
		{	// No confirmation of deletion.
			echo '<p>The player has NOT been deleted.</p>';
		}
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();		
	}

	// Make the query to retreive user information:		
	$q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name
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
	$stmt->bind_result($nameOB);	
		
	// Valid user ID, show the form.
	if ($stmt->num_rows == 1)
	{
		while ($stmt->fetch())
		{
			//Display the record being deleted:
			echo '<h3>Are you sure you want to delete the player ' . $nameOB . ' from your roster?</h3>';
		}
		
		// Create the form:
		echo '<form action ="delete_player.php" method="post" id="DelPlayerForm">
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
		
	// Close the statement:
	$stmt->close();
	unset($stmt);

			
	// Close the connection:
	$db->close();
	unset($db);
					
	include '../includes/footer.html';
?>