<?php
    /** single_subreq_data.php
    * This page queries a database, returnnig a single subrequest
    * 
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

    // Get user ID
    $userID = $user->getUserID();

    // If request is coming from the View SubRequest from the Profile page
    if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['idSubReq'])) {

        $subReqID = $_POST['idSubReq'];

        // Make the Query to find subrequest, event, and team info 
        $q = "SELECT tm.team_name, tm.level_of_play, e.venue_name, e.venue_address
              FROM subrequests AS s
                  INNER JOIN teams AS tm USING (id_team)        
                  INNER JOIN events AS e USING (id_event)
              WHERE s.id_subrequest = {$subReqID}
              LIMIT 1";

        // Execute the query & store results
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {
                
                // Translate level of play
                $tmlevel = translateLevelofPlay($result['level_of_play']);

                $json[] = array(
                                'Team Name' => $result['team_name'],
                                'Team Level' => $tmlevel,
                                'Venue Name' => $result['venue_name'],
                                'Venue Addr' => $result['venue_address']
                            );
            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
    }

    // Request is coming from the edit SubRequest on the manager Find Subs page
    if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['subRequestID'])) {

        $subReqID = $_POST['subRequestID'];

        // Make the Query
        $q = "SELECT id_team, id_event, sex_needed, experience_needed, id_region
              FROM subrequests
              WHERE id_subrequest = {$subReqID}
              LIMIT 1";

        // Execute the query:
        $results = $dbObject->getAll($q);    

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {

                $json[] = array(
                                'Team ID' => $result['id_team'],
                                'Event ID' => $result['id_event'],
                                'Sex' => $result['sex_needed'],
                                'Experience' => $result['experience_needed'],
                                'Region' => $result['id_region']
                            );
            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
    }