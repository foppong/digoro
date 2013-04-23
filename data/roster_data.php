<?php
    /** roster_data.php
    * This page queries a database, returnnig a list
    * of players on a roster
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

    // Pulls data of all members on a team
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullRosterData') {
        // Make the Query:
        $q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, u.sex,
                     u.email, p.id_member,
                     CONCAT(p.primary_position, ', ',p.secondary_position) AS pos,
                     p.jersey_number
              FROM members AS p
                  INNER JOIN users AS u USING (id_user)
              WHERE p.id_team = {$tm}";

        // Execute the query & store results:
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();    

            // Fetch and put results in the JSON array...
            foreach($results as $result) {
                // Translate sex data from database
                $sex = translateSex($result['sex']);

                $json[] = array(
                                'Name' => $result['name'],
                                'Email' => $result['email'],
                                'Sex' => $sex,
                                'Position' => $result['pos'],
                                'Jersey' => $result['jersey_number'],
                                'Edit' => '<button type="button" class="edit_member btn btn-mini" value=' . $result['id_member'] . '>Edit</button>',
                                'Delete' => '<button type="button" class="delete_member btn btn-mini" value=' . $result['id_member'] . '>Delete</button>'
                            );
            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);        
        }
        else { // No registered users
            $json[] = array(
                'You have no members on your roster. Click add player to add a member.');

            // Send the JSON data:
            echo json_encode($json);
        }        
    }

    // Pulls data of all members on a team for the transfer list
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullTransferListData') {
        // Make the Query:
        $q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, u.id_user, u.registration_date
              FROM members AS m
                INNER JOIN users AS u USING (id_user)
              WHERE m.id_team = {$tm}";

        // Execute the query & store results:
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {
                if($result['registration_date'] != '0000-00-00 00:00:00') { // If member is a registered on the site
                    $json[] = array(
                                    'MemberUserID' => $result['id_user'],
                                    'MemberName' => $result['name']
                                );
                }
            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
        else { // No registered users
            $json[] = array('You have no members on your roster. Click add player to add a member.');

            // Send the JSON data:
            echo json_encode($json);
        }
    }