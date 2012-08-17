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

	// Retrieve current team ID from session variable
	$tm = $_SESSION['ctmID'];

	// Make the Query:
	$q = "SELECT id_event, DATE_FORMAT(date, '%a: %b %e, %Y'), time, opponent, venue, result
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
			'Edit' => '<button class="edit_event" value=' . $idOB . '>Edit</button>',
			'Delete' => '<button class="delete_event" value=' . $idOB . '>Delete</button>');
		}	// End of WHILE loop
			
		// Send the JSON data:
		echo json_encode($json);

	}
	else 
	{	// No events or events scheduled
		
		$json[] = array('<p class="error">You have no events scheduled. Click the add event button to add a event.');
			
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