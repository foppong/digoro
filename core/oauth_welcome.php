<?php
	// mg_welcome.php
	// Landing page for a new OAuth User

	require_once('../includes/bootstrap.php');
	require_once('../includes/facebook.php');
	$page_title = 'digoro : Welcome';
	require_once('../includes/iheader.html');
	require_once('../includes/php-functions.php');

	$facebook = new Facebook(array(
	  'appId'  => '413593075351071',
	  'secret' => 'c91c70487679528d6d6b22547db88ea9',
	));

	// See if there is a user from a cookie
	$fbuser = $facebook->getUser();

	if(!isset($fbuser)) {
		redirect_to('fatbar.php');
	}
?>
<h1>Welcome to Digoro!</h1>

<div id="roleQuestion">
	<h3> To get started, are you a player or a manager?</h3>
	<p class="tip">TIP: You can become a manager in the future if you are not currently one.</p>
</div>

<div id="playerbox">
	<a href="oap_register.php">I'm a player!</a>
</div>

<div id="managerbox">
	<a href="oam_register.php">I'm a manager!</a>
</div>
<?php require_once('../includes/ifooter.html'); ?>