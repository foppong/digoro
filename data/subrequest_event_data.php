<?php
    /* subrequest_game_data.php
    * This script retrieves all the records from the schedule table for team.
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

    // Assume invalid values:
    $tm = FALSE;

    // Assign state variable from find_sub_view ajax call
    if(!empty($_POST["teamID"])) {
        $tm = $_POST["teamID"];
    }

    // Checks if team is selected before querying database.
    if($tm) {
        // Make the Query:
        $q = "SELECT id_event, DATE_FORMAT(date, '%a: %b %e, %Y') AS date_string
              FROM events
              WHERE id_team = {$tm}
              ORDER BY date ASC";

        // Execute the query & store results:
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {        
            // Fetch and print all records...
            foreach($results as $result) {
                $json[] = array(
                                'EventID' => $result['id_event'],
                                'DateInfo' => $result['date_string']
                            );
            } // End of FOR loop
 
            // Send the JSON data:
            echo json_encode($json);
        }
        else { // No events or events scheduled

            $json[] = array('<p class="error">You have no events scheduled.');

            // Send the JSON data:
            echo json_encode($json);
        }
    }