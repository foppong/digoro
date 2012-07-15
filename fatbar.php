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
	
	if ($fbuser) {
		try {
	    	// Proceed knowing you have a logged in user who's authenticated.
	   		$user_profile = $facebook->api('/me');
			$uemail = $user_profile['email'];

			// Create user object & login user
			$OAuser = new UserAuth();
			$OAuser->setDB($db);
			$OAuser->OAuthlogin($uemail);
			unset($OAuser);					
		} 
		catch (FacebookApiException $e) {
	    	echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
	    	$fbuser = null;
	  	}
	}

	// Authorized Login Check
	// If session value is present, redirect the user. Also validate the HTTP_USER_AGENT	
	if (isset($_SESSION['agent']) AND ($_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']))) {
	
		$url = BASE_URL . 'manager/manager_home.php';
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
			// Create user object & login user 
			$user = new UserAuth();
			$user->setDB($db);	
			$user->login($e, $p);
			unset($user);
		}
	}

	$db->close();
	unset($db);
?>
	
	<div id="Header">
		<h1>digoro</h1>
	</div>

    <h3>Login With Facebook</h3>
<!--    <?php if (isset($user_profile)) { ?>
      Your user profile is 
      <pre>            
        <?php print htmlspecialchars(print_r($user_profile, true)) ?>
      </pre> 
    <?php } else { ?> -->
    	
      <fb:login-button size="medium" scope="email, user_birthday">Login with Facebook</fb:login-button> 
     <!-- <div class="fb-login-button" data-show-faces="true" data-width="200" data-max-rows="1"></div>-->
    <?php } ?>
	<div id="fb-root"></div>
    <script>               
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>', // check login status
          cookie: true, // enable cookies to allow the server to access the session
          xfbml: true, // parse XFBML
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