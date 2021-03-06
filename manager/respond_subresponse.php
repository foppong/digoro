<?php
    // respond_subresponse.php
    // This page allows a manager to respond a subresponse

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

    // Need the database connection:
    require_once MYSQL2;

    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['SR-response'] == 'confirm' && !empty($_POST['z'])) {

        $subResponseID = $_POST['z'];
        $response = $_POST['SR-response'];

        // Validate comment
        if($_POST['respond-SRR-comment']) {
            $comments = $_POST['respond-SRR-comment'];
        }
        else {
            $comments = '';
        }

        $subResponse = new SubResponse();
        $subResponse->setSRRespID($subResponseID);
        $subResponse->confirmSubReqResp($subResponseID, $comments);
    }

    else if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['SR-response'] == 'decline' && !empty($_POST['z'])) {

        $subResponseID = $_POST['z'];
        $response = $_POST['SR-response'];

        // Validate comment
        if($_POST['respond-SRR-comment']) {
            $comments = $_POST['respond-SRR-comment'];
        }
        else {
            $comments = '';
        }

        $subResponse = new SubResponse();
        $subResponse->setSubReqID($subResponseID);
        $subResponse->declineSubReqResp($subResponseID, $comments);
    }
    else {
        // Accsessed without posting to form
        echo '<p class="error">This page has been accessed in error.</p>';
        exit();
    }

    // Delete objects
    unset($team);
    unset($user);