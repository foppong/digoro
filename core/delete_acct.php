<?php
    // delete_acct.php
    // This page deletes a user's account

    require '../includes/config.php';
    $page_title = 'digoro : Delete Account';
    include '../includes/iheader.html';
    include '../includes/php-functions.php';

    // autoloading of classes
    function __autoload($class) {
        require_once('../classes/' . $class . '.php');
    }

    // Assign user object from session variable
    if(isset($_SESSION['userObj'])) {
        $user = $_SESSION['userObj'];
        $userID = $user->getUserID();
    }
    else {
        redirect_to('index.php');
    }

    // Need the database connection:
    require_once MYSQL2;


    // Confirmation that form has been submitted:
    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        if($_POST['sure'] == 'Yes') { // If form submitted is yes, delete the record
            $user->deleteUser($userID);
            redirect_to('index.php');
        }
        else { // No confirmation of deletion.
            echo '<p>This account has NOT been deleted.</p>';
        }
    }
    else {
        //Confirmation message:
        echo '<h3>Are you sure you want to delete your account? We will miss you!</h3>';

        // Create the form:
        echo '<form action ="delete_acct.php" method="post" id="DelAcctForm">
            <input type="radio" name="sure" value="Yes" />Yes<br />
            <input type="radio" name="sure" value="No" checked="checked" />No<br />
            <input type="submit" name="submit" value="Delete" />
            </form>';
    }

    // Delete objects
    unset($user);

    include '../includes/footer.html';