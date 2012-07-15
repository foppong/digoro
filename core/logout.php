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
		$url = BASE_URL . 'fatbar.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}

	// Log off user
	$facebook->destroySession();
	$user->logoff();
	
	echo '<h3>You are now logged out.</h3>';
	echo '<h4>Click <a href="../fatbar.php">here</a> to return to the main login screen.</h4>'; 	
	
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
	        
		  function fbooklogoff(response) {
		    if (response.authResponse) {
				FB.logout();
		    };
		  }
		
		  // run once with current status and whenever the status changes
		  FB.getLoginStatus(fbooklogoff);
		  FB.Event.subscribe('auth.statusChange', fbooklogoff);    
	    };
	      (function() {
	        var e = document.createElement('script'); e.async = true;
	        e.src = document.location.protocol +
	          '//connect.facebook.net/en_US/all.js';
	        document.getElementById('fb-root').appendChild(e);
	      }());
	    </script>
    
<?php
	include '../includes/ifooter.html';
?>