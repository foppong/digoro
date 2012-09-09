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
	
		$url = BASE_URL . 'manager/home.php';
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
					<div class="span5 well"> <!-- discover info column -->
						<!-- Digoro video and testimonials -->
						<div id="carousel" class="carousel slide">
						  <!-- Carousel items -->
						  <div class="carousel-inner">
						    <div class="active item">
						    	<img src="../css/imgs/splashpage.jpg" width="500" height="400">
						    	<p>Digoro video can go here</p>
						    </div>
						    <div class="item">
						    	<img src="../css/imgs/FrankPort.jpg" width="500" height="400">
						    	<p>Learn more about Digoro slide</p></div>
						    <div class="item">
						    	<img src="../css/imgs/FrankPort2.jpg" width="500" height="400">
						    	<p>Contact slide</p></div>
						  </div>
						  <!-- Carousel nav -->
						  <a class="carousel-control left" href="#carousel" data-slide="prev">&lsaquo;</a>
						  <a class="carousel-control right" href="#carousel" data-slide="next">&rsaquo;</a>
						</div>					
					</div>
					<div class="span6 well"> <!-- signup info column -->
		
		

		<!-- Register New Users -->
		<div id="registerBlock">
			<h3>Start playing today - it's free!</h3>
			<h4>Registration takes less than 2 minutes</h4></br>
			
		<form method="post" class="form-horizontal">
				<div class="control-group">
					<label class="control-label" for="add-team-sel-region">Where are you located?</label>
					<div class="controls">
						<select class="input-large" name="add-team-sel-region" id="add-team-sel-region">
							<option value="">-Select Region-</option>
							<option value="1">San Francisco/ Bay Area</option>
						</select>
					</div>
				</div>	

				<div class="control-group">			
					<label class="control-label" for="add-member-fname">First name:</label>
					<div class="controls">
						<input type="text" name="add-member-fname" id="add-member-fname" size="20" maxlength="20" />
					</div>
				</div>

				<div class="control-group">			
					<label class="control-label" for="add-member-lname">Last name:</label>
					<div class="controls">
						<input type="text" name="add-member-lname" id="add-member-lname" size="20" maxlength="40" />
					</div>
				</div>

				<div class="control-group">			
					<label class="control-label" for="add-member-lname">Password:</label>
					<div class="controls">
						<input type="password" name="add-member-lname" id="add-member-lname" size="20" maxlength="40" />
						<span class="help-inline">6 or more characters</span>
					</div>
				</div>

				<div class="control-group">			
					<label class="control-label" for="add-member-lname">Confirm Password:</label>
					<div class="controls">
						<input type="password" name="add-member-lname" id="add-member-lname" size="20" maxlength="40" />
					</div>
				</div>

				<div class="control-group">			
					<label class="control-label" for="add-member-email">Email:</label>
					<div class="controls">
						<input type="text" name="add-member-email" id="add-member-email" size="30" maxlength="60" />
					</div>
				</div>

				<div class="control-group">	
					<label class="control-label" for="add-member-sel-sex">I am</label>
					<div class="controls">
						<select class="input-medium" name="add-member-sel-sex" id="add-member-sel-sex">
							<option value="">-Select Sex-</option>
							<option value="1">Female</option>
							<option value="2">Male</option>
						</select>
					</div>
				</div>

				<div class="control-group">	
					<label class="control-label" for="bday">Select Your Birthdate:</label>
					<div class="controls controls-row">
						<select class="input-small" name="DateOfBirth_Month" id="bdayM">
							<option value="">- Month -</option>
							<option value="1">January</option>
							<option value="2">Febuary</option>
							<option value="3">March</option>
							<option value="4">April</option>
							<option value="5">May</option>
							<option value="6">June</option>
							<option value="7">July</option>
							<option value="8">August</option>
							<option value="9">September</option>
							<option value="10">October</option>
							<option value="11">November</option>
							<option value="12">December</option>
						</select>

						<select class="input-small" name="DateOfBirth_Day" id="bdayD">
							<option value="">- Day -</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
							<option value="13">13</option>
							<option value="14">14</option>
							<option value="15">15</option>
							<option value="16">16</option>
							<option value="17">17</option>
							<option value="18">18</option>
							<option value="19">19</option>
							<option value="20">20</option>
							<option value="21">21</option>
							<option value="22">22</option>
							<option value="23">23</option>
							<option value="24">24</option>
							<option value="25">25</option>
							<option value="26">26</option>
							<option value="27">27</option>
							<option value="28">28</option>
							<option value="29">29</option>
							<option value="30">30</option>
							<option value="31">31</option>
						</select>

						<select class="input-small" name="DateOfBirth_Year" id="bdayY">
							<option value="">- Year -</option>
							<option value="2004">2004</option><option value="2003">2003</option><option value="2002">2002</option>
							<option value="2001">2001</option><option value="2000">2000</option><option value="1999">1999</option>
							<option value="1998">1998</option><option value="1997">1997</option><option value="1996">1996</option>
							<option value="1995">1995</option><option value="1994">1994</option><option value="1993">1993</option>
							<option value="1992">1992</option><option value="1991">1991</option><option value="1990">1990</option>
							<option value="1989">1989</option><option value="1988">1988</option><option value="1987">1987</option>
							<option value="1986">1986</option><option value="1985">1985</option><option value="1984">1984</option>
							<option value="1983">1983</option><option value="1982">1982</option><option value="1981">1981</option>
							<option value="1980">1980</option><option value="1979">1979</option><option value="1978">1978</option>
							<option value="1977">1977</option><option value="1976">1976</option><option value="1975">1975</option>
							<option value="1974">1974</option><option value="1973">1973</option><option value="1972">1972</option>
							<option value="1971">1971</option><option value="1970">1970</option><option value="1969">1969</option>
							<option value="1968">1968</option><option value="1967">1967</option><option value="1966">1966</option>
							<option value="1965">1965</option><option value="1964">1964</option><option value="1963">1963</option>
							<option value="1962">1962</option><option value="1961">1961</option><option value="1960">1960</option>
							<option value="1959">1959</option><option value="1958">1958</option><option value="1957">1957</option>
							<option value="1956">1956</option><option value="1955">1955</option><option value="1954">1954</option>
							<option value="1953">1953</option><option value="1952">1952</option><option value="1951">1951</option>
							<option value="1950">1950</option><option value="1949">1949</option><option value="1948">1948</option>
							<option value="1947">1947</option><option value="1946">1946</option><option value="1945">1945</option>
							<option value="1944">1944</option><option value="1943">1943</option><option value="1942">1942</option>
							<option value="1941">1941</option><option value="1940">1940</option><option value="1939">1939</option>
							<option value="1938">1938</option><option value="1937">1937</option><option value="1936">1936</option>
							<option value="1935">1935</option><option value="1934">1934</option><option value="1933">1933</option>
							<option value="1932">1932</option><option value="1931">1931</option><option value="1930">1930</option>
							<option value="1929">1929</option><option value="1928">1928</option><option value="1927">1927</option>
							<option value="1926">1926</option><option value="1925">1925</option><option value="1924">1924</option>
							<option value="1923">1923</option><option value="1922">1922</option><option value="1921">1921</option>
							<option value="1920">1920</option><option value="1919">1919</option><option value="1918">1918</option>
							<option value="1917">1917</option><option value="1916">1916</option><option value="1915">1915</option>
							<option value="1914">1914</option><option value="1913">1913</option><option value="1912">1912</option>
							<option value="1911">1911</option><option value="1910">1910</option><option value="1909">1909</option>
							<option value="1908">1908</option><option value="1907">1907</option><option value="1906">1906</option>
							<option value="1905">1905</option><option value="1904">1904</option><option value="1903">1903</option>
							<option value="1902">1902</option><option value="1901">1901</option><option value="1900">1900</option>
						</select>
					<span class="help-inline"><a href="help.php">Why do I need to provide my birthday?</a></span>
					</div>

				</div>
				<button type="submit" id="joinbutton" class="btn btn-primary btn-large">Join Now</button>
			</form>						
					</div>
					
				</div> <!-- end of content row -->

		
		</div> <!-- End of page row -->
		
<?php include 'includes/ifooter.html'; ?>