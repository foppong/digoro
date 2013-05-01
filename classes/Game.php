<?php
    /* This page defines the Game class.
     * Attributes:
     *  protected date
     *  protected time
     *  ptotected opponent
     *  protected venue
     *  protected result
     *  protected id_team
     *  protected note
     * 
     * Methods:
     *  setGameID()
     *  setGameAttributes()
     *  getGameAttribute()
     *  pullGameData()
     *  createGame()
     *  editGame()
     *  deleteGame()
     *  isManager()
     */


    class Game extends DigoroObject {

        // Declare the attributes
        protected $gdate, $gtime, $opponent, $venue, $result, $id_team, $note;

        protected $_mainTable = 'games';
        protected $_mainTablePrimaryKey = 'id_game';


        // Function to set game ID attribute
        public function setGameID($gameID)
        {
            $this->id_game = $gameID;
        }


        // Function to set Game attributes
        public function setGameAttributes($tmID = 0, $gmdate = '', $gmtime = '', $opp ='', $ven = '', $res = '', $gnote = '')
        {
            $this->id_team = $tmID;
            $this->gdate = $gmdate;
            $this->gtime = $gmtime;
            $this->opponent = $opp;
            $this->venue = $ven;
            $this->result = $res;
            $this->note = $gnote;
        }


        public function getGameAttribute($attribute)
        {
            return $this->$attribute;
        }


        // Function to pull complete game data from database and set attributes
        public function pullGameData()
        {
            // Make the query to retreive game information from games table in database:        
            $q = "SELECT id_team, date, time, opponent, venue, result, note
                  FROM games
                  WHERE id_game = {$this->id_game}
                  LIMIT 1";

            // Execute the query & store result
            $result = $this->dbObject->getRow($q);

            // Found result
            if($result !== false) {
                $this->setGameAttributes($result['id_team'], $result['date'], $result['time'],
                                         $result['opponent'], $result['venue'], $result['result'],
                                         $result['note']);
            }
        } // End of pullGameData function


        // Function to add game to team schedule
        public function createGame($teamID, $gmdate, $gtime, $opponent, $venue, $result, $note)
        {
            // Make the query:
            $q = "INSERT INTO games
                  (
                    id_team,
                    date,
                    time,
                    opponent,
                    venue,
                    result,
                    note
                  )
                  VALUES
                  (
                    {$teamID},
                    '{$this->dbObject->realEscapeString($gmdate)}',
                    '{$this->dbObject->realEscapeString($gtime)}',
                    '{$this->dbObject->realEscapeString($opponent)}',
                    '{$this->dbObject->realEscapeString($venue)}',
                    '{$this->dbObject->realEscapeString($result)}',
                    '{$this->dbObject->realEscapeString($note)}'
                  )";

            // Execute the query:
            $this->dbObject->query($q);

            // Print a message based upon result:
            if($this->dbObject->getNumRowsAffected() == 1) {
                echo 'Your game was added succesfully';
            }
            else {
                echo 'Your game was not added. Please contact the service administrator';
            }
        }


        // Edit Game Method
        public function editGame($userID, $gmdate, $gtime, $opponent, $venue, $result, $note)
        {
            // Make query
            $q = "UPDATE games
                  SET date = '{$this->dbObject->realEscapeString($gmdate)}',
                      time = '{$this->dbObject->realEscapeString($gtime)}',
                      opponent = '{$this->dbObject->realEscapeString($opponent)}',
                      venue = '{$this->dbObject->realEscapeString($venue)}',
                      result = '{$this->dbObject->realEscapeString($result)}',
                      note = '{$this->dbObject->realEscapeString($note)}'
                  WHERE id_game = {$this->id_game}
                  LIMIT 1";

            // Execute the query:
            $this->dbObject->query($q);

            if($this->dbObject->getNumRowsAffected() == 1) { // And update to the database was made
                echo 'This game has been edited';

                // Update attributes
                $this->setGameAttributes($this->id_team, $gmdate, $gtime, $opponent, $venue, $result, $note);
            }
            else { // Either did not run ok or no updates were made
                echo 'No changes were made';
            }
        } // End of editGame function


        // Function to delete game
        public function deleteGame($userID)
        {
            // Make the query    
            $q = "DELETE FROM games WHERE id_game = {$this->id_game} LIMIT 1";

            // Execute the query:
            $this->dbObject->query($q);

            // If the query ran ok.
            if($this->dbObject->getNumRowsAffected() == 1) {
                // Print a message
                echo 'This game has been deleted successfully';
            }
            else { // If the query did not run ok.
                echo 'The game could not be deleted due to a system error';
            }
        } // End of deleteGame function        


        // Function to check if user is manager of game
        public function isManager($userID)
        {
            // Make the query to retreive manager id associated with game:
            $q = "SELECT tm.id_manager
                  FROM teams AS tm
                    INNER JOIN games AS g USING (id_team)
                  WHERE g.id_game = {$this->id_game}
                  LIMIT 1";

            // Execute the query & store result
            $result = $this->dbObject->getOne($q);

            return ($result == $userID);
        } // End of isManager function
    } // End of Class
