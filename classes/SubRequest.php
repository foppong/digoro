<?php
    /* This page defines the SubRequest class.
     * Attributes:
     *  protected id_subrequest
     *  protected id_user
     *  protected id_team
     *  protected id_event
     *  protected sex_needed
     *  protected experience_needed
     *  protected id_region
     * 
     * Methods:
     *  setSubReqID()
     *  setSRAttributes()
     *  getSRAttribute()
     *  createSubReq()
     *  editSubReq()
     *  deleteSubReq()
     *  isManager()
     *  pullSubReqData()
     */

    class SubRequest extends DigoroObject {

        // Declare the attributes
        protected $id_subrequest, $id_user, $id_team, $id_event, $sex_needed, 
            $experience_needed, $id_region;


        // Function to set SubRequest ID attribute
        public function setSubReqID($srID)
        {
            $this->id_subrequest = $srID;
        }


        // Function to set SubRequest object attributes
        public function setSRAttributes($manID = 0, $tmID = 0, $evntID = 0, $sex ='', $exp = 0, $reg = 0)
        {
            $this->id_user = $manID;
            $this->id_team = $tmID;
            $this->id_event = $evntID;                        
            $this->sex_needed = $sex;
            $this->experience_needed = $exp;
            $this->id_region = $reg;
        }


        // Function to get specific SubRequest object attribute
        public function getSRAttribute($attribute)
        {
            return $this->$attribute;
        }


        // Function to create a SubRequest object
        public function createSubReq($manID, $tmID, $evntID, $sex, $exp, $reg)
        {
            // Make the query:
            $q = "INSERT INTO subrequests
                  (
                    id_user,
                    id_team,
                    id_event,
                    sex_needed,
                    experience_needed,
                    id_region
                  )
                  VALUES
                  (
                    {$manID},
                    {$tmID},
                    {$evntID},
                    '{$this->_dbObject->realEscapeString($sex)}',
                    {$exp},
                    {$reg}
                  )";

            // Execute the query
            $this->_dbObject->query($q);

            // Successfully added subrequest
            if($this->_dbObject->getNumRowsAffected() == 1) {
                echo '<div class="alert alert-success">Your subrequest was created succesfully</div>';
            }
            else {
                echo '<div class="alert alert-error">Your subrequest was not added. Please contact the service administrator</div>';
            }   
        } // End of createSubReq function


        // Function to edit a SubRequest object in database
        public function editSubReq($manID, $subReqid, $tmID, $evntID, $sex, $exp, $reg)
        {
            // Make the query:
            $q = "UPDATE subrequests
                  SET id_team = {$tmID},
                      id_event = {$evntID},
                      sex_needed = '{$this->_dbObject->realEscapeString($sex)}',
                      experience_needed = {$exp},
                      id_region = {$reg}
                  WHERE id_subrequest = {$subReqid}
                  LIMIT 1";

            // Execute the query
            $this->_dbObject->query($q);

            // Successfully added subrequest
            if($this->_dbObject->getNumRowsAffected() == 1) {
                echo '<div class="alert alert-success">Your subrequest was edited succesfully</div>';
            }
            else {
                // Either did not run ok or no updates were made
                echo '<div class="alert">No changes were made</div>';
            }
        } // End of editSubReq function


        // Function to delete subrequest
        public function deleteSubReq($subReqid)
        {
            // Make the query    
            $q = "DELETE FROM subrequests WHERE id_subrequest = {$subReqid} LIMIT 1";

            // Execute the query:
            $this->_dbObject->query($q);

            // If the query ran ok.
            if($this->_dbObject->getNumRowsAffected() == 1) {
                // Print a message
                echo '<div class="alert alert-success">This subrequest has been deleted successfully</div>';
            }
            else { // If the query did not run ok.
                echo '<div class="alert alert-error">The subrequest was not deleted</div>';
            }
        } // End of deleteSubReq function


        // Function to check if user is the manager
        public function isManager($userID, $lookupID = null)
        {
            // Make the query to retreive manager id associated with team:
            $q = "SELECT id_user
                  FROM teams
                  WHERE id_team = {$this->id_team}
                  LIMIT 1";

            // Exeecute the query
            $result = $this->_dbObject->getOne($q);

            return ($result == $userID);
        } // End of isManager function


        // Function to pull current SubRequest data from database & set attributes
        public function pullSubReqData()
        {
            // Make the query
            $q = "SELECT id_user, id_team, id_event, sex_needed,
                         experience_needed, id_region
                  FROM subrequests
                  WHERE id_subrequest = {$this->id_subrequest}
                  LIMIT 1";

            // Execute the query & store result
            $result = $this->_dbObject->getRow($q);

            // Found result
            if($result !== false) {
                $this->setSRAttributes($result['id_user'], $result['id_team'], $result['id_event'],
                                       $result['sex_needed'], $result['experience_needed'],
                                       $result['id_region']);
            }
        } // End of pullSubReqData function
    } // End of Class