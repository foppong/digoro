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

	$page_title = 'digoro : Roster';

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
		<script type="text/javascript" src="../js/roster.js"></script>
	</head>
	<body>
		<div id="Header">
			<h2>Roster</h2>
		</div>

		<p class="status"></p>

		<button id="add-player" class=".btn-small btn-primary">Add Player</button>
		
		<div id="AddPlayerForm" title="Add New Player">		
			<form method="post">			
				<label for="first_name" class="label">Enter Player's First Name:</label>
				<input type="text" name="first_name" id="first_name" size="20" maxlength="20" />
		
				<label for="last_name" class="label">Enter Player's Last Name:</label>
				<input type="text" name="last_name" id="last_name" size="20" maxlength="40" />
		
				<label for="email" class="label">Enter Player's Email Address:</label>
				<input type="text" name="email" id="email" size="30" maxlength="60" />
			</form>
		</div>

		<div id="EditPlayerForm" title="Edit Player">	
			<form method="post">
				<label for="position" class="label">Position:</label>
				<input type="text" name="position" id="position" 
				size="20" maxlength="20"/>				
				
				<label for="jersey_num" class="label">Jersey Number:</label>
				<input type="text" name="jersey_num" id="jersey_num" 
				size="4" maxlength="4" />
			</form>
		</div>

		<div id="DelPlayerForm" title="Delete Player">
			<form method="post">
				<p>Are you sure you want to remove this player?</p>
			</form>
		</div>
		
		<div id="content">
	
			<!-- Load ajax roster data here -->
			<table class="table table-striped table-bordered table-condensed" id="roster" width="100%">
				<caption>
					Current players
				</caption>
		</table>
		</div>
	</body>
</html>

<?php
	ob_end_flush();
?>		

