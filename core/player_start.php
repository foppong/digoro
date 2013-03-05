<?php
	// player_start.php
	// Landing page for a new player
		
	$page_title = 'digoro : Player Welcome';
	require_once '../includes/iheader.html';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$user = $_SESSION['userObj'];
	}
	else 
	{
		redirect_to('index.php');
	}
	
?>

<div class="container" id="contentWrapper">
	<div class="row"> <!-- Main row - for all content except footer -->
		<div class="span12"> <!-- Main column -->
			<div class="row">
				<div id="addProfileStatement">
					<h3>STEP 2: Add a sports profile</h3>
					<p class="tip">TIP: Don't worry if you play multiple sports, you can add more profiles later.</p>
				</div>
			</div>

			<div class="row">
			<form action="../core/add_profile.php" method="post" class="form-horizontal">
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

				<div class="row"><small>* Required Fields</small></div>
				<input type="hidden" id="newUser" value="1">
				<button type="submit" id="addProfileButton" class="btn btn-primary btn-primary">Create Profile</button>
			</form>
			</div>
			</div> <!-- end of main column -->
		</div> <!-- end of main row -->

<?php include '../includes/footer.html'; ?>
