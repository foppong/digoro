<?php 
	/*
	 * view_roster.php
	 * This page allows user to view roster.
	 */
	
	ob_start();
	session_start();

	// Authorized Login Check
	// If no session value is present, redirect the user. Also validate the HTTP_USER_AGENT	
	if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
	{
		session_unset();
		session_destroy();
		$url = 'http://www.digoro.com/index.php';
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
		<script type="text/javascript" src="roster.js"></script>
		<!-- CSS Style Sheet -->
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
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

