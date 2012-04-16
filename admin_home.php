<?php 
	// manager_homepage.php
	// This is the Manager Homepage
	require 'includes/config.php';
	$page_title = 'Admin Page';
	include 'includes/header.html';

	// autoloading of classes
	function __autoload($class) {
		require_once('classes/' . $class . '.php');
	}

	// Create user object
	$user = new UserAuth();

	// Site access level -> Administrator
	$lvl = 'A'; 

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}

	echo '<h2> Hello! This is the Administrative Homepage</h2>'; 

?>

<p style="font-family:times;color:green;">
	More updates to come
</p>

<form action="view_roster.php" method="post" id="ViewRosterForm">	
	<p id="teamP"><b>Select Team:</b>
	<select name="y" id="y">
		<option value=""> - Select Team - </option>
		<option value="1">Makan Bwya</option>
		<option value="6">Inter Alameda</option>
		<option value="7">Soccer Junkies</option>
		<option value="9">Alianza</option>
		<option value="13">Energy Strikers</option>
		<option value="14">Trial Team</option>
		<option value="15">Energy!!!!!</option>
		<option value="16">Juarez</option>
	</select>
	<span class="errorMessage" id="teamPError">You must select your team.</span></p>		
	
	<div align="center"><input type="submit" name="submit" value="View Roster" />
</form><br /><br />

<a href="add_player.php">Add Player</a><br />
<a href="add_team.php">Add Team</a><br />
<a href="view_users.php">View Registered Users</a><br />

<?php include 'includes/footer.html'; ?>