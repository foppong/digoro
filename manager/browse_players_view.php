<?php 
	/*
	 * browse_players_view.php
	 * This page allows a user to find players.
	 */
	
	ob_start();
	session_start();

	require '../includes/config.php';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Validate user
	checkSessionObject();	
	
	// Check user role
	checkRole('m');
	
	// Check for a $page_title value:
	if (!isset($page_title))
	{
		$page_title = 'digoro';
	}

	$page_title = 'digoro : Browse Players';

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $page_title; ?></title>
		<meta name="author" content="Frank" />
	</head>
	<body>


		<div id="Header">
			<h3>Browse Players</h3>
		</div>

				<div class="row"> <!-- row for alerts -->
					<div id="status"></div> 
				</div>	
		<hr>

		<div id="Search-Players-Form" title="Search Players">	
			<form method="post" class="form-horizontal">
		
				<div class="control-group">
					<label class="control-label" for="search-PL-sel-sex">Select Sex</label>
					<div class="controls">
						<select class="span3" name="search-PL-sel-sex" id="search-PL-sel-sex">
							<option value="1">Females</option>
							<option value="2">Males</option>
						</select>
					</div>
				</div>

        <div class="control-group">
          <label class="control-label" for="search-PL-sel-sport">Select Sport</label>
           <div class="controls">
             <select class="input-large" name="search-PL-sel-sport" id="search-PL-sel-sport">
           <!--   <option value="">-Select Sport-</option> -->
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
            </div>
        </div>

				<div class="control-group">
					<label class="control-label" for="search-PL-sel-exp">Minimum Experience?</label>
					<div class="controls">
						<select class="span3" name="search-PL-sel-exp" id="search-PL-sel-exp">
							<option value="2">Beginner</option>
							<option value="3">Youth League</option>
							<option value="4">High School - Varsity/ Club</option>
							<option value="5">College - Varsity/ Club</option>
							<option value="6">Adult League/ Pick-up</option>
							<option value="7">Pro/ Semi-pro</option>
						</select>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="search-PL-sel-reg">Region?</label>
					<div class="controls">								
						<select class="span3" name="search-PL-sel-reg" id="search-PL-sel-reg">
							<option value="1">San Francisco/ Bay Area</option>
						</select>
					</div>
				</div>
				<button type="submit" id="searchforplayers" class="btn btn-primary">Search</button>						
			</form>
		</div>
		<br>


		<!-- Keep these on the bottom of the page or gives problems with dialog boxes capturing form 
			*REVISIT THIS - see Home.php how i kept tables in divs, but may be b/c of tabs -->
		<!-- Load ajax player search results data here -->
		<table class="table table-striped table-bordered table-condensed" id="player-search-results" width="100%">
				
		<!-- External javascript call-->		
		<script type="text/javascript" src="../js/browse_players_pg.js"></script>				
	</body>
</html>

<?php
	ob_end_flush();
?>		

