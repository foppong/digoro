<?php
    // respond_subresponse.php
    // This page allows a manager to respond a subresponse

    require_once('../includes/bootstrap.php');
    require_once('../includes/php-functions.php');

    // Validate user
    checkSessionObject();

    // Check user role
    checkRole('m');

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