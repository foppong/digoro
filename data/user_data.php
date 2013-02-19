<?php
	/** user_data.php
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

	// Request is coming from profile view to query all profiles associated with user
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullUserData') {
		
		// Make the Query
		$q = "SELECT first_name, last_name, city, state,
				zipcode, sex, birth_date, phone_num
			FROM users			
			WHERE id_user=?";

		// Prepare the statement:
		$stmt = $db->prepare($q);
			
		// Bind the inbound variable:
		$stmt->bind_param('i', $userID);

		// Execute the query:
		$stmt->execute();		
						
		// Store results:
		$stmt->store_result();
					
		// Bind the outbound variable:
		$stmt->bind_result($fnameOB, $lnameOB, $cityOB, $stateOB, 
			$zipOB, $sexOB, $bdOB, $phoneOB);
		
		// If there are results to show.
		if ($stmt->num_rows > 0) {
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch()) {

				// Set up for sticky birthday form, opted to break apart here and not in js
				$bdarrayOB = explode("-", $bdOB);
				$bdyrOB = $bdarrayOB[0];
				$bdmnthOB = $bdarrayOB[1];
				$bddayOB = $bdarrayOB[2];	

				$json[] = array(
				'First Name' => $fnameOB,
				'Last Name' => $lnameOB,
				'City' => $cityOB,
				'State' => $stateOB,
				'Zipcode' => $zipOB,
				'Sex' => $sexOB,
				'Phone' => $phoneOB,
				'Byear' => $bdyrOB,
				'Bmon' => $bdmnthOB,
				'Bday' => $bddayOB);						
			}	// End of WHILE loop
		
			// Send the JSON data:
			echo json_encode($json);
					
			// Close the statement:
			$stmt->close();
			unset($stmt);			
		}
		
	}

	// Close the connection:
	$db->close();
	unset($db);

?>
