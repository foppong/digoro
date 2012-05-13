<?php
	// add_team.php
	// This page allows a logged-in user to add a team
		
	require '../includes/config.php';
	$page_title = 'digoro : Add Team';
	include '../includes/header.html';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Site access level -> Manager
	$lvl = 'M'; 

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$manager = $_SESSION['userObj'];
		$userID = $manager->getUserID();
	}
	else 
	{
		redirect_to('index.php');
	}

	// Need the database connection:
	require_once MYSQL2;

	// Assign Database Resource to object
	$manager->setDB($db);

	// Authorized Login Check
	if (!$manager->valid($lvl))
	{
		redirect_to('index.php');
	}

	// Get user ID
	//$userID = $manager->getUserID();
	
	// Assign userID to session variable
	//$_SESSION['userID'] = $userID;
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
				
		// Assign user ID from session variable
		//$userID = $_SESSION['userID'];
				
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
			echo '<p class="error">Please enter a brief description about your team.</p>';
		}

		// Checks if team name, userID, sport, team city, state, and league are valid before adding team to database.
		if ($lg && $userID && $sp && $tn && $ct && $st)
		{

			// Create team object for use & create team for database
			$team = new ManagerTeam();
			$team->setDB($db);
			$team->createTeam($lg, $sp, $userID, $tn, $ct, $st, $abtm);	

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

	// Delete objects
	unset($team);
	unset($manager);

	// Close the connection:
	$db->close();
	unset($db);	
?>

<h2>Add Team</h2>
<form action="add_team.php" method="post" id="AddTeamForm">
	<fieldset>
	<div>
		<label for="tname"><b>Enter Team Name:</b></label>
		<input type="text" name="tname" id="tname" size="30" maxlength="45"
		value="<?php if (isset($trimmed['tname'])) echo $trimmed['tname']; ?>" />
	</div>
	
	<div>
		<label for="sport"><b>Select Sport:</b></label>
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
	</div>

	<div>
		<label for="city"><b>Enter Team's Home City:</b></label>
		<input type="text" name="city" id="city" size="30" maxlength="40"
		value="<?php if (isset($trimmed['city'])) echo $trimmed['city']; ?>" />
	</div>

	<div>
		<label for="state"><b>Enter Team's Home State:</b></label>
		<select name="state" id="state" onchange="LEAGUE.showLeagues(this.value)">
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
		</select>		
	</div>
	
	<div>
		<label for="leaguelb"><b>Select League:</b></label>
		<select name="league" id="league">
		</select>
	</div>
	
	<div>
		<label for="abouttm"><b>Team Information:</b></label>
		<textarea id="abouttm" name="abouttm" cols="30" rows="2"></textarea><br />
		<small>Enter something cool about your team.</small>
	</div>
	</fieldset>	
	<div align="left"><input type="submit" name="submit" value="Add Team" /></div>

</form>

<?php include '../includes/footer.html'; ?>
