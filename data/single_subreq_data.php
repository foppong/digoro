<?php
	/** single_subreq_data.php
	* This page queries a database, returnnig a single subrequest
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

	// If request is coming from the View SubRequest from the Profile page
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['idSubReq'])) {

		$subReqID = $_POST['idSubReq'];

		// Make the Query to find subrequest, event, and team info 
		$q = "SELECT tm.team_name, tm.level_of_play, e.venue_name, e.venue_address
			FROM subrequests AS s 
			INNER JOIN teams AS tm USING (id_team)		
			INNER JOIN events AS e USING (id_event)
			WHERE s.id_subrequest=? LIMIT 1";
		
		// Prepare the statement:
		$stmt = $db->prepare($q);
		
		// Bind the inbound variable:
		$stmt->bind_param('i', $subReqID);
				
		// Execute the query:
		$stmt->execute();		
					
		// Store results:
		$stmt->store_result();
				
		// Bind the outbound variable:
		$stmt->bind_result($tmnameOB, $tmlvlOB, $venOB, $venaddOB);
				
		// If there are results to show.
		if ($stmt->num_rows == 1)
		{
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch())
			{
				
				// Translate level of play data from database
				switch ($tmlvlOB) {
					case 1: //  Recreational
						$tmlevel = 'Recreational';
						break;
					
					case 2: // Intermediate
						$tmlevel = 'Intermediate';
						break;
					
					case 3: // Advanced
						$tmlevel = 'Advanced';
						break;
						
					default: 
						$tmlevel = 'Recreational';
						break;
				}				
				
				$json[] = array(
				'Team Name' => $tmnameOB,
				'Team Level' => $tmlevel,
				'Venue Name' => $venOB,
				'Venue Addr' => $venaddOB);
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

	// Request is coming from the edit SubRequest on the manager Find Subs page
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['subRequestID'])) {

		$subReqID = $_POST['subRequestID'];

		// Make the Query
		$q = "SELECT id_team, id_event, sex_needed, experience_needed, id_region
			FROM subrequests 
			WHERE id_subrequest=? LIMIT 1";
		
		// Prepare the statement:
		$stmt = $db->prepare($q);
		
		// Bind the inbound variable:
		$stmt->bind_param('i', $subReqID);
				
		// Execute the query:
		$stmt->execute();		
					
		// Store results:
		$stmt->store_result();
				
		// Bind the outbound variable:
		$stmt->bind_result($tmidOB, $eventidOB, $sexOB, $expOB, $regOB);
				
		// If there are results to show.
		if ($stmt->num_rows == 1)
		{
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch())
			{
						
				$json[] = array(
				'Team ID' => $tmidOB,
				'Event ID' => $eventidOB,
				'Sex' => $sexOB,
				'Experience' => $expOB,
				'Region' => $regOB);
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
