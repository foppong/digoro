<?php
    /** team_data.php
    * This page queries a database, returnnig a list
    * of teams
    */

    ob_start();
    session_start();

    require '../includes/config.php';
    include '../includes/php-functions.php';

    // autoloading of classes
    function __autoload($class) {
        require_once('../classes/' . $class . '.php');
    }

    // Assign user object from session variable
    if(isset($_SESSION['userObj'])) {
        $user = $_SESSION['userObj'];
    }
    else {
        redirect_to('index.php');
    }

    // Need the database connection:    
    require_once MYSQL2;
    $dbObject = MySQLiDbObject::getInstance();

    // Get user ID
    $userID = $user->getUserID();

    // Make the Query to find all teams associated with user via a union of the members and teams table:
    $q = "SELECT id_team, team_name FROM teams WHERE id_user = {$userID}";

    // Execute the query and store results
    $results = $dbObject->getAll($q);        

    // If there are results to show.
    if(count($results) > 0) {
        // Initialize an array:
        $json = array();

        // Fetch and put results in the JSON array...
        foreach($results as $result) {
            $json[] = array(
                            'TeamID' => $result['id_team'],
                            'TeamName' => stripslashes($result['team_name'])
                        );
        } // End of FOR loop

        // Send the JSON data:
        echo json_encode($json);
    }
    else { // No registered users

        $json[] = array(
            '<p class="error">You have no teams associated with your account.</p><br />');

        // Send the JSON data:
        echo json_encode($json);
    }