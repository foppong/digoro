<?php
    // add_team.php
    // This page allows a logged-in user to add a team

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
        // IN FUTURE CAN ADD LOGIC HERE FOR PAYING CUSTOMERS TO ADD TEAM - similar to checks 
        // i have now for managers to edit and add players/games

        // Assume invalid values:
        $sp = $tn = $sex = $reg = $lvl = FALSE;

        // Validate a sport is selected
        if($_POST['add-team-sel-sport']) {
            $sp = $_POST['add-team-sel-sport'];
        }
        else {
            echo 'Please select a sport.';
            exit(); 
        }

        // Validate Team name entered
        if($_POST["add-team-name"]) {
            $tn = $_POST["add-team-name"];
        }
        else {
            echo 'Please enter a Team name.';
            exit();
        }

        // Validate Team sex selected
        if($_POST['add-team-sel-sex']) {
            $sex = $_POST['add-team-sel-sex'];
        }
        else {
            echo 'Please enter your teams sex.';
            exit();
        }

        // Validate Team region selected
        if($_POST['add-team-sel-region']) {
            $reg = $_POST['add-team-sel-region'];
        }
        else {
            echo 'Please enter your teams region.';
            exit();
        }

        // Validate Team level of play selected
        if($_POST['add-team-sel-level-play']) {
            $lvl = $_POST['add-team-sel-level-play'];
        }
        else {
            echo 'Please enter your teams level of play.';
            exit();
        }

        // Validate email address
        if(filter_var($_POST['add-team-email'], FILTER_VALIDATE_EMAIL)) {
            $e = $_POST['add-team-email'];
        }
        else {
            $e = '';
        }

        // Validate about team information
        if($_POST['add-team-abouttm']) {
            $abtm = trim($_POST['add-team-abouttm']);
        }
        else {
            $abtm = '';
        }

        // Check if values true before creating team
        if ($userID && $sp && $tn && $sex && $reg && $lvl) {
            // Create team object for use & create team for database
            $team = new Team();
            $team->createTeam($sp, $userID, $tn, $abtm, $lvl, $reg, $sex, $e);    
        }
        else {                                    
            echo 'Please try again.';
            exit();
        }

        // Perform following actions if first time user is successful in creating team
        if($_POST['newUser'] = '1') {
            // Create user object & update user information
            $user = new User($userID);
            $user->updateLoginBefore(); // *BUG Function displays "no changes made" regardless

            $role = 'm';
            $user->updateUserRole($role);

            $url = BASE_URL . 'manager/home.php';
            header("Location: $url");
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