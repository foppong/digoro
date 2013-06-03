<?php
    // This page is for deleting a profile
    // This page is accessed through profile.php

    require_once('../includes/bootstrap.php');
    require_once('../includes/php-functions.php');

    // Assign user object from session variable
    if(isset($_SESSION['userObj'])) {
        $manager = $_SESSION['userObj'];
        $userID = $manager->getUserID();
    }
    else {
        redirect_to('index.php');
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['z'])) { // Confirmation that form has been submitted
        $profileID = $_POST['z'];

        // Create object
        $profile = new Profile();

        $profile->deleteProfile($profileID);
    }
    else {
        // No valid ID, kill the script.
        echo '<p class="error">This page has been accessed in error.</p>';
        exit();        
    }

    // Delete objects
    unset($profile);
    unset($manager);