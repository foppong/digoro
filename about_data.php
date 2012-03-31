<?php
	/** about_data.php
	* This page queries a database, returnnig
	* information about the team
	*/
	
	ob_start();
	session_start();
			
	require 'includes/config.php';

	// Authorized Login Check
	// If no session value is present, redirect the user. Also validate the HTTP_USER_AGENT	
	if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}

	// Need the database connection:	
	require_once MYSQL;

	$userID = $_SESSION['userID'];
	$tm = $_SESSION['deftmID'];

	// Make the Query to find all teams associated with user via a union of the players and teams table:
	$q = "SELECT about
		FROM teams
		WHERE id_team=?";

	// Prepare the statement:
	$stmt = $db->prepare($q);
		
	// Bind the inbound variable:
	$stmt->bind_param('i', $tm);
			
	// Execute the query:
	$stmt->execute();		
				
	// Store results:
	$stmt->store_result();
			
	// Bind the outbound variable:
	$stmt->bind_result($abtOB);
			
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{
		// Initialize an array:
		$json = array();
				
		// Fetch and put results in the JSON array...
		while ($stmt->fetch())
		{		
			$json[] = array(
			'TeamAbout' => stripslashes($abtOB)); // If I get PHP >5.3 I believe I can use optional parameter in json_encode

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
?>
