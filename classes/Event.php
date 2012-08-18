<?php
	/* This page defines the Event class.
	 * Attributes:
	 * 	protected date
	 * 	protected time
	 * 	ptotected opponent
	 * 	protected venue
	 *  protected result
	 *  protected id_team
	 * 	protected id_event
	 *  protected dbc
	 *  protected note
	 * 
	 * Methods:
	 *  setEventID()
	 *  setDB()
	 *	setEventAttributes()
	 *  getEventAttribute()
	 *  pullEventData()
	 *  createEvent()
	 *  editEvent()
	 *  deleteEvent()
	 *  isManager()
	 */
		

	class Event {
	 	
		// Declare the attributes
		protected $gdate, $gtime, $opponent, $venue, $result, $id_team, $id_event, $dbc, $note;

		// Constructor
		function __construct() {}

		// Function to set event ID attribute
		function setEventID($eventID)
		{
			$this->id_event = $eventID;
		}

		// Set database connection attribute
		function setDB($db)
		{
			$this->dbc = $db;
		}		
		
		// Function to set Event attributes
		function setEventAttributes($tmID = 0, $gmdate = '', $gmtime = '', $opp ='', $ven = '', $res = '', $gnote = '')
		{
			$this->id_team = $tmID;	
			$this->gdate = $gmdate;
			$this->gtime = $gmtime;
			$this->opponent = $opp;
			$this->venue = $ven;
			$this->result = $res;
			$this->note = $gnote;
		}		

		function getEventAttribute($attribute)
		{
			return $this->$attribute;
		}
		
		// Function to pull complete event data from database and set attributes
		function pullEventData()
		{
			// Make the query to retreive event information from events table in database:		
			$q = "SELECT id_team, date, time, opponent, venue, result, note
				FROM events
				WHERE id_event=? LIMIT 1";
		
			// Prepare the statement: DATE_FORMAT(date, '%Y-%m-%d')
			$stmt = $this->dbc->prepare($q);
		
			// Bind the inbound variable:
			$stmt->bind_param('i', $this->id_event);
				
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
					self::setEventAttributes($id_teamOB, $gdateOB, $gtmOB, $oppOB, $venOB, $resOB, $noteOB);
				}
			}			
			
			// Close the statement
			$stmt->close();
			unset($stmt);			

		} // End of pullEventData function

		// Function to add event to team schedule
		function createEvent($teamID, $gmdate, $gtime, $opponent, $venue, $result, $note)
		{
			// Make the query:
			$q = 'INSERT INTO events (id_team, date, time, opponent, venue, result, note) 
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
				echo 'Your event was added succesfully';
			}
			else
			{
				echo 'Your event was not added. Please contact the service administrator';
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);			
		}
		
							
		// Edit Event Method
		function editEvent($userID, $gmdate, $gtime, $opponent, $venue, $result, $note) 
		{		
			// Make query
			$q = 'UPDATE events SET date=?, time=?, opponent=?, venue=?, result=?, note=?
				WHERE id_event=? LIMIT 1';
	
			// Prepare the statement
			$stmt = $this->dbc->prepare($q); 
	
			// Bind the inbound variables:
			$stmt->bind_param('ssssssi', $gmdate, $gtime, $opponent, $venue, $result, $note, $this->id_event);
					
			// Execute the query:
			$stmt->execute();
	
			if ($stmt->affected_rows == 1) // And update to the database was made
			{				
				echo 'This event has been edited';

				// Update attributes
				self::setEventAttributes($this->id_team, $gmdate, $gtime, $opponent, $venue, $result, $note);
			}
			else 
			{	// Either did not run ok or no updates were made
				echo 'No changes were made';
			}
		
			// Close the statement:
			$stmt->close();
			unset($stmt);

		} // End of editEvent function
		

		// Function to delete event
		function deleteEvent($userID)
		{
			// Make the query	
			$q = "DELETE FROM events WHERE id_event=? LIMIT 1";
	
			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);
	
			// Bind the inbound variable:
			$stmt->bind_param('i', $this->id_event);
	
			// Execute the query:
			$stmt->execute();
				
			// If the query ran ok.
			if ($stmt->affected_rows == 1) 
			{
				// Print a message
				echo 'This event has been deleted successfully';
			}
			else 
			{	// If the query did not run ok.
				echo 'The event was not deleted';
			}
				
			// Close the statement:
			$stmt->close();
			unset($stmt);
		
		} // End of deleteEvent function		
			
		// Function to check if user is manager of event
		function isManager($userID)
		{
			// Make the query to retreive manager id associated with event:		
			$q = "SELECT tm.id_manager
				FROM teams AS tm INNER JOIN events AS g
				USING (id_team)
				WHERE g.id_event=? LIMIT 1";
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables:
			$stmt->bind_param('i', $this->id_event);
			
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
						return True;  // User is the manager
					}
					else 
					{
						return False; // User is not the manager
					}
				}
			}
			else 
			{
				return False; // User was not found
			}
			
			// Close the statement:
			$stmt->close();
			unset($stmt);
	
		} // End of isManager function
	
} // End of Class
