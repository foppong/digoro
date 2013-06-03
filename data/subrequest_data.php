<?php
    /** subrequest_data.php
    * This page queries a database, returnnig a list
    * of subrequest
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

    // Pull all subrequest associated with user's teams
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullOpenSRData') {
        // Make the Query to find all subrequests associated with user
        $q = "SELECT s.id_subrequest, s.id_team, s.sex_needed,
                     DATE_FORMAT(e.date, '%a: %b %e, %Y') AS date_string,
                     tm.team_name, e.time
              FROM subrequests AS s
                  INNER JOIN teams AS tm USING (id_team)        
                    INNER JOIN events AS e USING (id_event)
              WHERE s.id_user = {$dbObject->cleanInteger($userID)}
              ORDER BY e.date ASC";

        // Execute the query & store results
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {
                $json[] = array(
                                'Team' => $result['team_name'],
                                'Sex Needed' => $result['sex_needed'],
                                'Event Date' => $result['date_string'],
                                'Event Time' => $result['time'],
                                'Edit' => '<button type="button" id="edit-subreq" class="btn btn-mini" value=' . $result['id_subrequest'] . '>Edit</button>',
                                'Delete' => '<button id="delete-subreq" class="btn btn-mini" value=' . $result['id_subrequest'] . '>Delete</button>'
                            );
            }    // End of WHILE loop

            // Send the JSON data:
            echo json_encode($json);
        }
        else { // No registered users
    
            $json[] = array(
                '<p class="error">You have no subrequests open. Click the create subrequest to create one.</p><br />');

            // Send the JSON data:
            echo json_encode($json);
        }
    }

    // Pull all sub request responses associated with users's teams
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullSRResponsesData') {

        // Make the query
        $q = "SELECT sr.id_sr_response, sr.id_subrequest, sr.id_user, sr.manager_respond,
                     subr.id_team, DATE_FORMAT(e.date, '%a: %b %e, %Y') AS date_string,
                     e.time, tm.team_name
              FROM subreq_responses AS sr
                  INNER JOIN events AS e USING (id_event)
                  INNER JOIN teams AS tm USING (id_team)
                  INNER JOIN subrequests AS subr USING (id_subrequest)
              WHERE subr.id_user= {$dbObject->cleanInteger($userID)}";

        // Execute the query & store results:
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {            
                // Make the query
                $q = "SELECT sex, CONCAT(first_name, ' ', last_name) AS name
                      FROM users
                      WHERE id_user = {$dbObject->cleanInteger($result['id_user'])}";

                // Execute the query & store results
                $subResults = $dbObject->getAll($q);

                // If there are results to show.
                if(count($subResults) > 0) {
                    foreach($subResults as $subResult) {
                        $membersex = translateSex($subResult['sex']);
                        $membername = $subResult['name'];
                    }
                }    

                // Translate status from database
                $status = translateSubResStatus($result['manager_respond']);

                $json[] = array(
                                'Name' => $membername,
                                'Sex' => $membersex,
                                'Team' => $result['team_name'],
                                'Event Date' => $result['date_string'],
                                'Event Time' => $result['time'],
                                'Status' => $status,
                                'Take Action' => '<button id="respond-subres" class="btn btn-mini" value=' . $result['id_sr_response'] . '>Respond</button>'
                            );

            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
    }