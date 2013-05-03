<?php
    /** subresponse_data.php
    * 
    */

    ob_start();
    session_start();

    require '../includes/config.php';
    require '../includes/php-functions.php';

    // autoloading of classes
    function __autoload($class) {
        require_once('../classes/' . $class . '.php');
    }

    // Validate user
    checkSessionObject();    

    // Assign user object from session variable
    $user = $_SESSION['userObj'];
    $userID = $user->getUserID();

    // Need the database connection:
    require_once MYSQL2;
    $dbObject = MySQLiDbObject::getInstance();

    // Request is coming from profile view to query all subresponses associated with user
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'loadmySRResponses') {

        // Make the Query
        $q = "SELECT subr.id_sr_response, subr.manager_respond,
                     DATE_FORMAT(e.date, '%a: %b %e, %Y') AS date_string,
                     e.time, tm.id_sport
              FROM subreq_responses AS subr
                  INNER JOIN events AS e USING (id_event)
                  INNER JOIN teams AS tm USING (id_team)
              WHERE subr.id_user = {$userID}
              ORDER BY e.date";

        // Execute the query:
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {

                // Translate data from database
                $status = translateSubResStatus($result['manager_respond']);
                $sport = translateSport($result['id_sport']);

                $json[] = array(
                                'Sport' => $sport,
                                'Event Date' => $result['date_string'],
                                'Event Time' => $result['time'],
                                'Status' => $status,
                                'Details' => '<button id="view-subreq" class="btn btn-mini" value=' . $result['id_sr_response'] . '>View</button>');
                            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }        
    }

    // Request is coming from the respond SubResponse form on the manager Find Subs page
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'Manager_single_SubResp_Data') {

        $idSubResponse = $_POST['idSubResp'];

        // Make the Query:
        $q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, subr.comments
              FROM subreq_responses AS subr
                  INNER JOIN users AS u USING (id_user)
              WHERE subr.id_sr_response = {$idSubResponse}
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
                                'Name' => $result['name'],
                                'Comment' => $result['comments']
                            );
            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }    
    }

    // Request is coming from the home page, so user can view the subresponse detail
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'User_single_SubResp_Data') {

        $idSubResponse = $_POST['idSubResp'];

        // Make the Query
        $q = "SELECT tm.team_name, tm.level_of_play, e.venue_name, e.venue_address
              FROM subreq_responses AS subr
                  INNER JOIN events AS e USING (id_event)
                  INNER JOIN teams AS tm USING (id_team)
              WHERE subr.id_sr_response = {$idSubResponse}
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
