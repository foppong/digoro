<?php
    // activate.php
    // This page activates the user's account

    require '../includes/config.php';
    $page_title = 'Activate Your Account';
    include '../includes/iheader.html';

    // If $x and $y don't exist or aren't of the proper format, redirect the user
    if(isset($_GET['x'], $_GET['y']) && filter_var($_GET['x'], FILTER_VALIDATE_EMAIL) && (strlen($_GET['y']) == 32)) {
        // Update the database
        require MYSQL2;
        $dbObject = MySQLiDbObject::getInstance();

        // Update database with the activation code.
        $q = "UPDATE users
              SET activation = NULL
              WHERE email = '{$dbObject->$dbObject->realEscapeString($_GET['x'])}'
                AND activation = '{$dbObject->realEscapeString($_GET['y'])}'
              LIMIT 1";

        // Execute the query:
        $dbObject->query($q);

        // Print a customized message:
        if($dbObject->getNumRowsAffected() == 1) { // It ran OK.
            echo '<h3>Your account is now active. You may now log in by clicking <a href="../fatbar.php">here</a></h3>';
        }
        else {
            echo '<p class="error">Your account could not be activated. Please 
                re-check the link or contact the system administrator. </p>';
        }
    }
    else {
        // Redirect
        $url = BASE_URL . 'fatbar.php';
        ob_end_clean();
        header("Location: $url");
        exit();
    }
    include '../includes/ifooter.html';