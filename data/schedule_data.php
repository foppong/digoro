<?php
	// schedule_data.php
	// 
	/** roster_data.php
	* For managers: This script retrieves all the records from the schedule table.
	*/
	
	ob_start();
	session_start();
			
	require '../includes/config.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Site access level -> General
	$lvl = 'G'; 

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$user = $_SESSION['userObj'];
	}
	else 
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}
	
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
	require_once MYSQL2;
	
/** Update this to reflect how the request is made - if AJAX call, may not need
	// Checks for a valid team roster request, through GET or POST:
	if ( (isset($_GET['y'])) && (is_numeric($_GET['y'])) )
	{
		// Assign variable from view_roster.php using GET method
		$tm = $_GET['y'];
	}
	elseif ( (isset($_POST['y'])) && (is_numeric($_POST['y'])) )
	{
		// Assign variable from manager_home.php FORM submission
		$tm = $_POST['y'];
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include 'includes/footer.html';
		exit();
	}

	// Determine the sort from view_roster.php click...
	// Default is by registration date.
	$sort = (isset($_GET['x'])) ? $_GET['x'] : 'nm'; // Ternary operator style syntax

	// Determine the sorting order:
	switch ($sort)
	{
		case 'nm':
			$order_by = 'name ASC';
			break;
		case 'gd':
			$order_by = 'u.gender ASC';
			break;
		case 'em':
			$order_by = 'u.email ASC';
			break;
		case 'pos':
			$order_by = 'sp.position ASC';
			break;
		default:
			$order_by = 'name ASC';
			$sort = 'nm';
			break;
	}
*/
	// Retrieve default team ID
	$tm = $user->getUserAttribute('dftm');

	// Make the Query:
	$q = "SELECT id_sch, date, time, opponent, venue, result
		FROM schedules
		WHERE id_team=?
		ORDER BY date ASC";
		
	// Prepare the statement:
	$stmt = $db->prepare($q);
	
	// Bind the inbound variable:
	$stmt->bind_param('i', $tm);
		
	// Execute the query:
	$stmt->execute();		
			
	// Store results:
	$stmt->store_result();
		
	// Bind the outbound variable:
	$stmt->bind_result($idOB, $dateOB, $timeOB, $oppOB, $venOB, $resOB);
		
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{		
		// Reformat date
		$gdt = new DateTime($dateOB);
		$bdfrmat = $gdt->format('m-d-Y');

		// Fetch and print all records...
		while ($stmt->fetch())
		{		
			$json[] = array(
			'Date' => $bdfrmat,
			'Time' => $timeOB,
			'Opponent' => stripslashes($oppOB),
			'Venue' => stripslashes($venOB),
			'Result' => $resOB,
			'Edit' => '<a href="edit_game.php?z=' . $idOB . '">Edit</a>',
			'Delete' => '<a href="delete_game.php?z=' . $idOB . '">Delete</a>');	
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
	{	// No games or events scheduled
		
		$json[] = array(
			'<p class="error">You have no games scheduled.
			<a href="add_game.php">Click Here</a> to a game or event.<br /></p><br /><br />');
			
		// Send the JSON data:
		echo json_encode($json);
	}	

?>
