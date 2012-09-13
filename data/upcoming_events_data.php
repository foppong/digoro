<?php
	/* upcoming_events_data.php
	* 
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

	// Get user ID
	$userID = $user->getUserID();

	// Make the Query:
	$q = "SELECT DATE_FORMAT(e.date, '%W: %M %e, %Y'), e.time, e.venue_name, tm.team_name
		FROM members AS mb
		INNER JOIN events AS e USING (id_team)
		INNER JOIN teams AS tm USING (id_team) 
		WHERE mb.id_user=? && e.date >= CURDATE()
		ORDER BY e.date ASC LIMIT 5";
		
	// Prepare the statement:
	$stmt = $db->prepare($q);
	
	// Bind the inbound variable:
	$stmt->bind_param('i', $userID);
		
	// Execute the query:
	$stmt->execute();		
			
	// Store results:
	$stmt->store_result();
		
	// Bind the outbound variable:
	$stmt->bind_result($dateOB, $timeOB, $venOB, $teamOB);
		
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{		
		// Fetch and print all records...
		while ($stmt->fetch())
		{		
			$json[] = array(
			'Edate' => $dateOB,
			'Etime' => $timeOB,
			'Venue' => $venOB,
			'TName' => $teamOB);
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

	// Close the connection:
	$db->close();
	unset($db);

?>