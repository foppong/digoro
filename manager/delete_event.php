<?php
    // This page is for deleting a event record
    // This page is accessed through view_roster.php

    require_once('../includes/bootstrap.php');
    require_once('../includes/php-functions.php');

    // Validate user
    checkSessionObject();    

    // Check user role
    checkRole('m');

    // Assign user object from session variable
    $user = $_SESSION['userObj'];
    $userID = $user->getUserID();

    if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['z'])) { // Confirmation that form has been submitted
        // Assign variable from FORM submission (hidden id field)    
        $eventid = $_POST['z'];

        // Create event object for use & pull latest data from database & initially set attributes
        $event = new Event();
        $event->setEventID($eventid);

        // Check if user is authroized to make edit
        if(!$event->isManager($userID, $eventid)) {
            echo 'You have to be the manager to delete a event.';
            exit();
        }

        $event->deleteEvent($eventid);
    }
    else {
        // No valid ID, kill the script.
        echo '<p class="error">This page has been accessed in error.</p>';
        exit();
    }

    // Delete objects
    unset($event);
    unset($user);