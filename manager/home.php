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

	// Validate user
	checkSessionObject();	

	// Check user role
	checkRole('m');

?>

	<div class="container" id="contentWrapper">
		<div class="row"> <!-- Main row - for all content except footer -->	
			<div class="span2"> <!-- column for icons --> 
				<div class="well">
<?php require_once('../includes/side_nav.html'); ?>
				</div>
			</div>
					
			<div class="span10"> <!-- column for main content --> 
				<div class="row"> <!-- Header row -->
					<div class="span10">
						<div class="page-header"><h1>Home</h1></div>
					</div>
				</div>
				<div class="row"> <!-- row for alerts -->
					<div id="status"></div> 
				</div>	
				<div class="row"> <!-- SubResponses row -->		
					<div class="span6">
						<div>
							<!-- Load ajax subrequest matches data here -->
							<table class="table table-striped table-bordered table-condensed" id="subrequests-matches" width="100%"></table>	
						</div>
						</br>
						<div>
							<!-- Load user subresponses here -->
							<table class="table table-striped table-bordered table-condensed" id="subrequests-responses" width="100%"></table>	
						</div>					
					</div>
					<div class="span3 offset1">
						<div>
							<!-- Load ajax upcoming events here -->
							<div class="row"><h4>Upcoming Events</h4></div>
							<div class="row" id="upcomingevents"></div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- End of main row -->


						<div id="Respond-SubRequest-Form" title="Respond SubRequest" class="span4">	
							<form method="post">
								<h4>Details:</h4>
								<div id="dynamicSRinfo"></div>
								<h4>Respond:</h4>
									<textarea id="respond-SR-comment" tabindex="-1" name="respond-SR-comment" cols="30" rows="2" class="input-xlarge text ui-widget-content ui-corner-all"
									placeholder="ex. I can't wait to play!"></textarea>			
							</form>
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

	<!-- External javascript call-->
	<script type="text/javascript" src="../js/home_pg.js"></script>

<?php include '../includes/footer.html'; ?>