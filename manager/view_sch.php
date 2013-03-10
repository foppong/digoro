<?php 
	/*
	 * view_sch.php
	 * This page allows a user to view the team's schedule.
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

	$page_title = 'digoro : Schedule';

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
				<div class="row"> <!-- row for Team Name header -->
					<div class="span7">
						<h3><span class="page-header teamdisplay"></span> Schedule</h3> <!-- Name dynamically inserted here -->
					</div>
					<div class="span2">
						<button type="button" id="add-event" class="btn btn-small btn-primary">Add Event</button>
					</div>
				</div>

		<!-- Load ajax schedule data here -->
		<table class="table table-striped table-bordered table-condensed" id="schedule" width="100%"></table>

			</div> <!-- End of column for main content -->
		</div> <!-- End of main row -->

		<!-- Modal Dialog Forms -->
		<div id="AddEventForm" title="Add New Event">	
			<form method="post" class="form-horizontal">

				<div class="control-group">
					<label class="control-label" for="add-event-sel-type">Event type*</label>
					<div class="controls">
						<select class="span4" name="add-event-sel-type" id="add-event-sel-type">
							<option value="1">Game</option>
							<option value="2">Practice</option>
							<option value="3">Scrimmage</option>
						</select>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="add-event-sel-date">Select event date*</label>
					<div class="controls">
						<input type="text" name="add-event-sel-date" id="add-event-sel-date" maxlength="10" class="span4 text ui-widget-content ui-corner-all pickdate" 
							placeholder="click icon"/>	
					</div>
				</div>

				<div class="control-group">		
					<label class="control-label" for="add-event-time">Event time*</label>
					<div class="controls">
						<input type="text" name="add-event-time" id="add-event-time" maxlength="9" class="span4" 
							placeholder="ex. 7:00 PM" />
					</div>
				</div>

				<div class="control-group">		
					<label class="control-label" for="add-event-opname">Opponent name</label>
					<div class="controls">
						<input type="text" name="add-event-opname" id="add-event-opname" maxlength="45" class="span4" />
					</div>
				</div>

				<div class="control-group">		
					<label class="control-label" for="add-event-vname">Venue name*</label>
					<div class="controls">
						<input type="text" name="add-event-vname" id="add-event-vname" maxlength="30" class="span4" 
							placeholder="ex. Polo Fields" />
					</div>
				</div>

				<div class="control-group">					
					<label class="control-label" for="add-event-vadd">Venue address*</label>
					<div class="controls">
						<input type="text" name="add-event-vadd" id="add-event-vadd" maxlength="70" class="span4" 
							placeholder="ex. 1234 Union Street, San Francisco" />					
					</div>
				</div>

				<div class="control-group">		
					<label class="control-label" for="add-event-note">Event Notes</label>
					<div class="controls">
						<textarea id="add-event-note" name="add-event-note" cols="30" rows="1" class="span4"
							placeholder="Enter any event notes"></textarea>
					</div>
				</div>
				<small>* Required Fields</small>
			</form>
		</div>

		<div id="EditEventForm" title="Edit Event">
			<form method="post" class="form-horizontal">

				<div class="control-group">						
					<label class="control-label" for="edit-event-sel-type">Event type</label>
					<div class="controls">
						<select class="span4" name="edit-event-sel-type" id="edit-event-sel-type">
							<option value="1">Game</option>
							<option value="2">Practice</option>
							<option value="3">Scrimmage</option>
						</select>
					</div>
				</div>

				<div class="control-group">		
					<label class="control-label" for="edit-event-sel-date">Select event date</label>
					<div class="controls">
						<input type="text" name="edit-event-sel-date" id="edit-event-sel-date" maxlength="10" class="span4 text ui-widget-content ui-corner-all pickdate" 
							placeholder="click icon" />	
					</div>
				</div>

				<div class="control-group">				
					<label class="control-label" for="edit-event-time">Event time</label>
					<div class="controls">
						<input type="text" name="edit-event-time" id="edit-event-time" maxlength="9" class="span4" 
							placeholder="ex. 7:00 PM" />
					</div>
				</div>

				<div class="control-group">				
					<label class="control-label" for="edit-event-opname">Opponent name</label>
					<div class="controls">
						<input type="text" name="edit-event-opname" id="edit-event-opname" maxlength="45" class="span4" />
					</div>
				</div>

				<div class="control-group">				
					<label class="control-label" for="edit-event-vname">Venue name</label>
					<div class="controls">
						<input type="text" name="edit-event-vname" id="edit-event-vname" maxlength="30" class="span4" 
							placeholder="ex. Polo Fields" />
					</div>
				</div>

				<div class="control-group">							
					<label class="control-label" for="edit-event-vadd">Venue address</label>
					<div class="controls">
						<input type="text" name="edit-event-vadd" id="edit-event-vadd" maxlength="70" class="span4" 
							placeholder="ex. 1234 Union Street, San Francisco" />					
					</div>
				</div>

				<div class="control-group">				
					<label class="control-label" for="edit-event-note">Event Notes</label>
					<div class="controls">
						<textarea id="edit-event-note" name="edit-event-note" cols="30" rows="1" class="span4"
							placeholder="Enter any event notes"></textarea>
					</div>
				</div>

				<div class="control-group">		
					<label class="control-label" for="edit-event-res">Enter Results</label>
					<div class="controls">
						<input type="text" name="edit-event-res" id="edit-event-res" maxlength="13" class="span4" 
							placeholder="ex. W 4-3" />
					</div>
				</div>
			</form>
		</div>

		<div id="ViewEventForm" title="View Event" class="span4">	
			<form method="post">
				<h4>Event Details:</h4>
				<div id="dynamicEventinfo"></div>		
			</form>
		</div>		

		<div id="DelEventForm" title="Delete Event">
			<form method="post">
				<p>Are you sure you want to remove this event?</p>
			</form>
		</div>
		<!-- End of Modal Dialog Form -->

	<!-- External javascript call-->			
	<script type="text/javascript" src="../js/schedule.js"></script>
	<script type="text/javascript" src="../js/myteams_pg.js"></script>
		
	</body>
</html>

<?php include '../includes/footer.html'; ?>	

