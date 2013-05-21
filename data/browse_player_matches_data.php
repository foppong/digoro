<?php
    /** browse_player_matches_data.php
    * This page queries a database, returnnig a list
    * of players that match
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

    // Make the Query to find all subrequests associated with user
    $q = "SELECT id_region, id_sport
          FROM profiles
          WHERE id_user = {$userID}";

    // Execute the query & store results
    $results = $dbObject->getAll($q);

    // If there are results to show.
    if(count($results) > 0) {

        // Initialize an array:
        $json = array();

        // Fetch
        foreach($results as $result) {
            // Make the Query
            $q = "SELECT sr.id_subrequest, DATE_FORMAT(e.date, '%a: %b %e, %Y') AS date_string, e.time
                  FROM subrequests AS sr
                      INNER JOIN teams AS tm USING (id_team)
                      INNER JOIN events AS e USING (id_event)
                  WHERE sr.id_region = {$result['id_region']} AND tm.id_sport = {$result['id_sport']}
                  ORDER BY e.date";

            // Execute the query & store results
            $subResults = $dbObject->getAll($q);

            // If there are results to show.
            if(count($subResults) > 0) {
                // Fetch and put results in the JSON array...
                foreach($subResults as $subResult) {

                    $sport = translateSport($result['id_sport']);

                    $json[] = array(
                                    'Sport' => $sport,
                                    'Event Date' => $subResult['date_string'],
                                    'Event Time' => $subResult['time'],
                                    'Take Action' => '<button type="button" id="view-subreq" class="btn btn-mini" value=' . $subResult['id_subrequest'] . '>View</button>'
                                );
                } // End of FOR loop
            }
        } // End of FOR loop

        // Send the JSON data:
        echo json_encode($json);
    }
    else { // No registered users

        $json[] = array(
            '<p class="error">You have no subrequests matches. Create a sport profile to get linked with teams.</p><br />');

        // Send the JSON data:
        echo json_encode($json);
    }