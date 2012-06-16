<?php 
	/*
	 * view_roster.php
	 * This page allows user to view roster.
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

	$page_title = 'digoro : Roster';

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $page_title; ?></title>
		<meta name="author" content="Frank" />
		<!-- External javascript call -->
		<script type="text/javascript" src="../js/roster.js"></script>
		<!-- CSS Style Sheet -->
		<link rel="stylesheet" type="text/css" href="../css/styles.css" />
	</head>
	<body>
		<div id="Header">
			<h2>Roster</h2>
		</div>

		<p id="status"></p>

		<button id="add-player">Add Player</button>
		
		<div id="AddPlayerForm" title="Add New Player">		
			<form method="post">			
				<label for="first_name"><b>Enter Player's First Name:</b></label>
				<input type="text" name="first_name" id="first_name" size="20" maxlength="20"
				value="<?php if (isset($trimmed['first_name'])) echo $trimmed['first_name']; ?>" />
		
				<label for="last_name"><b>Enter Player's Last Name:</b></label>
				<input type="text" name="last_name" id="last_name" size="20" maxlength="40"
				value="<?php if (isset($trimmed['last_name'])) echo $trimmed['last_name']; ?>" />
		
				<label for="email"><b>Enter Player's Email Address:</b></label>
				<input type="text" name="email" id="email" size="30" maxlength="60"
				value="<?php if (isset($trimmed['email'])) echo $trimmed['email']; ?>" />
			</form>
		</div>

		<div id="EditPlayerForm" title="Edit Player">	
			<form method="post">
				<label for="position"><b>Position:</b></label>
				<input type="text" name="position" id="position" 
				size="20" maxlength="20"/>				
				
				<label for="jersey_num"><b>Jersey Number:</b></label>
				<input type="text" name="jersey_num" id="jersey_num" 
				size="4" maxlength="4" />
			</form>
		</div>

		<div id="DelPlayerForm" title="Delete Player">
			<form method="post">
				<p>Are you sure you want to remove this player?</p>
			</form>
		</div>

		<!-- Load ajax roster data here -->
		<table id="roster"></table>
	</body>
</html>

<?php
	ob_end_flush();
?>		

