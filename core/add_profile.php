<?php
	// This page is for adding a profile
	// This page is accessed through profile.php
	
	ob_start();
	session_start();	
		
	require '../includes/config.php';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$player = $_SESSION['userObj'];
		$userID = $player->getUserID();
	}
	else 
	{
		redirect_to('index.php');
	}
	
	// Establish database connection
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Confirmation that form has been submitted	

		// Create object
		$profile = new Profile();
		$profile->setDB($db);

		// Assume invalid values
		$sex = $reg = $sport = $exp = FALSE;

		// Validate sport is selected
		if ($_POST['add-profile-sel-sport']) {
			$sport = $_POST['add-profile-sel-sport'];
		}
		else {
			echo 'Please select a sport.';
			exit();
		}
	
		// Validate team sex is selected
		if ($_POST['add-profile-sel-sex']) {
			$sex = $_POST['add-profile-sel-sex'];
		}
		else {
			echo 'Please select team sex preference.';
			exit();
		}

		// Validate region is selected
		if ($_POST['add-profile-sel-region']) {
			$reg = $_POST['add-profile-sel-region'];
		}
		else {
			echo 'Please select a region preference.';
			exit();
		}

		// Validate experience is selected
		if ($_POST['add-profile-sel-exp']) {
			$exp = $_POST['add-profile-sel-exp'];
		}
		else {
			echo 'Please select the experience level desired.';
			exit(); 
		}

		// Validate position entry
		if ($_POST['add-profile-ppos']) {
			$ppos = $_POST['add-profile-ppos'];
		}
		else {
			$ppos = ''; 
		}

		// Validate position entry
		if ($_POST['add-profile-spos']) {
			$spos = $_POST['add-profile-spos'];
		}
		else {
			$spos = ''; 
		}

		// Validate comment entry
		if ($_POST['add-profile-comments']) {
			$comm = $_POST['add-profile-comments'];
		}
		else {
			$comm = ''; 
		}

		// If data is valid, edit subrequest
		if ($sex && $reg && $sport && $exp) {
			$profile->createProfile($userID, $sex, $reg, $sport, $exp, $ppos, $spos, $comm);		
		}
		else {									
			echo 'Please try again';
			exit();
		}		

		// Perform following actions if first time user is successful in creating a profile
		if ($_POST['newUser'] = '1') {
			// Create user object & update user information
			$user = new User($userID);
			$user->setDB($db);
			$user->updateLoginBefore(); // *BUG Function displays "no changes made" regardless

			$role = 'p';
			$user->updateUserRole($role);			
						
			$url = BASE_URL . 'player/home.php';
			header("Location: $url");
			exit();		
		}

	}
	else {
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		exit();		
	}
		
	// Delete objects
	unset($user);
	unset($profile);
	unset($player);
			
	// Close the connection:
	$db->close();
	unset($db);

?>