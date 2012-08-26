<?php
	/** single_member_data.php
	* This page queries a database, returnnig a single event data
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

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$memberID = $_POST['memberID'];

		// Make the query to retreive user information:		
		$q = "SELECT u.first_name, u.last_name, u.sex, p.primary_position, 
			p.secondary_position, p.jersey_number
			FROM members AS p INNER JOIN users AS u
			USING (id_user)
			WHERE p.id_member=? LIMIT 1";
		
		// Prepare the statement:
		$stmt = $db->prepare($q);
		
		// Bind the inbound variable:
		$stmt->bind_param('i', $memberID);
				
		// Execute the query:
		$stmt->execute();		
					
		// Store results:
		$stmt->store_result();
				
		// Bind the outbound variable:
		$stmt->bind_result($fnameOB, $lnameOB, $sexOB, $pposOB, $sposOB, $jnumbOB);
				
		// If there are results to show.
		if ($stmt->num_rows == 1)
		{
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch())
			{			
				$json[] = array(
				'First Name' => $fnameOB,
				'Last Name' => $lnameOB,
				'Member Sex' => $sexOB,
				'Primary Position' => $pposOB,
				'Secondary Position' => $sposOB,
				'Jersey Num' => $jnumbOB);
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
