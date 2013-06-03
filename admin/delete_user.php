<?php
    // This page is for deleting a user record
    // This page is accessed through view_users.php

    require_once('../includes/bootstrap.php');
    $page_title = 'digoro : Delete User';
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

    echo '<h1>Delete a User</h1>';

    // Check for a valid user ID, through GET or POST:
    if((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
        // Point A in Code Flow
        // Assign variable from view_users.php using GET method
        $id = $_GET['id'];
    }
    else if((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
        // Point C in Code Flow
        // Assign variable from delete_user.php FORM submission (hidden id field)
        $id = $_POST['id'];
    }
    else {
        // No valid ID, kill the script.
        echo '<p class="error">This page has been accessed in error.</p>';
        include '../includes/footer.html';
        exit();
    }

    // Confirmation that form has been submitted:
    if($_SERVER['REQUEST_METHOD'] == 'POST') { // Point D in Code Flow

        if($_POST['sure'] == 'Yes') { // If form submitted is yes, delete the record

            // Make the query
            $q = "DELETE FROM users WHERE id_user = {$id} LIMIT 1";

            // Execute the query:
            $dbObject->query($q);

            // If the query ran ok.
            if($dbObject->getNumRowsAffected() == 1) { // Print a message
                echo '<p>The user has been deleted successfully.</p>';
            }
            else { // If the query did not run ok.
                echo '<p class="error">The user could not be deleted due to a system errror.</p>';
                exit();
            }
        }
        else { // No confirmation of deletion.
            echo '<p>The user has NOT been deleted.</p>';
        }
    }
    else { // Point B in Code Flow. Show the form

        // Make the Query to retrieve the user's information:
        $q = "SELECT CONCAT(last_name, ', ', first_name) AS name
              FROM users
              WHERE id_user = {$id}
              LIMIT 1";

        // Execute the query & store result:
        $nameOB = $dbObject->getOne($q);

        // Valid user ID, show the form.
        if($nameOB !== false) {
            //Display the record being deleted:
            echo "<h3>Name: $nameOB</h3>Are you sure you want to delete this user?";

            // Create the form:
            echo '<form action ="delete_user.php" method="post" id="DelUserForm">
                <input type="hidden" name="id" value="' . $id . '" />
                <input type="radio" name="sure" value="Yes" />Yes<br />
                <input type="radio" name="sure" value="No" checked="checked" />No<br />
                <input type="submit" name="submit" value="Delete" />
                </form>';
        }
        else { //Not a valid user ID.
            echo '<p class="error">This page has been accessed in error.</p>';
            exit();
        }
    } // End of the main submission conditional.

    require_once('../includes/footer.html');