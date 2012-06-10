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
		<!-- External javascript call 
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" charset="utf-8"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" charset="utf-8"></script>
-->		<script type="text/javascript" src="../js/schedule.js"></script>
		<!-- CSS Style Sheet -->
		<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" type="text/css" href="../css/styles.css" />
	</head>
	<body>


		<div id="Header">
			<h2>Schedule</h2>
		</div><br />

		<p id="status"></p>
		<div id="AddGameForm" title="Add New Game">
			
			<form method="post">
			<fieldset>
		
				<label for="date">Select Game Date:</label>
				<input type="text" name="date" id="date" tabindex="-1" size="10" maxlength="10" class="text ui-widget-content ui-corner-all" 
				value="<?php if (isset($_POST['date'])) echo $_POST['date']; ?>" />
		
				<label for="time">Enter Game Time:</label>
				<input type="text" name="time" id="time" size="9" maxlength="9" class="text ui-widget-content ui-corner-all" 
				value="<?php if (isset($_POST['time'])) echo $_POST['time']; ?>" />
		
				<label for="opp">Enter Opponent:</label>
				<input type="text" name="opp" id="opp" size="30" maxlength="45" class="text ui-widget-content ui-corner-all" 
				value="<?php if (isset($_POST['opp'])) echo $_POST['opp']; ?>" />
		
				<label for="ven">Enter Venue:</label>
				<input type="text" name="ven" id="ven" size="30" maxlength="45" class="text ui-widget-content ui-corner-all" 
				value="<?php if (isset($_POST['ven'])) echo $_POST['ven']; ?>" />
		
				<label for="note">Enter Game Notes:</label>
				<textarea id="note" name="note" cols="30" rows="2" class="text ui-widget-content ui-corner-all"> 
				<?php if (isset($_POST['note'])) echo $_POST['note']; ?></textarea><br />
				<small>Enter any notes about the game.</small><br />
		
				<label for="res">Enter Results:</label>
				<input type="text" name="res" id="res" size="13" maxlength="13" class="text ui-widget-content ui-corner-all" 
				value="<?php if (isset($_POST['res'])) echo $_POST['res']; ?>" />
				<small>Ex. W 4-3</small>
				
			</fieldset>
			</form>
		</div>
	
		<button id="add-game">Add Game</button>

		
		<a href="add_game.php" id="add-newgame">Add Game</a><br />
		
		<table id="schedule"></table>
		
	</body>
</html>

<?php
	ob_end_flush();
?>		

