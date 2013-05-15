<?php 
	/*
	 * about_team
	 * This page contains information about the team
	 */
	 
	require '../includes/config.php';
	$page_title = 'Welcome to digoro!';
	include '../includes/header.html';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}
	
	// Validate user
	checkSessionObject();	
	
	// Check user role
	checkRole('m');
	
	// Check for a $page_title value:
	if (!isset($page_title))
	{
		$page_title = 'digoro';
	}

	$page_title = 'digoro : About Team';

	// Retrieve current team ID in session
	$ctmID = $_SESSION['ctmID'];

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
				<div class="row"> <!-- Team Name header -->
					<div class="span5">
						<h3><span class="page-header teamdisplay"></span> Team Info</h3> <!-- Name dynamically inserted here -->
					</div>
					<div class="span5">
						<button type="button" id="edit-team" class="btn btn-small btn-primary">Edit Team</button>
						<button type="button" id="transfer-team" class="btn btn-small btn-warning">Transfer</button>
						<button type="button" id="delete-team" class="btn btn-small btn-danger">Delete Team</button>
					</div>
				</div>					
			</div>
			
			<!-- Load Dynamic Team Info Here -->
			<div id="teamInfo"></div>

			</div> <!-- End of column for main content -->
		</div> <!-- End of main row -->

	<!-- Modal Dialog Forms -->
	<div id="EditTeamForm" title="Edit Team" class="span4">		
		<form method="post" class="form-horizontal">

			<div class="control-group">
				<label class="control-label" for="edit-team-sel-sport">We play</label>
				<div class="controls">
					<select class="input-large" name="edit-team-sel-sport" id="edit-team-sel-sport">
						<option value="">-Select Sport-</option>
						<option value="1">Soccer</option>
						<option value="2">Flag Football</option>
						<option value="3">Hockey</option>
						<option value="4">Softball</option>
						<option value="5">Basketball</option>
						<option value="6">Ultimate</option>
						<option value="7">Volleyball</option>
						<option value="8">Kickball</option>
						<option value="9">Rugby</option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="edit-team-name">Our team name is</label>
				<div class="controls">
					<input class="input-large" type="text" name="edit-team-name" id="edit-team-name" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="edit-team-sel-sex">The team sex is</label>
				<div class="controls">
					<select class="input-large" name="edit-team-sel-sex" id="edit-team-sel-sex">
						<option value="">-Select Sex-</option>
						<option value="1">Coed</option>
						<option value="2">All Female</option>
						<option value="3">All Male</option>
					</select>
				</div>
			</div>
			
			<div class="control-group">			
				<label class="control-label" for="edit-team-sel-region">We are based in</label>
				<div class="controls">
					<select class="input-large" name="edit-team-sel-region" id="edit-team-sel-region">
						<option value="">-Select Region-</option>
						<option value="1">San Francisco/ Bay Area</option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="edit-team-sel-level-play">Our level of play is</label>
				<div class="controls">
					<select class="input-large" name="edit-team-sel-level-play" id="edit-team-sel-level-play">
						<option value="">-Select Level-</option>
						<option value="1">Recreational</option>
						<option value="2">Intermediate</option>
						<option value="3">Advanced</option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="edit-team-email">Our team email is</label>
				<div class="controls">
					<input class="input-large" type="text" name="edit-team-email" id="edit-team-email" />
				</div>
			</div>

			<div class="control-group">		
				<label class="control-label" for="edit-team-abouttm">Other team information to share</label>
				<div class="controls">
					<textarea class="input-large" id="edit-team-abouttm" name="edit-team-abouttm" 
						cols="30" rows="2" placeholder="enter something cool about your team"></textarea>
				</div>
			</div>
		</form>
	</div> 
	
	<div id="TransferTeamForm" title="Transfer Team">
		<form method="post" class="form-horizontal">
			<p>Are you sure you want to transfer team ownership? If so, select who from the registered team members:</p>
			<select class="input-medium" name="transfermember" id="transferlist"></select>
		</form>
	</div>
	
	<div id="DeleteTeamForm" title="Delete Team">
		<form method="post">
			<p>Are you sure you want to delete this team?</p>
			<p>If you are the manager and wish to transfer team ownership, 
					please cancel this selection and select the transfer button.</p>
		</form>
	</div>
	<!-- End of Modal Dialog Forms -->


	<!-- External javascript call -->
	<script type="text/javascript" src="../js/abtm.js"></script>

<?php include '../includes/footer.html'; ?>
