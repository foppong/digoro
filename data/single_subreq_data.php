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

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$subReqID = $_POST['idSubReq'];

	
		// Make the Query to find all subrequests associated with user
		$q = "SELECT s.id_team, s.sex_needed, DATE_FORMAT(e.date, '%a: %b %e, %Y'), tm.team_name, e.time
			FROM subrequests AS s 
			INNER JOIN teams AS tm USING (id_team)		
			INNER JOIN events AS e USING (id_event)
			WHERE s.id_subrequest=?";
		
		// Prepare the statement:
		$stmt = $db->prepare($q);
		
		// Bind the inbound variable:
		$stmt->bind_param('i', $subReqID);
				
		// Execute the query:
		$stmt->execute();		
					
		// Store results:
		$stmt->store_result();
				
		// Bind the outbound variable:
		$stmt->bind_result($idtmOB, $sexOB, $dateOB, $tmnameOB, $timeOB);
				
		// If there are results to show.
		if ($stmt->num_rows > 0)
		{
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch())
			{			
				$json[] = array(
				'Team' => $tmnameOB,
				'Sex Needed' => $sexOB,
				'Event Date' => $dateOB,
				'Event Time' => $timeOB);
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
		else 
		{	// No registered users
	
			$json[] = array(
				'<p class="error">You have no subrequests open. Click the create subrequest to create one.</p><br />');
				
			// Send the JSON data:
			echo json_encode($json);
	
		}
	}

?>
