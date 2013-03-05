<?php
	// welcome.php
	// Landing page for a new User

	$page_title = 'digoro : Welcome';
	require_once '../includes/iheader.html';
	
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

?>

<div class="container" id="contentWrapper">
	<div class="row"> <!-- Main row - for all content except footer -->
		<div class="span12"> <!-- Main column -->
			<div class="page-header"><h1>Welcome to Digoro!</h1></div>
			
			<div class="row">
				<div id="roleQuestion">
					<h3> STEP 1: Are you a player or a manager?</h3>
					<p class="tip">TIP: You can become a player or manager in the future if you are not currently one.</p>
				</div>
			</div>
	
			<div class="row">
				<div class="span6" id="playerbox">
					<a href="player_start.php"><img src="../css/imgs/splashpage.jpg" 
						height="100" width="200" class="img-polaroid"></a>
				</div>
				<div class="span6" id="managerbox">
					<a href="manager_start.php"><img src="../css/imgs/clipboard.jpg" 
						height="100" width="200" class="img-polaroid"></a>
				</div>							
			</div>
			<div class="row">
				<div class="span6">
					<p>I'm a player!</p>
				</div>
				<div class="span6">
					<p>I'm a manager!</p>
				</div>
			</div>
		</div> <!-- end of main column -->
	</div> <!-- end of main row -->
	
<?php include '../includes/ifooter.html'; ?>
