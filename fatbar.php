<?php 

	require 'includes/config.php';
	include 'includes/iheader.html';

	// autoloading of classes
	function __autoload($class) {
		require_once('classes/' . $class . '.php');
	}
	
	// Authorized Login Check
	// If session value is present, redirect the user. Also validate the HTTP_USER_AGENT	
	if (isset($_SESSION['agent']) AND ($_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT'])))
	{
		$role = $_SESSION['role'];
		
		//Redirect User
		switch ($role)
		{
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

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// Need the database connection:
		require MYSQL1;

		// Validate email address
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{
			$e = $_POST['email'];
		}
		else 
		{
			$e = FALSE;
			echo '<p class="error"> Please enter valid email address!</p>';
		}
		
		// Validate password
		if (!empty($_POST['pass']))
		{
			$p = $_POST['pass'];
		}
		else 
		{
			$p = FALSE;
			echo '<p class="error">You forgot to enter your password!</p>';
		}

		// Check if email and password entered are valid before proceeding to login procedure.
		if ($e && $p)
		{ 
			$user = new UserAuth();
			$user->setDB($db);	
			$user->login($e, $p);
			unset($user);
		}
		
		$db->close();
		unset($db);

	}

?>

</head>
<body>
<div id="fb-root"></div>
    <script>
      // Load the SDK Asynchronously
      (function(d){
         var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement('script'); js.id = id; js.async = true;
         js.src = "//connect.facebook.net/en_US/all.js";
         ref.parentNode.insertBefore(js, ref);
       }(document));

      // Init the SDK upon load
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '413593075351071', // App ID
          channelUrl : 'core/channel.html', // Path to your Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true  // parse XFBML
        });

        // listen for and handle auth.statusChange events
        FB.Event.subscribe('auth.statusChange', function(response) {
          if (response.authResponse) {
            // user has auth'd your app and is logged into Facebook
            FB.api('/me', function(me){
              if (me.name) {
                document.getElementById('auth-displayname').innerHTML = me.name;
              }
            })
            document.getElementById('auth-loggedout').style.display = 'none';
            document.getElementById('auth-loggedin').style.display = 'block';
          } else {
            // user has not auth'd your app, or is not logged into Facebook
            document.getElementById('auth-loggedout').style.display = 'block';
            document.getElementById('auth-loggedin').style.display = 'none';
          }
        });

        // respond to clicks on the login and logout links
        document.getElementById('auth-loginlink').addEventListener('click', function(){
          FB.login();
        });
        document.getElementById('auth-logoutlink').addEventListener('click', function(){
          FB.logout();
        }); 
      } 
    </script>

    <h1>Facebook Client-side Authentication Example</h1>
      <div id="auth-status">
        <div id="auth-loggedout">
          <a href="#" id="auth-loginlink">Login</a>
        </div>
        <div id="auth-loggedin" style="display:none">
          Hi, <span id="auth-displayname"></span>  
        (<a href="#" id="auth-logoutlink">logout</a>)
      </div>
    </div>

	<div id="Header">
		<h1>digoro</h1>
	</div>
	
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