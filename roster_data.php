<?php
	/** roster_data.php
	* This page queries a database, returnnig a list
	* of players on a roster
	*/
	
	ob_start();
	session_start();
			
	require 'includes/config.php';

	// Authorized Login Check
	// If no session value is present, redirect the user. Also validate the HTTP_USER_AGENT	
	if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}

	// Need the database connection:	
	require_once MYSQL;
/**
// Confirmation that form has been submitted:	
//if ($_SERVER['REQUEST_METHOD'] == 'POST')

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
 * 
	// Make the Query:
	$q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, u.gender, u.email, sp.id_soccer_player, sp.position
		FROM soccer_players AS sp INNER JOIN users AS u
		USING (id_user)
		WHERE sp.id_soccer_team=?
		ORDER BY $order_by"; 

**/	
	$tm = $_SESSION['deftmID'];

	// Make the Query:
	$q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, u.gender, u.email, p.id_player, p.position
		FROM players AS p INNER JOIN users AS u
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
	$stmt->bind_result($nOB, $genOB, $eOB, $idOB, $posOB);
			
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{
		// Initialize an array:
		$json = array();
				
		// Fetch and put results in the JSON array...
		while ($stmt->fetch())
		{		
			$json[] = array(
			'<b><a href="roster_data.php?x=nm&y=' . $tm . '">Name</a></b>' => $nOB,
			'<b><a href="roster_data.php?x=em&y=' . $tm . '">Email</a></b>' => $eOB,
			'<b><a href="roster_data.php?x=gd&y=' . $tm . '">Gender</a></b>' => $genOB,
			'<b><a href="roster_data.php?x=pos&y=' . $tm . '">Position</a></b>' => $posOB,
			'Edit' => '<a href="edit_player.php?z=' . $idOB . '">Edit</a>',
			'Delete' => '<a href="delete_player.php?z=' . $idOB . '">Delete</a>');

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
			'<p class="error">You have no players on your roster.
			<a href="add_player.php">Click Here</a> to add players.<br /></p><br /><br />');
			
		// Send the JSON data:
		echo json_encode($json);

	}

?>
