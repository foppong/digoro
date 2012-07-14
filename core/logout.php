<?php
	// logout.php
	// This page logs the user out of the site
	
	require '../includes/config.php';
	$page_title = 'digoro : Logout';
	include '../includes/iheader.html';
	include '../includes/facebook.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}


	$facebook = new Facebook(array(
	  'appId'  => '413593075351071',
	  'secret' => 'c91c70487679528d6d6b22547db88ea9',
	));
	
	// See if there is a user from a cookie
	$fbuser = $facebook->getUser();	

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

	// Log off user
	$user->logoff();
	
	echo '<h3>You are now logged out.</h3>';
	echo '<h4>Click <a href="../index.php">here</a> to return to the main login screen.</h4>'; 	
	
?>

</head>
<body>
	<div id="fb-root"></div>
    <script>               
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>', 
          cookie: true, 
          xfbml: true,
          oauth: true
        });
        // redirect user on login
        FB.Event.subscribe('auth.login', function(response) {
          window.location.reload();
        });
        // redirect user on logout
        FB.Event.subscribe('auth.logout', function(response) {
          window.location.reload();
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
	FB.logout(function(response) {
		Log.info('FB.logout callback', response);
	});
    </script>
    
<?php
	include '../includes/ifooter.html';
?>