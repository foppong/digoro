<?php
    // my_teams.php
    //
    require '../includes/config.php';
    $page_title = 'Welcome to digoro!';
    include '../includes/header.html';
    include '../includes/php-functions.php';
    //include '../includes/facebook.php';

    // autoloading of classes
    function __autoload($class) {
        require_once('../classes/' . $class . '.php');
    }

    // See if there is a user from a cookie
    $fbuser = $facebook->getUser();

    // Validate user
    checkSessionObject();

    // Check user role
    checkRole('m');

    // Need the database connection:
    require_once MYSQL2;

    // Assign user object from session variable
    $user = $_SESSION['userObj'];
    $userID = $user->getUserID();

    // Pull current user data from database and set object attributes
    $user->pullUserData();

    // Get user's default team ID
    $dftmID = $user->getUserAttribute('dftmID');

    // Update team object session variable as user selects different teams
    if((isset($_POST['y'])) && (is_numeric($_POST['y']))) {
        $_SESSION['ctmID'] = $_POST['y'];
        $ctmID = $_SESSION['ctmID'];
    }
    else {
        // Assign default team ID to current team ID session variable
        $_SESSION['ctmID'] = $dftmID;
    }

    // Delete objects
    unset($user);
?>
    <div class="container" id="contentWrapper">
        <div class="row"> <!-- Main row - for all content except footer -->    
            <div class="span2"> <!-- column for icons --> 
                <div class="well">
<?php require_once('../includes/side_nav_manager.html'); ?>
                </div>
            </div> <!-- end of column for icons --> 

			<div class="span10"> <!-- column for main content --> 
				<div class="row"> <!-- My Teams header -->
					<div class="span3">
						<div class="page-header"><h1>My Teams</h1></div>
					</div>
				</div>

				<div class="row"> <!-- Add Team button row -->
					<div class="span4 offset3">
						<h4>Add a team that you manage</h4>
					</div>
					<div class="span2">
						<button type="button" id="addTeam" class="btn btn-small btn-primary">Add Team</button>
					</div>
				</div>

				<div class="row"> <!-- Select team row -->
					<div class="span4">
						<form method="post" class="form-inline" id="SelectTeamForm">	
							<select class="span2" name="y" id="y"></select>		
							<button type="submit" id="selectTeam" class="btn">Select</button>
						</form>
					</div>
				</div>

			<div class="row"> <!-- Team Name header -->
				<div class="span5">
					<h3><span class="page-header teamdisplay"></span></h3> <!-- Name dynamically inserted here -->
				</div>
			</div>

				<div class="row"> <!-- row for alerts -->
					<div id="status"></div> 
				</div>				
				
				<div class="row"> <!-- row for team menu options -->
						<div class="row"> <!-- row 1 -->
							<div class="span4 offset1">
								<a href="view_abteam.php"><img src="../css/imgs/file.png" 
									alt="world-icon" height="128" width="128"></a>
								<p>Team Info</p>							
							</div>
							<div class="span4">
								<a href="view_roster.php"><img src="../css/imgs/group.png" 
									alt="world-icon" height="128" width="128"></a>
								<p>Roster</p>							
							</div>							
						</div>	
						<div class="row"> <!-- row 2 -->
							<div class="span4 offset1">
								<a href="view_sch.php"><img src="../css/imgs/list.png" 
									alt="world-icon" height="128" width="128"></a>
								<p>Schedule</p>							
							</div>
							<div class="span4">
								<a href=""><img src="../css/imgs/mail.png" 
									alt="world-icon" height="128" width="128"></a>
								<p>Communications</p>							
							</div>							
						</div>									
				</div>
				
			</div>
			 
		</div> <!-- End of main row -->



    <!-- Modal Dialog Form -->
    <div id="AddTeamForm" title="Add New Team" class="span4">        
        <form method="post" class="form-horizontal">
            <div class="control-group">
                <label class="control-label" for="add-team-sel-sport">We play*</label>
                <div class="controls">
                    <select class="input-large" name="add-team-sel-sport" id="add-team-sel-sport">
                        <option value="">-Select Sport-</option>
                        <option value="1">Soccer</option>
                        <option value="2">Flag Football</option>
                        <option value="3">Hockey</option>
                        <option value="4">Softball</option>
                        <option value="5">Basketball</option>
                        <option value="6">Ultimate</option>
                        <option value="7">Volleyball</option>
                        <option value="8">Kickball</option>
                        <option value="9">Rugby</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="add-team-name">Our team name is*</label>
                <div class="controls">
                    <input class="input-large" type="text" name="add-team-name" id="add-team-name" />    
                </div>
            </div>    

            <div class="control-group">            
                <label class="control-label" for="add-team-sel-sex">The team sex is*</label>
                <div class="controls">
                    <select class="input-large" name="add-team-sel-sex" id="add-team-sel-sex">
                        <option value="">-Select Sex-</option>
                        <option value="1">Coed</option>
                        <option value="2">All Female</option>
                        <option value="3">All Male</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="add-team-sel-region">We are based in*</label>
                <div class="controls">
                    <select class="input-large" name="add-team-sel-region" id="add-team-sel-region">
                        <option value="">-Select Region-</option>
                        <option value="1">San Francisco/ Bay Area</option>
                    </select>
                </div>
            </div>    

            <div class="control-group">
                <label class="control-label" for="add-team-sel-level-play">Our level of play is*</label>
                <div class="controls">
                    <select class="input-large" name="add-team-sel-level-play" id="add-team-sel-level-play">
                        <option value="">-Select Level-</option>
                        <option value="1">Recreational</option>
                        <option value="2">Intermediate</option>
                        <option value="3">Advanced</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="add-team-email">Our team email is</label>
                <div class="controls">
                    <input type="text" class="input-large" name="add-team-email" id="add-team-email" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="add-team-abouttm">Other team information to share</label>
                <div class="controls">
                    <textarea class="input-large" id="add-team-abouttm" name="add-team-abouttm" 
                    cols="30" rows="2" placeholder="enter something cool about your team"></textarea>
                </div>
            </div>
        </form>
    </div> 
    <!-- End of Modal Dialog Form -->

    <!-- External javascript call -->
    <script type="text/javascript" src="../js/myteams_pg.js"></script>

<?php include '../includes/footer.html'; ?>