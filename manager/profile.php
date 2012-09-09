<?php 
	// home.php
	// home page for users
	
	require '../includes/config.php';
	$page_title = 'Profile';
	include '../includes/header.html';
	include '../includes/php-functions.php';
	//include '../includes/facebook.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}
	
	// See if there is a user from a cookie
	$fbuser = $facebook->getUser();	
	
	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$manager = $_SESSION['userObj'];
		$userID = $manager->getUserID();
	}
	else 
	{
		redirect_to('index.php');
	}

	// Need the database connection:
	require_once MYSQL2;



?>
	<p class="status"></p> <!-- FIX ALARM/ ALERTS -->

	<div class="container" id="contentWrapper">
		<div class="row"> <!-- Main row - for all content except footer -->	
			<div class="span2"> <!-- column for icons --> 
				<div class="well">
				<div class="side-nav">
				<ul class="nav nav-list">
					<li>
						<a href="home.php"><img src="../css/imgs/home-icon.png" 
							alt="home-icon" height="60" width="60"></a>
					</li>
					<li><p>Home</p></li>
					<li>
						<a href="profile.php"><img src="../css/imgs/user-icon.png" 
							alt="user-icon" height="60" width="60"></a>	
					</li>
					<li><p>Profile</p></li>
					<li>
						<a href="my_teams.php"><img src="../css/imgs/clipboard-icon.png" 
							alt="clipboard-icon" height="60" width="60"></a>	
					</li>
					<li><p>My Teams</p></li>
					<li>
						<a href="find_players.php"><img src="../css/imgs/binoculars-icon.png" 
							alt="binoculars-icon" height="60" width="60"></a>
					</li>
					<li><p>Find Players</p></li>
					<li>
						<a href=""><img src="../css/imgs/world-icon.png" 
							alt="world-icon" height="60" width="60"></a>
					</li>
					<li><p>Find Teams</p></li>		
				</ul>
				</div>
				</div>
			</div>		
			<div class="span10"> <!-- column for main content --> 
				<div class="row"> <!-- Profile row -->
					<div class="span10">
						<div class="page-header"><h1>Profile</h1></div>
					</div>
				</div>
				<div class="row"> <!-- SubResponses row -->		
					<div class="span10">
						<div>
							<!-- Load user subresponses here -->
							<table class="table table-striped table-bordered table-condensed" id="subrequests-responses" width="100%"></table>	
						</div>
						<div id="View-SubRequest-Form" title="SubRequest Details" class="span4">	
							<form method="post">
								<h4>Details:</h4>
								<div id="dynamicSRinfo"></div>
								<h4>Respond:</h4>
									<textarea id="SR-response-comment" tabindex="-1" name="SR-response-comment" cols="30" rows="2" class="input-xlarge text ui-widget-content ui-corner-all"
									placeholder="ex. I can't make it, my car quit on me. Sorry!"></textarea>			
							</form>
						</div>						
					</div>
				</div>
			</div>
		</div><!-- End of main row -->


	<!-- External javascript call -->
	<script type="text/javascript" src="../js/profile_pg.js"></script>

<?php include '../includes/footer.html'; ?>