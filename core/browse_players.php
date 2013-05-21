<?php
    // browse_players.php
    // This page allows a logged-in user to search player database

    ob_start();
    session_start();

    require '../includes/config.php';
    include '../includes/php-functions.php';

    // autoloading of classes
    function __autoload($class) {
        require_once('../classes/' . $class . '.php');
    }

    // Assign user object from session variable
    retrieveUserObject();

    // Need the database connection:
    require_once MYSQL2;

    // Assign Database Resource to object

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Assume invalid values:
        $sex = $sport = $exp = $reg = FALSE;

        // Validate sex selected
        if($_POST['search-PL-sel-sex']) {
            $sex = $_POST['search-PL-sel-sex'];
        }
        else {
            echo 'Please enter the players sex.';
            exit();
        }

        // Validate a sport is selected
        if($_POST['search-PL-sel-sport']) {
            $sport = $_POST['search-PL-sel-sport'];
        }
        else {
            echo 'Please select a sport.';
            exit(); 
        }

        // Validate a experience level is selected
        if($_POST['search-PL-sel-exp']) {
            $exp = $_POST['search-PL-sel-exp'];
        }
        else {
            echo 'Please select the minimum experience.';
            exit(); 
        }

        // Validate Team region selected
        if($_POST['search-PL-sel-reg']) {
            $reg = $_POST['search-PL-sel-reg'];
        }
        else {
            echo 'Please enter your teams region.';
            exit();
        }

        // Check if values true before creating team
        if ($userID && $sex && $sport && $exp && $reg) {
            // Create team object for use & create team for database
            $team = new Team();
            $team->createTeam($sp, $userID, $tn, $abtm, $lvl, $reg, $sex, $e);    
        }
        else {                                    
            echo 'Please try again.';
            exit();
        }

    }
    else {
        // Accessed without posting to form
        echo '<p class="error">This page has been accessed in error.</p>';
        exit();        
    }
    // Delete objects
    unset($user);
    unset($team);