<?php
	/** league_data.php
	* This page queries a database, returnnig a list
	* of leagues
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

	// Assume invalid values:
	$st = FALSE;

	// Assign state variable from add_team.php ajax post
	if (!empty($_POST["state"])) 
	{
		$st = $_POST["state"];
	}
	
	// Checks if state is selected before querying database.
	if ($st)
	{
		// Make the Query:
		$q = "SELECT id_league, league_name FROM leagues WHERE state=?";
	
		// Prepare the statement:
		$stmt = $db->prepare($q);
			
		// Bind the inbound variable:
		$stmt->bind_param('s', $st);
				
		// Execute the query:
		$stmt->execute();		
					
		// Store results:
		$stmt->store_result();
				
		// Bind the outbound variable:
		$stmt->bind_result($idlgOB, $lgnmOB);
				
		// If there are results to show.
		if ($stmt->num_rows > 0)
		{
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch())
			{		
				$json[] = array(
				'LeagueID' => $idlgOB,
				'LeagueName' => $lgnmOB);
	
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
				'<p class="error">There are no leagues that match your query.</p><br />');
				
			// Send the JSON data:
			echo json_encode($json);
	
		}
	}


?>
