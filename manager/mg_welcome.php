<?php
	// add_team.php
	// This page allows a logged-in user to add a team
		
	require '../includes/config.php';
	$page_title = 'digoro : Manager Welcome';
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
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}

	// Get user ID
	$userID = $user->getUserID();
	
	// Assign userID to session variable
	$_SESSION['userID'] = $userID;

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{

		// Trim all the incoming data:
		$trimmed = array_map('trim', $_POST);
		
		// Assume invalid values:
		$tn = $sp = $ct = $st = $lg = FALSE;
				
		// Validate Team name
		if ($trimmed["tname"])
		{
			$tn = $trimmed["tname"];
		}
		else 
		{
			echo '<p class="error"> Please enter a Team name.</p>';
		}

		// Validate a sport is selected
		if ($_POST['sport'])
		{
			$sp = $_POST['sport'];
		}
		else 
		{
			echo '<p class="error">Please select your sport.</p>'; 
		}

		// Validate Team's homecity
		if ($trimmed['city'])
		{
			$ct = $trimmed['city'];
		}
		else 
		{
			echo '<p class="error"> Please enter your teams homecity.</p>';
		}

		// Validate Team's state
		if ($trimmed['state'])
		{
			$st = $trimmed['state'];
		}
		else 
		{
			echo '<p class="error"> Please enter your teams home state.</p>';
		}

		// Validate a league is selected
		if ($_POST['league'])
		{
			$lg = $_POST['league'];
		}
		else 
		{
			echo '<p class="error">Please select your league.</p>'; 
		}
		
		// Validate about team information
		if ($_POST['abouttm'])
		{
			$abtm = trim($_POST['abouttm']);
		}
		else 
		{
			$abtm = '';
		}	


		// Checks if team name, userID, sport, team city, state, and league are valid before adding team to database.
		if ($lg && $userID && $sp && $tn && $ct && $st)
		{

			// Make the query:
			$q = 'INSERT INTO teams (id_league, id_manager, id_sport, team_name, city, state, about) VALUES (?,?,?,?,?,?,?)';

			// Prepare the statement
			$stmt = $db->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('iiissss', $lg, $userID, $sp, $tn, $ct, $st, $abtm);
			
			// Execute the query:
			$stmt->execute();
			
			// Successfully added team
			if ($stmt->affected_rows == 1)
			{
				// Set the default team ID
				$_SESSION['deftmID'] = $stmt->insert_id;
				$tmID = $_SESSION['deftmID'];

				// Make the new query to add manager to player table:
				$q = 'INSERT INTO players (id_user, id_team) VALUES (?,?)';
					
				// Prepare the statement:
				$stmt2 = $db->prepare($q);
						
				// Bind the inbound variables:
				$stmt2->bind_param('ii', $userID, $tmID);
					
				// Execute the query:
				$stmt2->execute();
						
				if ($stmt2->affected_rows !== 1) // It didn't run ok
				{
					echo '<p class="error">Manager was not added to roster. Please contact the service administrator.</p>';
				}
			
				// Close the statement:
				$stmt2->close();
				unset($stmt2);				
				
				// Set boolean logic to true
				$bl = 1;
				
				// Update the user's info in the database
				$q = 'UPDATE users SET default_teamID=?, login_before=? WHERE id_user=? LIMIT 1';
	
				// Prepare the statement
				$stmt2 = $db->prepare($q); 
	
				// Bind the inbound variables:
				$stmt2->bind_param('iii', $tmID, $bl, $userID);
					
				// Execute the query:
				$stmt2->execute();
					
				if ($stmt2->affected_rows !== 1) // It didn't run ok
				{
					echo '<p class="error">Please contact the service administrator.</p>';
				}

				// Redirect user to manager homepage after success
				$url = BASE_URL . 'managers/manager_home.php';
				header("Location: $url");
				exit();	
					
				// Close the statement:
				$stmt2->close();
				unset($stmt2);
			}
			else
			{
				echo '<p class="error">Your team was not added. Please contact the service administrator.</p>';
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);
			
			// Close the connection:
			$db->close();
			unset($db);
			
			include '../includes/footer.html';
			exit();			
		}
		else 
		{									
			echo '<p class="error">Please try again.</p>';
		}
	}

	// Close the connection:
	$db->close();
	unset($db);	


?>

<h1>Welcome to Digoro!</h1>
<h2>To get started, add a team that you manage.</h2>
<form action="mg_welcome.php" method="post" id="AddTeamForm">
	<fieldset>
	<p id="tnameP"><b>Enter Team Name:</b><input type="text" name="tname" id="tname" size="30" maxlength="45"
	value="<?php if (isset($trimmed['tname'])) echo $trimmed['tname']; ?>" />
	<span class="errorMessage" id="tnamePError">Enter your team name.</span></p>

	<p id="sportP"><b>Select Sport:</b>
	<select name="sport" id="sport">
		<option value="">-Select Sport-</option>
		<option value="1">Soccer</option>
		<option value="2">Flag Football</option>
		<option value="3">Ice Hockey</option>
		<option value="4">Softball</option>
		<option value="5">Basketball</option>
		<option value="6">Ultimate</option>
		<option value="7">Volleyball</option>
		<option value="8">Kickball</option>
		<option value="9">Cricket</option>
	</select>
	<span class="errorMessage" id="sportPError">You must select your team's sport.</span></p>

	<p id="cityP"><b>Enter Team's Home City:</b><input type="text" name="city" id="city" size="30" maxlength="40"
	value="<?php if (isset($trimmed['city'])) echo $trimmed['city']; ?>" />
	<span class="errorMessage" id="cityPError">Enter your team's homecity.</span></p>

	<p id="stateP"><b>Enter Team's Home State:</b>
	<select name="state" id="state" onchange="showLeagues(this.value)">
		<option value="">-Select State-</option>
		<option value="AL">AL</option><option value="AK">AK</option>
		<option value="AZ">AZ</option><option value="AR">AR</option>
		<option value="CA">CA</option><option value="CO">CO</option>
		<option value="CT">CT</option><option value="DE">DE</option>
		<option value="FL">FL</option><option value="GA">GA</option>
		<option value="HI">HI</option><option value="ID">ID</option>
		<option value="IL">IL</option><option value="IN">IN</option>
		<option value="IA">IA</option><option value="KS">KS</option>
		<option value="KY">KY</option><option value="LA">LA</option>
		<option value="ME">ME</option><option value="MD">MD</option>
		<option value="MA">MA</option><option value="MI">MI</option>
		<option value="MN">MN</option><option value="MS">MS</option>
		<option value="MO">MO</option><option value="MT">MT</option>
		<option value="NE">NE</option><option value="NV">NV</option>
		<option value="NH">NH</option><option value="NJ">NJ</option>
		<option value="NM">NM</option><option value="NY">NY</option>
		<option value="NC">NC</option><option value="ND">ND</option>
		<option value="OH">OH</option><option value="OK">OK</option>
		<option value="OR">OR</option><option value="PA">PA</option>
		<option value="RI">RI</option><option value="SC">SC</option>
		<option value="SD">SD</option><option value="TN">TN</option>
		<option value="TX">TX</option><option value="UT">UT</option>
		<option value="VT">VT</option><option value="VA">VA</option>
		<option value="WA">WA</option><option value="WV">WV</option>
		<option value="WI">WI</option><option value="WY">WY</option>		
	</select><span class="errorMessage" id="birthError">You must select your team's homestate.</span></p>
	
	<p id="leagueP"><b>Select League:</b>
	<select name="league" id="league">
	</select>
	<span class="errorMessage" id="leaguePError">You must select your team's league.</span></p>
	
	<p id="abttmP"><b>Enter some information about the team:</b></p>
	<textarea id="abouttm" name="abouttm" cols="30" rows="2"></textarea>
	
	<div align="center"><input type="submit" name="submit" value="Add Team" />
</form>

<?php include 'includes/footer.html'; ?>
