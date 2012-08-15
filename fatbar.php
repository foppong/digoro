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

	<div class="container" id="contentWrapper">
		<div class="row"> <!-- page row - except footer -->
			<div class="span12">
				<div class="row"> <!-- Header row -->
					<div class="span2">
						<div class="row" id="headtxt">
							<h1>digoro</h1>
						</div>
						<div class="row pull-right">
							<h3>beta</h3>
						</div>
					</div>
					<div class="span5 offset5">
						<div class="row">
							<p><a href="core/forgot_password.php">Forgot your password?</a></p>
						</div>
						<div class="row" id="loginform">
							<form class="form-inline" method="post">
								<input class="span2" type="text" name="email" id="email" maxlength="60" placeholder="Email"/>
								<input class="span2" type="password" name="pass" id="pass" maxlength="20" placeholder="Password" />
								<button type="submit" id="signin" class="btn">Sign in</button>
							</form>
						</div>
						<div class="row" id="fbooklogin">
							<fb:login-button size="medium" scope="email, user_birthday">Login with Facebook</fb:login-button>
						</div>
					</div>
				</div> <!-- end of header row -->
				<hr>
				
				<div class="row"> <!-- tagline row -->
					<div class="span9 offset2">
						<h2>The virtual agent for amateur sports players and teams.</h2>
						<div id="no-script"><h2>You must have JavaScript enabled!</h2></div> <!-- Only shows if javascript is disabled -->
					</div>
				</div> <!-- end of tagline row -->
				
				<div class="row"> <!-- content row -->
					<div class="span4 offset1 well"> <!-- discover info column -->
						<!-- Digoro video and testimonials -->
						<div id="carousel" class="carousel slide">
						  <!-- Carousel items -->
						  <div class="carousel-inner">
						    <div class="active item">
						    	<img src="../css/imgs/splashpage.jpg" width="300" height="300">
						    	<p>Digoro video can go here</p>
						    </div>
						    <div class="item">
						    	<img src="../css/imgs/FrankPort.jpg" width="300" height="300">
						    	<p>Learn more about Digoro slide</p></div>
						    <div class="item">
						    	<img src="../css/imgs/FrankPort2.jpg" width="300" height="300">
						    	<p>Contact slide</p></div>
						  </div>
						  <!-- Carousel nav -->
						  <a class="carousel-control left" href="#carousel" data-slide="prev">&lsaquo;</a>
						  <a class="carousel-control right" href="#carousel" data-slide="next">&rsaquo;</a>
						</div>					
					</div>
					<div class="span5 well"> <!-- signup info column -->
		
		

		<!-- Register New Users -->
		<div id="registerBlock">
			<h4>Start playing today - it's free!</h4>


			<form action="register.php" method="post" id="SignUpForm">
				<fieldset>
				<input type="hidden" name="op" value="new">
		
				<div>
					<label for="role"><b>Are You A Manager or Player/Free Agent?</b></label>
					<select name="role" id="role">
						<option value="">- Select Role -</option>
						<option value="M">Manager</option>
						<option value="P">Player/Free Agent</option>
					</select><br />
					<small>If you are both, just select manager. Click <a href="help.php">here</a> for more information.</small>
				</div>
				
				<div>
					<label for="first_name"><b>First Name:</b></label>
					<input type="text" name="first_name" id="first_name" size="20" maxlength="20"
					value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name']; ?>" />
				</div>
			
				<div>
					<label for="last_name"><b>Last Name:</b></label>
					<input type="text" name="last_name" id="last_name" size="20" maxlength="40"
					value="<?php if (isset($_POST['last_name'])) echo $_POST['last_name']; ?>" />
				</div>
				</fieldset>		
				<button type="submit" class="btn btn-primary">Join Now</button>
				</div>
			</form>						
					</div>
					
				</div> <!-- end of content row -->

		
		</div> <!-- End of page row -->
		
<?php include 'includes/ifooter.html'; ?>