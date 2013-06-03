<?php
    /* upcoming_events_data.php
    * 
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

    // Make the Query:
    $q = "SELECT DATE_FORMAT(e.date, '%W: %M %e, %Y') AS date_string, e.time,
                 e.venue_name, tm.team_name
          FROM members AS mb
              INNER JOIN events AS e USING (id_team)
              INNER JOIN teams AS tm USING (id_team)
          WHERE mb.id_user = {$dbObject->cleanInteger($userID)} && e.date >= CURDATE()
          ORDER BY e.date ASC
          LIMIT 5";

    // Execute the query and store results
    $results = $dbObject->getAll($q);

    // If there are results to show.
    if(count($results) > 0) {
        // Fetch and print all records...
        foreach($results as $result) {
            $json[] = array(
                            'Edate' => $result['date_string'],
                            'Etime' => $result['time'],
                            'Venue' => $result['venue_name'],
                            'TName' => $result['team_name']
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