<?php
	/** team_data.php
	* This page queries a database, returnnig a list
	* of teams
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

	// Make the Query to find all teams associated with user via a union of the members and teams table:
	$q = "SELECT m.id_team, t.team_name, t.id_user
		FROM members AS m INNER JOIN teams AS t
		USING (id_team)
		WHERE m.id_user=?";
	
	// Prepare the statement:
	$stmt = $db->prepare($q);
		
	// Bind the inbound variable:
	$stmt->bind_param('i', $userID);
			
	// Execute the query:
	$stmt->execute();		
				
	// Store results:
	$stmt->store_result();
			
	// Bind the outbound variable:
	$stmt->bind_result($idteamOB, $tnameOB, $idmanagerOB);
			
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{
		// Initialize an array:
		$json = array();
				
		// Fetch and put results in the JSON array...
		while ($stmt->fetch())
		{		
			if ($userID == $idmanagerOB) {
				$json[] = array(
				'TeamID' => $idteamOB,
				'TeamName' => stripslashes($tnameOB));
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
	else 
	{	// No registered users

		$json[] = array(
			'<p class="error">You have no teams associated with your account.</p><br />');
			
		// Send the JSON data:
		echo json_encode($json);

	}

?>
