<?php 
	// home.php
	// home page for users
	
	require '../includes/config.php';
	$page_title = 'Profile';
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
		$userID = $manager->getUserID();
	}
	else 
	{
		redirect_to('index.php');
	}

	// Need the database connection:
	require_once MYSQL2;



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
				<div class="row"> <!-- Header row -->
					<div class="span10">
						<div class="page-header"><h1>Profiles</h1></div>
					</div>
				</div>
				<div class="row"> <!-- Add Team button row -->
					<div class="span5 offset2">
						<h4>Add a profile to connect with teams!</h4>
					</div>
					<div class="span2">
						<button type="button" id="add-profile" class="btn btn-small btn-primary">Add Profile</button>
					</div>
				</div>
				<br>
				<div class="row"> <!-- row for alerts -->
					<div id="status"></div> 
				</div>
				<div class="row"> <!-- User Profiles row -->		
					<div class="span10">
						<div>
							<!-- Load profiles here -->
							<table class="table table-striped table-bordered table-condensed" id="sport-profiles" width="100%"></table>	
						</div>					
					</div>
				</div>
			</div>
		</div><!-- End of main row -->

		<!-- Modal Dialog Forms -->
		<div id="AddProfileForm" title="Add New Profile">	
			<form method="post" class="form-horizontal">

				<div class="control-group">
					<label class="control-label" for="add-profile-sel-sport">I play*</label>
					<div class="controls">
						<select class="input-large" name="add-profile-sel-sport" id="add-profile-sel-sport">
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
					<label class="control-label" for="add-profile-sel-sex">I want to play on a team that is*</label>
					<div class="controls">
						<select class="input-large" name="add-profile-sel-sex" id="add-profile-sel-sex">
							<option value="">-Select Sex-</option>
							<option value="1">Coed</option>
							<option value="2">All Female</option>
							<option value="3">All Male</option>
						</select>
					</div>
				</div>

				<div class="control-group">			
					<label class="control-label" for="add-profile-sel-region">I'm currently in*</label>
					<div class="controls">
						<select class="input-large" name="add-profile-sel-region" id="add-profile-sel-region">
							<option value="">-Select Region-</option>
							<option value="1">San Francisco/ Bay Area</option>
						</select>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="add-profile-sel-exp">My experience level is*</label>
					<div class="controls">
						<select class="span3" name="add-profile-sel-exp" id="add-profile-sel-exp">
							<option value="2">Beginner</option>
							<option value="3">Youth League</option>
							<option value="4">High School - Varsity/ Club</option>
							<option value="5">College - Varsity/ Club</option>
							<option value="6">Adult League/ Pick-up</option>
							<option value="7">Pro/ Semi-pro</option>
						</select>
					</div>
				</div>

				<div class="control-group">				
					<label class="control-label" for="add-profile-ppos">Primary position</label>
					<div class="controls">
						<input type="text" class="input-medium" name="add-profile-ppos" id="add-profile-ppos" size="20" maxlength="30" 
							placeholder="ex. striker"/>			
					</div>
				</div>

				<div class="control-group">	
					<label class="control-label" for="add-profile-spos">Secondary position</label>
					<div class="controls">
						<input type="text" class="input-medium" name="add-profile-spos" id="add-profile-spos" size="20" maxlength="30"
							placeholder="ex. goalkeeper" />	
					</div>
				</div>
				
				<div class="control-group">		
					<label class="control-label" for="add-profile-comments">Other information to share</label>
					<div class="controls">
						<textarea class="input-large" id="add-profile-comments" name="add-profile-comments" 
							cols="30" rows="2" placeholder="ex. I'm new in town and looking forward to playing!"></textarea>
					</div>
				</div>

				<small>* Required Fields</small>
			</form>
		</div>

		<div id="EditProfileForm" title="Edit Profile">	
			<form method="post" class="form-horizontal">

				<div class="control-group">
					<label class="control-label" for="edit-profile-sel-sport">I play</label>
					<div class="controls">
						<select class="input-large" name="edit-profile-sel-sport" id="edit-profile-sel-sport">
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
					<label class="control-label" for="edit-profile-sel-sex">I want to play on a team that is</label>
					<div class="controls">
						<select class="input-large" name="edit-profile-sel-sex" id="edit-profile-sel-sex">
							<option value="">-Select Sex-</option>
							<option value="1">Coed</option>
							<option value="2">All Female</option>
							<option value="3">All Male</option>
						</select>
					</div>
				</div>

				<div class="control-group">			
					<label class="control-label" for="edit-profile-sel-region">I'm currently in</label>
					<div class="controls">
						<select class="input-large" name="edit-profile-sel-region" id="edit-profile-sel-region">
							<option value="">-Select Region-</option>
							<option value="1">San Francisco/ Bay Area</option>
						</select>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="edit-profile-sel-exp">My experience level is</label>
					<div class="controls">
						<select class="span3" name="edit-profile-sel-exp" id="edit-profile-sel-exp">
							<option value="2">Beginner</option>
							<option value="3">Youth League</option>
							<option value="4">High School - Varsity/ Club</option>
							<option value="5">College - Varsity/ Club</option>
							<option value="6">Adult League/ Pick-up</option>
							<option value="7">Pro/ Semi-pro</option>
						</select>
					</div>
				</div>

				<div class="control-group">				
					<label class="control-label" for="edit-profile-ppos">Primary position</label>
					<div class="controls">
						<input type="text" class="input-medium" name="edit-profile-ppos" id="edit-profile-ppos" size="20" maxlength="30" 
							placeholder="ex. striker"/>			
					</div>
				</div>

				<div class="control-group">	
					<label class="control-label" for="edit-profile-spos">Secondary position</label>
					<div class="controls">
						<input type="text" class="input-medium" name="edit-profile-spos" id="edit-profile-spos" size="20" maxlength="30"
							placeholder="ex. goalkeeper" />	
					</div>
				</div>
				
				<div class="control-group">		
					<label class="control-label" for="edit-profile-comments">Other information to share</label>
					<div class="controls">
						<textarea class="input-large" id="edit-profile-comments" name="edit-profile-comments" 
							cols="30" rows="2" placeholder="ex. I'm new in town and looking forward to playing!"></textarea>
					</div>
				</div>
			</form>
		</div>

		<div id="DelProfileForm" title="Delete Profile">
			<form method="post">
				<p>Are you sure you want to remove this profile?</p>
			</form>
		</div>

	<!-- External javascript call -->
	<script type="text/javascript" src="../js/profile_pg.js"></script>

<?php include '../includes/footer.html'; ?>