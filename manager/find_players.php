<?php 
	// find_players.php
	// Page to discover and recruit players
	
	require '../includes/config.php';
	$page_title = 'Find Players';
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
						<a href=""><img src="../css/imgs/home-icon.png" 
							alt="home-icon" height="60" width="60"></a>
					</li>
					<li><p>Home</p></li>
					<li>
						<a href="profile.php"><img src="../css/imgs/user-icon.png" 
							alt="user-icon" height="60" width="60"></a>	
					</li>
					<li><p>Profile</p></li>
					<li>
						<a href="manager_home.php"><img src="../css/imgs/clipboard-icon.png" 
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
				<div class="row"> <!-- Find Players header -->
					<div class="span3">
						<div class="page-header"><h1>Find Players</h1></div>
					</div>
				</div>
				<div class="row"> <!-- Tabs container -->		
					<div id="find-players-tabs" class="ui-tabs">
						<ul>
							<li><a href="find_subs_view.php"><span>Find Subs</span></a></li>
							<li><a href=""><span>Browse Players</span></a></li>
						</ul>
							<div id="find_subs_view.php" class="ui-tabs-hide">Find Subs</div>
							<div id="" class="ui-tabs-hide">Browse Players</div>
					</div>
				</div>
			</div>
			 
		</div> <!-- End of main row -->

<?php include '../includes/footer.html'; ?>