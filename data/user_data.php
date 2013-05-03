<?php
    /** user_data.php
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

    // Get user ID
    $userID = $user->getUserID();

    // Request is coming from profile view to query all profiles associated with user
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullUserData') {

        // Make the Query
        $q = "SELECT first_name, last_name, city, state,
                zipcode, sex, birth_date, phone_num
              FROM users
              WHERE id_user = {$userID}";

        // Execute the query and store results 
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {

                // Set up for sticky birthday form, opted to break apart here and not in js
                $bdarray = explode("-", $result['birth_date']);
                $bdyr = $bdarray[0];
                $bdmnth = $bdarray[1];
                $bdday = $bdarray[2];    

                $json[] = array(
                                'First Name' => $result['first_name'],
                                'Last Name' => $result['last_name'],
                                'City' => $result['city'],
                                'State' => $result['state'],
                                'Zipcode' => $result['zipcode'],
                                'Sex' => $result['sex'],
                                'Phone' => $result['phone_num'],
                                'Byear' => $bdyr,
                                'Bmon' => $bdmnth,
                                'Bday' => $bdday
                             );
            }// End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
    }