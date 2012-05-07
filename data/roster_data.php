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

	// Site access level -> General
	$lvl = 'G'; 

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

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		redirect_to('index.php');
	}


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
	// Retrieve team object from session variable
	//$team = $_SESSION['teamObj'];

	// Retrieve current team ID from session variable
	$tm = $_SESSION['ctmID'];	

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
			'Name' => $nOB,
			'Email' => $eOB,
			'Gender' => $genOB,
			'Position' => $posOB,
			'Edit' => '<form action="edit_player.php" method="post">
				<input type="hidden" name="x" value="' . $idOB . '" />
				<input type="submit" name="submit" value="Edit"/></form>',
			'Delete' => '<form action="delete_player.php" method="post">
				<input type="hidden" name="x" value="' . $idOB . '" />
				<input type="submit" name="submit" value="Delete"/></form>');
		}	// End of WHILE loop
	
		// Send the JSON data:
		echo json_encode($json);		
	}
	else 
	{	// No registered users
		$json[] = array(
			'<p class="error">You have no players on your roster.
			<a href="../manager/add_player.php">Click Here</a> to add players.<br /></p><br /><br />');
			
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
