<?php 
	// manager_homepage.php
	// This is the Manager Homepage
	require '../includes/config.php';
	$page_title = 'Welcome to digoro!';
	include '../includes/header.html';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Site access level -> Manager
	$lvl = 'M'; 
	
	// Need the database connection:
	require MYSQL2;

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

	// Assign Database Resource to object
	$user->setDB($db);

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		$user->logoff();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}

	// Pull user data from database
	$user->pullUserData();

	// Get user ID
	$userID = $user->getUserID();
	
	// Assign userID to session variable
	$_SESSION['userID'] = $userID;
	
	if ( (isset($_POST['y'])) && (is_numeric($_POST['y'])) )
	{
		$_SESSION['deftmID'] = $_POST['y'];
	}	

	if ($_SESSION['deftmID'])
	{

		// Assign team ID to retreive data with
		$tm = $_SESSION['deftmID'];
	
		// Make the query	
		$q = "SELECT team_name FROM teams WHERE id_team=? LIMIT 1";
	
		// Prepare the statement
		$stmt = $db->prepare($q);
	
		// Bind the inbound variable:
		$stmt->bind_param('i', $tm);
	
		// Execute the query:
		$stmt->execute();
				
		// Store result
		$stmt->store_result();
				
		// Bind the outbound variable:
		$stmt->bind_result($tmnmOB);
	
		// If there are results to show.
		if ($stmt->num_rows > 0)
		{
			//Assign the outbound variables			
			while ($stmt->fetch())
			{
				$tmnm = $tmnmOB;
			}	
		}
		else 
		{	// No team exists with that team ID so find alternative team and make default
			
			// Make the Query to find all teams associated with user via a union of the players and teams table:
			$q = "SELECT p.id_team, t.team_name
				FROM players AS p INNER JOIN teams AS t
				USING (id_team)
				WHERE p.id_user=? LIMIT 1";
			
			// Prepare the statement:
			$stmt2 = $db->prepare($q);
				
			// Bind the inbound variable:
			$stmt2->bind_param('i', $userID);
					
			// Execute the query:
			$stmt2->execute();		
						
			// Store results:
			$stmt2->store_result();
					
			// Bind the outbound variable:
			$stmt2->bind_result($idtmOB, $tmnmOB);
					
			// If there are results to show.
			if ($stmt2->num_rows > 0)
			{
				//Assign the outbound variables			
				while ($stmt2->fetch())
				{
					$tmnm = $tmnmOB;
					$_SESSION['deftmID'] = $idtmOB;
					
					// Update the user's info in the database
					$q = 'UPDATE users SET default_teamID=? WHERE id_user=? LIMIT 1';
	
					// Prepare the statement
					$stmt3 = $db->prepare($q); 
	
					// Bind the inbound variables:
					$stmt3->bind_param('ii', $idtmOB, $userID);
					
					// Execute the query:
					$stmt3->execute();
					
					if ($stmt3->affected_rows !== 1) // It didn't run ok
					{
						echo '<p class="error">Please contact the service administrator.</p>';
					}
					
					// Close the statement:
					$stmt3->close();
					unset($stmt3);	
				}	
			}
			else 
			{
				// If user has no teams added, take them to welcome page
				$url = BASE_URL . 'manager/mg_welcome.php';
				header("Location: $url");
				exit();
			}
			
			// Close the statement:
			$stmt2->close();
			unset($stmt2);
		}

		// Close the statement:
		$stmt->close();
		unset($stmt);
	}

	// Close the connection:
	$db->close();
	unset($db);	

?>

<div>
	<form action="manager_home.php" method="post" id="ViewRosterForm">	
		<p id="teamP"><b>View Team:</b>
		<select name="y" id="y"></select>
		<span class="errorMessage" id="teamPError">You must select your team.</span></p>		
		
		<div align="left"><input id="submit" type="submit" name="submit" value="Select" /></div>
	</form>
</div>

<h2><?php echo stripslashes($tmnm); ?></h2>
<div id="tabmenu" class="ui-tabs">
	<ul>
		<li><a href="about_team.php"><span>About</span></a></li>
		<li><a href="view_roster.php"><span>Roster</span></a></li>
	    <li><a href="view_sch.php"><span>Schedule</span></a></li>
	    <li><a href="#"><span>SquadFill</span></a></li>
	    <li><a href="#"><span>Bulletin</span></a></li>
	</ul>
		<div id="about_team.php" class="ui-tabs-hide">About</div>
		<div id="view_roster.php" class="ui-tabs-hide">Roster</div>
		<div id="view_sch.php" class="ui-tabs-hide">Schedule</div>
		<div id="#" class="ui-tabs-hide">SquadFill</div>
		<div id="#" class="ui-tabs-hide">Bulletin</div>
</div><br />

<a href="add_player.php">Add Player</a><br />
<a href="add_team.php">Add Team</a><br />	

<?php include '../includes/footer.html'; ?>