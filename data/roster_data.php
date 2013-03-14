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

	// Validate user
	checkSessionObject();

	// Need the database connection:	
	require_once MYSQL2;

	// Assign user object from session variable
	$user = $_SESSION['userObj'];
	$userID = $user->getUserID();

	// Retrieve current team ID from session variable
	$tm = $_SESSION['ctmID'];	

	// Pulls data of all members on a team
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullRosterData') {
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
				$sex = translateSex($genOB);
	
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
				'You have no members on your roster. Click add player to add a member.');
				
			// Send the JSON data:
			echo json_encode($json);
		}
	
		// Close the statement:
		$stmt->close();
		unset($stmt);			
	}


	// Pulls data of all members on a team for the transfer list
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullTransferListData') {
		// Make the Query:
		$q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, u.id_user, u.registration_date
			FROM members AS m INNER JOIN users AS u
			USING (id_user)
			WHERE m.id_team=?";
	
		// Prepare the statement:
		$stmt = $db->prepare($q);
			
		// Bind the inbound variable:
		$stmt->bind_param('i', $tm);
				
		// Execute the query:
		$stmt->execute();		
					
		// Store results:
		$stmt->store_result();
				
		// Bind the outbound variable:
		$stmt->bind_result($nameOB, $iduserOB, $registerOB);
				
		// If there are results to show.
		if ($stmt->num_rows > 0)
		{
			// Initialize an array:
			$json = array();	
							
			// Fetch and put results in the JSON array...
			while ($stmt->fetch())
			{
												
				if ($registerOB != '0000-00-00 00:00:00') {	// If member is a registered on the site
					$json[] = array(
					'MemberUserID' => $iduserOB,
					'MemberName' => $nameOB);
				}
				
			}	// End of WHILE loop
		
			// Send the JSON data:
			echo json_encode($json);		
		}
		else 
		{	// No registered users
			$json[] = array(
				'You have no members on your roster. Click add player to add a member.');
				
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
