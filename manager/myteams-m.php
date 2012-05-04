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

	// Make the Query to find all teams associated with user via a union of the players and teams table:
	$q = "SELECT p.id_team, t.team_name, t.city, t.state
		FROM players AS p INNER JOIN teams AS t
		USING (id_team)
		WHERE p.id_user=?
		ORDER BY team_name ASC";
		
	// Prepare the statement:
	$stmt = $db->prepare($q);
		
	// Bind the inbound variable:
	$stmt->bind_param('i', $userID);
			
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
		echo '<form action="null" method="post">     
			 	<table id="myTeams">
          			<thead>
            			<tr>
              			<th>Team Name</th>
              			<th>City</th>
              			<th>State</th>
            			</tr>
          			</thead>
          			<tbody>';
				
		while ($stmt->fetch())
		{
			// Strip the escaped addon from database
			$tmnm = stripslashes($tmnmOB);
			
			echo '<tr bgcolor="#eeeeee">
				<td align="left">' . $tmnm . '</td>
				<td align="left">' . $tctyOB . '</td>
				<td align="left">' . $tstOB . '</td>
				</td></tr>';	
		}	// End of WHILE loop
	
		echo '</tbody></table></form><br />';
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
			
?>


<br>

<form action="myteams-m.php" method="post" id="ViewRosterForm">	
	<p id="teamP"><b>Select Your Default Team:</b>
	<select name="y" id="y"></select>
	<span class="errorMessage" id="teamPError">You must select your team.</span></p>		
	
	<div align="left"><input id="submit" type="submit" name="submit" value="Select" /></div>
</form><br>


<?php 	include '../includes/footer.html'; ?>