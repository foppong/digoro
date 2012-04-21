<?php
	// forgot_password.php
	// This page allows a user to reset their password if forgotten.

	require '../includes/PasswordHash.php';	
	require '../includes/config.php';
	$page_title = 'digoro : Password Reset';
	include '../includes/iheader.html';
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		require MYSQL2;
		
		$e = $e2 = '';
		
		// Assume nothing:
		$ue = FALSE;

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

		// Validate confirm email address
		if (filter_var($_POST['email2'], FILTER_VALIDATE_EMAIL))
		{
			$e2 = $_POST['email2'];
		}
		else 
		{

			echo '<p class="error"> Please enter valid email address!</p>';
		}

		// Check if both emails entered are the same, then set the final user email variable
		if ($_POST['email'] == $_POST['email2'])
		{
			$ue = $_POST['email'];
		}	
		else 
		{
			echo '<p class="error">Your email did not match the confirmed email!</p>';
		}
	

		
		// If email entered is valid, proceed to verify the email address in database
		if ($ue)
		{
			// Make the query	
			$q = 'SELECT id_user FROM users WHERE email=? LIMIT 1';

			// Prepare the statement
			$stmt = $db->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('s', $ue);

			// Execute the query:
			$stmt->execute();
			
			// Store result:
			$stmt->store_result();

			// Bind the outbound variable:
			$stmt->bind_result($id_fetch);

			// Assign the outbound variables			
			while ($stmt->fetch())
			{
				$id_user = $id_fetch;
			}

			$hasher = new PasswordHash($hash_cost_log2, $hash_portable);

			// Email was not found in database
			if ($stmt->num_rows == 0)
			{
				echo '<p class="error"> The submitted email address does not match those on file!</p>';
			}
			else 
			{	// Email was found in database
				
				// Create a new, random password:
				$p = substr(md5(uniqid(rand(), true)), 3, 10);
		
				$hash = $hasher->HashPassword($p);
				if (strlen($hash) < 20)
				{
					fail('Failed to hash new password');
				}
				unset($hasher);
				
				// Make the query
				$q = "UPDATE users SET pass=? WHERE id_user=? LIMIT 1";
	
				// Prepare the statement
				$stmt = $db->prepare($q);
	
				// Bind the inbound variable:
				$stmt->bind_param('si', $hash, $id_user);
	
				// Execute the query:
				$stmt->execute();

				if ($stmt->affected_rows == 1) // It ran OK.
				{
					// Send an email:
					$body = "Your password to log into digoro has been temporarily changed to: \n\n$p\n\nPlease log in using this password and your email address. Then you may change your password once into the system.";
					$body = wordwrap($body, 70);
					mail ($ue, 'digoro.com - Your temporary password.', $body);
					
					// Print message on screen
					echo '<h2>Your password has been changed. You will receive the new, temporary password at the
						email address with which you registered. Once you have logged in with this password,
						you may change it by clicking on the "Change Password" link.</h2>';
						
					// Close the statement:
					$stmt->close();
					unset($stmt);
					
					// Close the connection:
					$db->close();
					unset($db);

					include '../includes/ifooter.html';
					exit();	
				}
				else 
				{
					echo '<p class="error">Your password could not be changed due to a system error. We apologize
						for any inconvenience.</p>';
				}
			}
			// Close hasher
			unset($hasher);
		}
		else
		{
			echo '<p class="error">Please try again.</p>';
		}
		
		$db->close();
		unset($db);
	}
?>



</head>
<body>
	<div id="Header">
		<h1>digoro</h1>
		<h2>Connecting teams and players!</h2>
	</div>

	<h2>Reset Your Password</h2>
	<p>Enter your email address below and your password will be reset.</p>
	<form action="forgot_password.php" method="post" id="ForgotPassForm">
		<fieldset>
			<div>
				<label for="email"><b>Email Address:</b></label>
				<input type="text" name="email" id="email" size="30" maxlength="60"
				value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" />
			</div>		
			<div>
				<label for="email2"><b>Confirm Email Address:</b></label>
				<input type="text" name="email2" id="email2" size="30" maxlength="60" />
			</div>	
		<input type="submit" name="submit" id="submit" value="Reset My Password" />
		</fieldset>
	</form>

<?php include '../includes/ifooter.html'; ?>


