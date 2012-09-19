<?php
	/** subrequest_matches_data.php
	* This page queries a database, returnnig a list
	* of subrequests that match
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
	$userSex = $user->getUserAttribute('gd'); // Can utilize this value in logic later in queries

	// Make the Query to find all subrequests associated with user
	$q = "SELECT id_region, id_sport FROM profiles 
		WHERE id_user=?";
	
	// Prepare the statement:
	$stmt = $db->prepare($q);
	
	// Bind the inbound variable:
	$stmt->bind_param('i', $userID);
			
	// Execute the query:
	$stmt->execute();		
				
	// Store results:
	$stmt->store_result();
			
	// Bind the outbound variable:
	$stmt->bind_result($idregOB, $idsportOB);
			
	// If there are results to show.
	if ($stmt->num_rows > 0) {

		// Initialize an array:
		$json = array();

		// Fetch
		while ($stmt->fetch()) {			
			// Make the Query
			$q = "SELECT sr.id_subrequest, DATE_FORMAT(e.date, '%a: %b %e, %Y'), e.time
				FROM subrequests AS sr
				INNER JOIN teams AS tm USING (id_team)				
				INNER JOIN events AS e USING (id_event)
				WHERE sr.id_region=? AND tm.id_sport=?
				ORDER BY e.date";

			// Prepare the statement:
			$stmt2 = $db->prepare($q);
			
			// Bind the inbound variable:
			$stmt2->bind_param('ii', $idregOB, $idsportOB);
					
			// Execute the query:
			$stmt2->execute();		
						
			// Store results:
			$stmt2->store_result();
					
			// Bind the outbound variable:
			$stmt2->bind_result($idSROB, $dateOB, $timeOB);
					
			// If there are results to show.
			if ($stmt2->num_rows > 0)
			{			
				// Fetch and put results in the JSON array...
				while ($stmt2->fetch()) {	

					$sport = translateSport($idsportOB);

					$json[] = array(
					'Sport' => $sport,
					'Event Date' => $dateOB,
					'Event Time' => $timeOB, 
					'Take Action' => '<button type="button" id="view-subreq" class="btn btn-mini" value=' . $idSROB . '>View</button>');			
				} // End of WHILE loop

			// Close the statement:
			$stmt2->close();
			unset($stmt2);
			}						
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
	else {	// No registered users

		$json[] = array(
			'<p class="error">You have no subrequests matches. Create a sport profile to get linked with teams.</p><br />');
			
		// Send the JSON data:
		echo json_encode($json);

	}

?>
