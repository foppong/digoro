<?php
    /** browse_player_matches_data.php
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

    // Request is coming from profile view to query all subresponses associated with user
    if($_SERVER['REQUEST_METHOD'] == 'POST') {

				$sex = $_POST['search-PL-sel-sex'];
				$sport = $_POST['search-PL-sel-sport'];
				$exp = $_POST['search-PL-sel-exp'];
				$reg = $_POST['search-PL-sel-reg'];

        // Make the Query
        $q = "SELECT p.id_profile, p.id_user, p.team_sex_preference, p.id_region,
        						 p.id_sport, p.sport_experience,
        						 CONCAT(p.primary_position, ', ',p.secondary_position) AS pos,
        						 CONCAT(u.first_name, ' ', u.last_name) AS name, u.sex
              FROM profiles AS p
                  INNER JOIN users AS u USING (id_user)
              WHERE u.sex = {$sex} AND p.id_sport = {$sport} AND
              			p.sport_experience = {$exp} AND p.id_region = {$reg}
              ORDER BY name";

        // Execute the query:
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {

                // Translate sport
                $teamsprt = translateSport($result['id_sport']);

                // Translate level of play
                $plrlvl = translateExperience($result['sport_experience']);

                // Translate region
                $plrreg = translateRegion($result['id_region']);

                // Translate player sex
                $plrsex = translateSex($result['sex']);

                $json[] = array(
                                'Player Name' => $result['name'],
                                'Sex' => $plrsex,
                                'Sport' => $teamsprt,
                                'Experience' => $plrlvl,
                                'Position' => $result['pos'],
                                'Region' => $plrreg,
                                'Contact' => '<button id="view-player" class="btn btn-mini" value=' . $result['id_profile'] . '>Contact</button>');
                            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }        
    }
