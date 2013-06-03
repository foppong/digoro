<?php
    // add_player.php
    // This page allows a logged-in user to add a player to a team

    require_once('../includes/bootstrap.php');
    require_once('../includes/php-functions.php');

    // Validate user
    checkSessionObject();    

    // Check user role
    checkRole('m');

    // Assign user object from session variable
    $user = $_SESSION['userObj'];
    $userID = $user->getUserID();

    // Retrieve current team ID in session
    $ctmID = $_SESSION['ctmID'];

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Create team object for use & pull latest data from database & initially set attributes - used to add member
        $team = new Team();
        $team->setTeamID($ctmID);
        //$team->pullTeamData();

        // Check if user is authroized to make edit
        if(!$team->isManager($userID, $ctmID)) {
            echo 'You have to be the manager to add a member.';
            exit();
        }

        // Assume invalid values:
        $fn = $ln = $sex = $e = $ppos = FALSE;

        // Validate firstname
        if(preg_match('/^[A-Z \'.-]{2,20}$/i', $_POST['add-member-fname'])) {
            $fn = $_POST['add-member-fname'];
        }
        else {
            echo "Please enter a valid first name";
            exit();
        }

        // Validate lastname
        if(preg_match('/^[A-Z \'.-]{2,40}$/i', $_POST['add-member-lname'])) {
            $ln = $_POST['add-member-lname'];
        }
        else {
            echo "Please enter a valid last name";
            exit();
        }

        // Validate sex is selected
        if($_POST['add-member-sel-sex']) {
            $sex = $_POST['add-member-sel-sex'];
        }
        else {
            echo 'Please select a sex.';
            exit();
        }

        // Validate email
        if(filter_var($_POST['add-member-email'], FILTER_VALIDATE_EMAIL)) {
            $e = $_POST['add-member-email'];
        }
        else {
            echo "Please enter a valid email";
            exit();
        }

        // Validate primary position is entered
        if($_POST['add-member-ppos']) {
            $ppos = $_POST['add-member-ppos'];
        }
        else {
            echo "Please enter a primary position";
            exit();
        }

        // Validate secondary position
        if($_POST['add-member-spos']) {
            $spos = $_POST['add-member-spos'];
        }
        else {
            $spos = '';
        }

        // Validate jersey number input
        if(filter_var($_POST['add-member-jernum'], FILTER_VALIDATE_INT)) {
            $jnumb = $_POST['add-member-jernum'];
        }
        else {
            $jnumb = 0;
        }

        // Validate invite selection
        if ($_POST['add-member-invite']) {
            $invite = $_POST['add-member-invite'];
        }
        else {
            $invite = 0;
        }

        // Checks if name, email, and league are valid before proceeding.
        if($ctmID && $fn && $ln && $sex && $e && $ppos) {
            $member = new Member();
            $member->createMember($e, $ctmID, $fn, $ln, $sex, $ppos, $spos, $jnumb, $invite);
        }
        else {
            echo "Please try again";
            exit();
        }
    }
    else {
        // Accsessed without posting to form
        echo '<p class="error">This page has been accessed in error.</p>';
        exit();
    }

    // Delete objects
    unset($member);
    unset($user);