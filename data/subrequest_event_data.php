<?php
	/* subrequest_game_data.php
	* This script retrieves all the records from the schedule table for team.
	* 
	*/
	
	ob_start();
	session_start();
			
	require '../includes/config.php';
	require '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Validate user
	checkSessionObject();	

	// Assign user object from session variable
	$user = $_SESSION['userObj'];
	$userID = $user->getUserID();

	// Need the database connection:	
	require_once MYSQL2;

	// Assume invalid values:
	$tm = FALSE;

	// Assign state variable from find_sub_view ajax call
	if (!empty($_POST["teamID"])) 
	{
		$tm = $_POST["teamID"];
	}

	// Checks if team is selected before querying database.
	if ($tm)
	{
		// Make the Query:
		$q = "SELECT id_event, DATE_FORMAT(date, '%a: %b %e, %Y')
			FROM events
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
		$stmt->bind_result($idOB, $dateOB);
			
		// If there are results to show.
		if ($stmt->num_rows > 0)
		{		
			// Fetch and print all records...
			while ($stmt->fetch())
			{		
				$json[] = array(
				'EventID' => $idOB,
				'DateInfo' => $dateOB);
			}	// End of WHILE loop
				
			// Send the JSON data:
			echo json_encode($json);
		}
		else 
		{	// No events or events scheduled
			
			$json[] = array('<p class="error">You have no events scheduled.');
				
			// Send the JSON data:
			echo json_encode($json);
		}	
	
		// Close the statement:
		$stmt->close();
		unset($stmt);			
	}
	
	// Close the connection:
	$db->close();
	unset($db);

?>