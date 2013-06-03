<?php
    /** profile_data.php
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

    // Request is coming from profile view to query all profiles associated with user
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullProfiles') {

        // Make the Query
        $q = "SELECT id_profile, team_sex_preference, id_region, id_sport,
                     sport_experience, primary_position, secondary_position, comments
              FROM profiles
              WHERE id_user = {$dbObject->cleanInteger($userID)}
              ORDER BY id_sport ASC";

        // Execute the query & store results
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {

                // Translate sport
                $sport = translateSport($result['id_sport']);

                // Translate team sex
                $teamsex = translateTmSex($result['team_sex_preference']);

                // Translate region
                $reg = translateRegion($result['id_region']);

                // Translate experience
                $exp = translateExperience($result['sport_experience']);

                $json[] = array(
                                'Sport' => $sport,
                                'Desired Team Sex' => $teamsex,
                                'Region' => $reg,
                                'My Experience' => $exp,
                                'Details' => '<button id="view-subreq" class="btn btn-mini" value=' . $result['id_profile'] . '>View</button>',
                                'Edit' => '<button id="edit-profile" class="btn btn-mini" value=' . $result['id_profile'] . '>Edit</button>',
                                'Delete' => '<button id="delete-profile" class="btn btn-mini" value=' . $result['id_profile'] . '>Delete</button>'
                            );
            } // End of WHILE loop

            // Send the JSON data:
            echo json_encode($json);
        }
    }

    // Request is coming from the edit Profile form
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'pullSingleProfile') {

        $profileID = $_POST['profileID'];

        // Make the Query
        $q = "SELECT team_sex_preference, id_region, id_sport, sport_experience,
                     primary_position, secondary_position, comments
              FROM profiles
              WHERE id_profile = {$dbObject->cleanInteger($profileID)}
              LIMIT 1";

        // Execute the query & store result
        $results = $dbObject->getAll($q);

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {            

                $json[] = array(
                                'Sport' => $result['id_sport'],
                                'Desired Team Sex' => $result['team_sex_preference'],
                                'Region' => $result['id_region'],
                                'My Experience' => $result['sport_experience'],
                                'Primary Position' => $result['primary_position'],
                                'Secondary Position' => $result['secondary_position'],
                                'Comments' => $result['comments']
                            );
            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
    }

    // Request is coming from the profile page, so user can view the subresponse detail
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['actionvar'] == 'User_single_SubResp_Data') {

        $idSubResponse = $_POST['idSubResp'];

        // Make the Query
        $q = "SELECT id_profile, team_sex_preference, id_region, id_sport, sport_experience,
                     primary_position, secondary_position, comments
              FROM profiles
              WHERE id_user = {$dbObject->cleanInteger($userID)}
              ORDER BY id_sport ASC";

        // Execute the query & store results
        $results = $dbObject->getAll($q);    

        // If there are results to show.
        if(count($results) > 0) {
            // Initialize an array:
            $json = array();

            // Fetch and put results in the JSON array...
            foreach($results as $result) {

                // Translate sport
                $sport = translateSport($result['id_sport']);

                // Translate team sex
                $teamsex = translateTmSex($result['team_sex_preference']);

                // Translate region
                $reg = translateRegion($result['id_region']);

                // Translate experience
                $exp = translateExperience($result['sport_experience']);

                $json[] = array(
                                'Sport' => $sport,
                                'Desired Team Sex' => $teamsex,
                                'Region' => $reg,
                                'My Experience' => $exp,
                                'Details' => '<button id="view-subreq" class="btn btn-mini" value=' . $result['id_profile'] . '>View</button>',
                                'Edit' => '<button id="edit-profile" class="btn btn-mini" value=' . $result['id_profile'] . '>Edit</button>',
                                'Delete' => '<button id="delete-profile" class="btn btn-mini" value=' . $result['id_profile'] . '>Delete</button>'
                            );
            } // End of FOR loop

            // Send the JSON data:
            echo json_encode($json);
        }
    }