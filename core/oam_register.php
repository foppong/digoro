<?php
    // mg_welcome.php
    // Landing page for a new OAuth User

    require '../includes/config.php';
    $page_title = 'digoro : Welcome';
    require_once '../includes/header.html';
    include '../includes/php-functions.php';

    // autoloading of classes
    function __autoload($class) {
        require_once('../classes/' . $class . '.php');
    }

    // Need the database connection:
    require MYSQL2;

    // See if there is a user from a cookie
    $fbuser = $facebook->getUser();

    if($fbuser) {
        try {
            // Proceed knowing you have a logged in user who's authenticated.
            $user_profile = $facebook->api('/me');

            $first_name = $user_profile['first_name'];
            $last_name = $user_profile['last_name'];
            $uemail = $user_profile['email'];
            $gender = $user_profile['gender'];
            $oa_provider = 'facebook';
            $oa_id = $user_profile['id'];
    
            // Format Facebook birthday to database format    
            $facebirthday = $user_profile['birthday'];
            $bday = explode("/", $facebirthday);    
            $month = $bday[0];
            $day = $bday[1];
            $year = $bday[2];
            $bdarray = array($year, $month, $day);
            $bdstring = implode("-", $bdarray);
            $bd = new DateTime($bdstring);
            $bdfrmat = $bd->format('Y-m-d');

            // Create user object
            $OAuser = new UserAuth();
        }
        catch(FacebookApiException $e) {
            echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
            $fbuser = null;
          }
    }
    else {
        redirect_to('fatbar.php');
    }

    //Set role of user
    $role = 'M';

    // Register user in system once I know the role
    $OAuser->addOAuthUser($uemail, $first_name, $last_name, $role, $gender, $bdfrmat);
    $OAuser = $_SESSION['userObj'];
    $userID = $OAuser->getUserID();

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // IN FUTURE CAN ADD LOGIC HERE FOR PAYING CUSTOMERS TO ADD TEAM - similar to checks 
        // i have now for managers to edit and add players/games

        // Assume invalid values:
        $tn = $sp = $ct = $st = $lg = FALSE;

        // Validate Team name
        if($_POST["tname"]) {
            $tn = $_POST["tname"];
        }
        else {
            echo '<p class="error"> Please enter a Team name.</p>';
        }

        // Validate a sport is selected
        if($_POST['sport']) {
            $sp = $_POST['sport'];
        }
        else {
            echo '<p class="error">Please select your sport.</p>'; 
        }

        // Validate Team's homecity
        if($_POST['city']) {
            $ct = $_POST['city'];
        }
        else {
            echo '<p class="error"> Please enter your teams homecity.</p>';
        }

        // Validate Team's state
        if($_POST['state']) {
            $st = $_POST['state'];
        }
        else {
            echo '<p class="error"> Please enter your teams home state.</p>';
        }

        // Validate a league is selected
        if($_POST['league']) {
            $lg = $_POST['league'];
        }
        else {
            echo '<p class="error">Please select your league.</p>'; 
        }

        // Validate about team information
        if($_POST['abouttm']) {
            $abtm = trim($_POST['abouttm']);
        }
        else {
            echo '<p class="error">Please enter a brief description about your team.</p>';
        }

        // Checks if team name, userID, sport, team city, state, and league are valid before adding team to database.
        if($lg && $userID && $sp && $tn && $ct && $st) {
            // Create team object for use & create team for database
            $team = new Team();
            $team->createTeam($lg, $sp, $userID, $tn, $ct, $st, $abtm);

            // Redirect to manager home pag
            $url = BASE_URL . 'manager/manager_home.php';
            header("Location: $url");    
        }
        else {                                    
            echo '<p class="error">Please try again.</p>';
        }
    }

    // Delete objects
    unset($OAuser);
    unset($team);
?>

<h1>Welcome to Digoro!</h1>
<h2>This page is designed to help get you started!</h2>

<form action="oam_register.php" method="post" id="OAFirstTeamForm">
        <h3>Now you can use the form below to enter your very first team!</h3><br />
        <label for="tname"><b>Enter Team Name:</b></label>
        <input type="text" name="tname" id="tname" size="30" maxlength="45" />
    
        <label for="sport"><b>Select Sport:</b></label>
        <select name="sport" id="sport">
            <option value="">-Select Sport-</option>
            <option value="1">Soccer</option>
            <option value="2">Flag Football</option>
            <option value="3">Ice Hockey</option>
            <option value="4">Softball</option>
            <option value="5">Basketball</option>
            <option value="6">Ultimate</option>
            <option value="7">Volleyball</option>
            <option value="8">Kickball</option>
            <option value="9">Cricket</option>
        </select>

        <label for="city"><b>Enter Team's Home City:</b></label>
        <input type="text" name="city" id="city" size="30" maxlength="40" />

        <label for="state"><b>Enter Team's Home State:</b></label>
        <select name="state" id="state" onchange="LEAGUE.showLeagues(this.value)">
            <option value="">-Select State-</option>
            <option value="AL">AL</option><option value="AK">AK</option>
            <option value="AZ">AZ</option><option value="AR">AR</option>
            <option value="CA">CA</option><option value="CO">CO</option>
            <option value="CT">CT</option><option value="DE">DE</option>
            <option value="FL">FL</option><option value="GA">GA</option>
            <option value="HI">HI</option><option value="ID">ID</option>
            <option value="IL">IL</option><option value="IN">IN</option>
            <option value="IA">IA</option><option value="KS">KS</option>
            <option value="KY">KY</option><option value="LA">LA</option>
            <option value="ME">ME</option><option value="MD">MD</option>
            <option value="MA">MA</option><option value="MI">MI</option>
            <option value="MN">MN</option><option value="MS">MS</option>
            <option value="MO">MO</option><option value="MT">MT</option>
            <option value="NE">NE</option><option value="NV">NV</option>
            <option value="NH">NH</option><option value="NJ">NJ</option>
            <option value="NM">NM</option><option value="NY">NY</option>
            <option value="NC">NC</option><option value="ND">ND</option>
            <option value="OH">OH</option><option value="OK">OK</option>
            <option value="OR">OR</option><option value="PA">PA</option>
            <option value="RI">RI</option><option value="SC">SC</option>
            <option value="SD">SD</option><option value="TN">TN</option>
            <option value="TX">TX</option><option value="UT">UT</option>
            <option value="VT">VT</option><option value="VA">VA</option>
            <option value="WA">WA</option><option value="WV">WV</option>
            <option value="WI">WI</option><option value="WY">WY</option>
        </select>        
    
        <label for="league"><b>Select League:</b></label>
        <select name="league" id="league"></select>

        <label for="abouttm"><b>Team Information:</b></label>
        <textarea id="abouttm" name="abouttm" cols="30" rows="2"></textarea><br />
        <small>Enter something cool about your team.</small>
    <div align="left"><input type="submit" name="submit" value="Add Team" /></div>
</form>

<?php include '../includes/footer.html'; ?>