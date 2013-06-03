<?php
    /* This page defines the Member class.
     * Attributes:
     *  protected primary position
     *  protected secondary postion
     *  protected jersey_numb
     *  protected id_member
     * 
     * Methods:
     *  setMembID()
     *  setMemberAttributes()
     *  getMemberAttribute()
     *  pullMemberData()
     *  createMember()
     *  inUserDatabase()
     *  editMember()
     *  deleteMember()
     *  checkAuth()
     *  isManager()
     */


    class Member extends DigoroObject {

        // Declare the attributes
        protected $prim_position, $sec_position, $jersey_numb, $id_user, $_id;
        protected $_mainTable = 'members';
        protected $_mainTablePrimaryKey = 'id_member';

        // Function to set member ID attribute
        public function setMembID($memberID)
        {
            $this->_id = $memberID;
        }


        public function setMemberAttributes($ppos = '', $spos = '', $jernumb = 0, $userID = 0)
        {
            $this->prim_position = $ppos;
            $this->sec_position = $spos;
            $this->jersey_numb = $jernumb;
            $this->id_user = $userID;
        }


        public function getMemberAttribute($attribute)
        {
            return $this->$attribute;
        }


        // Function to pull complete member data from database and set attributes
        public function pullMemberData()
        {
            // Make the query to retreive user information:        
            $q = "SELECT id_user, 
                         primary_position,
                         secondary_position,
                         jersey_number
                  FROM members
                  WHERE id_member = {$this->_dbObject->cleanInteger($this->_id)}
                  LIMIT 1";

            // Execute the query and store result
            $result = $this->_dbObject->getRow($q);

            // Found result
            if($result !== false) {
                $this->setMemberAttributes($result['primary_position'], $result['secondary_position'],
                                           $result['jersey_number'], $result['id_user']);
            }
        } // End of pullMemberData function


        // Functon to create member and add to team
        public function createMember($userEmail, $teamID, $fn, $ln, $sex, $ppos, $spos, $jnumb, $invite)
        {
            // Code checks users database to see if member is already in database. If not, enters a place holder until 
            // added player completes registeration.
            if($this->inUserDatabase($userEmail)) {
                // Make the query:
                $q = "INSERT INTO members
                      (id_user, id_team, primary_position, secondary_position, jersey_number)
                      VALUES
                      ({$this->_dbObject->cleanInteger($this->id_user)}, {$this->_dbObject->cleanInteger($teamID)}, '{$this->_dbObject->realEscapeString($ppos)}', '{$this->_dbObject->realEscapeString($spos)}', {$this->_dbObject->cleanInteger($jnumb)})";

                // Execute the query:
                $this->_dbObject->query($q);

                if ($this->_dbObject->getNumRowsAffected() == 1) { // It ran ok
                    echo '<div class="alert alert-success">Member was added successfully</div>';
                }
                else {
                    echo '<div class="alert alert-error">Oh Snap! Member was not added. Please contact the service administrator</div>';
                }
            }
            else {
                // If player doesn't exist in user table, add skeleton informatoin to user table, add them to sport_table, & send invitation.        

                // Boolean used for invitation column, setting to True for invited
                $iv = 1;

                // Make the query to add new user to database
                $q = "INSERT INTO users
                      (
                        first_name,
                        last_name,
                        email,
                        sex,
                        invited
                      )
                      VALUES
                      (
                        '{$this->_dbObject->realEscapeString($fn)}',
                        '{$this->_dbObject->realEscapeString($ln)}',
                        '{$this->_dbObject->realEscapeString($userEmail)}',
                        {$this->_dbObject->cleanInteger($sex)},
                        {$this->_dbObject->cleanInteger($iv)}
                      )";

                // Execute the query:
                $this->_dbObject->query($q);
                
                if($this->_dbObject->getNumRowsAffected() == 1) { // It ran OK.

                    $newuserID = $this->_dbObject->getLastInsertId();

                    // Make the query:
                    $q = "INSERT INTO members
                          (
                            id_user,
                            id_team,
                            primary_position,
                            secondary_position,
                            jersey_number
                          )
                          VALUES
                          (
                            {$this->_dbObject->cleanInteger($newuserID)},
                            {$this->_dbObject->cleanInteger($teamID)},
                            '{$this->_dbObject->realEscapeString($ppos)}',
                            '{$this->_dbObject->realEscapeString($spos)}',
                            {$this->_dbObject->cleanInteger($jnumb)}
                          )";

                    // Execute the query:
                    $this->_dbObject->query($q);

                    if($this->_dbObject->getNumRowsAffected() == 1) { // It ran ok
                        echo '<div class="alert alert-success">Member was added successfully</div>';
                    }
                    else {
                        echo '<div class="alert alert-error">Oh Snap! Member was not added. Please contact the service administrator</div>';
                        exit();
                    }

                    // Send invitation if have permission
                    if($invite = 1) {
                        // SEND GRID INSERT
                        // Add conditional here somehow so that Manager has option to submit request or not
                        // Send the invitation email
                        $body = "Hello, you have been invited to join digoro!\n\nWe are a site devoted to connecting players and teams.\n\n 
                            You can find more information at our website:";
                        $body .= "\n" . BASE_URL;
                        mail($userEmail, 'digoro.com - Digoro Invitation', $body);

                        echo '<div class="alert alert-success">Invitation successfully sent!</div>';
                    }
                    else {
                        echo '<div class="alert alert-error">Oh Snap! Invitation was not sent</div>';
                    }
                }
                else {    // Registration process did not run OK.
                    echo '<div class="alert alert-error">Oh Snap! Registration process didnt work. Please contact service administrator</div>';
                }
            }
        } // End of createMember function


        // Edit Member Method
        public function editMember($memberid, $fn, $ln, $sex, $ppos, $spos, $jnumb) 
        {
            // Update the user's info in the database
            $q = "UPDATE members
                  SET primary_position = '{$this->_dbObject->realEscapeString($ppos)}',
                      secondary_position = '{$this->_dbObject->realEscapeString($spos)}',
                      jersey_number = {$this->_dbObject->cleanInteger($jnumb)}
                  WHERE id_member = {$this->_dbObject->cleanInteger($memberid)}
                  LIMIT 1";

            // Execute the query:
            $this->_dbObject->query($q);

            if($this->_dbObject->getNumRowsAffected() == 1) { // And update to the database was made
                echo '<div class="alert alert-success">This member has been edited</div>';
            }
            else { // Either did not run ok or no updates were made
                echo '<div class="alert">No changes were made</div>';
            }

            // If user hasn't registered with the site, update user table on their behalf
            if(isset($this->id_user) && !$this->isRegistered($this->id_user)) {
                // Make the query
                $q = "UPDATE users
                      SET first_name = '{$this->_dbObject->realEscapeString($fn)}',
                          last_name = '{$this->_dbObject->realEscapeString($ln)}',
                          sex = {$this->_dbObject->cleanInteger($sex)}
                      WHERE id_user = {$this->_dbObject->cleanInteger($this->id_user)}
                      LIMIT 1";

                // Execute the statement
                $this->_dbObject->query($q);
            }
        } // End of editMember function


        // Function to delete member
        public function deleteMember($memberid)
        {
            // Make the query    
            $q = "DELETE FROM members WHERE id_member = {$this->_dbObject->cleanInteger($memberid)} LIMIT 1";

            // Execute the query:
            $this->_dbObject->query($q);

            // If the query ran ok.
            if($this->_dbObject->getNumRowsAffected() == 1) {
                echo '<div class="alert alert-success">This member has been deleted successfully</div>';            
            }
            else {    // If the query did not run ok.
                echo '<div class="alert alert-error">Oh Snap! The member could not be deleted. Please contact the system administrator.</div>';
                exit();
            }
        } // End of deleteMember function        


        // Function to check if user is the manager
        public function isManager($userID, $lookupID = null)
        {
            // Make the query to retrieve all teams associated with member and selected team
            $q = "SELECT tm.{$this->_userIdColumn}
                  FROM teams AS tm
                    INNER JOIN members AS p USING (id_team)
                  WHERE p.id_member = {$this->_dbObject->cleanInteger($lookupID)}
                  LIMIT 1";

            // Execute the query and store result
            $result = $this->_dbObject->getOne($q);

            return ($result  == $userID);
        } // End of isManager function


        // Function to check if requested member is in database
        public function inUserDatabase($userEmail)
        {
            // Make the query    
            $q = "SELECT id_user
                  FROM users
                  WHERE email = '{$this->_dbObject->realEscapeString($userEmail)}'
                  LIMIT 1";

            // Execute the query and store result
            $result = $this->_dbObject->getOne($q);

            if($result !== false) {
                $this->id_user = $result;
                return true;
            }
            else {
                return false;
            }
        } // End of inUserDatabase function


        // Function to check if member has registered with site
        public function isRegistered($memberID)
        {
            // Make the query
            $q = "SELECT login_before FROM users WHERE id_user = {$this->_dbObject->cleanInteger($memberID)} LIMIT 1";

            // Execute the query and store result
            $result = $this->_dbObject->getOne($q);

            return ($result == 1);   
        } // End of isRegistered function
    } // End of Class