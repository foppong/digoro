<?php 
	// manager_homepage.php
	// This is the Manager Homepage
	require 'includes/config.php';
	$page_title = 'Welcome to digoro!';
	include 'includes/header.html';

	// Authorized Login Check
	// If not an administrator or player, or no session value is present, redirect the user. Also validate the HTTP_USER_AGENT
	if (($_SESSION['role'] == 'M') OR !isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();
	}

	echo '<h2> Hello! This is your player homepage.</h2>'; 

?>

<div id="player-menu">
	<ul id="info-nav">
		<li><a href="player_home.php">Roster</a></li>
	    <li><a href="player_home.php">Schedule</a></li>
	    <li><a href="account.php">SquadFill</a></li>
	</ul>
</div><br />

<p style="font-family:times;color:green;">
	More updates to come
</p>

<form action="roster.php" method="post" id="ViewRosterFormPlayer">	
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

<?php include 'includes/footer.html'; ?>