<?php 
    /*
     * view_roster.php
     * This page allows user to view roster.
     */

    require_once('../includes/bootstrap.php');
    $page_title = 'digoro : Roster';
    require_once('../includes/header.html');
    require_once('../includes/php-functions.php');

    // Validate user
    checkSessionObject();    

    // Check user role
    checkRole('m');
?>
    <div class="container" id="contentWrapper">
        <div class="row"> <!-- Main row - for all content except footer -->    
            <div class="span2"> <!-- column for icons --> 
                <div class="well">
                <div class="side-nav">
                <ul class="nav nav-list">
                    <li>
                        <a href="home.php"><img src="../css/imgs/home-icon.png" 
                            alt="home-icon" height="60" width="60"></a>
                    </li>
                    <li><p>Home</p></li>
                    <li>
                        <a href="profile.php"><img src="../css/imgs/user-icon.png" 
                            alt="user-icon" height="60" width="60"></a>    
                    </li>
                    <li><p>Profiles</p></li>
                    <li>
                        <a href="my_teams.php"><img src="../css/imgs/clipboard-icon.png" 
                            alt="clipboard-icon" height="60" width="60"></a>    
                    </li>
                    <li><p>My Teams</p></li>
                    <li>
                        <a href="find_players.php"><img src="../css/imgs/binoculars-icon.png" 
                            alt="binoculars-icon" height="60" width="60"></a>
                    </li>
                    <li><p>Find Players</p></li>
                    <li>
                        <a href=""><img src="../css/imgs/world-icon.png" 
                            alt="world-icon" height="60" width="60"></a>
                    </li>
                    <li><p>Find Teams</p></li>        
                </ul>
                </div>
                </div>
            </div> <!-- end of column for icons --> 

            <div class="span10"> <!-- column for main content -->
                <div class="row"> <!-- row for Team Name header -->
                    <div class="span6">
                        <h3><span class="page-header teamdisplay"></span> Roster</h3> <!-- Name dynamically inserted here -->
                    </div>
                    <div class="span2">
                        <button type="button" id="add-member" class="btn btn-small btn-primary">Add Member</button>
                    </div>
                </div>

            <!-- Load ajax roster data here -->
            <div id="content">
                <table class="table table-striped table-bordered table-condensed" id="roster" width="100%">
                    <caption>
                        Current Members
                    </caption>
                </table>
            </div>

            </div> <!-- End of column for main content -->
        </div> <!-- End of main row -->

        <!-- Modal Dialog Forms -->
        <div id="AddMemberForm" title="Add New Member">        
            <form method="post" class="form-horizontal">
                
                <div class="control-group">            
                    <label class="control-label" for="add-member-fname">First name*</label>
                    <div class="controls">
                        <input type="text" class="input-medium" name="add-member-fname" id="add-member-fname" size="20" maxlength="20" />
                    </div>
                </div>

                <div class="control-group">            
                    <label class="control-label" for="add-member-lname">Last name*</label>
                    <div class="controls">
                        <input type="text" class="input-medium" name="add-member-lname" id="add-member-lname" size="20" maxlength="40" />
                    </div>
                </div>

                <div class="control-group">    
                    <label class="control-label" for="add-member-sel-sex">Member's sex is*</label>
                    <div class="controls">
                        <select class="input-medium" name="add-member-sel-sex" id="add-member-sel-sex">
                            <option value="">-Select Sex-</option>
                            <option value="1">Female</option>
                            <option value="2">Male</option>
                        </select>
                    </div>
                </div>

                <div class="control-group">            
                    <label class="control-label" for="add-member-email">Member's email is*</label>
                    <div class="controls">
                        <input type="text" class="input-medium" name="add-member-email" id="add-member-email" size="30" maxlength="60" />
                    </div>
                </div>

                <div class="control-group">                
                    <label class="control-label" for="add-member-ppos">Primary position*</label>
                    <div class="controls">
                        <input type="text" class="input-medium" name="add-member-ppos" id="add-member-ppos" size="20" maxlength="30" 
                            placeholder="ex. striker"/>            
                    </div>
                </div>

                <div class="control-group">    
                    <label class="control-label" for="add-member-spos">Secondary position</label>
                    <div class="controls">
                        <input type="text" class="input-medium" name="add-member-spos" id="add-member-spos" size="20" maxlength="30"
                            placeholder="ex. goalkeeper" />    
                    </div>
                </div>

                <div class="control-group">    
                    <label class="control-label" for="add-member-jernum">Jersey Number</label>
                    <div class="controls">
                        <input type="text" class="input-medium" name="add-member-jernum" id="add-member-jernum" size="4" maxlength="4" />
                    </div>
                </div>

                <div class="control-group">    
                    <label class="control-label">Send Invite?</label>
                    <div class="controls">
                        <label class="radio">
                          <input type="radio" name="add-member-invite" id="add-member-inviteY" value="1" checked>
                          Yes
                        </label>
                        <label class="radio">
                          <input type="radio" name="add-member-invite" id="add-member-inviteN" value="0">
                          No
                        </label>
                    </div>
                </div>
                <small>* Required Fields</small>
            </form>
        </div>
        
        <div id="EditMemberForm" title="Edit Member">    
            <form method="post" class="form-horizontal">
                <div class="control-group">
                    <label class="control-label" for="edit-member-fname">First name</label>
                    <div class="controls">
                        <input type="text" class="input-medium" name="edit-member-fname" id="edit-member-fname" size="20" maxlength="20" />
                    </div>
                </div>

                <div class="control-group">        
                    <label class="control-label" for="add-member-lname">Last name</label>
                    <div class="controls">
                        <input type="text" class="input-medium" name="edit-member-lname" id="edit-member-lname" size="20" maxlength="40" />
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="edit-member-sel-sex">Member's sex is</label>
                    <div class="controls">
                        <select class="input-medium" name="edit-member-sel-sex" id="edit-member-sel-sex">
                            <option value="">-Select Sex-</option>
                            <option value="1">Female</option>
                            <option value="2">Male</option>
                        </select>
                    </div>
                </div>

                <div class="control-group">            
                    <label class="control-label" for="edit-member-ppos">Primary position:</label>
                    <div class="controls">
                        <input type="text" class="input-medium" name="edit-member-ppos" id="edit-member-ppos" size="20" maxlength="30" />            
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="edit-member-spos">Secondary position:</label>
                    <div class="controls">
                        <input type="text" class="input-medium" name="edit-member-spos" id="edit-member-spos" size="20" maxlength="30" />    
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="edit-member-jernum">Jersey Number:</label>
                    <div class="controls">
                        <input type="text" class="input-medium" name="edit-member-jernum" id="edit-member-jernum" size="4" maxlength="4" />
                    </div>
                </div>
            </form>
        </div>

        <div id="DelMemberForm" title="Delete Member">
            <form method="post">
                <p>Are you sure you want to remove <span id="member_name"></span> this member?</p>
            </form>
        </div>
        <!-- End of Modal Dialog Form -->

    <!-- External javascript call -->
    <script type="text/javascript" src="../js/roster.js"></script>
    <script type="text/javascript" src="../js/myteams_pg.js"></script>
<?php require_once('../includes/footer.html'); ?>