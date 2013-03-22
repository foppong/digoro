<?php
    /* This page defines the User class and extends the UserAuth class.
     * Attributes:
     *  protected fn (first name)
     *  protected ln (last name)
     *  protected role
     *  protected city
     *  protected state
     *  protected zp (zip code)
     *  protected gd (sex)
     *     protected email
     *  protected pass
     *  protected rdate (registration date)
     *  protected bday (birthday)
     *  protected pnum (phone number)
     *  protected digscore (digoro score)
     *  protected invited
     *  protected dftmID (default team id)
     *  protected lb (login in before)
     * 
     *     
     * Methods:
     *  setUserAttributes()
     *  getUserAttribute()
     *  viewTeams()
     *  countTeams()
     *  setDefaultTeam()
     *  viewSchedule()
     *  isNewUser()
     *  editAccount()
     *  viewRoster()
     *  pullUserData()
     *  pullSpecificData()
     *  updateUserAcct()
     *  updateLoginBefore()
     *  updateUserRole() 
     */

    class User extends UserAuth {
         
        // Declare the attributes
        protected $fn, $ln, $role, $city, $state, $zp, $gd, $email,
            $pass, $rdate, $bday, $pnum, $digscore, $invited, $dftmID, $lb;

        // Constructor
        public function __construct($dbObject, $userID) 
        {
            parent::__construct($dbObject);
            $this->setUserID($userID);
            //$this->pullUserData(); // Pull current database information and set attributes
        }

        // Function to set the User attributes
        public function setUserAttributes($fnIN='', $lnIN='', $roleIN='', $cityIN='', $stIN='', $zpIN=0, $gdIN='', $emailIN='',
            $passIN='', $rdateIN='', $bdayIN='', $pnumIN=0, $digscoreIN=0, $invIN=0, $dftmIN=0, $lbIN=0)
        {
            $this->fn = $fnIN;
            $this->ln = $lnIN;
            $this->role = $roleIN;
            $this->city = $cityIN;
            $this->state = $stIN;
            $this->zp = $zpIN;
            $this->gd = $gdIN;
            $this->email = $emailIN;
            $this->pass = $passIN;
            $this->rdate = $rdateIN;
            $this->bday = $bdayIN;
            $this->pnum = $pnumIN;
            $this->digscore = $digscoreIN;
            $this->invited = $invIN;
            $this->dftmID = $dftmIN;
            $this->lb = $lbIN;
        }


        public function getUserAttribute($attribute)
        {
            return $this->$attribute;
        }


        // Function to view associated teams
        public function viewTeams()
        {

        }


        // Function to count number of teams associated with user
        public function countTeams()
        {
            // Make query to count the number of teams associated with user
            $q = "SELECT COUNT(id_team) FROM members WHERE id_user = {$this->id_user}";
            
            //return the value
            return $this->dbObject->getOne($q);
        }


        // Function to set default team
        public function setDefaultTeam($teamID)
        {
            // Update the user's info in the database
            $q = "UPDATE users
                  SET default_teamID = {$teamID}
                  WHERE id_user = {$this->id_user}
                  LIMIT 1";
         
            // Execute the query:
            $this->dbObject->query($q);

            if($this->dbObject->getNumRowsAffected() == 1) { // It ran ok
                echo '<div class="alert alert-success">Default team successfully changed!</div>';
                $this->pullUserData(); // Update object attributes
            }
            else { // Either did not run ok or no updates were made
                echo '<div class="alert">Default team not changed.</div>';
            }
        } // End of setDefaultTeam function


        // Function to view schedule of team
        public function viewSchedule()
        {

        }


        // Function to check if new user
        public function isnewUser()
        {

        }


        // Function to edit account settings
        public function editAccount()
        {

        }


        // Function to view roster
        public function viewRoster()
        {

        }


        // Function to pull complete user data from database and set all attributes
        public function pullUserData()
        {
            // Make the query
            $q = "SELECT first_name, last_name, role, city, state, zipcode,
                         sex, email, pass, registration_date, birth_date,
                         phone_num, digoro_score, invited, default_teamID,
                         login_before
                  FROM users
                  WHERE id_user = {$this->id_user}
                  LIMIT 1";
 
            // Execute the query and store the result
            $result = $this->dbObject->getRow($q);

            // Found result
            if($result !== false) {
                $this->setuserAttributes($result['first_name'], $result['last_name'],
                                         $result['role'], $result['city'],
                                         $result['state'], $result['zipcode'],
                                         $result['sex'], $result['email'], $result['pass'],
                                         $result['registration_date'], $result['birth_date'],
                                         $result['phone_num'], $result['digoro_score'],
                                         $result['invited'], $result['default_teamID'],
                                         $result['login_before']);
            }

        } // End of pullUserData function


        // Function to pull specific user data from database
        public function pullSpecificData($datacolumn)
        {
            // Make the query
            $q = "SELECT {$datacolumn}
                  FROM users
                  WHERE id_user = {$this->id_user}
                  LIMIT 1";

            // Prepare the statement
            return $this->dbObject->getOne($q);
        } // End of pullSpecificData function


        // Function to update user information in database
        public function updateUserAcct($e, $fn, $ln, $cty, $st, $zp, $gd, $bdfrmat, $pnumb) {
            // Update the user's info in the database
            $q = "UPDATE users
                  SET email = '{$this->dbObject->realEscapeString($e)}',
                      first_name = '{$this->dbObject->realEscapeString($fn)}',
                      last_name = '{$this->dbObject->realEscapeString($ln)}',
                      city = '{$this->dbObject->realEscapeString($cty)}',
                      state = '{$this->dbObject->realEscapeString($st)}',
                      zipcode = {$zp},
                      sex = '{$this->dbObject->realEscapeString($gd)}',
                      birth_date = '{$this->dbObject->realEscapeString($bdfrmat)}',
                      phone_num = {$pnumb}
                WHERE id_user = {$this->id_user}
                LIMIT 1";

            // Execute the query:
            $this->dbObject->query($q);

            if($this->dbObject->getNumRowsAffected() == 1) { // And update to the database was made      
                echo '<div class="alert alert-success">The users account has been edited.</div>';
                $this->pullUserData(); // Update object attributes
            }
            else { // Either did not run ok or no updates were made
                echo '<div class="alert">No changes were made.</div>';
            }
        } // End of updateUserAcct function


        // Function to update the loginbefore variable in the database
        public function updateLoginBefore() {
            // Variable for us to update the database with
            $setTrue = 1;

            // Update the user's info in the database
            $q = "UPDATE users SET login_before = {$setTrue} WHERE id_user = {$this->id_user} LIMIT 1";

            // Execute the query:
            $this->dbObject->query($q);

            if($this->dbObject->getNumRowsAffected() == 1) {// And update to the database was made
                echo '<div class="alert alert-success">The users account has been updated.</div>';
            }
            else { 
                // Either did not run ok or no updates were made
                echo '<div class="alert">No changes were made.</div>';
            }
        } // End of updateLoginBefore function      

     
        // Function to update the user role in the database
        public function updateUserRole($role) {

            // Update the user's info in the database
            $q = "UPDATE users
                  SET role = '{$this->dbObject->realEscapeString($role)}'
                  WHERE id_user = {$this->id_user}
                  LIMIT 1";

            // Execute the query:
            $this->dbObject->query($q);

            if($this->dbObject->getNumRowsAffected() == 1) {// And update to the database was made
                echo '<div class="alert alert-success">The users account has been updated.</div>';
            }
            else { 
                // Either did not run ok or no updates were made
                echo '<div class="alert">No changes were made.</div>';
            }
        } // End of updateUserRole function

    } // End of Class