<?php
	/* This page defines the Game class.
	 * Attributes:
	 * 	protected date
	 * 	protected time
	 * 	ptotected opponent
	 * 	protected venue
	 *  protected result
	 *  protected id_team
	 * 	protected id_game
	 *  protected dbc
	 *  protected note
	 * 
	 * Methods:
	 *  setGameID()
	 *  setDB()
	 *	setGameAttributes()
	 *  getGameAttribute()
	 *  pullGameData()
	 *  createGame()
	 *  editGame()
	 *  deleteGame()
	 *	checkAuth()
	 *  isManager()
	 */
		

	class Game {
	 	
		// Declare the attributes
		protected $gdate, $gtime, $opponent, $venue, $result, $id_team, $id_game, $dbc, $note;

		// Constructor
		function __construct() {}

		// Function to set game ID attribute
		function setGameID($gameID)
		{
			$this->id_game = $gameID;
		}

		// Set database connection attribute
		function setDB($db)
		{
			$this->dbc = $db;
		}		
		
		// Function to set Game attributes
		function setGameAttributes($tmID = 0, $gmdate = '', $gmtime = '', $opp ='', $ven = '', $res = '', $gnote = '')
		{
			$this->id_team = $tmID;	
			$this->gdate = $gmdate;
			$this->gtime = $gmtime;
			$this->opponent = $opp;
			$this->venue = $ven;
			$this->result = $res;
			$this->note = $gnote;
		}		

		function getGameAttribute($attribute)
		{
			return $this->$attribute;
		}
		
		// Function to pull complete game data from database and set attributes
		function pullGameData()
		{
			// Make the query to retreive game information from games table in database:		
			$q = "SELECT id_team, date, time, opponent, venue, result, note
				FROM games
				WHERE id_game=? LIMIT 1";
		
			// Prepare the statement: DATE_FORMAT(date, '%Y-%m-%d')
			$stmt = $this->dbc->prepare($q);
		
			// Bind the inbound variable:
			$stmt->bind_param('i', $this->id_game);
				
			// Execute the query:
			$stmt->execute();		
				
			// Store results:
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($id_teamOB, $gdateOB, $gtmOB, $oppOB, $venOB, $resOB, $noteOB);

			// Found result
			if ($stmt->num_rows == 1)
			{	
				while ($stmt->fetch())
				{				
					self::setGameAttributes($id_teamOB, $gdateOB, $gtmOB, $oppOB, $venOB, $resOB, $noteOB);
				}
			}			
			
			// Close the statement
			$stmt->close();
			unset($stmt);			

		} // End of pullGameData function

		// Function to add game to team schedule
		function createGame($teamID, $gmdate, $gtime, $opponent, $venue, $result, $note)
		{
			// Make the query:
			$q = 'INSERT INTO games (id_team, date, time, opponent, venue, result, note) 
				VALUES (?,?,?,?,?,?,?)';

			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('issssss', $teamID, $gmdate, $gtime, $opponent, $venue, $result, $note);
			
			// Execute the query:
			$stmt->execute();
			
			// Print a message based upon result:
			if ($stmt->affected_rows == 1)
			{
				echo 'Your game was added succesfully';
			}
			else
			{
				echo 'Your game was not added. Please contact the service administrator';
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);			
		}
		
							
		// Edit Game Method
		function editGame($userID, $gmdate, $gtime, $opponent, $venue, $result, $note) 
		{		
			// Update the user's info in the players' table in database
			$q = 'UPDATE games SET date=?, time=?, opponent=?, venue=?, result=?, note=?
				WHERE id_game=? LIMIT 1';
	
			// Prepare the statement
			$stmt = $this->dbc->prepare($q); 
	
			// Bind the inbound variables:
			$stmt->bind_param('ssssssi', $gmdate, $gtime, $opponent, $venue, $result, $note, $this->id_game);
					
			// Execute the query:
			$stmt->execute();
	
			if ($stmt->affected_rows == 1) // And update to the database was made
			{				
				echo 'This game has been edited';

				// Update attributes
				self::setGameAttributes($this->id_team, $gmdate, $gtime, $opponent, $venue, $result, $note);
			}
			else 
			{	// Either did not run ok or no updates were made
				echo 'No changes were made';
			}
		
			// Close the statement:
			$stmt->close();
			unset($stmt);

		} // End of editGame function
		

		// Function to delete game
		function deleteGame($userID)
		{
			// Make the query	
			$q = "DELETE FROM games WHERE id_game=? LIMIT 1";
	
			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);
	
			// Bind the inbound variable:
			$stmt->bind_param('i', $this->id_game);
	
			// Execute the query:
			$stmt->execute();
				
			// If the query ran ok.
			if ($stmt->affected_rows == 1) 
			{
				// Print a message
				echo 'This game has been deleted successfully';
			}
			else 
			{	// If the query did not run ok.
				echo 'The game could not be deleted due to a system errror';
			}
				
			// Close the statement:
			$stmt->close();
			unset($stmt);
		
		} // End of deleteGame function

		// Function to check if user is authroized to view page (need to have a game created first)
		function checkAuth($userID)
		{
			if (self::isManager($this->id_game, $userID) == False)
			{
				$url = BASE_URL . 'manager/manager_home.php';
				header("Location: $url");
				exit();
			}		
		}			
			
		// Function to check if user is manager of game
		function isManager($gameID, $userID)
		{
			// Make the query to retreive manager id associated with game:		
			$q = "SELECT tm.id_manager
				FROM teams AS tm INNER JOIN games AS g
				USING (id_team)
				WHERE g.id_game=? LIMIT 1";
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables:
			$stmt->bind_param('i', $gameID);
			
			// Exeecute the query
			$stmt->execute();
			
			// Store results:
			$stmt->store_result();
			
			// Bind the outbound variables:
			$stmt->bind_result($manIDOB);
			
			// user ID found
			if ($stmt->num_rows == 1)
			{
				while ($stmt->fetch())
				{				
					if ($manIDOB == $userID) 
					{
						return True;
					}
					else 
					{
						return False;
					}
				}
			}
			else 
			{
				return False;
			}
			
			// Close the statement:
			$stmt->close();
			unset($stmt);
	
		} // End of isManager function
	
} // End of Class
	 
?>