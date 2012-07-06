<?php 

	require 'includes/config.php';
	include 'includes/iheader.html';
	require 'includes/facebook.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('classes/' . $class . '.php');
	}

	// Need the database connection:
	require MYSQL1;

	$facebook = new Facebook(array(
	  'appId'  => '413593075351071',
	  'secret' => 'c91c70487679528d6d6b22547db88ea9',
	));
	
	// See if there is a user from a cookie
	$fbuser = $facebook->getUser();
echo "fatbar start A";	
	if ($fbuser) {
		try {
	    	// Proceed knowing you have a logged in user who's authenticated.
	   		$user_profile = $facebook->api('/me');
/*
			$first_name = $user_profile['first_name'];
			$last_name = $user_profile['last_name'];
			$uemail = $user_profile['email'];
			$gender = $user_profile['gender'];
			$oa_provider = 'facebook';
			$oa_id = $fbuser;
	
			// Format Facebook birthday to database format	
			$facebirthday = $user_profile['birthday'];
			$bday = explode("/", $facebirthday);	
			$month = $bday[0];
			$day = $bday[1];
			$year = $bday[2];
			$bdarray = array($year, $month, $day);
			$bdstring = implode("-", $bdarray);
			$bd = new DateTime($bdstring);
			$bdfrmat = $bd->format('Y-m-d');

			// Create user object
			$OAuser = new UserAuth();
			$OAuser->setDB($db);
echo "fatbar B";			
			if ($OAuser->isOAuthRegistered($oa_provider, $oa_id)) {
echo "fatbar C";
				$OAuser->OAuthlogin($uemail);
				unset($OAuser);					
			}
			else {
echo "fatbar D";			
				$OAuser->addOAuthUser($uemail, $first_name, $last_name, $gender, $bdfrmat, $oa_provider, $oa_id);
				$OAuser->OAuthlogin($uemail);
				unset($OAuser);
			}
*/		} 
		catch (FacebookApiException $e) {
	    	echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
	    	$fbuser = null;
	  	}
	}


	// Authorized Login Check
	// If session value is present, redirect the user. Also validate the HTTP_USER_AGENT	
	if (isset($_SESSION['agent']) AND ($_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']))) {
		$role = $_SESSION['role'];
		
		//Redirect User
		switch ($role) {
			case 'A':
				$url = BASE_URL . 'admin/admin_home.php'; 
				break;
			case 'M':
				$url = BASE_URL . 'manager/manager_home.php';
				break;
			case 'P':
				$url = BASE_URL . 'player/player_home.php';
				break;
			default:
				$url = BASE_URL . 'fatbar.php';
				break;
		}
		
		header("Location: $url");
		exit();			
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Validate email address
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$e = $_POST['email'];
		}
		else {
			$e = FALSE;
			echo '<p class="error"> Please enter valid email address!</p>';
		}
		
		// Validate password
		if (!empty($_POST['pass'])) {
			$p = $_POST['pass'];
		}
		else {
			$p = FALSE;
			echo '<p class="error">You forgot to enter your password!</p>';
		}

		// Check if email and password entered are valid before proceeding to login procedure.
		if ($e && $p) { 
			$user = new UserAuth();
			$user->setDB($db);	
			$user->login($e, $p);
			unset($user);
		}
	}

	$db->close();
	unset($db);
?>

</head>
<body>
	
	<div id="Header">
		<h1>digoro</h1>
	</div>

    <h3>Login With Facebook</h3>
    <?php if (isset($user_profile)) { ?>
      Your user profile is 
      <pre>            
        <?php print htmlspecialchars(print_r($user_profile, true)) ?>
      </pre> 
    <?php } else { ?>
      <fb:login-button></fb:login-button>
    <?php } ?>
	<div id="fb-root"></div>
    <script>               
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>', 
          cookie: true, 
          xfbml: true,
          oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
          window.location.reload();
        });
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
    </script>
	
	<!-- Existing Member Login Form -->
	<h2>Log In</h2>
	<p id="no-script">You must have JavaScript enabled!</p>
	<p>Your browser must allow cookies in order to log in.</p>
	<form action="fatbar.php" method="post" id="loginform">
		<fieldset>
		<div>
			<label for="email"><b>Email Address:</b></label>
			<input type="text" name="email" id="email" size="30" maxlength="60" />
		</div>
		<div>
			<label for="pass"><b>Password:</b></label>
			<input type="password" name="pass" id="pass" size="20" maxlength="20" />
		</div>
		<input type="submit" name="submit" id="submit" value="Log In" />
		</fieldset>
	</form>

	<p>Click <a href="core/register.php">here</a> to create an account.</p>
	<p><a href="core/forgot_password.php">Forgot your password?</a></p>


<?php include 'includes/ifooter.html'; ?>