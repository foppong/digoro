<?php 
	// my_teams.php
	// 
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
	
	// Validate user
	checkSessionObject();	

	// Check user role
	checkRole('m');

	// Need the database connection:
	require_once MYSQL2;

	// Assign user object from session variable
	$user = $_SESSION['userObj'];
	$userID = $user->getUserID();

	// Assign Database Resource to object.
	$user->setDB($db);
	
	// Pull current user data from database and set object attributes
	$user->pullUserData();
	
	// Get user's default team ID
	$dftmID = $user->getUserAttribute('dftmID');

	// Update team object session variable as user selects different teams
	if ( (isset($_POST['y'])) && (is_numeric($_POST['y'])) ) {
		$_SESSION['ctmID'] = $_POST['y'];
		$ctmID = $_SESSION['ctmID'];
	}
	else {
		// Assign default team ID to current team ID session variable
		$_SESSION['ctmID']  = $dftmID;		
	}

	// Delete objects
	unset($user);
		
	// Close the connection:
	$db->close();
	unset($db);	

?>

	<div class="container" id="contentWrapper">
		<div class="row"> <!-- Main row - for all content except footer -->	
			<div class="span2"> <!-- column for icons --> 
				<div class="well">
				<div class="side-nav">
				<ul class="nav nav-list">
					<li>
						<a href="home.php"><img src="../css/imgs/home-icon.png" 
							alt="home-icon" height="60" width="60"></a>
					</li>
					<li><p>Home</p></li>
					<li>
						<a href="profile.php"><img src="../css/imgs/user-icon.png" 
							alt="user-icon" height="60" width="60"></a>	
					</li>
					<li><p>Profiles</p></li>
					<li>
						<a href="my_teams.php"><img src="../css/imgs/clipboard-icon.png" 
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
			</div> <!-- end of column for icons --> 

			<div class="span10"> <!-- column for main content --> 
				<div class="row"> <!-- My Teams header -->
					<div class="span3">
						<div class="page-header"><h1>My Teams</h1></div>
					</div>
				</div>

				<div class="row"> <!-- Add Team button row -->
					<div class="span4 offset3">
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

			<div class="row"> <!-- Team Name header -->
				<div class="span5">
					<h3><span class="page-header teamdisplay"></span></h3> <!-- Name dynamically inserted here -->
				</div>
			</div>

				<div class="row"> <!-- row for alerts -->
					<div id="status"></div> 
				</div>				
				
				<div class="row"> <!-- row for team menu options -->
						<div class="row"> <!-- row 1 -->
							<div class="span4 offset1">
								<a href="view_abteam.php"><img src="../css/imgs/file.png" 
									alt="world-icon" height="128" width="128"></a>
								<p>Team Info</p>							
							</div>
							<div class="span4">
								<a href="view_roster.php"><img src="../css/imgs/group.png" 
									alt="world-icon" height="128" width="128"></a>
								<p>Roster</p>							
							</div>							
						</div>	
						<div class="row"> <!-- row 2 -->
							<div class="span4 offset1">
								<a href="view_sch.php"><img src="../css/imgs/list.png" 
									alt="world-icon" height="128" width="128"></a>
								<p>Schedule</p>							
							</div>
							<div class="span4">
								<a href=""><img src="../css/imgs/mail.png" 
									alt="world-icon" height="128" width="128"></a>
								<p>Communications</p>							
							</div>							
						</div>									
				</div>
				
			</div>
			 
		</div> <!-- End of main row -->

	<!-- External javascript call -->
	<script type="text/javascript" src="../js/myteams_pg.js"></script>

<?php include '../includes/footer.html'; ?>