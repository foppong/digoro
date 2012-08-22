<?php 
	/*
	 * view_sch.php
	 * This page allows a user to view the team's schedule.
	 */
	
	ob_start();
	session_start();

	require '../includes/config.php';
	include '../includes/php-functions.php';

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
	
	// Check for a $page_title value:
	if (!isset($page_title))
	{
		$page_title = 'digoro';
	}

	$page_title = 'digoro : Schedule';

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $page_title; ?></title>
		<meta name="author" content="Frank" />

		<!-- External javascript call-->		
		<script type="text/javascript" src="../js/schedule.js"></script>
	</head>
	<body>


		<div id="Header">
			<h2>Schedule</h2>
		</div><br />

		<p class="status"></p>		
		<button type="button" id="add-event" class="btn btn-small btn-primary">Add Event</button>

		<div id="AddEventForm" title="Add New Event">	
			<form method="post">

				<label for="add-event-sel-type">Event type</label>
				<select class="span3" name="add-event-sel-type" id="add-event-sel-type">
					<option value="1">Game</option>
					<option value="2">Practice</option>
					<option value="3">Scrimmage</option>
				</select>

				<label for="add-event-sel-date">Select event date</label>
				<input type="text" name="add-event-sel-date" id="add-event-sel-date" maxlength="10" class="span2 text ui-widget-content ui-corner-all pickdate" 
					placeholder="click icon"/>	
		
				<label for="add-event-time">Event time</label>
				<input type="text" name="add-event-time" id="add-event-time" maxlength="9" class="span2 text ui-widget-content ui-corner-all" 
					placeholder="ex. 7:00 PM" />
		
				<label for="add-event-opname">Opponent name</label>
				<input type="text" name="add-event-opname" id="add-event-opname" maxlength="45" class="span2 text ui-widget-content ui-corner-all" />
		
				<label for="add-event-vname">Venue name</label>
				<input type="text" name="add-event-vname" id="add-event-vname" maxlength="30" class="span2 text ui-widget-content ui-corner-all" 
					placeholder="ex. Polo Fields" />
					
				<label for="add-event-vadd">Venue address</label>
				<input type="text" name="add-event-vadd" id="add-event-vadd" maxlength="70" class="span3 text ui-widget-content ui-corner-all" 
					placeholder="ex. 1234 Union Street, San Francisco" />					
		
				<label for="add-event-note">Event Notes:</label>
				<textarea id="add-event-note" name="add-event-note" cols="30" rows="1" class="input-large text ui-widget-content ui-corner-all"
					placeholder="Enter any event notes"></textarea>
		
			</form>
		</div>

		<div id="EditEventForm" title="Edit Event">
			<form method="post">
				
				<label for="edit-event-sel-type">Event type</label>
				<select class="span3" name="edit-event-sel-type" id="edit-event-sel-type">
					<option value="1">Game</option>
					<option value="2">Practice</option>
					<option value="3">Scrimmage</option>
				</select>

				<label for="edit-event-sel-date">Select event date</label>
				<input type="text" name="edit-event-sel-date" id="edit-event-sel-date" maxlength="10" class="span2 text ui-widget-content ui-corner-all pickdate" 
					placeholder="click icon" />	
		
				<label for="edit-event-time">Event time</label>
				<input type="text" name="edit-event-time" id="edit-event-time" maxlength="9" class="span2 text ui-widget-content ui-corner-all" 
					placeholder="ex. 7:00 PM" />
		
				<label for="edit-event-opname">Opponent name</label>
				<input type="text" name="edit-event-opname" id="edit-event-opname" maxlength="45" class="span2 text ui-widget-content ui-corner-all" />
		
				<label for="edit-event-vname">Venue name</label>
				<input type="text" name="edit-event-vname" id="edit-event-vname" maxlength="30" class="span2 text ui-widget-content ui-corner-all" 
					placeholder="ex. Polo Fields" />
					
				<label for="edit-event-vadd">Venue address</label>
				<input type="text" name="edit-event-vadd" id="edit-event-vadd" maxlength="70" class="span3 text ui-widget-content ui-corner-all" 
					placeholder="ex. 1234 Union Street, San Francisco" />					
		
				<label for="edit-event-note">Event Notes:</label>
				<textarea id="edit-event-note" name="edit-event-note" cols="30" rows="1" class="input-large text ui-widget-content ui-corner-all"
					placeholder="Enter any event notes"></textarea>

				<label for="edit-event-res">Enter Results:</label>
				<input type="text" name="edit-event-res" id="edit-event-res" maxlength="13" class="span2 text ui-widget-content ui-corner-all" 
					placeholder="ex. W 4-3" />

			</form>
		</div>

		<div id="DelEventForm" title="Delete Event">
			<form method="post">
				<p>Are you sure you want to remove this event?</p>
			</form>
		</div>
		
		<!-- Load ajax schedule data here -->
		<table class="table table-striped table-bordered table-condensed" id="schedule" width="100%"></table>
		
	</body>
</html>

<?php
	ob_end_flush();
?>		

