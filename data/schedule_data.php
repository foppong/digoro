<?php
	/* schedule_data.php
	* This script retrieves all the records from the schedule table.
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

	// Validate user
	checkSessionObject();

	// Need the database connection:	
	require_once MYSQL2;

	// Assign user object from session variable
	$user = $_SESSION['userObj'];
	$userID = $user->getUserID();

	// Retrieve current team ID from session variable & create team object
	$tm = $_SESSION['ctmID'];
	$team = new Team();
	$team->setDB($db);

	// Check user's role on team
	$isManager = $team->isManager($userID, $tm);

	// Pulls data of all members on a team
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullScheduleData') {

		// Make the Query:
		$q = "SELECT id_event, DATE_FORMAT(date, '%a: %b %e, %Y'), time, opponent, venue_name, result, type
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
		$stmt->bind_result($idOB, $dateOB, $timeOB, $oppOB, $venOB, $resOB, $typeOB);
			
		// If there are results to show.
		if ($stmt->num_rows > 0)
		{		
			// Fetch and print all records...
			while ($stmt->fetch())
			{		
	
				// Translate event type data from database
				$type = translateEventType($typeOB);

				// Adjust display based on user's role
				if ($isManager == TRUE) {
					
					$json[] = array(
					'Type' => $type,
					'Date' => $dateOB,
					'Time' => $timeOB,
					'Opponent' => stripslashes($oppOB),
					'Venue' => stripslashes($venOB),
					'Result' => $resOB,
					'Details' => '<button type="button" class="view_event btn btn-mini" value=' . $idOB . '>View</button>',
					'Edit' => '<button type="button" class="edit_event btn btn-mini" value=' . $idOB . '>Edit</button>',
					'Delete' => '<button type="button" class="delete_event btn btn-mini" value=' . $idOB . '>Delete</button>');					
				} else {
					
					$json[] = array(
					'Type' => $type,
					'Date' => $dateOB,
					'Time' => $timeOB,
					'Opponent' => stripslashes($oppOB),
					'Venue' => stripslashes($venOB),
					'Result' => $resOB,
					'Details' => '<button type="button" class="view_event btn btn-mini" value=' . $idOB . '>View</button>');					
				}
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
	}

	// Close the statement:
	$stmt->close();
	unset($stmt);			

	// Close the connection:
	$db->close();
	unset($db);

?>