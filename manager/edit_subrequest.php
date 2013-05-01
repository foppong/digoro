<?php
    // This page is for editing a subrequest
    // This page is accessed through find_subs_view.php

    ob_start();
    session_start();

    require '../includes/config.php';
    include '../includes/php-functions.php';

    // autoloading of classes
    function __autoload($class) {
        require_once('../classes/' . $class . '.php');
    }

    // Validate user
    checkSessionObject();    

    // Check user role
    checkRole('m');

    // Assign user object from session variable
    $user = $_SESSION['userObj'];
    $userID = $user->getUserID();

    // Establish database connection
    require_once MYSQL2;

    if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['z'])) { // Confirmation that form has been submitted
        $subReqid = $_POST['z'];

        // Create object for use & pull latest data from database & initially set attributes
        $subReq = new SubRequest();
        $subReq->setSubReqID($subReqid);

        // Validate team is selected
        if($_POST['edit-SR-sel-teams']) {
            $tmID = $_POST['edit-SR-sel-teams'];
        }
        else {
            echo 'Please select a team.';
            exit();
        }

        // Validate game is selected
        if($_POST['edit-SR-sel-events']) {
            $evntID = $_POST['edit-SR-sel-events'];
        }
        else {
            echo 'Please select an event.';
            exit();
        }

        // Validate sex is selected
        if($_POST['edit-SR-sel-sex']) {
            $sex = $_POST['edit-SR-sel-sex'];
        }
        else {
            echo 'Please select a sex.';
            exit();
        }

        // Validate experience is selected
        if($_POST['edit-SR-sel-exp']) {
            $exp = $_POST['edit-SR-sel-exp'];
        }
        else {
            echo 'Please select the experience level desired.';
            exit();
        }

        // Validate region is selected
        if($_POST['edit-SR-sel-reg']) {
            $reg = $_POST['edit-SR-sel-reg'];
        }
        else {
            echo 'Please select a region.';
            exit();
        }

        // If data is valid, edit subrequest
        if($tmID && $evntID && $sex && $exp && $reg) {
            $subReq->editSubReq($userID, $subReqid, $tmID, $evntID, $sex, $exp, $reg);
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
    unset($subReq);
    unset($user);