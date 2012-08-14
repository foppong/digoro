<?php 
	/*
	 * about_team
	 * This page contains information about the team
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
		$ctmID = $_SESSION['ctmID']; //Retrieve current team in session variable
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

	$page_title = 'digoro : About Team';
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $page_title; ?></title>
		<meta name="author" content="Frank" />
		<!-- CSS Style Sheet -->

		<!-- External javascript call -->
		<script type="text/javascript" src="../js/abtm.js"></script>
	</head>
	<body>
		
		<div id="Header">
			<h2>About Team</h2>
		</div>
		<p class="status"></p>	
		
		<!-- <div id="TeamName"><h2><?php echo stripslashes($teamname); ?></h2></div><br /> -->

		<div id="EditTeamForm" title="Edit Team">	
			<form method="post">
				<label for="tname">New Team Name:</label>
				<input type="text" name="tname" id="tname" size="10" maxlength="45" /><br />

				<label for="abouttm">Team Information:</label>
				<textarea id="abouttm" name="abouttm" cols="30" rows="2"></textarea><br />
				<small>Enter something cool about your team.</small><br />

				<label for="transfer">Transfer Team?</label>
				<input type="radio" name="transfer" value="Yes" />Yes<br />
				<input type="radio" name="transfer" value="No" checked="checked" />No<br />

				<label for="email">If Yes, please enter new manager email address:</label>
				<input type="text" name="email" id="email" size="30" maxlength="60" />
			</form>
		</div>
		<div id="about"></div>
	</body>
</html>

<?php
	ob_end_flush();
?>		

