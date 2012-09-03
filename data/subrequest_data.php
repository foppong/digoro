<?php
	/** subrequest_data.php
	* This page queries a database, returnnig a list
	* of subrequest
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

	// Pull all subrequest associated with user's teams
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullOpenSRData') {
		// Make the Query to find all subrequests associated with user
		$q = "SELECT s.id_subrequest, s.id_team, s.sex_needed, DATE_FORMAT(e.date, '%a: %b %e, %Y'), tm.team_name, e.time
			FROM subrequests AS s 
			INNER JOIN teams AS tm USING (id_team)		
			INNER JOIN events AS e USING (id_event)
			WHERE s.id_user=?
			ORDER BY e.date ASC";
		
		// Prepare the statement:
		$stmt = $db->prepare($q);
		
		// Bind the inbound variable:
		$stmt->bind_param('i', $userID);
				
		// Execute the query:
		$stmt->execute();		
					
		// Store results:
		$stmt->store_result();
				
		// Bind the outbound variable:
		$stmt->bind_result($idSROB, $idtmOB, $sexOB, $dateOB, $tmnameOB, $timeOB);
				
		// If there are results to show.
		if ($stmt->num_rows > 0) {
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch()) {			
				$json[] = array(
				'Team' => $tmnameOB,
				'Sex Needed' => $sexOB,
				'Event Date' => $dateOB,
				'Event Time' => $timeOB,
				'Edit' => '<button type="button" id="edit-subreq" class="btn btn-mini" value=' . $idSROB . '>Edit</button>',
				'Delete' => '<button id="delete-subreq" class="btn btn-mini" value=' . $idSROB . '>Delete</button>');
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
				'<p class="error">You have no subrequests open. Click the create subrequest to create one.</p><br />');
				
			// Send the JSON data:
			echo json_encode($json);
		}
	}


	// Pull all sub request responses associated with users's teams
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullSRResponsesData') {

		// Make the query
		$q = "SELECT sr.id_sr_response, sr.id_subrequest, sr.id_user, sr.manager_respond, subr.id_team,
				DATE_FORMAT(e.date, '%a: %b %e, %Y'), e.time, tm.team_name
			FROM subreq_responses AS sr
			INNER JOIN events AS e USING (id_event)
			INNER JOIN teams AS tm USING (id_team)
			INNER JOIN subrequests AS subr USING (id_subrequest)
			WHERE subr.id_user=?";
	
		// Prepare the statement:
		$stmt = $db->prepare($q);
		
		// Bind the inbound variable:
		$stmt->bind_param('i', $userID);
				
		// Execute the query:
		$stmt->execute();		
					
		// Store results:
		$stmt->store_result();
				
		// Bind the outbound variable:
		$stmt->bind_result($idsubRes, $idsubResp, $iduserOB, $respOB, 
			$idTeamOB, $dateOB, $timeOB, $tmnameOB);
				
		// If there are results to show.
		if ($stmt->num_rows > 0) {
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch()) {			
				// Make the query
				$q = "SELECT sex, CONCAT(first_name, ' ', last_name) AS name
					FROM users WHERE id_user=?";
					
				// Prepare the statement	
				$stmt2 = $db->prepare($q);
				
				// Bind the inbound variable
				$stmt2->bind_param('i', $iduserOB);
				
				// Execute the query
				$stmt2->execute();
				
				// Store the results
				$stmt2->store_result();
				
				// Bind the outbound variables
				$stmt2->bind_result($sexOB, $nameOB);
				
				// If there are results to show.
				if ($stmt2->num_rows > 0) {
					while ($stmt2->fetch()){
						$membersex = translateSex($sexOB);
						$membername = $nameOB;
					}
				}	

				// Close the statement:
				$stmt2->close();
				unset($stmt2);	

				// Translate status from database
				$status = translateSubResStatus($respOB);

				$json[] = array(
				'Name' => $membername,
				'Sex' => $membersex,
				'Team' => $tmnameOB,
				'Event Date' => $dateOB,
				'Event Time' => $timeOB,
				'Status' => $status,
				'Take Action' => '<button id="respond-subres" class="btn btn-mini" value=' . $idsubRes . '>Respond</button>');
				
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
