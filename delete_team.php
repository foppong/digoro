<?php
	// This page is for deleting a team record
	// This page is accessed through myteams-m.php
	
	require 'includes/config.php';
	$page_title = 'digoro : Delete Team';
	include 'includes/header.html';

	// autoloading of classes
	function __autoload($class) {
		require_once('classes/' . $class . '.php');
	}

	// Create user object
	$user = new UserAuth();

	// Site access level -> Manager
	$lvl = 'M'; 

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
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
		// Assign variable from delete_player.php FORM submission (hidden id field)
		$id = $_POST['z'];
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include 'includes/footer.html';
		exit();
	}

	require_once MYSQL;

	// Confirmation that form has been submitted:	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{	// Point D in Code Flow

		if ($_POST['sure'] == 'Yes')
		{	// If form submitted is yes, delete the record
		
			// Make the query	
			$q = "DELETE FROM teams WHERE id_team=? LIMIT 1";

			// Prepare the statement:
			$stmt = $db->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('i', $id);

			// Execute the query:
			$stmt->execute();
			
			// If the query ran ok.
			if ($stmt->affected_rows == 1) 
			{	// Print a message
				echo '<p>This team has been deleted successfully.</p>';
			}
			else 
			{	// If the query did not run ok.
				echo '<p class="error">The team could not be deleted due to a system errror.</p>';
				exit();
			}
		}
		else
		{	// No confirmation of deletion.
			echo '<p>The team has NOT been deleted.</p>';
		}
	}
	else
	{	// Point B in Code Flow. Show the form

		// Make the query to retreive user information:		
		$q = "SELECT team_name FROM teams WHERE id_team=?";		

		// Prepare the statement:
		$stmt = $db->prepare($q);

		// Bind the inbound variable:
		$stmt->bind_param('i', $id);
		
		// Execute the query:
		$stmt->execute();		
		
		// Store results:
		$stmt->store_result();
	
		// Bind the outbound variable:
		$stmt->bind_result($tnameOB);	
		
		// Valid user ID, show the form.
		if ($stmt->num_rows == 1)
		{
			while ($stmt->fetch())
			{	
				//Display the record being deleted:
				echo '<h3>Are you sure you want to delete Team ' . $tnameOB . ' from your profile?</h3>';
			}
			
			// Create the form:
			echo '<form action ="delete_team.php" method="post" id="DelTeamForm">
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
	
	} // End of the main submission conditional.
			
	// Close the connection:
	$db->close();
	unset($db);
					
	include 'includes/footer.html';
?>