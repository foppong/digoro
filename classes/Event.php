<?php
    /* This page defines the Event class.
     * Attributes:
     *  protected date
     *  protected time
     *  ptotected opponent
     *  protected venue_name
     *  protected venue_address
     *  protected result
     *  protected id_team
     *  protected note
     *  protected type
     * 
     * Methods:
     *  setEventID()
     *  setEventAttributes()
     *  getEventAttribute()
     *  pullEventData()
     *  createEvent()
     *  editEvent()
     *  deleteEvent()
     *  isManager()
     */


    class Event extends DigoroObject {

        // Declare the attributes
        protected $gdate, $gtime, $opponent, $v_name, $v_address, $result, 
            $id_team, $note, $type;

        protected $_mainTable = 'events';
        protected $_mainTablePrimaryKey = 'id_event';

        // Function to set event ID attribute
        public function setEventID($eventID)
        {
            $this->_id = $eventID;
        }


        // Function to set Event attributes
        public function setEventAttributes($tmID = 0, $gmdate = '', $gmtime = '', $opp ='', $ven = '',
                                            $venad = '', $res = '', $gnote = '', $typ = 0)
        {
            $this->id_team = $tmID;    
            $this->gdate = $gmdate;
            $this->gtime = $gmtime;
            $this->opponent = $opp;
            $this->v_name = $ven;
            $this->v_address = $venad;
            $this->result = $res;
            $this->note = $gnote;
            $this->type = $typ;
        }


        public function getEventAttribute($attribute)
        {
            return $this->$attribute;
        }


        // Function to pull complete event data from database and set attributes
        public function pullEventData()
        {
            // Make the query to retreive event information from events table in database:        
            $q = "SELECT id_team, date, time, opponent, venue_name,
                         venue_address, result, note, type
                  FROM events
                  WHERE id_event = {$this->_dbObject->cleanInteger($this->_id)}
                  LIMIT 1";

            // Execute the query & store the result
            $result = $this->_dbObject->getRow($q);

            // Found result
            if($result !== false) {
                $this->setEventAttributes($result['id_team'], $result['date'], $result['time'],
                                          $result['opponent'], $result['venue_name'],
                                          $result['venue_address'], $result['result'],
                                          $result['note'], $result['type']);
            }
        } // End of pullEventData function


        // Function to add event to team schedule
        public function createEvent($teamID, $gmdate, $gtime, $opponent, $ven, $venad, $note, $type)
        {
            // Make the query:
            $q = "INSERT INTO events
                  (
                   id_team,
                   date,
                   time,
                   opponent,
                   venue_name,
                   venue_address,
                   note,
                   type
                  )
                  VALUES
                  (
                    {$this->_dbObject->cleanInteger($teamID)},
                    '{$this->_dbObject->realEscapeString($gmdate)}',
                    '{$this->_dbObject->realEscapeString($gtime)}',
                    '{$this->_dbObject->realEscapeString($opponent)}',
                    '{$this->_dbObject->realEscapeString($ven)}',
                    '{$this->_dbObject->realEscapeString($venad)}',
                    '{$this->_dbObject->realEscapeString($note)}',
                    {$this->_dbObject->cleanInteger($type)}
                  )";

            // Execute the query:
            $this->_dbObject->query($q);

            // Print a message based upon result:
            if($this->_dbObject->getNumRowsAffected() == 1) {
                echo '<div class="alert alert-success">Your event was added succesfully</div>';
            }
            else {
                echo '<div class="alert alert-error">Oh Snap! Your event was not added. Please contact the service administrator</div>';
            }
        }


        // Edit Event Method
        public function editEvent($userID, $gmdate, $gtime, $opponent, $ven, $venad, $result, $note, $type)
        {
            // Make query
            $q = "UPDATE events
                  SET date = '{$this->_dbObject->realEscapeString($gmdate)}',
                      time = '{$this->_dbObject->realEscapeString($gtime)}',
                      opponent = '{$this->_dbObject->realEscapeString($opponent)}',
                      venue_name = '{$this->_dbObject->realEscapeString($ven)}',
                      venue_address = '{$this->_dbObject->realEscapeString($venad)}',
                      result = '{$this->_dbObject->realEscapeString($result)}',
                      note = '{$this->_dbObject->realEscapeString($note)}',
                      type = {$this->_dbObject->cleanInteger($type)}
                  WHERE id_event = {$this->_dbObject->cleanInteger($this->_id)}
                  LIMIT 1";

            // Execute the query:
            $this->_dbObject->query($q);

            if($this->_dbObject->getNumRowsAffected() == 1) { // And update to the database was made
                echo '<div class="alert alert-success">This event has been edited</div>';
            }
            else { // Either did not run ok or no updates were made
                echo '<div class="alert">No changes were made</div>';
            }
        } // End of editEvent function


        // Function to delete event
        public function deleteEvent($eventid)
        {
            // Make the query    
            $q = "DELETE FROM events WHERE id_event = {$this->_dbObject->cleanInteger($eventid)} LIMIT 1";

            //Execute the query
            $this->_dbObject->query($q);

            // If the query ran ok.
            if($this->_dbObject->getNumRowsAffected() == 1) {
                // Print a message
                echo '<div class="alert alert-success">This event has been deleted successfully</div>';
            }
            else { // If the query did not run ok.
                echo '<div class="alert alert-error">Oh Snap! Your event was not deleted. Please contact the service administrator</div>';
            }
        } // End of deleteEvent function


        // Function to check if user is manager of event
        public function isManager($userID, $lookupID = null)
        {
            // Make the query to retreive manager id associated with event:
            $q = "SELECT tm.id_user
                  FROM teams AS tm
                    INNER JOIN events AS g USING (id_team)
                  WHERE g.id_event = {$this->_dbObject->cleanInteger($lookupID)}
                  LIMIT 1";

            // Exeecute the query & store result:
            $result = $this->_dbObject->getOne($q);

            return ($result == $userID);
        } // End of isManager function
    } // End of Class