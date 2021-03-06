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

	// Validate user
	checkSessionObject();	
	
	// Check user role
	checkRole('m');

?>

	<div class="container" id="contentWrapper">
		<div class="row"> <!-- Main row - for all content except footer -->	
			<div class="span2"> <!-- column for icons --> 
				<div class="well">
<?php require_once('../includes/side_nav_manager.html'); ?>
				</div>
			</div>		

			<div class="span10"> <!-- column for main content --> 
				<div class="row"> <!-- Find Players header -->
					<div class="span3">
						<div class="page-header"><h2>Find Players</h2></div>
					</div>
				</div>
				<div class="row"> <!-- Tabs container -->		
					<div id="find-players-tabs" class="ui-tabs">
						<ul>
							<li><a href="find_subs_view.php"><span>Find Subs</span></a></li>
							<li><a href="browse_players_view.php"><span>Browse Players</span></a></li>
						</ul>
							<div id="find_subs_view.php" class="ui-tabs-hide">Find Subs</div>
							<div id="browse_players_view.php" class="ui-tabs-hide">Browse Players</div>
					</div>
				</div>
			</div>
			 
		</div> <!-- End of main row -->

	<!-- External javascript call -->
	<script type="text/javascript" src="../js/findplayers_pg.js"></script>

<?php 

	include '../includes/footer.html'; 

?>
