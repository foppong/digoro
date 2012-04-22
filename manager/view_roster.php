<?php 
	/*
	 * view_roster.php
	 * This page allows user to view roster.
	 */
	
	ob_start();
	session_start();

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
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}
	
	// Check for a $page_title value:
	if (!isset($page_title))
	{
		$page_title = 'digoro';
	}

	$page_title = 'digoro : Roster';

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $page_title; ?></title>
		<meta name="author" content="Frank" />
		<!-- External javascript call -->
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" charset="utf-8"></script>
		<script type="text/javascript" src="../js/roster.js"></script>
		<!-- CSS Style Sheet -->
		<link rel="stylesheet" type="text/css" href="../css/styles.css" />
	</head>
	<body>
		<div id="Header">
			<h2>Roster</h2>
		</div>

		<table id="roster"></table>
	</body>
</html>

<?php
	ob_end_flush();
?>		

