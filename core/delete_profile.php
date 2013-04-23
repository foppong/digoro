<?php
    // This page is for deleting a profile
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
    if(isset($_SESSION['userObj'])) {
        $manager = $_SESSION['userObj'];
        $userID = $manager->getUserID();
    }
    else {
        redirect_to('index.php');
    }

    // Establish database connection
    require_once MYSQL2;

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