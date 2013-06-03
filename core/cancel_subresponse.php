<?php
    // create_subresponse.php
    // This page allows a user to respond to a subrequest

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

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $subResponseID = $_POST['z'];

        // Validate comment enetered
        if ($_POST['SR-response-comment']) {
            $com = $_POST['SR-response-comment'];
        }
        else {
            echo 'Please provide a reason for cancelling';
            exit();
        }

        // If data is valid, cancel subrequest response
        if($subResponseID) {
            $subResponse = new SubResponse();
            $subResponse->setSRRespID($subResponseID);
            $subResponse->cancelSubResponse($subResponseID, $com);    
        }
        else {                                    
            echo 'Please try again';
            exit();
        }
    }
    else {
        // Accsessed without posting to form
        echo '<p class="error">This page has been accessed in error.</p>';
        exit();        
    }

    // Delete objects
    unset($team);
    unset($manager);