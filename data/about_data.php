<?php
	/** about_data.php
	* This page queries a database, returnnig
	* information about the team
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

	// Retrieve current team ID from session variable
	$tm = $_SESSION['ctmID'];

	// Define the Query 
	$q = "SELECT about, team_name FROM teams WHERE id_team=? LIMIT 1";

	// Prepare the statement:
	$stmt = $db->prepare($q);
		
	// Bind the inbound variable:
	$stmt->bind_param('i', $tm);
			
	// Execute the query:
	$stmt->execute();		
				
	// Store results:
	$stmt->store_result();
			
	// Bind the outbound variable:
	$stmt->bind_result($abtOB, $tmnmOB);
			
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{
		// Initialize an array:
		$json = array();
				
		// Fetch and put results in the JSON array...
		while ($stmt->fetch())
		{		
			$json[] = array(
			'TeamAbout' => stripslashes($abtOB), // If I get PHP >5.3 I believe I can use optional parameter in json_encode
			'TeamName' => stripslashes($tmnmOB),
			'Edit' => '<button type="button" id="editTeam" value=' . $tm . '>Edit</button>');			
		}	// End of WHILE loop
	
		// Send the JSON data:
		echo json_encode($json);
				
		// Close the statement:
		$stmt->close();
		unset($stmt);			
	}
	
	// Close the connection:
	$db->close();
	unset($db);	
?>
