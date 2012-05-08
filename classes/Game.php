<?php
	/* This page defines the Game class.
	 * Attributes:
	 * 	protected date
	 * 	protected time
	 * 	ptotected opponent
	 * 	protected venue
	 *  protected result
	 * 	protected id_game
	 * Methods:
	 * 	editGame()
	 * 	displayGame()
	 */
		

	class Game {
	 	
		// Declare the attributes
		protected $gdate, $gtime, $opponent, $venue, $result, $id_team, $id_game, $dbc;

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
		function setGameAttributes($tmID = 0, $gmdate = '', $gmtime = '', $opp ='', $ven = '', $res = '')
		{
			$this->id_team = $tmID;	
			$this->gdate = $gmdate;
			$this->gtime = $gmtime;
			$this->opponent = $opp;
			$this->venue = $ven;
			$this->result = $res;
		}		

		function getGameAttribute($attribute)
		{
			return $this->$attribute;
		}
		
		// Function to pull complete game data from database and set attributes
		function pullGameData()
		{
			// Make the query to retreive game information from games table in database:		
			$q = "SELECT date, time, opponent, venue, result
				FROM games
				WHERE id_game=? LIMIT 1";
		
			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);
		
			// Bind the inbound variable:
			$stmt->bind_param('i', $id);
				
			// Execute the query:
			$stmt->execute();		
				
			// Store results:
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($bdfrmatOB, $tmOB, $oppOB, $venOB, $resOB);

			// Found result
			if ($stmt->num_rows == 1)
			{	
				while ($stmt->fetch())
				{				
					self::setGameAttributes($bdfrmatOB, $tmOB, $oppOB, $venOB, $resOB);
				}
			}			
			
			// Close the statement
			$stmt->close();
			unset($stmt);			

		} // End of pullGameData function
					
		// Edit Game Method
		function editGame($userID, $gmdate, $gtime, $opponent, $venue, $result) 
		{
			if (self::checkAuth($this->id_game, $userID))
			{		
				// Update the user's info in the players' table in database
				$q = 'UPDATE games SET date=?, time=?, opponent=?, venue=?, result=?
					WHERE id_game=? LIMIT 1';
	
				// Prepare the statement
				$stmt = $this->dbc->prepare($q); 
	
				// Bind the inbound variables:
				$stmt->bind_param('sssssi', $gdate, $gmdate, $gtime, $opponent, $venue, $result, $this->id_game);
					
				// Execute the query:
				$stmt->execute();
	
				if ($stmt->affected_rows == 1) // And update to the database was made
				{				
					echo '<p>The game has been edited.</p>';
				}
				else 
				{	// Either did not run ok or no updates were made
					echo '<p>No changes were made.</p>';
				}

				// Close the statement:
				$stmt->close();
				unset($stmt);
			}
			else 
			{
				echo '<p class="error">This page has been accessed in error.</p>';
				include '../includes/footer.html';
				exit();				
			}
		} // End of editGame function
		
	
	// Function to check if user is authorized to make any changes
	function checkAuth($gameID, $userID)
	{
		// Make the query to retreive game and team info:		
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
			if ($manIDOB == $userID) 
			{
				return True;
			}
			else 
			{
				return False;
			}
		}
		else 
		{
			return False;
		}
		
		// Close the statement:
		$stmt->close();
		unset($stmt);

	} // End of checkAuth function
	
} // End of Class
	 
?>