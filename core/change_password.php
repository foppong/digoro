<?php
	// change_password.php
	// This page allows a logged-in user to change their password
	
	require '../includes/config.php';
	$page_title = 'digoro : Change Your Password';
	include '../includes/header.html';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Site access level -> General
	$lvl = 'G'; 

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$user = $_SESSION['userObj'];
	}
	else 
	{
		redirect_to('index.php');
	}

	// Authorized Login Check
	if (!$user->valid($lvl))
	{
		redirect_to('index.php');
	}

	// Need the database connection:
	require MYSQL2;

	// Assign Database Resource to object
	$user->setDB($db);
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
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

		$pass1 = $_POST['pass1'];
		$pass2 = $_POST['pass2'];		

		// Checks if email and old password entered are valid before proceeding to change password.
		if ($e && $oldp) {
			$user->chgPassword($e, $oldp, $pass1, $pass2);
		} else {
			echo '<p class="error">Please enter a valid password</p>';
		}
	}
	
	// Delete objects
	unset($user);
			
	// Close the connection:
	$db->close();
	unset($db);		

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

<?php include '../includes/footer.html'; ?>
