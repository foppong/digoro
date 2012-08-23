<?php 
	/*
	 * find_subs_view.php
	 * This page allows a user to find substitutes.
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

	$page_title = 'digoro : Find Subs';

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $page_title; ?></title>
		<meta name="author" content="Frank" />

		<!-- External javascript call-->		
		<script type="text/javascript" src="../js/subrequest.js"></script>
	</head>
	<body>


		<div id="Header">
			<h2>Find Subs</h2>
		</div><br />

		<p class="status"></p>		

				<div class="row"> <!-- Add Team button row -->
					<div class="span4">
						<h4>Create an alert to find substitutes</h4>
					</div>
					<div class="span3">
						<button type="button" id="create-sub-request" class="btn btn-small btn-primary">Create SubRequest</button>
					</div>
				</div>

		<hr>



		<!-- Modal Dialog Form -->
		<div id="Create-SubRequest-Form" title="Create SubRequest">	
			<form method="post">
				
				<select class="span3 SR-myteams-menu" name="create-SR-sel-teams" id="create-SR-sel-teams" onchange="SUBREQUEST.showEvents(this.value)"></select>	
		
				<select class="span3 SR-teamevents-menu" name="create-SR-sel-events" id="create-SR-sel-events"></select>
		
				<select class="span3" name="create-SR-sel-sex" id="create-SR-sel-sex">
					<option value="">-Sex?-</option>
					<option value="Both">Females and Males</option>
					<option value="Females">Females Only</option>
					<option value="Males">Males Only</option>
				</select>

				<select class="span4" name="create-SR-sel-exp" id="create-SR-sel-exp">
					<option value="">-Minimum Experience?-</option>
					<option value="1">Any</option>
					<option value="2">Beginner</option>
					<option value="3">Youth League</option>
					<option value="4">High School - Varsity/ Club</option>
					<option value="5">College - Varsity/ Club</option>
					<option value="6">Adult League/ Pick-up</option>
					<option value="7">Pro/ Semi-pro</option>
				</select>
				
				<select class="span4" name="create-SR-sel-reg" id="create-SR-sel-reg">
					<option value="">-Select Region-</option>
					<option value="1">San Francisco/ Bay Area</option>
				</select>			
			</form>
		</div>

		<div id="Edit-SubRequest-Form" title="Edit SubRequest" class="span4">	
			<form method="post">
				
				<select class="span3 SR-myteams-menu" name="edit-SR-sel-teams" id="edit-SR-sel-teams" onchange="SUBREQUEST.showEvents(this.value)"></select>	
		
				<select class="span3 SR-teamevents-menu" name="edit-SR-sel-events" id="edit-SR-sel-events"></select>
		
				<select class="span3" name="edit-SR-sel-sex" id="edit-SR-sel-sex">
					<option value="">-Sex?-</option>
					<option value="Both">Females and Males</option>
					<option value="Females">Females Only</option>
					<option value="Males">Males Only</option>
				</select>

				<select class="span4" name="edit-SR-sel-exp" id="edit-SR-sel-exp">
					<option value="">-Minimum Experience?-</option>
					<option value="1">Any</option>
					<option value="2">Beginner</option>
					<option value="3">Youth League</option>
					<option value="4">High School - Varsity/ Club</option>
					<option value="5">College - Varsity/ Club</option>
					<option value="6">Adult League/ Pick-up</option>
					<option value="7">Pro/ Semi-pro</option>
				</select>
				
				<select class="span4" name="edit-SR-sel-reg" id="edit-SR-sel-reg">
					<option value="">-Select Region-</option>
					<option value="1">San Francisco/ Bay Area</option>
				</select>			
			</form>
		</div>

		<div id="Del-SubRequest-Form" title="Delete SubRequest" class="span3">
			<form method="post">
				<p>Are you sure you want to cancel this subrequest?</p>
			</form>
		</div>
<!--
		<div id="Respond-SubResponse-Form" title="Respond" class="span4">	
			<form method="post">
				<label for="respond-SRR-comment">Enter any comments:</label>
					<textarea id="respond-SRR-comment" name="respond-SR-comment" cols="30" rows="2" class="input-xlarge text ui-widget-content ui-corner-all"
					placeholder="ex. please bring a red shirt. thanks!"></textarea>			
			</form>
		</div>
-->

		<!-- Keep these on the bottom of the page or gives problems with dialog boxes capturing form -->
		<div>
		<h4>Open SubRequests</h4>
		<!-- Load ajax open subrequest data here -->
		<table class="table table-striped table-bordered table-condensed" id="open-subrequests" width="100%">
		</div>
		
		<div>
		<h4>Responses</h4>
		<!-- Load ajax subrequest responses data here -->
		<table class="table table-striped table-bordered table-condensed" id="subrequests-responses" width="100%">		
		</div>
				
	</body>
</html>

<?php
	ob_end_flush();
?>		

