<?php
	/** single_event_data.php
	* This page queries a database, returnnig a single event data
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

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$eventID = $_POST['eventID'];

		// Make the query to retreive event information from events table in database:		
		$q = "SELECT date, time, opponent, venue_name, venue_address, result, note, type
			FROM events
			WHERE id_event=? LIMIT 1";
		
		// Prepare the statement:
		$stmt = $db->prepare($q);
		
		// Bind the inbound variable:
		$stmt->bind_param('i', $eventID);
				
		// Execute the query:
		$stmt->execute();		
					
		// Store results:
		$stmt->store_result();
				
		// Bind the outbound variable:
		$stmt->bind_result($gdateOB, $gtmOB, $oppOB, $venOB, $venadOB, $resOB, $noteOB, $typeOB);
				
		// If there are results to show.
		if ($stmt->num_rows == 1)
		{
			// Initialize an array:
			$json = array();

			// Translate database data					
			$eventtxt = translateEventType($typeOB);				
			$bd = new DateTime($gdateOB);
			$gdfrmat = $bd->format('m-d-Y');

			// Fetch and put results in the JSON array...
			while ($stmt->fetch())
			{			
				$json[] = array(
				'Event Type' => $typeOB,
				'Event Date' => $gdfrmat,
				'Event Time' => $gtmOB,
				'Event Oppo' => $oppOB,
				'Event Ven Name' => $venOB,
				'Event Ven Addr' => $venadOB,
				'Event Note' => $noteOB,								
				'Event Res' => $resOB,
				'Event Text' => $eventtxt);
			}	// End of WHILE loop
		
			// Send the JSON data:
			echo json_encode($json);
					
			// Close the statement:
			$stmt->close();
			unset($stmt);			
		
			// Close the connection:
			$db->close();
			unset($db);
		}
	}

?>
