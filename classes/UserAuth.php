<?php
	/* This page defines the UserAuth class.
	 * Attributes:
	 * 	protected id_user
	 *  protected dbc
	 *  protected inv_case
	 *  protected OAuth_case
	 * 	
	 * Methods:
	 *  setDB()
	 *  setUserID()
	 *  setinvCase()
	 *  getDB()
	 *  getUserID()
	 *  getinvCase()
	 *  isEmailAvailable()
	 * 	checkPass()
	 * 	checkUser()
	 *  createUser()
	 *  deleteUser()
	 *  login()
	 *  logout()
	 *  valid()
	 *  chgPassword()
	 */
	
	
	class UserAuth {
	 	
		// Declare the attributes
		protected $id_user, $dbc, $inv_case, $OAuth_case;

		// Constructor
		function __construct() {}

		// Set database connection attribute
		function setDB($db)
		{
			$this->dbc = $db;
		}

		// Set userID attribute
		function setUserID($id)
		{
			$this->id_user = $id;
		}

		// Set inv_case attribute
		function setinvCase($ivc)
		{
			$this->inv_case = $ivc;
		}
		
		// Set the OAuth_case attribute
		function setOAuthCase($OAc) {
			$this->OAuth_case = $OAc;
		}

		function getDB($db)
		{
			return $this->dbc;
		}
		
		
		function getUserID()
		{
			return $this->id_user;
		}


		function getinvCase()
		{
			return $this->inv_case;
		}

		// Function to check if user email is available for existing user
		function isEmailAvailable($e)
		{
			// Make the query to make sure User's new email is available	
			$q = 'SELECT id_user,email FROM users WHERE email=? AND id_user !=? LIMIT 1';

			// Prepare the statement
			$stmt = $this->dbc->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('si', $e, $this->id_user);

			// Execute the query:
			$stmt->execute();
			
			// Store result
			$stmt->store_result();

			// User login available, i.e. querey found nothing
			if ($stmt->num_rows == 0) {
				return True;
			}
			else {
				return False;
			}
		}

		// Method to check user against password entered
		function checkPass($e, $p)
		{
			// Assign variable in case no matches
			$pass = '';

			// Make the query	
			$q = "SELECT pass, oauth_registered FROM users WHERE email=? LIMIT 1";

			// Prepare the statement
			$stmt = $this->dbc->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('s', $e);

			// Execute the query:
			$stmt->execute();
			
			// Store result
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($passOB,$oaOB);

			//Assign the outbound variables			
			while ($stmt->fetch())
			{
				$pass = $passOB;
				
				// Set the OAuth case
				self::setOAuthCase($oaOB);
			}

			//$hasher = new PasswordHash($hash_cost_log2, $hash_portable);	
			$hasher = new PasswordHash(8, FALSE);

			if ($hasher->CheckPassword($p, $pass))
			{
				return True;
			}
			else 
			{
				return False;
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);
			
		} // End of checkPass function
		
		// Method to check and set User registration status
		function checkUser($userEmail) {

			// Make the query to make sure New User's email is available	
			$q = 'SELECT id_user,invited FROM users WHERE email=? LIMIT 1';

			// Prepare the statement
			$stmt = $this->dbc->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('s', $userEmail);

			// Execute the query:
			$stmt->execute();
			
			// Store result
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($idOB, $invitedOB);

			//Assign the outbound variables			
			while ($stmt->fetch()) {
				self::setuserID($idOB);
				$invited = $invitedOB;
			}

			if ($stmt->num_rows == 0) {
				self::setinvCase(1); // User login available & not invited by manager
			}

			if (($stmt->num_rows == 1 && $invited == 1)) {
				self::setinvCase(2); // Manager has already entered skeleton information about new user & invited player
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);
							
		} // End of checkUser function

		// Function to create users
		function createUser($fn, $ln, $e, $p, $sex, $bdfrmat) {
			
			// Call checkUser function	
			self::checkUser($e);

			$hasher = new PasswordHash(8, FALSE);
					
			// Encrypt the new password by making a new hash.
			$hash = $hasher->HashPassword($p);
			if (strlen($hash) < 20)
			{
				fail('Failed to hash new password'); //Custom function
				exit();
			}
			unset($hasher);

			// Determine registration method
			switch ($this->inv_case) {
				case 1: // User is new to the system & not invited by manager
					
					// Create the activation code
					$a = md5(uniqid(rand(), TRUE));	
		
					// Make the query to add new user to database
					$q = 'INSERT INTO users (first_name, last_name, email, pass, sex, activation, birth_date, invited, registration_date) 
						VALUES (?,?,?,?,?,?,?,?,NOW())';
		
					// Prepare the statement
					$stmt = $this->dbc->prepare($q); 
		
					// Bind the inbound variables:
					$stmt->bind_param('sssssssi', $fn, $ln, $e, $hash, $sex, $a, $bdfrmat, $this->inv_case);
						
					// Execute the query:
					$stmt->execute();
						
					if ($stmt->affected_rows == 1) // It ran OK.
					{
						// Send the activation email
						$body = "Welcome to digoro and thank you for registering!\n\nTo activate your account, please click on this link:";
						$body .= "\n" . BASE_URL . 'core/activate.php?x=' . urlencode($e) . "&y=$a";
						mail($e, 'digoro.com - Registration Confirmation', $body);
						
						echo '<h3>Thank you for registering! A confirmation email has been sent to your address. Please
							click on the link in that email in order to activate your account. </h3>';
		
						// Close the statement:
						$stmt->close();
						unset($stmt);
							
						include '../includes/ifooter.html';
						exit();	
					}
					else 
					{	// Registration process did not run OK.
						echo '<p class="error">You could not be registered due to a system error. We apologize
							for any inconvenience.</p>';
					}
					break;

				case 2: // User invited by manager
				
					// Create the activation code
					$a = md5(uniqid(rand(), TRUE));			
				
					// Make the query to update user in database
					$q = 'UPDATE users SET pass=?, first_name=?, last_name=?, sex=?, activation=?, birth_date=?, registration_date=NOW() 
						WHERE id_user=? LIMIT 1';
	
					// Prepare the statement
					$stmt = $this->dbc->prepare($q);
	
					// Bind the inbound variables:
					$stmt->bind_param('ssssssi', $hash, $fn, $ln, $sex, $a, $bdfrmat, $this->id_user);
					
					// Execute the query:
					$stmt->execute();
		
					if ($stmt->affected_rows == 1) // It ran OK.
					{
						// Send the activation email
						$body = "Welcome to digoro and thank you for registering!\n\nTo activate your account, please click on this link:";
						$body .= "\n" . BASE_URL . 'core/activate.php?x=' . urlencode($e) . "&y=$a";
						mail($e, 'digoro.com - Registration Confirmation', $body);
						
						echo '<h3>Thank you for registering! A confirmation email has been sent to your address. Please
							click on the link in that email in order to activate your account. </h3>';
	
						// Close the statement:
						$stmt->close();
						unset($stmt);
						
						include '../includes/ifooter.html';
						exit();	
					}
					else 
					{	// Registration process did not run OK.
						echo '<p class="error">You could not be registered due to a system error. We apologize
							for any inconvenience.</p>';
					}
					break;
					
				default:
					// The email address is not available and player was not previously invited
					echo '<p class="error">That email address has already been registered. If you have forgotten your password,
						use the link below to have your password sent to you.</p>';
					break;
					
			} // End of switch
		} // End of createUser function

		// Function to delete user
		function deleteUser($id)
		{	
			// Make the query	
			$q = "DELETE FROM users WHERE id_user=? LIMIT 1";
		
			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);
		
			// Bind the in bound variable:
			$stmt->bind_param('i', $id);
		
			// Execute the query:
			$stmt->execute();
					
			// If the query ran ok.
			if ($stmt->affected_rows == 1) {
				session_unset();
				session_destroy();
			}
			else {	// If the query did not run ok.
				echo '<div class="alert alert-error">This account could not be deleted due to a system error</div>';
			}

			// Close the statement
			$stmt->close();
			unset($stmt);
		} // End of deleteUser function

		// Function to log in users
		function login($e, $p) {
			
			if (self::checkPass($e, $p)) // Call checkPass function	
			{
				// Make the query	
				$q = "SELECT role, id_user, login_before, default_teamID FROM users 
					WHERE (email=? AND activation='') LIMIT 1";

				// Prepare the statement
				$stmt = $this->dbc->prepare($q);
	
				// Bind the inbound variable:
				$stmt->bind_param('s', $e);
	
				// Execute the query:
				$stmt->execute();
				
				// Store result
				$stmt->store_result();
				
				// Bind the outbound variable:
				$stmt->bind_result($roleOB, $idOB, $logbfOB, $deftmIDOB);
	
				if ($stmt->num_rows == 1) // Found match in database
				{
					//Assign the outbound variables			
					while ($stmt->fetch()) {
						$role = $roleOB;
						$userID = $idOB;
						$lb = $logbfOB;
						$deftmID = $deftmIDOB;
					}					

					session_regenerate_id(True);

					// Set default team to session variable
					$_SESSION['deftmID'] = $deftmID;
				
					// Store the HTTP_USER_AGENT:
					$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);			
					
					// Redirect if user hasn't logged in before take them to welcome page
					if ($lb == FALSE) {
						$user = new User($userID);
						$_SESSION['userObj'] = $user;
						$url = BASE_URL . 'core/welcome.php';
						header("Location: $url");
						exit();
					}

					// Redirect if user is a registered manager
					if ($lb == TRUE && $role == 'm') {
						$user = new User($userID);
						$_SESSION['userObj'] = $user;
						$_SESSION['role'] = $role;
						$url = BASE_URL . 'manager/home.php';
						header("Location: $url");
						exit();
					}

					// Redirect if user is a registered player
					if ($lb == TRUE && $role == 'p') {
						$user = new User($userID);
						$_SESSION['userObj'] = $user;
						$_SESSION['role'] = $role;
						$url = BASE_URL . 'player/home.php';
						header("Location: $url");
						exit();
					}
	
					ob_end_clean();
					header("Location: $url");
	
					// Close hasher
					unset($hasher);
					
					// Close the statement:
					$stmt->close();
					unset($stmt);
						
					exit();
				}
				else 
				{
					echo '<div class="alert alert-error">You could not be logged in. Please check that you have activated your account</div>';
				}
				
				// Close the statement:
				$stmt->close();
				unset($stmt);
			}
			elseif ($this->OAuth_case == 1) {
				echo '<div class="alert alert-error">You are registered with facebook. You must login using the Facebook login feature</div>';
			}
			else {
				echo '<div class="alert alert-error">Either the email address and password entered do not match those
					those on file or you have not yet activated your account</div>';				
			}

		} // End of login function
		
		// Function to log off users
		function logoff()
		{
			session_unset();
			session_destroy();
		}

		// Function to check if user is authorized for access [**Currently not really using at moment**]
		function valid($lvl)
		{
			switch ($lvl)
			{
				case 'G': // General level
					if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
					{
						return False;
					}
					else 
					{
						return True;
					}
					break;
					
				case 'A': // Administrator level
					if (($_SESSION['role'] != 'A') OR !isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
					{
						return False;
					}
					else
					{
						return True;
					}
					break;
					
				case 'M': // Manager level minimum
					if (($_SESSION['role'] == 'P') OR !isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
					{
						return False;
					}
					else
					{
						return True;
					}
					break;
					
				case 'P': // Player level minimum
					if (($_SESSION['role'] == 'M') OR !isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
					{
						return False;
					}
					else
					{
						return True;
					}
					break;
					
				default:
					return False;
					break;				
			}
		} // End of valid function

		// Function to change password
		function chgPassword($e, $oldp, $pass1, $pass2)
		{
			// Make the query	
			$q = "SELECT pass FROM users WHERE (email=? AND activation='') LIMIT 1";

			// Prepare the statement
			$stmt = $this->dbc->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('s', $e);

			// Execute the query:
			$stmt->execute();
			
			// Store result
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($outbdp);

			//Assign the outbound variables			
			while ($stmt->fetch())
			{
				$pass = $outbdp;
			}
			
			$hasher = new PasswordHash(8, FALSE);					
				
			// Checks if old password matches current password in database. If so proceed to change password.
			if ($hasher->CheckPassword($oldp, $pass)) {
				
				// Checks if new password matches confirm new password and also validates.
				$p = FALSE;
				if (strlen($pass1) > 5)
				{
					if ($pass1 == $pass2)
					{
						$p = $pass1;
					}	
					else 
					{
						echo '<div class="alert alert-error">Your password did not match the confirmed password!</div>';
					}
				}
				else 
				{
					echo '<div class="alert alert-error">Please enter a valid new password!</div>';
				}		
		
				// Encrypt the new password by making a new hash.
				$hash = $hasher->HashPassword($p);				
				if (strlen($hash) < 20)
				{
					fail('Failed to hash new password'); // Custom function
					exit();
				}
				unset($hasher);
				
				// If new password is valid, proceed to update database with new password.
				if ($p) {
					
					// Make the query
					$q = "UPDATE users SET pass=? WHERE email=? LIMIT 1";
		
					// Prepare the statement
					$stmt = $this->dbc->prepare($q);
		
					// Bind the inbound variable:
					$stmt->bind_param('ss', $hash, $e);
		
					// Execute the query:
					$stmt->execute();
		
					if ($stmt->affected_rows == 1) { // It ran OK.
						$body = "Your password has been changed. If you feel you got this email in error please contact the system administrator.";
						$body = wordwrap($body, 70);
						mail ($e, 'digoro.com - Password Changed', $body);
										
						echo '<h3>Your password has been changed.</h3>';

						// Close the statement:
						$stmt->close();
						unset($stmt);					
					}
					else {
						echo '<p class="error">Your password was not changed. Make sure your new password
							is different than the current password. Contact the system administrator if you think
							and error occured.</p>';
					}
				}
			}
		} // End of chgPassword function

		
		// Function to check if user already registered with OAuth
		function isOAuthRegistered($email) {
		
			// Make the query	
			$q = "SELECT oauth_registered FROM users WHERE email=? LIMIT 1";
			
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables
			$stmt->bind_param('s', $email);
						
			// Execute the query
			$stmt->execute();
			
			// Store result
			$stmt->store_result();
			
			// Bind the outbound variable
			$stmt->bind_result($OAstatusOB);	
						
			if ($stmt->num_rows == 1) { // Found match in database
				while ($stmt->fetch()) {
					$OAstatus = $OAstatusOB;
				}					
				if ($OAstatus == 1) {
					return True;
				}
				else {				
					return False;
				}	
			}		
		
			// Close the statement
			$stmt->close();
			unset($stmt);
			
		} // End of isOAuthRegistered function
		
		
		// Function to add OAuth Users
		function addOAuthUser($e, $fn, $ln, $role, $gd, $bdfrmat) {

			if (!self::isOAuthRegistered($e)) {
	
				// Define constant
				$oauth_reg = 1;
	
				// Call checkUser function	
				self::checkUser($e);
	
				// Determine registration method
				switch ($this->inv_case)
				{
					case 1: // User is new to the system & not invited by manager		
						$iv = 0; // Define invite constant in database to "brand new user"
						
						// Make the query to add new user to database
						$q = 'INSERT INTO users (email, first_name, last_name, role, gender, birth_date, invited, oauth_registered, registration_date) 
							VALUES (?,?,?,?,?,?,?,?,NOW())';
			
						// Prepare the statement
						$stmt = $this->dbc->prepare($q); 
			
						// Bind the inbound variables:
						$stmt->bind_param('ssssssii', $e, $fn, $ln, $role, $gd, $bdfrmat, $iv, $oauth_reg);
							
						// Execute the query:
						$stmt->execute();
	
						if ($stmt->affected_rows == 1) // It ran OK.
						{
							$userID = $stmt->insert_id;	
	
							if ($role == 'M') {
								$user = new Manager($userID);					
								$_SESSION['userObj'] = $user;
							}
								
							if ($role == 'P') {
								$user = new Player($userID);
								$_SESSION['userObj'] = $user;
							}					
						}
						else 
						{	// Registration process did not run OK.
							echo '<div class="alert alert-error">You could not be registered due to a system error. We apologize
								for any inconvenience. [Case 1]</div>';
						}
							
						// Close the statement:
						$stmt->close();
						unset($stmt);
		
						break;
	
					case 2: // User invited by manager					
	
						// Make the query to select the user ID
						$q = "SELECT id_user FROM users WHERE email=? LIMIT 1";
						
						// Prepare the statement
						$stmt = $this->dbc->prepare($q);
						
						// Bind the inbound variable:
						$stmt->bind_param('s', $e);
						
						// Execute the statement
						$stmt->execute();
						
						// Store result
						$stmt->store_result();
						
						// Bind the outbound variable
						$stmt->bind_result($OAid);	
									
						if ($stmt->num_rows == 1) { // Found match in database
						
							//Assign the outbound variables	
							while($stmt->fetch()) {
								$userID = $OAid;
							}
									
							// Make the query to update user in database
							$q = 'UPDATE users SET first_name=?, last_name=?, role=?, gender=?, birth_date=?, registration_date=NOW(), oauth_registered=?
								WHERE id_user=? LIMIT 1';
			
							// Prepare the statement
							$stmt2 = $this->dbc->prepare($q);
			
							// Bind the inbound variables:
							$stmt2->bind_param('sssssii', $fn, $ln, $role, $gd, $bdfrmat, $oauth_reg, $userID);
							
							// Execute the query:
							$stmt2->execute();
				
							if ($stmt2->affected_rows == 1) {// It ran OK.
		
								if ($role == 'M') {
									$user = new Manager($userID);						
									$_SESSION['userObj'] = $user;
								}
									
								if ($role == 'P') {
									$user = new Player($userID);
									$_SESSION['userObj'] = $user;
								}
							}
							else {
								//Update failed
								echo '<div class="alert alert-error">You could not be registered due to a system error. We apologize
									for any inconvenience. [Case 2]</div>';
							}
	
							// Close the statement:
							$stmt2->close();
							unset($stmt2);
	
						}
						else {	
							// Registration process did not run OK.
							echo '<div class="alert alert-error">You could not be registered due to a system error. We apologize
								for any inconvenience.</div>';
						}
						
						// Close the statement:
						$stmt->close();
						unset($stmt);
						break;
						
					default:
						break;
				} // End of switch
			}
		} // End of addOAuthUser function
		

		//	Function to login an OAuth User
		function OAuthlogin($e) {

			// Make the query	
			$q = "SELECT id_user, login_before, default_teamID FROM users 
				WHERE (email=? AND activation='') LIMIT 1";
		
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variable:
			$stmt->bind_param('s', $e);
			
			// Execute the query:
			$stmt->execute();
						
			// Store result
			$stmt->store_result();
						
			// Bind the outbound variable:
			$stmt->bind_result($idOB, $logbfOB, $deftmIDOB);
			
			if ($stmt->num_rows == 1) // Found match in database
			{
				//Assign the outbound variables			
				while ($stmt->fetch())
				{
					$userID = $idOB;
					$lb = $logbfOB;
					$deftmID = $deftmIDOB;
				}					
	
				session_regenerate_id(True);
		
				// Set default team to session variable
				$_SESSION['deftmID'] = $deftmID;
						
				// Store the HTTP_USER_AGENT:
				$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
							
				if (self::isOAuthRegistered($e) && ($lb == 1)) {
						
					//Redirect User
					$user = new User($userID);
					$_SESSION['userObj'] = $user;							
					$url = BASE_URL . 'manager/home.php';
	
					ob_end_clean();
					header("Location: $url");
					
					exit();
				}
				elseif (!self::isOAuthRegistered($e) && ($lb == 1)) { //User is not OAuth registered but has logged in before
					// Set boolean logic to true
					$bl = 1;
					
					// Update the user's info in the database
					$q = 'UPDATE users SET oauth_registered=? WHERE id_user=? LIMIT 1';
		
					// Prepare the statement
					$stmt2 = $this->dbc->prepare($q); 
		
					// Bind the inbound variables:
					$stmt2->bind_param('ii', $bl, $userID);
						
					// Execute the query:
					$stmt2->execute();
						
					if ($stmt2->affected_rows !== 1) // It didn't run ok
					{
						echo '<div class="alert alert-error">There was an error. Please contact the service administrator</div>';
						exit();
					}						

					//Redirect User					
					$user = new User($userID);
					$_SESSION['userObj'] = $user;
					$url = BASE_URL . 'manager/home.php';
					
					ob_end_clean();
					header("Location: $url");
					
					// Close the statement:
					$stmt2->close();
					unset($stmt2);					
					exit();
				}
				else {
					
					//Redirect User
					$user = new User($userID);
					$_SESSION['userObj'] = $user;				
					$url = BASE_URL . 'core/oauth_welcome.php';
					ob_end_clean();
					header("Location: $url");
					exit();				
				}
			}	
			else {
				//Redirect User
				$url = BASE_URL . 'core/oauth_welcome.php';
				ob_end_clean();
				header("Location: $url");
				exit();
			}
			
			// Close the statement:
			$stmt->close();
			unset($stmt);

		} // End of OAuthlogin function
						
		
	} // End of Class
