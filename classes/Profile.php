<?php
    /* This page defines the Profile class.
     * Attributes:
     *  protected dbObject
     * 
     * Methods:
     */

    class Profile extends DigoroObject {

        // Declare the attributes
        protected $id_profile, $id_user, $team_sex_preference, $id_region, $id_sport, $sport_experience,
            $primary_position, $secondary_position, $comments;


        // Function to set Profile ID attribute
        public function setProfileID($profileID)
        {
            $this->id_profile = $profileID;
        }


        // Function to set Profile object attributes
        public function setPRAttributes($userID = 0, $tmSexPref = 0, $regID = 0, $sprtID = 0, $sprtexp = 0, $ppos = '', $spos = '', $comm = '')
        {
            $this->id_user = $userID;
            $this->team_sex_preference = $tmSexPref;
            $this->id_region = $regID;                        
            $this->id_sport = $sprtID;
            $this->sport_experience = $sprtexp;
            $this->primary_position = $ppos;
            $this->secondary_position = $spos;
            $this->comments = $comm;
        }


        // Function to get specific Profile object attribute
        public function getPRAttribute($attribute)
        {
            return $this->$attribute;
        }


        // Function to create a Profile object
        public function createProfile($userID, $tmSexPref, $regID, $sprtID, $sprtexp, $ppos, $spos, $comm)
        {
            // Make the query:
            $q = "INSERT INTO profiles
                  (
                    id_user,
                    team_sex_preference,
                    id_region,
                    id_sport,
                    sport_experience,
                    primary_position,
                    secondary_position,
                    comments
                  )
                  VALUES
                  (
                    {$this->_dbObject->cleanInteger($userID)},
                    {$this->_dbObject->cleanInteger($tmSexPref)},
                    {$this->_dbObject->cleanInteger($regID)},
                    {$this->_dbObject->cleanInteger($sprtID)},
                    {$this->_dbObject->cleanInteger($sprtexp)},
                    '{$this->_dbObject->realEscapeString($ppos)}',
                    '{$this->_dbObject->realEscapeString($spos)}',
                    '{$this->_dbObject->realEscapeString($comm)}'
                  )";

            // Execute the query
            $this->_dbObject->query($q);

            // Successfully added subrequest
            if($this->_dbObject->getNumRowsAffected() == 1) {
                echo '<div class="alert alert-success">Your profile was created succesfully</div>';
            }
            else {
                echo '<div class="alert alert-error">Your profile was not added. Please contact the service administrator</div>';
            }
        } // End of createProfile function


        // Function to edit a Profile object in database
        public function editProfile($userID, $tmSexPref, $regID, $sprtID, $sprtexp, $ppos, $spos, $comm)
        {
            // Make the query:
            $q = "UPDATE profiles
                  SET id_user = {$this->_dbObject->cleanInteger($userID)},
                      team_sex_preference = {$this->_dbObject->cleanInteger($tmSexPref)},
                      id_region = {$this->_dbObject->cleanInteger($regID)},
                      id_sport = {$this->_dbObject->cleanInteger($sprtID)},
                      sport_experience = {$this->_dbObject->cleanInteger($sprtexp)},
                      primary_position = '{$this->_dbObject->realEscapeString($ppos)}',
                      secondary_position = '{$this->_dbObject->realEscapeString($spos)}',
                      comments = '{$this->_dbObject->realEscapeString($comm)}'
                  LIMIT 1";

            // Execute the query
            $this->_dbObject->query($q);

            // Successfully added subrequest
            if($this->_dbObject->getNumRowsAffected() == 1) {
                echo '<div class="alert alert-success">Your profile was edited succesfully</div>';
            }
            else {
                // Either did not run ok or no updates were made
                echo '<div class="alert">No changes were made</div>';
            }
        } // End of editProfile function


        // Function to delete Profile
        public function deleteProfile($profileID)
        {
            // Make the query    
            $q = "DELETE FROM profiles WHERE id_profile = {$this->_dbObject->cleanInteger($profileID)} LIMIT 1";

            // Execute the query:
            $this->_dbObject->query($q);

            // If the query ran ok.
            if($this->_dbObject->getNumRowsAffected() == 1) {
                // Print a message
                echo '<div class="alert alert-success">This profile has been deleted successfully</div>';
            }
            else {    // If the query did not run ok.
                echo '<div class="alert alert-error">The profile was not deleted</div>';
            }
        } // End of deleteProfile function
    } // End of Class