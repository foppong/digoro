<?php
	// roster.php
	// For Players: This script retrieves all the records from the players table.
	
	require '../includes/config.php';
	$page_title = 'digoro : Roster';
	include '../includes/header.html';	

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Site access level -> Player
	$lvl = 'P'; 

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
		include '../includes/footer.html';
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


	// Make the Query:
	$q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, u.gender, u.email, sp.position
		FROM soccer_players AS sp INNER JOIN users AS u
		USING (id_user)
		WHERE sp.id_soccer_team=?
		ORDER BY $order_by";
		
	// Prepare the statement:
	$stmt = $db->prepare($q);
	
	// Bind the inbound variable:
	$stmt->bind_param('i', $tm);
		
	// Execute the query:
	$stmt->execute();		
			
	// Store results:
	$stmt->store_result();
		
	// Bind the outbound variable:
	$stmt->bind_result($nOB, $genOB, $eOB, $posOB);
		
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{
		// Table Header
		echo '<br /><table id="roster" class="roster_data" align="left" cellspacing="0" cellpadding="2" width="50%"
			<tr>
			<td align="left"><b><a href="roster.php?x=nm&y=' . $tm . '">Name</a></b></td>
			<td align="left"><b><a href="roster.php?x=em&y=' . $tm . '">Email</a></b></td>
			<td align="left"><b><a href="roster.php?x=gd&y=' . $tm . '">Gender</a></b></td>
			<td align="left"><b><a href="roster.php?x=pos&y=' . $tm . '">Position</a></b></td>
			</tr>';
			
		// Fetch and print all records...
		while ($stmt->fetch())
		{		
			echo '<tr>
				<td align="left">' . $nOB . '</td>
				<td align="left">' . $eOB . '</td>
				<td align="left">' . $genOB . '</td>
				<td align="left">' . $posOB . '</td>
				</td></tr>';	
		}	// End of WHILE loop
			
		echo '</table><br /><br />';
			
		// Close the statement:
		$stmt->close();
		unset($stmt);			

		// Close the connection:
		$db->close();
		unset($db);
	}
	else 
	{	// No registered users
		echo '<p class="error">You have no players on your roster.
			<a href="add_player.php">Click Here</a> to add players.<br /></p><br /><br />';
	}	
			
	include '../includes/footer.html';
?>
