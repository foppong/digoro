<?php
    /** team_data.php
    * This page queries a database, returnnig a list
    * of teams
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

    // Pulls Data for all the teams associated with the user
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'teammenu') {

        // Get user ID
        $userID = $user->getUserID();

        // Make the Query to find all teams associated with user via a union of the members and teams table:
        $q = "SELECT m.id_team, t.team_name
              FROM members AS m INNER JOIN teams AS t
                  USING (id_team)
              WHERE m.id_user = {$userID}";

        // Execute the query and store results
        $results = $dbObject->getAll($q);    

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {
                $json[] = array(
                                'TeamID' => $result['id_team'],
                                'TeamName' => stripslashes($result['team_name'])
                            );
            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
        else {

            $json[] = array(
                'You have no teams associated with your account');

            // Send the JSON data:
            echo json_encode($json);
        }
    }

    // Pulls Data for specific team for edit team form
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullTeamData') {

        // Retrieve current team ID from session variable
        $tm = $_SESSION['ctmID'];

        // Make the query
        $q = "SELECT id_sport, id_user, team_name, about, level_of_play,
                     id_region, team_sex, team_email
              FROM teams
              WHERE id_team = {$tm}
              LIMIT 1";

        // Execute the query and store results
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Fetch records...
            foreach($results as $result) {
                $json[] = array(
                                'Sport' => $result['id_sport'],
                                'ManagerID' => $result['id_user'],
                                'Team Name' => $result['team_name'],
                                'About' => $result['about'],
                                'Level' => $result['level_of_play'],
                                'Region' => $result['id_region'],
                                'Sex' => $result['team_sex'],
                                'TEmail' => $result['team_email'],
                                'Team ID' => $tm
                            );
            }    // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }    
    }

    // Pulls Data for team info display
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullDisplayTeamData') {

        // Retrieve current team ID from session variable
        $tm = $_SESSION['ctmID'];

        // Make the query
        $q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS mname, u.email, u.phone_num,
                     t.id_sport, t.team_email, t.team_sex, t.level_of_play, t.id_region,
                     t.about
              FROM teams AS t
                  INNER JOIN users AS u USING (id_user)
              WHERE t.id_team = {$tm}
              LIMIT 1";

        // Execute the query and store results
        $results = $dbObject->getAll($q);    

        // If there are results to show.
        if(count($results) > 0) {        
            // Fetch records...
            foreach($results as $result) {

                // Translate sport
                $teamsprt = translateSport($result['id_sport']);

                // Translate level of play
                $teamlvl = translateLevelofPlay($result['level_of_play']);

                // Translate region
                $teamreg = translateRegion($result['id_region']);

                // Translate team sex
                $teamsex = translateTmSex($result['team_sex']);

                $json[] = array(
                                'Manager Name' => $result['mname'],
                                'Manager Email' => $result['email'],
                                'Manager Phone' => $result['phone_num'],
                                'Sport' => $teamsprt,
                                'Team Email' => $result['team_email'],
                                'Sex' => $teamsex,
                                'Level' => $teamlvl,
                                'Region' => $teamreg,
                                'About' => $result['about']
                            );
            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
    }