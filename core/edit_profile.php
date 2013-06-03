<?php
    // This page is for editing a profile
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
        $profile->setProfileID($profileID);

        // Assume invalid values
        $sex = $reg = $sport = $exp = FALSE;

        // Validate sport is selected
        if($_POST['edit-profile-sel-sport']) {
            $sport = $_POST['edit-profile-sel-sport'];
        }
        else {
            echo 'Please select a sport.';
            exit();
        }

        // Validate team sex is selected
        if($_POST['edit-profile-sel-sex']) {
            $sex = $_POST['edit-profile-sel-sex'];
        }
        else {
            echo 'Please select team sex preference.';
            exit();
        }

        // Validate region is selected
        if($_POST['edit-profile-sel-region']) {
            $reg = $_POST['edit-profile-sel-region'];
        }
        else {
            echo 'Please select a region preference.';
            exit();
        }

        // Validate experience is selected
        if($_POST['edit-profile-sel-exp']) {
            $exp = $_POST['edit-profile-sel-exp'];
        }
        else {
            echo 'Please select the experience level desired.';
            exit(); 
        }

        // Validate position entry
        if($_POST['edit-profile-ppos']) {
            $ppos = $_POST['edit-profile-ppos'];
        }
        else {
            $ppos = '';
        }

        // Validate position entry
        if($_POST['edit-profile-spos']) {
            $spos = $_POST['edit-profile-spos'];
        }
        else {
            $spos = ''; 
        }

        // Validate comment entry
        if($_POST['edit-profile-comments']) {
            $comm = $_POST['edit-profile-comments'];
        }
        else {
            $comm = '';
        }

        // If data is valid, edit subrequest
        if($sex && $reg && $sport && $exp) {
            $profile->editProfile($userID, $sex, $reg, $sport, $exp, $ppos, $spos, $comm);
        }
        else {                                    
            echo 'Please try again';
            exit();
        }        
    }
    else {
        // No valid ID, kill the script.
        echo '<p class="error">This page has been accessed in error.</p>';
        exit();        
    }

    // Delete objects
    unset($profile);
    unset($manager);