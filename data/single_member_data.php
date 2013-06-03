<?php
    /** single_member_data.php
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

        $memberID = $_POST['memberID'];

        // Make the query to retreive user information:        
        $q = "SELECT u.first_name, u.last_name, u.sex, p.primary_position, 
                     p.secondary_position, p.jersey_number
              FROM members AS p
                  INNER JOIN users AS u USING (id_user)
              WHERE p.id_member = {$dbObject->cleanInteger($memberID)}
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
                                'First Name' => $result['first_name'],
                                'Last Name' => $result['last_name'],
                                'Member Sex' => $result['sex'],
                                'Primary Position' => $result['primary_position'],
                                'Secondary Position' => $result['secondary_position'],
                                'Jersey Num' => $result['jersey_number']
                            );
            }    // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
    }