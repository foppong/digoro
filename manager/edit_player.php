<?php
	// This page is for editing a player
	// This page is accessed through view_roster.php
	
	require '../includes/config.php';
	$page_title = 'digoro : Edit Player';
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

	// Authorized Login Check
	if (!$manager->valid($lvl))
	{
		redirect_to('index.php');
	}

	// Establish database connection
	require_once MYSQL2;


	if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) ) // From view roster page
	{
		$id = $_GET['x'];

		// Create member object for use & pull latest data from database & initially set attributes
		$member = new Member($id);
		$member->setDB($db);
		$member->pullMemberData();
		$member->checkAuth($userID);

	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted from edit_player page	
	{
		$id = $_POST['z'];

		// Create member object for use & pull latest data from database & initially set attributes
		$member = new Member($id);
		$member->setDB($db);
		$member->pullMemberData();
		$member->checkAuth($userID);

		// Trim all the incoming data:
		$trimmed = array_map('trim', $_POST);
		
		// Assume invalid values:
		$pos = $jnumb = FALSE;
		
		// Validate position input
		if (is_string($trimmed['position']))
		{
			$pos = $trimmed['position'];
		}
		else 
		{
			echo '<p class="error">Please enter your position.</p>';
		}

		// Validate jersey number input
		if (filter_var($_POST['jersey_num'], FILTER_VALIDATE_INT))
		{
			$jnumb = $_POST['jersey_num'];
		}
		else 
		{
			echo '<p class="error">Please enter your jersey number.</p>';
		}
		

		// Check if user entered information is valid before continuing to edit player
		if ($pos && $jnumb)
		{
			$member->editMember($userID, $pos, $jnumb);
		}
		else
		{	// Errors in the user entered information
			echo '<p class="error">Please try again.</p>';
		}
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();		
	}

	// Get attributes from member object
	$name = $member->getMemberAttribute('mname');
	$pos = $member->getMemberAttribute('position');
	$jnumb = $member->getMemberAttribute('jersey_numb');
	
		
	// Valid user name, show the form.
	if ($name != '')
	{
		// Headliner
		echo '<h2>Edit ' . $name . '\'s Player Profile</h2>';
			
		// Create the form:
		echo '<form action ="edit_player.php" method="post" id="EditPlayerForm">
			<fieldset>
			<input type="hidden" name="z" value="' . $id . '" />
				
			<div>
				<label for="position"><b>Position:</b></label>
				<input type="text" name="position" id="position" 
				size="20" maxlength="20" value="' . $pos . '" />				
			</div>
				
			<div>
				<label for="jersey_num"><b>Jersey Number:</b></label>
				<input type="text" name="jersey_num" id="jersey_num" 
				size="4" maxlength="4" value="' . $jnumb . '" />
			</div>
				
			<input type="submit" name="submit" value="Save"/>
			</fieldset>
			</form><br />';
	}
	else 
	{	//Not a valid user ID, kill the script
		echo '<p class="error">This page has been accessed in error.</p>';
		include '../includes/footer.html';
		exit();
	}
		
	// Delete objects
	unset($member);
	unset($manager);
			
	// Close the connection:
	$db->close();
	unset($db);
					
	include '../includes/footer.html';
?>