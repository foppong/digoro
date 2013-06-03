<?php
	// manager_start.php
	// Landing page for a new manager
    require_once('../includes/bootstrap.php');
	$page_title = 'digoro : Manager Welcome';
	require_once('../includes/iheader.html');
	require_once('../includes/php-functions.php');

	// Validate user
	checkSessionObject();	

	// Check user role
	checkRole('m');
?>
<div class="container" id="contentWrapper">
	<div class="row"> <!-- Main row - for all content except footer -->
		<div class="span12"> <!-- Main column -->
			<div class="row">
				<div id="addTeamStatement">
					<h3>STEP 2: Add a team that you manage</h3>
				</div>
			</div>
			
			<div class="row">
				<form action="../core/add_team.php" method="post" id="FirstTeamForm" class="form-horizontal">
					<div class="control-group">
						<label class="control-label" for="add-team-sel-sport">We play*</label>
						<div class="controls">
							<select class="input-large" name="add-team-sel-sport" id="add-team-sel-sport">
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
						<label class="control-label" for="add-team-name">Our team name is*</label>
						<div class="controls">
							<input class="input-large" type="text" name="add-team-name" id="add-team-name" />	
						</div>
					</div>	
		
					<div class="control-group">			
						<label class="control-label" for="add-team-sel-sex">The team sex is*</label>
						<div class="controls">
							<select class="input-large" name="add-team-sel-sex" id="add-team-sel-sex">
								<option value="">-Select Sex-</option>
								<option value="1">Coed</option>
								<option value="2">All Female</option>
								<option value="3">All Male</option>
							</select>
						</div>
					</div>
		
					<div class="control-group">
						<label class="control-label" for="add-team-sel-region">We are based in*</label>
						<div class="controls">
							<select class="input-large" name="add-team-sel-region" id="add-team-sel-region">
								<option value="">-Select Region-</option>
								<option value="1">San Francisco/ Bay Area</option>
							</select>
						</div>
					</div>	
		
					<div class="control-group">
						<label class="control-label" for="add-team-sel-level-play">Our level of play is*</label>
						<div class="controls">
							<select class="input-large" name="add-team-sel-level-play" id="add-team-sel-level-play">
								<option value="">-Select Level-</option>
								<option value="1">Recreational</option>
								<option value="2">Intermediate</option>
								<option value="3">Advanced</option>
							</select>
						</div>
					</div>
						
					<div class="control-group">
						<label class="control-label" for="add-team-email">Our team email is</label>
						<div class="controls">
							<input type="text" class="input-large" name="add-team-email" id="add-team-email" />
						</div>
					</div>
				
					<div class="control-group">
						<label class="control-label" for="add-team-abouttm">Other team information to share</label>
						<div class="controls">
							<textarea class="input-large" id="add-team-abouttm" name="add-team-abouttm" 
							cols="30" rows="2" placeholder="enter something cool about your team"></textarea>
						</div>
					</div>
					
					<div class="row"><small>* Required Fields</small></div>
					<input type="hidden" id="newUser" value="1">
					<button type="submit" id="addTeamButton" class="btn btn-primary btn-primary">Add Team</button>
					
				</form>

			</div> <!-- end of main column -->
		</div> <!-- end of main row -->
<?php require_once('../includes/ifooter.html'); ?>