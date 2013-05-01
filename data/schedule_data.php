<?php
    /* schedule_data.php
    * For managers: This script retrieves all the records from the schedule table.
    * 
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

    // Retrieve current team ID from session variable
    $tm = $_SESSION['ctmID'];

    // Make the Query:
    $q = "SELECT id_event, DATE_FORMAT(date, '%a: %b %e, %Y') AS date_string, time, 
                 opponent, venue_name, result, type
          FROM events
          WHERE id_team = {$tm}
          ORDER BY date ASC";

    // Execute the query & store results
    $results = $dbObject->getAll($q);

    // If there are results to show.
    if(count($results) > 0) {
        // Fetch and print all records...
        foreach($results as $result) {

            // Translate event type data from database
            $type = translateEventType($result['type']);

            $json[] = array(
                            'Type' => $type,
                            'Date' => $result['date_string'],
                            'Time' => $result['time'],
                            'Opponent' => stripslashes($result['opponent']),
                            'Venue' => stripslashes($result['opponent']),
                            'Result' => $result['result'],
                            'Details' => '<button type="button" class="view_event btn btn-mini" value=' . $result['id_event'] . '>View</button>',
                            'Edit' => '<button type="button" class="edit_event btn btn-mini" value=' . $result['id_event'] . '>Edit</button>',
                            'Delete' => '<button type="button" class="delete_event btn btn-mini" value=' . $result['id_event'] . '>Delete</button>'
                        );
        } // End of FOR loop

        // Send the JSON data:
        echo json_encode($json);
    }
    else { // No events or events scheduled

        $json[] = array('<p class="error">You have no events scheduled. Click the add event button to add a event.');

        // Send the JSON data:
        echo json_encode($json);
    }
