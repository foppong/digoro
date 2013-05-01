<?php
    /* This page defines the Team class.
     * Attributes:
     *  protected tmname
     *  protected about
     *  protected id_sport
     *  protected id_user
     *  protected level
     *  protected id_region
     *  protected team_sex
     *  protected team_email
     * 
     * Methods:
     *  setTeamID()
     *  setTeamNM()
     *  setTeamABT()
     *  setTeamAttributes()
     *  getTeamAttribute()
     *  pullTeamData()
     *  pullSpecificData()
     *  createTeam()
     *  editTeam()
     *  transferTeam()
     *  deleteTeam()
     *  isManager()
     *  removeMember()
     */
    
    class Team extends DigoroObject {

        // Declare the attributes
        protected $tmname, $about, $id_sport, $id_user, $level, 
            $id_region, $team_sex, $team_email;

        protected $_mainTable = 'teams';
        protected $_mainTablePrimaryKey = 'id_team';

        // Function to set team ID attribute
        public function setTeamID($teamID)
        {
            $this->_id = $teamID;
        }


/*
        // Function to set team name
        public function setTeamNM($attribute)
        {
            $this->tmname = $attribute;
        }

        // Function to set team about me
        public function setTeamABT($attribute)
        {
            $this->about = $attribute;
        }
*/


        // Function to set Team attributes
        public function setTeamAttributes($sprtID = 0, $manID = 0, $tmname ='', 
            $abtm = '', $lvl = '', $sex = '', $reg = 0, $tmemail = '')
        {
            $this->id_sport = $sprtID;
            $this->id_user = $manID;                        
            $this->tmname = $tmname;
            $this->about = $abtm;
            $this->level = $lvl;
            $this->team_sex = $sex;
            $this->id_region = $reg;
            $this->team_email = $tmemail;
        }


        // Function to get specific class attribute
        public function getTeamAttribute($attribute)
        {
            return $this->$attribute;
        }


        // Function to pull complete team data from database and set attributes
        public function pullTeamData()
        {
            // Make the query
            $q = "SELECT id_sport, id_user, team_name, about, level_of_play,
                         id_region, team_sex, team_email
                  FROM teams
                  WHERE id_team = {$this->_id}
                  LIMIT 1";

            // Execute the query & Store result
            $result = $this->_dbObject->getRow($q);

            // Found result
            if($result !== false) {
                $this->setTeamAttributes($result['id_sport'], $result['id_user'], $result['team_name'],
                                            $result['about'], $result['level_of_play'], $result['id_region'],
                                            $result['team_sex'], $result['team_email']);
            }
        } // End of pullTeamData function


        // Function to pull specific user data from database
        public function pullSpecificData($datacolumn)
        {
            // Make the query
            $q = "SELECT {$datacolumn} FROM teams WHERE id_team = {$this->_id} LIMIT 1";

            // Execute the query & Store result
            return $this->_dbObject->getOne($q);
        } // End of pullSpecificData function        


        // Function to create team
        public function createTeam($sprtID, $manID, $tmname, $abtm, $lvl, $reg, $sex, $tmemail)
        {
            // Make the query:
            $q = "INSERT INTO teams
                  (
                    id_sport,
                    id_user,
                    team_name,
                    about,
                    level_of_play,
                    id_region,
                    team_sex,
                    team_email
                  ) 
                  VALUES
                  (
                    {$sprtID},
                    {$manID},
                    '{$this->_dbObject->realEscapeString($tmname)}',
                    '{$this->_dbObject->realEscapeString($abtm)}',
                    {$lvl},
                    {$reg},
                    {$sex},
                    '{$this->_dbObject->realEscapeString($tmemail)}'
                  )";

            // Execute the query:
            $this->_dbObject->query($q);

            // Successfully added team
            if($this->_dbObject->getNumRowsAffected() == 1) {

                // Assign id_team with setTeamID function
                $this->setTeamID($this->_dbObject->getLastInsertId());

                // Set the default team ID
                $_SESSION['deftmID'] = $this->_id;

                // Make the new query to add manager to player table:
                $q = "INSERT INTO members (id_user, id_team) VALUES ({$manID}, {$this->_id})";

                // Execute the query:
                $this->_dbObject->query($q);

                if($this->_dbObject->getNumRowsAffected() !== 1) { // It didn't run ok
                    echo '<div class="alert alert-error">Oh Snap! Manager was not added to roster. Please contact the service administrator.</div>';
                    exit();
                }           

                // Set boolean logic to true
                $bl = 1;

                // Update the user's info in the database
                // This is wher the LOGIC of "logged in before" is set
                // NOTE: When develop player portion, will need to have a different trigger
                $q = "UPDATE users
                      SET default_teamID = {$this->_id},
                          login_before = {$bl}
                      WHERE id_user = {$manID}
                      LIMIT 1";

                // Execute the query:
                $this->_dbObject->query($q);
                    
                if($this->_dbObject->getNumRowsAffected() !== 1) // It didn't run ok
                {
                    echo '<div class="alert alert-error">Oh Snap! Team was not added. Please contact the service administrator.</div>';
                    exit();
                }

                echo '<div class="alert alert-success">Team was added successfully!</div>';
            }
            else {
                echo '<div class="alert alert-error">Oh Snap! Team was not added. Please contact the service administrator.</div>';
                exit();
            }
        } // End of createTeam function

        
        // Function to edit team
        public function editTeam($sprtID, $tmname, $abtm, $lvl, $reg, $sex, $tmemail, $teamid)
        {
            // Update the user's info in the members' table in database
            $q = "UPDATE teams
                  SET id_sport = {$sprtID},
                      team_name = '{$this->_dbObject->realEscapeString($tmname)}',
                      about = '{$this->_dbObject->realEscapeString($abtm)}',
                      level_of_play = {$lvl},
                      id_region = {$reg},
                      team_sex = {$sex},
                      team_email = '{$this->_dbObject->realEscapeString($tmemail)}'
                WHERE id_team = {$teamid}
                LIMIT 1";

            // Execute the query:
            $this->_dbObject->query($q);

            // Print a message based upon result:
            if($this->_dbObject->getNumRowsAffected() == 1) {
                echo '<div class="alert alert-success">Team was edited succesfully.</div>';
            }
            else {
                echo '<div class="alert">No changes made.</div>';
            }
        } // End of editTeam function


        // Function to transfer Manager role
        public function transferTeam($memberUserID, $teamid)
        {
            // Make the query to update the team record with another manager ID
            $q = "UPDATE teams
                  SET id_user = {$memberUserID}
                  WHERE id_team = {$teamid}
                  LIMIT 1";

            // Execute the query:
            $this->_dbObject->query($q);
                    
            if($this->_dbObject->getNumRowsAffected() == 1) { // Update to database was made
                echo '<div class="alert alert-success">The team has been transferred.</div>';

                // ADD CODE HERE TO SEND EMAIL TO RECEIPIENT

            }
            else {    
                echo '<div class="alert alert-error">The team could not be transferred due to a system error.</div>';
                exit();
            }
        }


        // Functon to delete team from database
        public function deleteTeam($teamid)
        {
            // Make the query    
            $q = "DELETE FROM teams WHERE id_team = {$teamid} LIMIT 1";

            // Execute the query:
            $this->_dbObject->query($q);

            // If the query ran ok.
            if($this->_dbObject->getNumRowsAffected() == 1) {
                // Print a message
                echo '<div class="alert alert-success">This team has been deleted successfully</div>';
            }
            else {
            // If the query did not run ok.
                echo '<div class="alert alert-error">The team could not be deleted due to a system error</div>';
                exit();
            }
        } // End of deleteTeam function    


        // Function to remove player from team if not manager
        public function removeMember($userID)
        {
            // Make the query
            $q = "DELETE FROM members WHERE id_user = {$userID} LIMIT 1";

            // Execute the query
            $this->_dbObject->query($q);

            if($this->_dbObject->getNumRowsAffected() == 1) {
                echo '<div class="alert alert-success">You have successfully removed yourself from the team</div>';
            }
            else {
                echo '<div class="alert alert-error">The removal did not work. Pleaes contact the system admistrator</div>';
                exit();
            }
        } // End of removePlayer function
    } // End of Class
