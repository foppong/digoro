<?php
	/* This page defines the UserAuth class.
	 * Attributes:
	 * 	protected id_user
	 *  protected dbc
	 *  protected inv_case
	 * 	
	 * Methods:
	 *  setDB()
	 * 	checkPass()
	 * 	checkUser()
	 * 	createUser()
	 *  login()
	 *  logout()
	 */
	
	require_once 'includes/PasswordHash.php';
	
	class UserAuth {
	 	
		// Declare the attributes
		protected $id_user, $dbc, $inv_case;

		// Constructor
		function __construct() {}

		// Set database connection
		function setDB($db)
		{
			$this->dbc = $db;
		}

		// Method to check user against password entered
		function checkPass($e, $p)
		{
			// Assign variable in case no matches
			$pass = '';

			// Make the query	
			$q = "SELECT pass FROM users WHERE email=? LIMIT 1";

			// Prepare the statement
			$stmt = $this->dbc->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('s', $e);

			// Execute the query:
			$stmt->execute();
			
			// Store result
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($passOB);

			//Assign the outbound variables			
			while ($stmt->fetch())
			{
				$pass = $passOB;
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
		}
		
		// Method to check User registration status
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
			$stmt->bind_result($idOB, $inviteOB);

			//Assign the outbound variables			
			while ($stmt->fetch()) {
				$this->id_user = $idOB;
				$invite = $inviteOB;
			}

			if ($stmt->num_rows == 0) {
				$this->inv_case = 1; // User login available & not invited by manager
			}

			if (($stmt->num_rows == 1 && $invite == 1)) {
				$this->inv_case = 2; // Manager has already entered skeleton information about new user & invited player
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);
				
		} // End of function

		// Function to create users
		function createUser($e, $p, $fn, $ln, $mstatus, $zp, $gd, $bdfrmat, $iv) 
		{
			// Call checkUser function	
			self::checkUser($e);

			$hasher = new PasswordHash(8, FALSE);
					
			// Encrypt the new password by making a new hash.
			$hash = $hasher->HashPassword($p);
			if (strlen($hash) < 20)
			{
				fail('Failed to hash new password');
				exit();
			}
			unset($hasher);

			// Determine registration method
			switch ($this->inv_case)
			{
				case 1: // User is new to the system & not invited by manager
					
					// Create the activation code
					$a = md5(uniqid(rand(), TRUE));	
		
					// Make the query to add new user to database
					$q = 'INSERT INTO users (email, pass, first_name, last_name, role, zipcode, gender, activation, birth_date, invited, registration_date) 
						VALUES (?,?,?,?,?,?,?,?,?,?,NOW())';
		
					// Prepare the statement
					$stmt = $this->dbc->prepare($q); 
		
					// Bind the inbound variables:
					$stmt->bind_param('sssssssssi', $e, $hash, $fn, $ln, $mstatus, $zp, $gd, $a, $bdfrmat, $iv);
						
					// Execute the query:
					$stmt->execute();
						
					if ($stmt->affected_rows == 1) // It ran OK.
					{
						// Send the activation email
						$body = "Welcome to digoro and thank you for registering!\n\nTo activate your account, please click on this link:";
						$body .= "\n" . BASE_URL . 'activate.php?x=' . urlencode($e) . "&y=$a";
						mail($e, 'digoro.com - Registration Confirmation', $body);
						
						echo '<h3>Thank you for registering! A confirmation email has been sent to your address. Please
							click on the link in that email in order to activate your account. </h3>';
		
						// Close the statement:
						$stmt->close();
						unset($stmt);
						
						// Close the connection:
						$this->dbc->close();
						unset($this->dbc);
							
						include 'includes/footer.html';
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
					$q = 'UPDATE users SET pass=?, first_name=?, last_name=?, role=?, zipcode=?, gender=?, activation=?, birth_date=?, registration_date=NOW() 
						WHERE id_user=? LIMIT 1';
	
					// Prepare the statement
					$stmt = $this->dbc->prepare($q);
	
					// Bind the inbound variables:
					$stmt->bind_param('ssssisssi', $hash, $fn, $ln, $mstatus, $zp, $gd, $a, $bdfrmat, $this->id_user);
					
					// Execute the query:
					$stmt->execute();
		
					if ($stmt->affected_rows == 1) // It ran OK.
					{
						// Send the activation email
						$body = "Welcome to digoro and thank you for registering!\n\nTo activate your account, please click on this link:";
						$body .= "\n" . BASE_URL . 'activate.php?x=' . urlencode($e) . "&y=$a";
						mail($e, 'digoro.com - Registration Confirmation', $body);
						
						echo '<h3>Thank you for registering! A confirmation email has been sent to your address. Please
							click on the link in that email in order to activate your account. </h3>';
	
						// Close the statement:
						$stmt->close();
						unset($stmt);
						
						// Close the connection:
						$this->dbc->close();
						unset($this->dbc);
						
						include 'includes/footer.html';
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
		} // End of function

		// Function to delete users
		function deleteUser($id)
		{
			// Confirmation that form has been submitted:	
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if ($_POST['sure'] == 'Yes')
				{	// If form submitted is yes, delete the record
				
					// Make the query	
					$q = "DELETE FROM users WHERE id_user=? LIMIT 1";
		
					// Prepare the statement:
					$stmt = $this->dbc->prepare($q);
		
					// Bind the inbound variable:
					$stmt->bind_param('i', $id);
		
					// Execute the query:
					$stmt->execute();
					
					// If the query ran ok.
					if ($stmt->affected_rows == 1) 
					{
						session_unset();
						session_destroy();
					}
					else 
					{	// If the query did not run ok.
						echo '<p class="error">This account could not be deleted due to a system errror.</p>';
					}
				}
				else
				{	// No confirmation of deletion.
					echo '<p>This account has NOT been deleted.</p>';
				}
			}
			else
			{
				//Confirmation message:
				echo '<h3>Are you sure you want to delete your account? We will miss you!</h3>';
					
				// Create the form:
				echo '<form action ="delete_acct.php" method="post" id="DelAcctForm">
					<input type="radio" name="sure" value="Yes" />Yes<br />
					<input type="radio" name="sure" value="No" checked="checked" />No<br />
					<input type="submit" name="submit" value="Delete" />
					</form>';
			
			} // End of the main submission conditional.		
		}

		// Function to log in users
		function login($e, $p)
		{
			if (self::checkPass($e, $p)) // Call checkPass function	
			{
				// Make the query	
				$q = "SELECT role, id_user, first_name, last_name, login_before, default_teamID FROM users 
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
				$stmt->bind_result($roleOB, $idOB, $fnOB, $lnOB, $logbfOB, $deftmIDOB);
	
				if ($stmt->num_rows == 1) // Found match in database
				{
					//Assign the outbound variables			
					while ($stmt->fetch())
					{
						$role = $roleOB;
						$userID = $idOB;
						$fn = $fnOB;
						$ln = $lnOB;
						$lb = $logbfOB;
						$deftmID = $deftmIDOB;
					}					

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
					$this->dbc->close();
					unset($this->dbc);
						
					include 'includes/footer.html';
					exit();
				}
				else 
				{
					echo '<p class="error">You could not be logged in. Please check that you have activated your account.</p>';
				}

			}
			else 
			{
				echo '<p class="error">Either the email address and password entered do not match those
					those on file or you have not yet activated your account.</p>';
			}	
		}
		
		// Function to log off users
		function logoff()
		{
			session_unset();
			session_destroy();
		}

	} // End of Class
?>