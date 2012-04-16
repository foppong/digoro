<?php
	/** team_data.php
	* This page queries a database, returnnig a list
	* of teams
	*/
	
	ob_start();
	session_start();
			
	require 'includes/config.php';
	
	// autoloading of classes
	function __autoload($class) {
		require_once('classes/' . $class . '.php');
	}

	// Create user object
	$user = new UserAuth();

	// Site access level -> General
	$lvl = 'G'; 

	// Authorized Login Check
	if (!$user->valid($lvl))
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
	$q = "SELECT p.id_team, t.team_name
		FROM players AS p INNER JOIN teams AS t
		USING (id_team)
		WHERE p.id_user=?";

		
	// Prepare the statement:
	$stmt = $db->prepare($q);
		
	// Bind the inbound variable:
	$stmt->bind_param('i', $userID);
			
	// Execute the query:
	$stmt->execute();		
				
	// Store results:
	$stmt->store_result();
			
	// Bind the outbound variable:
	$stmt->bind_result($idtmOB, $tmnmOB);
			
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{
		// Initialize an array:
		$json = array();
				
		// Fetch and put results in the JSON array...
		while ($stmt->fetch())
		{		
			$json[] = array(
			'TeamID' => $idtmOB,
			'TeamName' => stripslashes($tmnmOB)); // If I get PHP >5.3 I believe I can use optional parameter in json_encode

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
			'<p class="error">You have no teams associated with your account.</p><br />');
			
		// Send the JSON data:
		echo json_encode($json);

	}

?>
