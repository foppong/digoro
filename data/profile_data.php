<?php
	/** profile_data.php
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
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullProfiles') {
		
		// Make the Query
		$q = "SELECT id_profile, team_sex_preference, id_region, id_sport, 
				sport_experience, primary_position, secondary_position, comments
			FROM profiles			
			WHERE id_user=? 
			ORDER BY id_sport ASC";

		// Prepare the statement:
		$stmt = $db->prepare($q);
			
		// Bind the inbound variable:
		$stmt->bind_param('i', $userID);

		// Execute the query:
		$stmt->execute();		
						
		// Store results:
		$stmt->store_result();
					
		// Bind the outbound variable:
		$stmt->bind_result($profIDOB, $tmSexPrefOB, $regIDOB, $sprtIDOB, 
			$sprtexpOB, $pposOB, $sposOB, $commOB);
		
		// If there are results to show.
		if ($stmt->num_rows > 0) {
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch()) {			

				// Translate sport
				$sport = translateSport($sprtIDOB);

				// Translate team sex
				$teamsex = translateTmSex($tmSexPrefOB);
			
				// Translate region
				$reg = translateRegion($regIDOB);
					
				// Translate experience
				$exp = translateExperience($sprtexpOB);

				$json[] = array(
				'Sport' => $sport,
				'Desired Team Sex' => $teamsex,
				'Region' => $reg,
				'My Experience' => $exp,
				'Details' => '<button id="view-subreq" class="btn btn-mini" value=' . $profIDOB . '>View</button>',
				'Edit' => '<button id="edit-profile" class="btn btn-mini" value=' . $profIDOB . '>Edit</button>',
				'Delete' => '<button id="delete-profile" class="btn btn-mini" value=' . $profIDOB . '>Delete</button>');
			}	// End of WHILE loop
		
			// Send the JSON data:
			echo json_encode($json);
					
			// Close the statement:
			$stmt->close();
			unset($stmt);			
		}		
		
	}


	// Request is coming from the edit Profile form
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullSingleProfile') {

		$profileID = $_POST['profileID'];

		// Make the Query
		$q = "SELECT team_sex_preference, id_region, id_sport, 
				sport_experience, primary_position, secondary_position, comments
			FROM profiles			
			WHERE id_profile=? LIMIT 1";

		// Prepare the statement:
		$stmt = $db->prepare($q);
			
		// Bind the inbound variable:
		$stmt->bind_param('i', $profileID);

		// Execute the query:
		$stmt->execute();		
						
		// Store results:
		$stmt->store_result();
					
		// Bind the outbound variable:
		$stmt->bind_result($tmSexPrefOB, $regIDOB, $sprtIDOB, 
			$sprtexpOB, $pposOB, $sposOB, $commOB);
		
		// If there are results to show.
		if ($stmt->num_rows > 0) {
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch()) {			

				$json[] = array(
				'Sport' => $tmSexPrefOB,
				'Desired Team Sex' => $tmSexPrefOB,
				'Region' => $regIDOB,
				'My Experience' => $sprtexpOB,
				'Primary Position' => $pposOB,
				'Secondary Position' => $sposOB,
				'Comments' => $commOB);
			}	// End of WHILE loop
		
			// Send the JSON data:
			echo json_encode($json);
					
			// Close the statement:
			$stmt->close();
			unset($stmt);			
		}		
		
	}


	// Request is coming from the profile page, so user can view the subresponse detail
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'User_single_SubResp_Data') {

			$idSubResponse = $_POST['idSubResp'];
		
		// Make the Query
		$q = "SELECT id_profile, team_sex_preference, id_region, id_sport, 
				sport_experience, primary_position, secondary_position, comments
			FROM profiles			
			WHERE id_user=? 
			ORDER BY id_sport ASC";

		// Prepare the statement:
		$stmt = $db->prepare($q);
			
		// Bind the inbound variable:
		$stmt->bind_param('i', $userID);

		// Execute the query:
		$stmt->execute();		
						
		// Store results:
		$stmt->store_result();
					
		// Bind the outbound variable:
		$stmt->bind_result($profIDOB, $tmSexPrefOB, $regIDOB, $sprtIDOB, 
			$sprtexpOB, $pposOB, $sposOB, $commOB);
		
		// If there are results to show.
		if ($stmt->num_rows > 0) {
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch()) {			

				// Translate sport
				$sport = translateSport($sprtIDOB);

				// Translate team sex
				$teamsex = translateTmSex($tmSexPrefOB);
			
				// Translate region
				$reg = translateRegion($regIDOB);
					
				// Translate experience
				$exp = translateExperience($sprtexpOB);

				$json[] = array(
				'Sport' => $sport,
				'Desired Team Sex' => $teamsex,
				'Region' => $reg,
				'My Experience' => $exp,
				'Details' => '<button id="view-subreq" class="btn btn-mini" value=' . $profIDOB . '>View</button>',
				'Edit' => '<button id="edit-profile" class="btn btn-mini" value=' . $profIDOB . '>Edit</button>',
				'Delete' => '<button id="delete-profile" class="btn btn-mini" value=' . $profIDOB . '>Delete</button>');
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
