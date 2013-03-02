<?php
	// This page is for registering a user
	// This page is accessed through the login page

	require '../includes/config.php';
	include '../includes/iheader.html';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// If session value is present, redirect the user. Also validate the HTTP_USER_AGENT	
	if (isset($_SESSION['agent']) AND ($_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']))) {
	
		$url = BASE_URL . 'manager/home.php';
		header("Location: $url");
		exit();			
	}
	
	// Establish database connection
	require_once MYSQL2;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Confirmation that form has been submitted	

		if ($_POST['add-user-fname']) {
			$fname = $_POST['add-user-fname'];
		}
		else {
			echo 'Please enter a valid first name';
			exit();
		}		
		
		if ($_POST['add-user-lname']) {
			$lname = $_POST['add-user-lname'];
		}
		else {
			echo 'Please enter a valid last name';
			exit();
		}		

		// Validate password
		if (strlen($_POST['add-user-pass1']) > 5)
		{
			if ($_POST['add-user-pass1'] == $_POST['add-user-pass2'])
			{
				$p = $_POST['add-user-pass1'];
			}
			else 
			{
				echo 'Your password did not match the confirmed password';
			}
		}
		else 
		{
			echo 'Please enter a valid password';
		}		

		// Validate email
		if (filter_var($_POST['add-user-email'], FILTER_VALIDATE_EMAIL)) {
			$e = $_POST['add-user-email'];
		}
		else {
			echo 'Please enter valid email address';
		}	

		if ($_POST['edit-user-sel-sex']) {
			$sex = $_POST['edit-user-sel-sex'];
		}
		else {
			echo 'Please select your sex';
			exit();
		}	

		if ($_POST['DateOfBirth_Day']) {
			$bdday = $_POST['DateOfBirth_Day'];
		}
		else {
			echo 'Please enter your birthday day';
			exit();
		}

		if ($_POST['DateOfBirth_Month']) {
			$bdmnth = $_POST['DateOfBirth_Month'];
		}
		else {
			echo 'Please enter your birthday month';
			exit();
		}

		if ($_POST['DateOfBirth_Year']) {
			$bdyr = $_POST['DateOfBirth_Year'];
		}
		else {
			echo 'Please enter your birthday year';
			exit();
		}

		$bdarray = array($bdyr, $bdmnth, $bdday);

		// Validate if date entered is actually a date
		if (checkdate($bdmnth, $bdday, $bdyr))
		{
			$bdstring = implode("-", $bdarray);
			$bd = new DateTime($bdstring);
			$bdfrmat = $bd->format('Y-m-d');
		}
		else 
		{
			echo 'Please enter a valid birthdate';
		}		
	
		// Check if user entered information is valid before registering user
		if ($fname && $lname && $e && $p && $sex && $bdfrmat) {
			$user = new UserAuth();
			$user->setDB($db);				
			$user->createUser($fname, $lname, $e, $p, $zip, $sex, $bdfrmat);
		}
		else {	// Errors in the user entered information
			echo 'Please try again';
			exit();
		}
	}

	// Delete objects
	unset($user);
			
	// Close the connection:
	$db->close();
	unset($db);

?>