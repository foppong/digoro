<?php
	/** roster_data.php
	* This page queries a database, returnnig a list
	* of players on a roster
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

	// Make the Query:
	$q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, u.sex, 
		u.email, p.id_member, CONCAT(p.primary_position, ', ',p.secondary_position) AS pos, p.jersey_number
		FROM members AS p INNER JOIN users AS u
		USING (id_user)
		WHERE p.id_team=?";

	// Prepare the statement:
	$stmt = $db->prepare($q);
		
	// Bind the inbound variable:
	$stmt->bind_param('i', $tm);
			
	// Execute the query:
	$stmt->execute();		
				
	// Store results:
	$stmt->store_result();
			
	// Bind the outbound variable:
	$stmt->bind_result($nOB, $genOB, $eOB, $idOB, $posOB, $jnOB);
			
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{
		// Initialize an array:
		$json = array();	
						
		// Fetch and put results in the JSON array...
		while ($stmt->fetch())
		{								
				
			// Translate sex data from database
			switch ($genOB) {
				case 1: //  Female
					$sex = 'Female';
					break;
				
				case 2: // Male
					$sex = 'Male';
					break;
					
				default: 
					$sex = 'Undefined';
					break;
			}	

			$json[] = array(
			'Name' => $nOB,
			'Email' => $eOB,
			'Sex' => $sex,
			'Position' => $posOB,
			'Jersey' => $jnOB,
			'Edit' => '<button type="button" class="edit_member btn btn-mini" value=' . $idOB . '>Edit</button>',
			'Delete' => '<button type="button" class="delete_member btn btn-mini" value=' . $idOB . '>Delete</button>');
		}	// End of WHILE loop
	
		// Send the JSON data:
		echo json_encode($json);		
	}
	else 
	{	// No registered users
		$json[] = array(
			'<p class="error">You have no players on your roster. Click add player to add a player.</p><br />');
			
		// Send the JSON data:
		echo json_encode($json);
	}

	// Close the statement:
	$stmt->close();
	unset($stmt);			
	
	// Close the connection:
	$db->close();
	unset($db);

?>
