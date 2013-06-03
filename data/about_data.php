<?php
    /** about_data.php
    * This page queries a database, returnnig
    * information about the team
    */

    require_once('../includes/bootstrap.php');
    require_once('../includes/php-functions.php');    

    // Assign user object from session variable
    if(isset($_SESSION['userObj'])) {
        $user = $_SESSION['userObj'];
    }
    else {
        redirect_to('index.php');
    }

    // Retrieve current team ID from session variable
    $tm = $_SESSION['ctmID'];

    // Define the Query 
    $q = "SELECT about, team_name FROM teams WHERE id_team = {$tm} LIMIT 1";

    // Execute the query & store result
    $results = $dbObject->getAll($q);

    // If there are results to show.
    if(count($results) > 0) {
        // Initialize an array:
        $json = array();

        foreach($results as $result) {
            $json[] = array(
                        'TeamAbout' => stripslashes($result['about']), // If I get PHP >5.3 I believe I can use optional parameter in json_encode
                        'TeamName' => stripslashes($result['team_name']),
                        'Edit' => '<button type="button" id="editTeam" value=' . $tm . '>Edit</button>'
                      );
        }

        // Send the JSON data:
        echo json_encode($json);    
    }