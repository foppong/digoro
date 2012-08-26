<?php
	/* team_info_data.php
	* This script retrieves all the records for a team
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

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		// Retrieve current team ID from session variable
		$tm = $_SESSION['ctmID'];
	
		// Make the query
		$q = 'SELECT id_sport,id_manager,team_name,about,level_of_play,id_region,team_sex,team_email
			FROM teams WHERE id_team=? LIMIT 1';
			
		// Prepare the statement:
		$stmt = $db->prepare($q);
		
		// Bind the inbound variable:
		$stmt->bind_param('i', $tm);
			
		// Execute the query:
		$stmt->execute();		
				
		// Store results:
		$stmt->store_result();
		
		// Bind the outbound variables
		$stmt->bind_result($sprtIDOB, $manIDOB, $tmnameOB, $abtmOB, $lvlOB, $regOB, $sexOB, $tmemailOB);
			
		// If there are results to show.
		if ($stmt->num_rows > 0)
		{		
			// Fetch records...
			while ($stmt->fetch())
			{		
				$json[] = array(
				'Sport' => $sprtIDOB,
				'ManagerID' => $manIDOB,
				'Team Name' => $tmnameOB,
				'About' => $abtmOB,
				'Level' => $lvlOB,
				'Region' => $regOB,
				'Sex' => $sexOB,
				'TEmail' => $tmemailOB,
				'Team ID' => $tm);
			}	// End of WHILE loop
				
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