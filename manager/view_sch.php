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

	// Site access level -> General
	$lvl = 'G'; 

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$user = $_SESSION['userObj'];
	}
	else 
	{
		redirect_to('index.php');
	}

	// Authorized Login Check
	if (!$user->valid($lvl))
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
		<!-- CSS Style Sheet -->
		<link rel="stylesheet" type="text/css" href="../css/styles.css" />
	</head>
	<body>


		<div id="Header">
			<h2>Schedule</h2>
		</div><br />

		<p id="status"></p>		
		<button id="add-game">Add Game</button>

		<div id="AddGameForm" title="Add New Game">	
			<form method="post">
				<label for="date">Select Game Date:</label>
				<input type="text" name="date" class="date" tabindex="-1" size="10" maxlength="10" class="text ui-widget-content ui-corner-all" />
				<br /><small>Click calendar icon to enter date</small>
		
				<label for="time">Enter Game Time:</label>
				<input type="text" name="time" id="time" size="9" maxlength="9" class="text ui-widget-content ui-corner-all" />
		
				<label for="opp">Enter Opponent:</label>
				<input type="text" name="opp" id="opp" size="30" maxlength="45" class="text ui-widget-content ui-corner-all" />
		
				<label for="ven">Enter Venue:</label>
				<input type="text" name="ven" id="ven" size="30" maxlength="45" class="text ui-widget-content ui-corner-all" />
		
				<label for="note">Enter Game Notes:</label>
				<textarea id="note" name="note" cols="30" rows="2" class="text ui-widget-content ui-corner-all"></textarea>
				<br /><small>Enter any notes about the game.</small>
		
				<label for="res">Enter Results:</label>
				<input type="text" name="res" id="res" size="13" maxlength="13" class="text ui-widget-content ui-corner-all" />
				<br /><small>Ex. W 4-3</small>
			</form>
		</div>

		<div id="EditGameForm" title="Edit Game">
			<form method="post">
				<label for="date">Select Game Date:</label>
				<input type="text" name="date" class="date" tabindex="-1" size="10" maxlength="10" class="text ui-widget-content ui-corner-all" />
				<br /><small>Click calendar icon to enter date</small>
		
				<label for="time">Enter Game Time:</label>
				<input type="text" name="time" id="time" size="9" maxlength="9" class="text ui-widget-content ui-corner-all" />
		
				<label for="opp">Enter Opponent:</label>
				<input type="text" name="opp" id="opp" size="30" maxlength="45" class="text ui-widget-content ui-corner-all" />
		
				<label for="ven">Enter Venue:</label>
				<input type="text" name="ven" id="ven" size="30" maxlength="45" class="text ui-widget-content ui-corner-all" />
		
				<label for="note">Enter Game Notes:</label>
				<textarea id="note" name="note" cols="30" rows="2" class="text ui-widget-content ui-corner-all"></textarea>
				<br /><small>Enter any notes about the game.</small>
		
				<label for="res">Enter Results:</label>
				<input type="text" name="res" id="res" size="13" maxlength="13" class="text ui-widget-content ui-corner-all" />
				<br /><small>Ex. W 4-3</small>
			</form>
		</div>

		<div id="DelGameForm" title="Delete Game">
			<form method="post">
				<p>Are you sure you want to remove this game?</p>
			</form>
		</div>
		
		<table id="schedule"></table>
		
	</body>
</html>

<?php
	ob_end_flush();
?>		

