<?php
	// myteams-m.php
	// This script retrieves all the team records associated with user.
	
	require '../includes/config.php';
	$page_title = 'digoro : My Teams';
	include '../includes/header.html';	
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
		$manager = $_SESSION['userObj'];
	}
	else 
	{
		redirect_to('index.php');
	}

	// Need the database connection:	
	require_once MYSQL2;

	// Authorized Login Check
	if (!$manager->valid($lvl))
	{
		redirect_to('index.php');
	}

	// Get user ID
	$userID = $manager->getUserID();

	// Page header:
	echo '<h2>My Teams</h2>';

	//Series of code to set the default team
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// Retreieve team ID selection from user form submission	
		$teamID = $_POST['y'];
		
		// Set the new global session variable to new team ID
		$_SESSION['ctmID'] = $teamID;
					
		// Update the user's info in the database
		$q = 'UPDATE users SET default_teamID=? WHERE id_user=? LIMIT 1';
		
		// Prepare the statement
		$stmt = $db->prepare($q); 
	
		// Bind the inbound variables:
		$stmt->bind_param('ii', $teamID, $userID);
					
		// Execute the query:
		$stmt->execute();
					
		if ($stmt->affected_rows == 1) // It ran ok
		{
			echo '<p>Default team successfully changed!</p>';
		}
		else 
		{	// Either did not run ok or no updates were made
			echo '<p>Default team not changed.</p>';
		}
					
		// Close the statement:
		$stmt->close();
		unset($stmt);		
	}
	
	// Number of records to show per page:
	$display = 3;
	
	// Determine how many pages there are...
	if (isset($_GET['p']) && is_numeric($_GET['p']))
	{	// Already been determined
		$pages = $_GET['p'];
	}
	else 
	{	
		// Make the query to count the number of teams ssociated with user via a union of the players and teams table
		$q = "SELECT COUNT(id_team) FROM players WHERE id_user=?";

		// Prepare the statement:
		$stmt = $db->prepare($q);

		// Bind the inbound variable:
		$stmt->bind_param('i', $userID);

		// Execute the query:
		$stmt->execute();

		//Store results:
		$stmt->store_result();

		// Bind the outbound variable:
		$stmt->bind_result($recOB);

		while ($stmt->fetch())
		{
			$records = $recOB;
		}

		// Calculate the number of pages...
		if ($records > $display)
		{	// More than 1 page
			$pages = ceil ($records/$display);
		}
		else 
		{
			$pages = 1;
		}
	}
	
	// Determine where in the database to start returning results...
	if (isset($_GET['s']) && is_numeric($_GET['s']))
	{
		$start = $_GET['s'];
	}
	else 
	{
		$start = 0;
	}

	// Determine the sort...
	// Default is by team name.
	$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'tn'; // Ternary operator style syntax
	
	// Determine the sorting order:
	switch ($sort)
	{
		case 'tn':
			$order_by = 'team_name ASC';
			break;
		case 'cty':
			$order_by = 'city ASC';
			break;
		case 'st':
			$order_by = 'state ASC';
			break;
		default:
			$order_by = 'team_name ASC';
			$sort = 'tn';
			break;
	}

	// Make the Query to find all teams associated with user via a union of the players and teams table:
	$q = "SELECT p.id_team, t.team_name, t.city, t.state
		FROM players AS p INNER JOIN teams AS t
		USING (id_team)
		WHERE p.id_user=?
		ORDER BY $order_by LIMIT ?,?";
		
	// Prepare the statement:
	$stmt = $db->prepare($q);
		
	// Bind the inbound variable:
	$stmt->bind_param('iii', $userID, $start, $display);
			
	// Execute the query:
	$stmt->execute();		
				
	// Store results:
	$stmt->store_result();
			
	// Bind the outbound variable:
	$stmt->bind_result($idtmOB, $tmnmOB, $tctyOB, $tstOB);
			
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{
		// Table Header
		echo '<table align="left" cellspacing="0" cellpadding="5" width="75%"
			<tr>
			<td align="left"><b><a href="myteams-m.php?sort=tn">Team Name</a></b></td>
			<td align="left"><b><a href="myteams-m.php?sort=cty">City</a></b></td>
			<td align="left"><b><a href="myteams-m.php?sort=st">State</a></b></td>
			<td align="left"><b>Edit</b></td>
			<td align="left"><b>Delete</b></td>
			</tr>';
		
		// Fetch and print all records...
		$bg = '#eeeeee'; // Set the initial background color
				
		while ($stmt->fetch())
		{
			// Switch the background color.
			$bg = ($bg == '#eeeeee' ? '#ffffff' : '#eeeeee'); // Ternary operator style syntax
			
			// Strip the escaped addon from database
			$tmnm = stripslashes($tmnmOB);
			
			echo '<tr bgcolor="' . $bg . '">
				<td align="left">' . $tmnm . '</td>
				<td align="left">' . $tctyOB . '</td>
				<td align="left">' . $tstOB . '</td>
				<td align="left"><a href="edit_team.php?z=' . $idtmOB . '">Edit</a></td>
				<td align="left"><a href="delete_team.php?z=' .$idtmOB . '">Delete</a></td>
				</td></tr>';	
		}	// End of WHILE loop
	
		echo '</table><br />';
	}
	else 
	{	// No teams created
		echo '<p class="error">You have no teams created. Click <a href="add_team.php">here</a> to add a team.</p>';
	}

	// Close the statement:
	$stmt->close();
	unset($stmt);

	// Close the connection:
	$db->close();
	unset($db);

	// Make the links to other pages, if necessary.
	if ($pages > 1)
	{
		// Add some spacing and start a paragraph:
		echo '<br /><p><br />';

		// Determine what page the script is on:
		$current_page = ($start/$display) + 1;
		
		// If it's not the first page, make a Previous Link:
		if ($current_page != 1)
		{
			echo '<a href="myteams-m.php?s=' . ($start - $display) . '&p=' . $pages . 
				'$sort=' . $sort . '">Previous</a> ';
		}
		
		// Make all the numbered pages:
		for ($i = 1; $i <= $pages; $i++)
		{
			if ($i != $current_page)
			{
				echo '<a href="myteams-m.php?s=' . (($display * ($i - 1))) . '&p=' . $pages . 
					'$sort=' . $sort . '">' . $i . '</a> ';
			}
			else
			{
				echo $i . ' ';
			}
		}	// End of FOR loop
		
		// If it's not the last page, make a Next button:
		if ($current_page != $pages)
		{
			echo '<a href="myteams-m.php?s=' . ($start + $display) . '&p=' . $pages . 
				'$sort=' . $sort . '">Next</a>';
		}
		echo '</p><br />'; // Close the paragraph
	} // End of links secton.	
			
?>


<br>

<form action="myteams-m.php" method="post" id="ViewRosterForm">	
	<p id="teamP"><b>Select Your Default Team:</b>
	<select name="y" id="y"></select>
	<span class="errorMessage" id="teamPError">You must select your team.</span></p>		
	
	<div align="left"><input id="submit" type="submit" name="submit" value="Select" /></div>
</form><br>


<?php 	include '../includes/footer.html'; ?>