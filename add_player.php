<?php
	// add_player.php
	// This page allows a logged-in user to add a player to a team
	
	require 'includes/config.php';
	$page_title = 'digoro : Add Player';
	include 'includes/header.html';

	// Authorized Login Check
	// If not an administrator or manager, or no session value is present, redirect the user. Also validate the HTTP_USER_AGENT
	if ( ($_SESSION['role'] == 'P') OR !isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) )
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();
	}

	// Assign team ID from session variable
	$tm = $_SESSION['deftmID'];
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		require MYSQL;

		// Trim all the incoming data:
		$trimmed = array_map('trim', $_POST);
		
		// Assume invalid values:
		$fn = $ln = $e = FALSE;

		// Validate firstname
		if (preg_match('/^[A-Z \'.-]{2,20}$/i', $trimmed['first_name']))
		{
			$fn = $trimmed['first_name'];
		}
		else 
		{
			echo '<p class="error"> Please enter valid first name.</p>';
		}
	
		// Validate lastname
		if (preg_match('/^[A-Z \'.-]{2,40}$/i', $trimmed['last_name']))
		{
			$ln = $trimmed['last_name'];
		}
		else 
		{
			echo '<p class="error"> Please enter valid last name.</p>';
		}
	
		// Validate email
		if (filter_var($trimmed['email'], FILTER_VALIDATE_EMAIL))
		{
			$e = $trimmed['email'];
		}
		else 
		{
			echo '<p class="error"> Please enter valid email address.</p>';
		}

		// Checks if name, email, and league are valid before proceeding.
		if ($tm && $fn && $ln && $e)
		{
			// Code checks users database to see if player is already registered. If not, enters a place holder until 
			// added player completes registeration.
			
			// Assign variable in case no matches
			$id_user = '';
			
			// Make the query	
			$q = 'SELECT id_user FROM users WHERE email=? LIMIT 1';

			// Prepare the statement:
			$stmt = $db->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('s', $e);

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

			// If player exists in user table, add player to sport_player table 
			if ($stmt->num_rows == 1) 
			{
				// Make the query:
				$q = 'INSERT INTO players (id_user, id_team) VALUES (?,?)';
				
				// Prepare the statement:
				$stmt = $db->prepare($q);
					
				// Bind the inbound variables:
				$stmt->bind_param('ii', $id_user, $tm);
				
				// Execute the query:
				$stmt->execute();
					
				if ($stmt->affected_rows == 1) // It ran ok
				{
					echo '<p>' . $fn . ' ' . $ln . ' was added successfully.</p>';
				}
				else
				{
					echo '<p class="error">Player ' . $fn . ' ' . $ln . ' was not added. Please contact the service administrator.</p>';
				}
		
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
			{	// If player doesn't exist in user table, add skeleton informatoin to user table, add them to sport_table, & send invitation.		

				// Boolean used for invitation column, setting to True for invited
				$iv = True;
			
				// Make the query to add new user to database
				$q = 'INSERT INTO users (email, invited) VALUES (?,?)';
	
				// Prepare the statement
				$stmt = $db->prepare($q); 
	
				// Bind the inbound variables:
				$stmt->bind_param('ss', $e, $iv);
					
				// Execute the query:
				$stmt->execute();
				
				$newID = $stmt->insert_id;
	
				if ($stmt->affected_rows == 1) // It ran OK.
				{
					// Make the query:
					$q = 'INSERT INTO players (id_user, id_team) VALUES (?,?)';
					
					// Prepare the statement
					$stmt = $db->prepare($q);
						
					// Bind the inbound variables
					$stmt->bind_param('ii', $newID, $tm);
					
					// Execute the query:
					$stmt->execute();
						
					if ($stmt->affected_rows == 1) // It ran ok
					{
						echo '<p>' . $fn . ' ' . $ln . ' was added successfully.</p>';
					}
					else
					{
						echo '<p class="error">Player' . $fn . ' ' . $ln . ' was not added. Please contact the service administrator.</p>';
					}

					// Add conditional here somehow so that Manager has option to submit request or not
					// Send the invitation email
					$body = "Hello, you have been invited to join digoro!\n\nWe are a site devoted to connecting players and teams.\n\n 
						You can find more information at our website:";
					$body .= "\n" . BASE_URL;
					mail($e, 'digoro.com - Digoro Invitation', $body);
						
					echo '<h3>Invitation successfully sent.</h3>';
	
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
				{	// Registration process did not run OK.
					echo '<p class="error">Invitation could not be sent. We apologize
						for any inconvenience.</p>';
				}
			}			
		}
		else 
		{									
			echo '<p class="error">Please try again.</p>';
		}

	// Close the connection:
	$db->close();
	unset($db);			
	}
?>

<h2>Add Player to Team</h2>
<form action="add_player.php" method="post" id="AddPlayerForm">
	<fieldset>		
	<div>	
		<label for="first_name"><b>Enter Player's First Name:</b></label>
		<input type="text" name="first_name" id="first_name" size="20" maxlength="20"
		value="<?php if (isset($trimmed['first_name'])) echo $trimmed['first_name']; ?>" />
	</div>

	<div>
		<label for="last_name"><b>Enter Player's Last Name:</b></label>
		<input type="text" name="last_name" id="last_name" size="20" maxlength="40"
		value="<?php if (isset($trimmed['last_name'])) echo $trimmed['last_name']; ?>" />
	</div>

	<div>
		<label for="email"><b>Enter Player's Email Address:</b></label>
		<input type="text" name="email" id="email" size="30" maxlength="60"
		value="<?php if (isset($trimmed['email'])) echo $trimmed['email']; ?>" />
	</div>
	</fieldset>
	<div align="center"><input type="submit" name="submit" value="Add Player" />
</form>

<?php include 'includes/footer.html'; ?>
