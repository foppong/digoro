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
		<button type="button" id="add-game" class=".btn-small btn-primary">Add Game</button>

		<div id="AddGameForm" title="Add New Game">	
			<form method="post">
				<label for="dateAdd">Select Game Date:</label>
				<input type="text" name="dateAdd" id="dateAdd" tabindex="-1" maxlength="10" class="span2 text ui-widget-content ui-corner-all pickdate" 
					placeholder="Click icon"/>	
		
				<label for="time">Enter Game Time:</label>
				<input type="text" name="time" id="time" maxlength="9" class="span2 text ui-widget-content ui-corner-all" 
					placeholder="ex. 7:00 PM" />
		
				<label for="opp">Enter Opponent:</label>
				<input type="text" name="opp" id="opp" maxlength="45" class="span2 text ui-widget-content ui-corner-all" />
		
				<label for="ven">Enter Venue:</label>
				<input type="text" name="ven" id="ven" maxlength="45" class="span2 text ui-widget-content ui-corner-all" 
					placeholder="ex. Polo Fields" />
		
				<label for="note">Enter Game Notes:</label>
				<textarea id="note" name="note" cols="30" rows="2" class="input-xlarge text ui-widget-content ui-corner-all"
					placeholder="Enter any game notes"></textarea>
		
				<label for="res">Enter Results:</label>
				<input type="text" name="res" id="res" maxlength="13" class="span2 text ui-widget-content ui-corner-all" 
					placeholder="ex. W 4-3" />			
			</form>
		</div>

		<div id="EditGameForm" title="Edit Game">
			<form method="dateEdit">
				<label for="date">Select Game Date:</label>
				<input type="text" name="dateEdit" id="dateEdit" tabindex="-1" maxlength="10" class="span2 text ui-widget-content ui-corner-all pickdate"
					placeholder="Click icon"/>
		
				<label for="time">Enter Game Time:</label>
				<input type="text" name="time" id="time" maxlength="9" class="span2 text ui-widget-content ui-corner-all" 
					placeholder="ex. 7:00 PM" />
		
				<label for="opp">Enter Opponent:</label>
				<input type="text" name="opp" id="opp" maxlength="45" class="span2 text ui-widget-content ui-corner-all" />
		
				<label for="ven">Enter Venue:</label>
				<input type="text" name="ven" id="ven" maxlength="45" class="span2 text ui-widget-content ui-corner-all" 
					placeholder="ex. Polo Fields" />
		
				<label for="note">Enter Game Notes:</label>
				<textarea id="note" name="note" cols="30" rows="2" class="input-xlarge text ui-widget-content ui-corner-all"
					placeholder="Enter any notes about the game"></textarea>
		
				<label for="res">Enter Results:</label>
				<input type="text" name="res" id="res" maxlength="13" class="span2 text ui-widget-content ui-corner-all" 
					placeholder="ex. W 4-3" />
			</form>
		</div>

		<div id="DelGameForm" title="Delete Game">
			<form method="post">
				<p>Are you sure you want to remove this game?</p>
			</form>
		</div>
		
		<!-- Load ajax schedule data here -->
		<table class="table table-striped table-bordered table-condensed" id="schedule" width="100%"></table>
		
	</body>
</html>

<?php
	ob_end_flush();
?>		

