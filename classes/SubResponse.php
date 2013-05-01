<?php
    /* This page defines the SubResponse class.
     * Attributes:
     * 
     * 
     * Methods:
     * 
     *  
     */

    class SubResponse extends DigoroObject {

        // Declare the attributes
        protected $id_sr_response, $id_subrequest, $id_user, $id_event, $manager_respond, 
            $manager_response, $comments, $did_showup;


        // Function to set SubResponse ID attribute
        public function setSRRespID($IDsubresponse)
        {
            $this->id_sr_response = $IDsubresponse;
        }


        // Function to set SubRequest ID attribute
        public function setSubReqID($IDsubrequest)
        {
            $this->id_subrequest = $IDsubrequest;
        }


        // Function to set SubRequest object attributes
        public function setSRRAttributes($userID = 0, $evntID = 0, $mrespond = 0, $mresponse = '', $com = '', $didsh = 0)
        {
            $this->id_user = $userID;
            $this->id_event = $evntID;
            $this->manager_respond = $mrespond;                        
            $this->manager_response = $mresponse;
            $this->comments = $com;
            $this->did_showup = $didsh;
        }


        // Function to get specific SubRequest object attribute
        public function getSRRAttribute($attribute)
        {
            return $this->$attribute;
        }


        // Function to create a SubResponse object
        public function createSubReqResp($userID, $evntID, $com)
        {
            // Make the query
            $q = "INSERT INTO subreq_responses
                  (id_user, id_subrequest, id_event, comments)
                  VALUES
                  ({$userID}, {$this->id_subrequest}, {$evntID}, '{$this->_dbObject->realEscapeString($com)}')";

            // Execute the query
            $this->_dbObject->query($q);

            // Successfully added subResponse
            if($this->_dbObject->getNumRowsAffected() == 1) {
                echo '<div class="alert alert-success">SubResponse was created successfully</div>';
            }
            else {
                echo '<div class="alert alert-error">SubResponse was not added. Please contact the service administrator</div>';
            }
        } // End of createSubReqResp function


        // Function for manager to confirm SubResponse
        public function confirmSubReqResp($subresponseID, $man_comments)
        {
            $didrespond = 1;

            // Make the query
            $q = "UPDATE subreq_responses
                  SET manager_respond = {$didrespond},
                      manager_response = '{$this->_dbObject->realEscapeString($man_comments)}'
                  WHERE id_sr_response= {$subresponseID}";

            // Execute the query
            $this->_dbObject->query($q);

            // Successfully added subResponse
            if($this->_dbObject->getNumRowsAffected() == 1) {
                echo '<div class="alert alert-success">SubResponse was confirmed</div>';

                // SEND COMMENTS VIA EMAIL VIA SENDGRID HERE -write separate
                // function to query database and retrieve manager comments and email
            }
            else {
                echo '<div class="alert alert-error">SubResponse was not confirmed. Please contact the service administrator</div>';
            }
        } // End of confirmSubReqResp function


        // Function for manager to confirm SubResponse
        public function declineSubReqResp($subresponseID, $man_comments)
        {
            $didrespond = 2;

            // Make the query
            $q = "UPDATE subreq_responses
                  SET manager_respond = {$didrespond},
                      manager_response = '{$this->_dbObject->realEscapeString($man_comments)}'
                  WHERE id_sr_response = {$subresponseID}";

            // Execute the query
            $this->_dbObject->query($q);

            // Successfully added subResponse
            if($this->_dbObject->getNumRowsAffected() == 1) {
                echo '<div class="alert alert-success">SubResponse was declined</div>';

                // SEND COMMENTS VIA EMAIL VIA SENDGRID HERE -write separate 
                // function to query database and retrieve manager comments and email
            }
            else {
                echo '<div class="alert alert-error">SubResponse was not declined. Please contact the service administrator</div>';
            }
        } // End of confirmSubReqResp function        


        // Function to cancel a SubResponse object
        public function cancelSubResponse($subresponseID, $user_comments)
        {
            // Make the query    
            $q = "DELETE FROM subreq_responses WHERE id_sr_response = {$subresponseID} LIMIT 1";

            // Execute the query:
            $this->_dbObject->query($q);

            // If the query ran ok.
            if($this->_dbObject->getNumRowsAffected() == 1) {
                // ********** SEND EMAIL HERE WITH USER COMMENTS ************
                
                // Print a message
                echo '<div class="alert alert-success">This subrequest response was cancelled successfully</div>';
            }
            else 
            {    // If the query did not run ok.
                echo '<div class="alert alert-error">The subrequest response was not cancelled. Please contact the service administrator</div>';
            }
        } // End of deleteSubReqResp function


        // Function to pull current data from database and set attributes
        public function pullSubReqRespData()
        {
            
        } // End of pullSubReqRespData function


        // Function to check if SubResponse is current
        public function isSRcurrent()
        {
            
        }
    } // End of Class