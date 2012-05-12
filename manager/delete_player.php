<?php
	// This page is for deleting a player record
	// This page is accessed through view_roster.php
	
	require '../includes/config.php';
	$page_title = 'digoro : Delete Player';
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

	// Need the database connection:
	require_once MYSQL2;

	if ( (isset($_GET['x'])) && (is_numeric($_GET['x'])) ) // From view roster page
	{
		$id = $_GET['x'];

		// Create member object for use & pull latest data from database & initially set attributes
		$member = new Member();
		$member->setDB($db);
		$member->setMembID($id);
		$member->pullMemberData();
		$member->checkAuth($userID);

	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['z'])) // Confirmation that form has been submitted from delete_player page	
	{
		$id = $_POST['z'];

		// Create member object for use & pull latest data from database & initially set attributes
		$member = new Member();
		$member->setDB($db);
		$member->setMembID($id);
		$member->pullMemberData();
		$member->checkAuth($userID);

		if ($_POST['sure'] == 'Yes')
		{	// If form submitted is yes, delete the record
			$member->deleteMember($userID);
		}
		else
		{	// No confirmation of deletion.
			echo '<p>The member has NOT been deleted.</p>';
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
		
	// Valid user ID, show the form.
	if ($name != '')
	{
		//Display the record being deleted:
		echo '<h3>Are you sure you want to delete the player ' . $name . ' from your roster?</h3>';
		
		// Create the form:
		echo '<form action ="delete_player.php" method="post" id="DelPlayerForm">
			<input type="hidden" name="z" value="' . $id . '" />
			<input type="radio" name="sure" value="Yes" />Yes<br />
			<input type="radio" name="sure" value="No" checked="checked" />No<br />
			<input type="submit" name="submit" value="Delete" />
			</form>';
	}
	else 
	{	//Not a valid user ID.
		echo '<p class="error">This page has been accessed in error.</p>';
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