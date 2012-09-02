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

	// Pulls Data for all the teams associated with the user
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'teammenu') {
	
		// Get user ID
		$userID = $user->getUserID();
	
		// Make the Query to find all teams associated with user via a union of the members and teams table:
		$q = "SELECT p.id_team, t.team_name
			FROM members AS p INNER JOIN teams AS t
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
		if ($stmt->num_rows > 0) {
			// Initialize an array:
			$json = array();
					
			// Fetch and put results in the JSON array...
			while ($stmt->fetch())
			{		
				$json[] = array(
				'TeamID' => $idtmOB,
				'TeamName' => stripslashes($tmnmOB));
			}	// End of WHILE loop
		
			// Send the JSON data:
			echo json_encode($json);
					
			// Close the statement:
			$stmt->close();
			unset($stmt);			
		
		}
		else {
	
			$json[] = array(
				'You have no teams associated with your account');
				
			// Send the JSON data:
			echo json_encode($json);
	
		}
	}

	// Pulls Data for specific team for edit team form
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullTeamData') {
	
			// Retrieve current team ID from session variable
			$tm = $_SESSION['ctmID'];
		
			// Make the query
			$q = 'SELECT id_sport,id_user,team_name,about,level_of_play,id_region,team_sex,team_email
				FROM teams WHERE id_team=? LIMIT 1';
				
			// Prepare the statement:
			$stmt = $db->prepare($q);
			
			// Bind the inbound variable:
			$stmt->bind_param('i', $tm);
				
			// Execute the query:
			$stmt->execute();		
					
			// Store results:
			$stmt->store_result();
			
			// Bind the outbound variables
			$stmt->bind_result($sprtIDOB, $manIDOB, $tmnameOB, $abtmOB, $lvlOB, $regOB, $sexOB, $tmemailOB);
				
			// If there are results to show.
			if ($stmt->num_rows > 0)
			{		
				// Fetch records...
				while ($stmt->fetch())
				{		
					$json[] = array(
					'Sport' => $sprtIDOB,
					'ManagerID' => $manIDOB,
					'Team Name' => $tmnameOB,
					'About' => $abtmOB,
					'Level' => $lvlOB,
					'Region' => $regOB,
					'Sex' => $sexOB,
					'TEmail' => $tmemailOB,
					'Team ID' => $tm);
				}	// End of WHILE loop
					
				// Send the JSON data:
				echo json_encode($json);
			}
		
			// Close the statement:
			$stmt->close();
			unset($stmt);			
		}
		

	// Pulls Data for team info display
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullDisplayTeamData') {
		
			// Retrieve current team ID from session variable
			$tm = $_SESSION['ctmID'];
		
			// Make the query
			$q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS mname, u.email, u.phone_num,
				t.id_sport, t.team_email, t.team_sex, t.level_of_play, t.id_region, t.about
				FROM teams AS t INNER JOIN users AS u
				USING (id_user)
				WHERE t.id_team=? LIMIT 1";
				
			// Prepare the statement:
			$stmt = $db->prepare($q);
			
			// Bind the inbound variable:
			$stmt->bind_param('i', $tm);
				
			// Execute the query:
			$stmt->execute();		
					
			// Store results:
			$stmt->store_result();
			
			// Bind the outbound variables
			$stmt->bind_result($mnameOB, $emailOB, $phnumOB, $sportOB, 
				$temail, $sexOB, $lvlOB, $regOB, $aboutOB);
				
			// If there are results to show.
			if ($stmt->num_rows > 0)
			{		
				// Fetch records...
				while ($stmt->fetch())
				{		

					// Translate sport
					$teamsprt = translateSport($sportOB);
					
					// Translate level of play
					$teamlvl = translateLevelofPlay($lvlOB);
					
					// Translate region
					$teamreg = translateRegion($regOB);
					
					// Translate team sex
					$teamsex = translateTmSex($sexOB);

					$json[] = array(
					'Manager Name' => $mnameOB,
					'Manager Email' => $emailOB,
					'Manager Phone' => $phnumOB,
					'Sport' => $teamsprt,
					'Team Email' => $temail,
					'Sex' => $teamsex,
					'Level' => $teamlvl,
					'Region' => $teamreg,
					'About' => $aboutOB);
				}	// End of WHILE loop
					
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
