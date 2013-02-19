<?php
	/** subresponse_data.php
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

	// Request is coming from profile view to query all subresponses associated with user
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'loadmySRResponses') {
		
		// Make the Query
		$q = "SELECT subr.id_sr_response, subr.manager_respond, DATE_FORMAT(e.date, '%a: %b %e, %Y'), e.time, tm.id_sport
			FROM subreq_responses AS subr
			INNER JOIN events AS e USING (id_event)			
			INNER JOIN teams AS tm USING (id_team)				
			WHERE subr.id_user=?
			ORDER BY e.date";

		// Prepare the statement:
		$stmt = $db->prepare($q);
			
		// Bind the inbound variable:
		$stmt->bind_param('i', $userID);

		// Execute the query:
		$stmt->execute();		
						
		// Store results:
		$stmt->store_result();
					
		// Bind the outbound variable:
		$stmt->bind_result($srrID, $respOB, $dateOB, $timeOB, $sportOB);
		
		// If there are results to show.
		if ($stmt->num_rows > 0) {
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch()) {			

				// Translate data from database
				$status = translateSubResStatus($respOB);
				$sport = translateSport($sportOB);

				$json[] = array(
				'Sport' => $sport,
				'Event Date' => $dateOB,
				'Event Time' => $timeOB,
				'Status' => $status,
				'Details' => '<button id="view-subreq" class="btn btn-mini" value=' . $srrID . '>View</button>');
			}	// End of WHILE loop
		
			// Send the JSON data:
			echo json_encode($json);
					
			// Close the statement:
			$stmt->close();
			unset($stmt);			
		}		
		
	}


	// Request is coming from the respond SubResponse form on the manager Find Subs page
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'Manager_single_SubResp_Data') {

		$idSubResponse = $_POST['idSubResp'];

		// Make the Query:
		$q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, subr.comments
			FROM subreq_responses AS subr INNER JOIN users AS u
			USING (id_user)
			WHERE subr.id_sr_response=? LIMIT 1";
	
		// Prepare the statement:
		$stmt = $db->prepare($q);
			
		// Bind the inbound variable:
		$stmt->bind_param('i', $idSubResponse);
				
		// Execute the query:
		$stmt->execute();		
					
		// Store results:
		$stmt->store_result();
				
		// Bind the outbound variable:
		$stmt->bind_result($nameOB, $comments);
				
		// If there are results to show.
		if ($stmt->num_rows > 0)
		{
			// Initialize an array:
			$json = array();	
							
			// Fetch and put results in the JSON array...
			while ($stmt->fetch())
			{								
				$json[] = array(
				'Name' => $nameOB,
				'Comment' => $comments);
			}	// End of WHILE loop
		
			// Send the JSON data:
			echo json_encode($json);	
		}
	
		// Close the statement:
		$stmt->close();
		unset($stmt);			
	}


	// Request is coming from the home page, so user can view the subresponse detail
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'User_single_SubResp_Data') {

			$idSubResponse = $_POST['idSubResp'];
		
			// Make the Query
			$q = "SELECT tm.team_name, tm.level_of_play, e.venue_name, e.venue_address
				FROM subreq_responses AS subr
				INNER JOIN events AS e USING (id_event)
				INNER JOIN teams AS tm USING (id_team)										
				WHERE subr.id_sr_response=? LIMIT 1";
	
			// Prepare the statement:
			$stmt = $db->prepare($q);
			
			// Bind the inbound variable:
			$stmt->bind_param('i', $idSubResponse);
					
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
					
					// Translate level of play
					$tmlevel = translateLevelofPlay($tmlvlOB);
									
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
		}
	}

	// Close the connection:
	$db->close();
	unset($db);

?>
