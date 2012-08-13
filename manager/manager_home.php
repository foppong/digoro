<?php 
	// manager_homepage.php
	// This is the Manager Homepage
	require '../includes/config.php';
	$page_title = 'Welcome to digoro!';
	include '../includes/header.html';
	include '../includes/php-functions.php';
	//include '../includes/facebook.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}
	
	// See if there is a user from a cookie
	$fbuser = $facebook->getUser();	
	
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

	// Assign Database Resource to object
	$manager->setDB($db);
	
	// Pull current user data from database and set object attributes
	$manager->pullUserData();
	
	// Get user's default team ID
	$dftmID = $manager->getUserAttribute('dftmID');

	// Update team object session variable as user selects different teams
	if ( (isset($_POST['y'])) && (is_numeric($_POST['y'])) ) {
		$_SESSION['ctmID'] = $_POST['y'];
		$ctmID = $_SESSION['ctmID'];
	
		// Create team object with current team selection
		$team = new Team();
		$team->setDB($db);
		$team->setTeamID($ctmID);
		$team->pullTeamData();

	}
	elseif (isset($_SESSION['ctmID'])) {
		$ctmID = $_SESSION['ctmID'];

		// Create team object
		$team = new Team();
		$team->setDB($db);
		$team->setTeamID($ctmID);
		$team->pullTeamData();
		
	}
	else {
		// Create team object
		$team = new Team();
		$team->setDB($db);
		$team->setTeamID($dftmID);
		$team->pullTeamData();
		
		// Assign default team ID to current team ID session variable
		$_SESSION['ctmID']  = $dftmID;		
	}

	// Get team name attribute for page display purposes
	$teamname = $team->getTeamAttribute('tmname');	

	// Delete objects
	unset($team);
	unset($manager);
		
	// Close the connection:
	$db->close();
	unset($db);	

?>
    <script type="text/javascript">               
		$(document).ready(function() {
			// Load teams associated with user into select menu
			TEAM.teamMenu();		
		});
    </script>
	<div id="mainContent">   
		<div>
			<form action="manager_home.php" method="post" id="ViewRosterForm">	
				<p id="teamP"><b>View Team:</b>
				<select name="y" id="y"></select>
				<span class="errorMessage" id="teamPError">You must select your team.</span></p>		
				
				<div align="left"><input id="submit" type="submit" name="submit" value="Select" /></div>
			</form>
		</div>
		
		<p id="tmstatus"></p>
		<button id="add_team" class=".btn-small btn-primary">Add Team</button>
		
		<div id="TeamName"><h2><?php echo stripslashes($teamname); ?></h2></div><br />
		
		<div id="tabmenu" class="ui-tabs">
			<ul>
				<li><a href="view_abteam.php"><span>About</span></a></li>
				<li><a href="view_roster.php"><span>Roster</span></a></li>
			    <li><a href="view_sch.php"><span>Schedule</span></a></li>
			    <li><a href="#"><span>SquadFill</span></a></li>
			    <li><a href="#"><span>Bulletin</span></a></li>
			</ul>
				<div id="view_abteam.php" class="ui-tabs-hide">About</div>
				<div id="view_roster.php" class="ui-tabs-hide">Roster</div>
				<div id="view_sch.php" class="ui-tabs-hide">Schedule</div>
				<div id="#" class="ui-tabs-hide">SquadFill</div>
				<div id="#" class="ui-tabs-hide">Bulletin</div>
		</div><br />
		
		
		<div id="AddTeamForm" title="Add New Team" class="span4">		
			<form method="post" class="form-horizontal">
				<label for="tname">Enter Team Name</label>
				<input class="span2" type="text" name="tname" id="tname" />
							
				<label for="sport">Select Sport</label>
				<select class="span3" name="sport" id="sport">
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
		
				<label for="city">Enter Team's Home City</label>
				<input type="text" class="span3" name="city" id="city" maxlength="40" />
		
				<label for="state">Enter Team's Home State</label>
				<select class="span2" name="state" id="state" onchange="LEAGUE.showLeagues(this.value)">
					<option value="">Select State</option>
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
			
				<label for="league">Select League</label>
				<select class="span3" name="league" id="league"></select>
		
				<label for="abouttm">Team Information</label>
				<textarea class="input-xlarge" id="abouttm" name="abouttm" cols="30" rows="2" placeholder="Enter something cool about your team"></textarea><br />
			</form>
		</div>
	</div> 
	
<?php include '../includes/footer.html'; ?>