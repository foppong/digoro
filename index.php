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
				$url = BASE_URL . 'admin_home.php';
				break;
			case 'M':
				$url = BASE_URL . 'manager_home.php';
				break;
			case 'P':
				$url = BASE_URL . 'player_home.php';
				break;
			default:
				$url = BASE_URL . 'index.php';
				break;
		}
		
		header("Location: $url");
		exit();			
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// Need the database connection:
		require MYSQL;

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
			$user->setDatabaseConnection($db);	
			$user->login($e, $p);

/*
			// Assign variable in case no matches
			$pass = '';

			// Make the query	
			$q = "SELECT pass, role, id_user, first_name, last_name, login_before, default_teamID FROM users 
				WHERE (email=? AND activation='') LIMIT 1";

			// Prepare the statement
			$stmt = $db->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('s', $e);

			// Execute the query:
			$stmt->execute();
			
			// Store result
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($passOB, $roleOB, $idOB, $fnOB, $lnOB, $logbfOB, $deftmIDOB);

			//Assign the outbound variables			
			while ($stmt->fetch())
			{
				$pass = $passOB;
				$role = $roleOB;
				$userID = $idOB;
				$fn = $fnOB;
				$ln = $lnOB;
				$lb = $logbfOB;
				$deftmID = $deftmIDOB;
			}

			$hasher = new PasswordHash($hash_cost_log2, $hash_portable);					
			
			// If password matches database, then proceed to login user	
			if ($hasher->CheckPassword($p, $pass))
			{
				session_regenerate_id(True);
			
				$_SESSION['LoggedIn'] = True;
				$_SESSION['email'] = $e;
				$_SESSION['role'] = $role;
				$_SESSION['userID'] = $userID;
				$_SESSION['firstName'] = $fn;
				$_SESSION['lastName'] = $ln;
				$_SESSION['deftmID'] = $deftmID;
			
				// Store the HTTP_USER_AGENT:
				$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);			
				
				// If user hasn't logged in before and is a manager, take them to welcome page
				if ($lb == FALSE && $role == 'M')
				{
					$url = BASE_URL . 'mg_welcome.php';
					header("Location: $url");
					exit();
				}
				
				//Redirect User
				switch ($role)
				{
					case 'A':
						$url = BASE_URL . 'admin_home.php';
						break;
					case 'M':
						$url = BASE_URL . 'manager_home.php';
						break;
					case 'P':
						$url = BASE_URL . 'player_home.php';
						break;
					default:
						$url = BASE_URL . 'index.php';
						break;
				}

				ob_end_clean();
				header("Location: $url");

				// Close hasher
				unset($hasher);
				
				// Close the statement:
				$stmt->close();
				unset($stmt);
					
				// Close the connection:
				$db->close();
				unset($db);
					
				include 'includes/footer.html';
				exit();	
			}
			else
			{
				echo '<p class="error">Either the email address and password entered do not match those
					those on file or you have not yet activated your account.</p>';
			}
			// Close hasher
			unset($hasher);

			// Close the statement:
			$stmt->close();
			unset($stmt);
*/		}
		
		$db->close();
		unset($db);

	}

?>

</head>
<body>
	<div id="Header">
		<h1>digoro</h1>
	</div>
	
	<!-- Existing Member Login Form -->
	<h2>Log In</h2>
	<p id="no-script">You must have JavaScript enabled!</p>
	<p>Your browser must allow cookies in order to log in.</p>
	<form action="index.php" method="post" id="loginform">
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

	<p>Click <a href="register.php">here</a> to create an account.</p>
	<p><a href="forgot_password.php">Forgot your password?</a></p>


<?php include 'includes/ifooter.html'; ?>