<?php 
	/*
	 * view_roster.php
	 * This page allows user to view roster.
	 */
	
	ob_start();
	session_start();

	require '../includes/config.php';
	include '../includes/php-functions.php';
	
	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Site access level -> General
	$lvl = 'G'; 

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$user = $_SESSION['userObj'];
	}
	else 
	{
		redirect_to('index.php');
	}

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		redirect_to('index.php');
	}
	
	// Check for a $page_title value:
	if (!isset($page_title))
	{
		$page_title = 'digoro';
	}

	$page_title = 'digoro : Roster';

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $page_title; ?></title>
		<meta name="author" content="Frank" />

		<!-- CSS Style Sheet -->
		<link rel="stylesheet" type="text/css" href="../css/styles.css" />
		<link rel="stylesheet" type="text/css" href="../css/skins/tango/skin.css" />

		<!-- External javascript call -->
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.js" charset="utf-8"></script>		
		<script type="text/javascript" src="../js/jquery.jcarousel.min.js"></script>
		<script type="text/javascript" src="../js/home.js"></script>

	</head>
	<body>

		<!-- jCarousel -->
		<ul id="mycarousel" class="jcarousel-skin-tango">
		   <!-- The content will be dynamically loaded in here -->
		</ul>

	</body>
</html>

<?php
	ob_end_flush();
?>		

