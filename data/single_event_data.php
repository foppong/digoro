<?php
    /** single_event_data.php
    * This page queries a database, returnnig a single event data
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

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $eventID = $_POST['eventID'];

        // Make the query to retreive event information from events table in database:        
        $q = "SELECT DATE_FORMAT(date, '%m/%d/%Y') AS date_string, time, opponent, venue_name,
                     venue_address, result, note, type
              FROM events
              WHERE id_event = {$eventID}
              LIMIT 1";

        // Execute the query & store results:
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {
                $json[] = array(
                                'Event Type' => $result['type'],
                                'Event Date' => $result['date_string'],
                                'Event Time' => $result['time'],
                                'Event Oppo' => $result['opponent'],
                                'Event Ven Name' => $result['venue_name'],
                                'Event Ven Addr' => $result['venue_address'],
                                'Event Note' => $result['note'],
                                'Event Res' => $result['result'],
                                'Event Text' => translateEventType($result['type'])
                            );
            }    // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
    }