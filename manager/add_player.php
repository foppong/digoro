<?php
	// add_player.php
	// This page allows a logged-in user to add a player to a team
	
	require '../includes/config.php';
	$page_title = 'digoro : Add Player';
	include '../includes/header.html';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Site access level -> Manager
	$lvl = 'M'; 

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$manager = $_SESSION['userObj'];
		$userID = $manager->getUserID();
	}
	else 
	{
		redirect_to('index.php');
	}

	// Establish database connection
	require_once MYSQL2;

	// Assign Database Resource to object
	$manager->setDB($db);

	// Authorized Login Check
	if (!$manager->valid($lvl))
	{
		redirect_to('index.php');
	}

	// Retrieve current team ID in session
	$ctmID = $_SESSION['ctmID'];
		
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{

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
		if ($ctmID && $fn && $ln && $e)
		{
			$member = new Member();
			$member->setDB($db);
			$member->createMember($e, $ctmID, $fn, $ln);

			// Close the connection:
			$db->close();
			unset($db);
				
			include '../includes/footer.html';
			exit();	

		}
		else 
		{									
			echo '<p class="error">Please try again.</p>';
		}
	}

	// Delete objects
	unset($member);
	unset($manager);

	// Close the connection:
	$db->close();
	unset($db);	

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

<?php include '../includes/footer.html'; ?>
