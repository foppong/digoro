<?php
	// change_password.php
	// This page allows a logged-in user to change their password
	
	require 'includes/PasswordHash.php';	
	require 'includes/config.php';
	$page_title = 'digoro : Change Your Password';
	include 'includes/header.html';

	// autoloading of classes
	function __autoload($class) {
		require_once('classes/' . $class . '.php');
	}

	// Create user object
	$user = new UserAuth();

	// Site access level -> General
	$lvl = 'G'; 

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();	
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
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

		// Validate old password
		if (!empty($_POST['oldpass']))
		{
			$oldp = $_POST['oldpass'];
		}
		else 
		{
			$oldp = FALSE;
			echo '<p class="error">You forgot to enter your old password!</p>';
		}

		// Checks if email and old password entered are valid before proceeding to change password.
		if ($e && $oldp)
		{
			// Assign variable in case no matches
			$pass = '';

			// Make the query	
			$q = "SELECT pass FROM users WHERE (email=? AND activation='') LIMIT 1";

			// Prepare the statement
			$stmt = $db->prepare($q);

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
			
			$hasher = new PasswordHash($hash_cost_log2, $hash_portable);					
				
			// Checks if old password matches current password in database. If so proceed to change password.
			if ($hasher->CheckPassword($oldp, $pass)) 
			{		
				// Checks if new password matches confirm new password and also validates.
				$p = FALSE;
				if (strlen($_POST['pass1']) > 5)
				{
					if ($_POST['pass1'] == $_POST['pass2'])
					{
						$p = $_POST['pass1'];
					}	
					else 
					{
						echo '<p class="error">Your password did not match the confirmed password!</p>';
					}
				}
				else 
				{
					echo '<p class="error"> Please enter a valid new password!</p>';
				}		
		
				// Encrypt the new password by making a new hash.
				$hash = $hasher->HashPassword($p);				
				if (strlen($hash) < 20)
				{
					fail('Failed to hash new password');
					exit();
				}
				unset($hasher);
				
				// If new password is valid, proceed to update database with new password.
				if ($p)
				{
					// Make the query
					$q = "UPDATE users SET pass=? WHERE email=? LIMIT 1";
		
					// Prepare the statement
					$stmt = $db->prepare($q);
		
					// Bind the inbound variable:
					$stmt->bind_param('ss', $hash, $e);
		
					// Execute the query:
					$stmt->execute();
		
					if ($stmt->affected_rows == 1) // It ran OK.
					{
						$body = "Your password has been changed. If you feel you got this email in error please contact the system administrator.";
						$body = wordwrap($body, 70);
						mail ($e, 'digoro.com - Password Changed', $body);
										
						echo '<h3>Your password has been changed.</h3>';

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
						echo '<p class="error">Your password was not changed. Make sure your new password
							is different than the current password. Contact the system administrator if you think
							and error occured.</p>';
					}
				}
				else
				{
					echo '<p class="error">Please try again.</p>';
				}
			}
			else
			{
				echo '<p class="error">Either the email address and password entered do not match those
					those on file or you have not yet activated your account.</p>';
			}
			// Close hasher
			unset($hasher);
		}
		$db->close();
		unset($db);
	}
?>


<h2>Change Your Password</h2>
<form action="change_password.php" method="post" id="ChgPassForm">
	<fieldset>
	<div>
		<label for="email"><b>Email Address:</b></label>
		<input type="text" name="email" id="email" size="30" maxlength="60" />
	</div>

	<div>
		<label for="oldpass"><b>Enter Current Password:</b></label>
		<input type="password" name="oldpass" id="oldpass" size="20" maxlength="20" />
	</div>
	
	<div>
		<label for="pass1"><b>Enter New Password:</b></label>
		<input type="password" name="pass1" id="pass1" size="20" maxlength="20" />
		<small>Password must be between 6 and 20 characters long.</small>
	</div>
	
	<div>
		<label for="pass2"><b>Confirm New Password:</b></label>
		<input type="password" name="pass2" id="pass2" size="20" maxlength="20" />
	</div>

	<input type="submit" name="submit" id="submit" value="Change My Password" />	
	</fieldset>
</form>

<?php include 'includes/footer.html'; ?>
