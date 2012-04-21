<?php
	/* This page defines the Game class.
	 * Attributes:
	 * 	protected date
	 * 	protected time
	 * 	ptotected opponent
	 * 	protected venue
	 *  protected result
	 * 	protected id_sch
	 * Methods:
	 * 	editGame()
	 * 	displayGame()
	 */
		

	class Game {
	 	
		// Declare the attributes
		protected $gdate, $gtime, $opponent, $venue, $result, $id_team, $id_game, $db;

		// Constructor
		function __construct($inidteam, $indate, $intime, $indb, $inopp = '', $inven = '', $inres = '') {
			$this->id_team = $inidteam;	
			$this->gdate = $indate;
			$this->gtime = $intime;
			$this->opponent = $inopp;
			$this->venue = $inven;
			$this->result = $inres;
			$this->db = $indb;
		
			// Make the query:
			$q = 'INSERT INTO schedules (id_team, date, time, opponent, venue, result) VALUES (?,?,?,?,?,?)';

			// Prepare the statement
			$stmt = $db->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('isssss', $id_team, $gdate, $gtime, $opponent, $venue, $result);
			
			// Execute the query:
			$stmt->execute();
			
			// Print a message based upon result:
			if ($stmt->affected_rows == 1)
			{
				echo '<p>Your game was added succesfully.</p>';
			}
			else
			{
				echo '<p class="error">Your game was not added. Please contact the service administrator.</p>';
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);
			
			// Close the connection:
			$db->close();
			unset($db);		
		
		
		
		
		}
		
		// Edit Game Method
		function editGame($inidgame, $indate, $intime, $inopp = '', $inven = '', $inres = '') {

			$this->id_game = $inidteam;	
			$this->gdate = $indate;
			$this->gtime = $intime;
			$this->opponent = $inopp;
			$this->venue = $inven;
			$this->result = $inres;
			
			// Update the user's info in the players' table in database
			$q = 'UPDATE schedules SET date=?, time=?, opponent=?, venue=?, result=?
				WHERE id_sch=? LIMIT 1';

			// Prepare the statement
			$stmt = $db->prepare($q); 

			// Bind the inbound variables:
			$stmt->bind_param('sssssi', $gdate, $gtime, $opponent, $venue, $result, $inidgame);
				
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
		}
		
		
	}
	 
?>