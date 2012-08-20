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
	<p class="status"></p> <!-- FIX ALARM/ ALERTS -->

	<div class="container" id="contentWrapper">
		<div class="row"> <!-- Main row - for all content except footer -->	
			<div class="span2"> <!-- column for icons --> 
				<div class="well">
				<div class="side-nav">
				<ul class="nav nav-list">
					<li>
						<a href=""><img src="../css/imgs/home-icon.png" 
							alt="home-icon" height="60" width="60"></a>
					</li>
					<li><p>Home</p></li>
					<li>
						<a href="profile.php"><img src="../css/imgs/user-icon.png" 
							alt="user-icon" height="60" width="60"></a>	
					</li>
					<li><p>Profile</p></li>
					<li>
						<a href="manager_home.php"><img src="../css/imgs/clipboard-icon.png" 
							alt="clipboard-icon" height="60" width="60"></a>	
					</li>
					<li><p>My Teams</p></li>
					<li>
						<a href="find_players.php"><img src="../css/imgs/binoculars-icon.png" 
							alt="binoculars-icon" height="60" width="60"></a>
					</li>
					<li><p>Find Players</p></li>
					<li>
						<a href=""><img src="../css/imgs/world-icon.png" 
							alt="world-icon" height="60" width="60"></a>
					</li>
					<li><p>Find Teams</p></li>		
				</ul>
				</div>
				</div>
			</div>		

			<div class="span10"> <!-- column for main content --> 
				<div class="row"> <!-- My Teams header -->
					<div class="span3">
						<div class="page-header"><h1>My Teams</h1></div>
					</div>
				</div>
				<div class="row"> <!-- Add Team button row -->
					<div class="span3 offset4">
						<h4>Add a team that you manage</h4>
					</div>
					<div class="span2">
						<button type="button" id="addTeam" class="btn btn-small btn-primary">Add Team</button>
					</div>
				</div>
				<div class="row"> <!-- Select team row -->
					<div class="span4">
						<form method="post" class="form-inline" id="SelectTeamForm">	
							<select class="span2" name="y" id="y"></select>		
							<button type="submit" id="selectTeam" class="btn">Select</button>
						</form>
					</div>
				</div>
				<div class="row"> <!-- Tabs container -->		
					<div id="tabmenu" class="ui-tabs">
						<ul>
							<li><a href="view_abteam.php"><span>Team Info</span></a></li>
							<li><a href="view_roster.php"><span>Roster</span></a></li>
						    <li><a href="view_sch.php"><span>Schedule</span></a></li>
						</ul>
							<div id="view_abteam.php" class="ui-tabs-hide">Team Info</div>
							<div id="view_roster.php" class="ui-tabs-hide">Roster</div>
							<div id="view_sch.php" class="ui-tabs-hide">Schedule</div>
					</div>
				</div>
			</div>
			 
		</div> <!-- End of main row -->



	<!-- Modal Dialog Form -->
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
	</div> <!-- End of Modal Dialog Form -->


<?php include '../includes/footer.html'; ?>