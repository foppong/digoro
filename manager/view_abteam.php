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
		<p class="status"></p>	
		

			<div class="row"> <!-- Team Name header -->
				<div class="span5">
					<div class="page-header teamdisplay"></div> <!-- Name dynamically inserted here -->
				</div>
				<div class="span4">
					<button type="button" id="edit-team" class="btn btn-small btn-primary">Edit Team</button>
					<button type="button" id="transfer-team" class="btn btn-small btn-warning">Transfer</button>
					<button type="button" id="delete-team" class="btn btn-small btn-danger">Delete Team</button>
				</div>				
			</div>	
			
		
		<!-- Load Dynamic Team Info Here -->
		<div id="teamInfo"></div>

	<!-- Modal Dialog Forms -->
	<div id="EditTeamForm" title="Edit Team" class="span4">		
		<form method="post">

			<label for="edit-team-sel-sport">We play</label>
			<select class="span3" name="edit-team-sel-sport" id="edit-team-sel-sport">
				<option value="">-Select Sport-</option>
				<option value="1">Soccer</option>
				<option value="2">Flag Football</option>
				<option value="3">Hockey</option>
				<option value="4">Softball</option>
				<option value="5">Basketball</option>
				<option value="6">Ultimate</option>
				<option value="7">Volleyball</option>
				<option value="8">Kickball</option>
				<option value="9">Rugby</option>
			</select>

			<label for="edit-team-name">Our team name is</label>
			<input class="span3" type="text" name="edit-team-name" id="edit-team-name" />	

			<label for="edit-team-sel-sex">The team sex is</label>
			<select class="span3" name="edit-team-sel-sex" id="edit-team-sel-sex">
				<option value="">-Select Sex-</option>
				<option value="1">Coed</option>
				<option value="2">All Female</option>
				<option value="3">All Male</option>
			</select>
			
			<label for="edit-team-sel-region">We are based in</label>
			<select class="span3" name="edit-team-sel-region" id="edit-team-sel-region">
				<option value="">-Select Region-</option>
				<option value="1">San Francisco/ Bay Area</option>
			</select>

			<label for="edit-team-sel-level-play">Our level of play is</label>
			<select class="span3" name="edit-team-sel-level-play" id="edit-team-sel-level-play">
				<option value="">-Select Level-</option>
				<option value="1">Recreational</option>
				<option value="2">Intermediate</option>
				<option value="3">Advanced</option>
			</select>

			<label for="edit-team-email">Our team name email is</label>
			<input class="span3" type="text" name="edit-team-email" id="edit-team-email" />
		
			<label for="edit-team-abouttm">Other team information to share</label>
			<textarea class="input-large" id="edit-team-abouttm" name="edit-team-abouttm" 
				cols="30" rows="2" placeholder="enter something cool about your team"></textarea>
		</form>
	</div> 
	
	<div id="TransferTeamForm" title="Transfer Team">
		<form method="post">
			<input type="radio" name="transfer" value="Yes" />Yes<br />
			<input type="radio" name="transfer" value="No" checked="checked" />No<br />

			<label for="email">If Yes, please enter new manager email address:</label>
			<input type="text" name="email" id="email" size="30" maxlength="60" />
		</form>
	</div>
	
	<div id="DeleteTeamForm" title="Delete Team">
		<form method="post">
			<p>Are you sure you want to delete this team?</p>
			<p>If you are the manager and wish to transfer team ownership, 
					please cancel this selection and select the transfer button.</p>
		</form>
	</div>
	<!-- End of Modal Dialog Forms -->

	</body>
</html>

<?php
	ob_end_flush();
?>		

