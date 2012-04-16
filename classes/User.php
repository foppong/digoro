<?php
	/* This page defines the User class.
	 * Attributes:
	 * 	protected id_user
	 *  protected dbc
	 *  protected inv_case
	 * 	
	 * Methods:
	 *  addTeam()
	 * 	createUser()
	 *  login()
	 *  logout()
	 *  valid()
	 */
	
	require_once 'includes/PasswordHash.php';
	
	class User extends UserAuth {
	 	
		// Declare the attributes
		protected $id_user, $dbc, $inv_case;

		// Constructor
		function __construct() {}

		// Function to create users
		function createUser($e, $p, $fn, $ln, $mstatus, $zp, $gd, $bdfrmat, $iv) 
		{
			// Call checkUser function	
			parent::checkUser($e);

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




	} // End of Class
?>