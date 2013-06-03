<?php
    /** league_data.php
     * This page queries a database, returnnig a list
     * of leagues
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

    // Assume invalid values:
    $st = FALSE;

    // Assign state variable from add_team.php ajax post
    if(!empty($_POST["state"])) {
        $st = $_POST["state"];
    }

    // Checks if state is selected before querying database.
    if($st) {
        // Make the Query:
        $q = "SELECT id_league, league_name FROM leagues WHERE state = '{$dbObject->realEscapeString($st)}'";

        // Execute the query & store results
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {
                $json[] = array(
                                'LeagueID' => $result['id_league'],
                                'LeagueName' => $result['league_name']
                            );
            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
        else { // No registered users

            $json[] = array(
                '<p class="error">There are no leagues that match your query.</p><br />');

            // Send the JSON data:
            echo json_encode($json);
        }
    }