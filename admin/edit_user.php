<?php
	// This page is for editing a user record
	// This page is accessed through view_users.php

	require_once('../includes/bootstrap.php');
	$page_title = 'digoro : Edit User';
	require_once('../includes/header.html');

	// Site access level -> Administrator
	$lvl = 'A';

	// Assign user object from session variable
	if(isset($_SESSION['userObj'])) {
		$user = $_SESSION['userObj'];
	}
	else {
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();
	}

	// Authorized Login Check
	if(!$user->valid($lvl)) {
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();
	}

	echo '<h1>Edit a User</h1>';

	// Check for a valid user ID, through GET or POST:
	if((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
		// Point A in Code Flow
		// Assign variable from view_users.php using GET method
		$id = $_GET['id'];
	}
	else if((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
		// Point C in Code Flow
		// Assign variable from edit_user.php FORM submission (hidden id field)
		$id = $_POST['id'];
	}
	else {
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		require_once('../includes/footer.html');
		exit();
	}

	// Confirmation that form has been submitted:	
	if($_SERVER['REQUEST_METHOD'] == 'POST') { // Point D in Code Flow

		// Trim all the incoming data:
		$trimmed = array_map('trim', $_POST);

		// Assume invalid values:
		$fn = $ln = $e = $zp = $bd = $gd = FALSE;

		// Validate firstname
		if(preg_match('/^[A-Z \'.-]{2,20}$/i', $trimmed['first_name'])) {
			$fn = $trimmed['first_name'];
		}
		else {
			echo '<p class="error">Please enter valid first name.</p>';
		}

		// Validate lastname
		if(preg_match('/^[A-Z \'.-]{2,40}$/i', $trimmed['last_name'])) {
			$ln = $trimmed['last_name'];
		}
		else {
			echo '<p class="error">Please enter valid last name.</p>';
		}

		// Validate email
		if(filter_var($trimmed['email'], FILTER_VALIDATE_EMAIL)) {
			$e = $trimmed['email'];
		}
		else {
			echo '<p class="error">Please enter valid email address.</p>';
		}

		// Validate zipcode
		if(filter_var($_POST['zipcode'], FILTER_VALIDATE_INT)) {
			$zp = $_POST['zipcode'];
		}
		else {
			echo '<p class="error">Please enter a valid zip code.</p>';
		}

		// Validate a gender is selected
		if($_POST['sex']) {
			$gd = $_POST['sex'];
		}
		else {
			echo '<p class="error">Please select your gender.</p>';
		}

		// Check if user entered information is valid before continuing to edit user
		if($fn && $ln && $e && $zp && $gd) {
			// Make the query to make sure User's new email is available
			$q = "SELECT id_user, email
                  FROM users
                  WHERE email = '{$dbObject->realEscapeString($e)}'
                    AND id_user != {$dbObject->cleanInteger($id)}
                  LIMIT 1";

			// Execute the query & store result
			$result = $dbObject->getRow($q);

			// User login available, i.e. query found nothing
			if($result === false) {
				// Update the user's info in the database
				$q = "UPDATE users
                      SET email = '{$dbObject->realEscapeString($e)}',
                          first_name = '{$dbObject->realEscapeString($fn)}',
                          last_name = '{$dbObject->realEscapeString($ln)}',
                          zipcode = '{$dbObject->realEscapeString($zp)}',
                          gender = '{$dbObject->realEscapeString($gd)}'
                      WHERE id_user = {$dbObject->cleanInteger($id)}
                      LIMIT 1";

				// Execute the query:
				$dbObject->query($q);

				if($dbObject->getNumRowsAffected() == 1) { // And update to the database was made
					echo '<p>The users account has been edited.</p>';
				}
				else { // Either did not run ok or no updates were made
					echo '<p>No changes were made.</p>';
				}
			}
			else { // Email is already registered
				echo '<p class="error">The email address has already been registered.</p>';
			}
		}
		else { // Errors in the user entered information
			echo '<p class="error">Please try again.</p>';
		}
	}	// End of submit conditional.

	// Point B in Code Flow
	// Always show the form...
	
	// Make the query to retreive user information:
	$q = "SELECT email, first_name, last_name, zipcode, gender
          FROM users
          WHERE id_user = {$dbObject->cleanInteger($id)}
          LIMIT 1";		

	// Execute the query & store results
    $result = $dbObject->getRow($q);

	// Valid user ID, show the form.
	if($result !== false) {

        // Set up for sticky gender select in form
        if($result['gender'] == "F") {
            $Fsel = ' selected="selected"';
            $Msel = NULL;
        }
        else {
            $Fsel = NULL;
            $Msel = ' selected="selected"';	
        }

        // Create the form:
        echo '<form action ="edit_user.php" method="post" id="EditUserForm">
            <input type="hidden" name="id" value="' . $dbObject->cleanInteger($id) . '" />

            <p id="first_nameP"><b>First Name:</b><input type="text" name="first_name" id="first_name" 
            size="20" maxlength="20" value="' . $result['first_name'] . '" /></p>

            <p id="last_nameP"><b>Last Name:</b><input type="text" name="last_name" id="last_name" 
            size="20" maxlength="40" value="' . $result['last_name'] . '" /></p>

            <p id="emailP"><b>Email Address:</b><input type="text" name="email" id="email" 
            size="30" maxlength="60" value="' . $result['email'] . '" /></p>

            <p id="zipP"><b>Zip Code:</b><input type="text" name="zipcode" id="zipcode" size="5" maxlength="5" 
            value="' . $result['zipcode'] . '" /></p>

            <p id="sexP"><b>Sex:</b>
            <select name="sex" id="sex">
            <option value=""> - Select Sex - </option>
            <option' . $Fsel . ' value="F">Female</option>
            <option' . $Msel . ' value="M">Male</option>
            </select></p>

            <input type="submit" name="submit" value="Save" />
            </form><br />';
        echo '<a href="../core/change_password.php">Change Password</a><br />';
	}
	else { //Not a valid user ID, kill the script
		echo '<p class="error">This page has been accessed in error.</p>';
		require_once('../includes/footer.html');
		exit();
	}

	require_once('../includes/footer.html');