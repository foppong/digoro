<?php
	/* This page defines the Member class.
	 * Attributes:
	 *  protected mname
	 * 	protected position
	 * 	protected jersey_numb
	 * 	protected id_player
	 *  protected dbc
	 * Methods:
	 * 	
	 * 
	 *  
	 */
		

	class Member {
	 	
		// Declare the attributes
		protected $mname, $position, $jersey_numb, $id_player, $dbc;

		// Constructor
		function __construct($memberID) 
		{
			self::setMembID($memberID);			
		}
		
		// Function to set member ID attribute
		function setMembID($memberID)
		{
			$this->id_player = $memberID;
		}

		function setDB($db)
		{
			$this->dbc = $db;
		}
		
		function setMemberAttributes($MembName = '', $pos = '', $jernumb = 0)
		{
			$this->mname = $MembName;
			$this->position= $pos;	
			$this->jersey_numb = $jernumb;		
		}		

		function getMemberAttribute($attribute)
		{
			return $this->$attribute;
		}
		
		// Function to pull complete member data from database and set attributes
		function pullMemberData()
		{
			// Make the query to retreive user information:		
			$q = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, p.position, p.jersey_number
				FROM players AS p INNER JOIN users AS u
				USING (id_user)
				WHERE p.id_player=? LIMIT 1";
		
			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);
		
			// Bind the inbound variable:
			$stmt->bind_param('i', $this->id_player);
				
			// Execute the query:
			$stmt->execute();		
				
			// Store results:
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($nameOB, $posOB, $jnumbOB);

			// Found result
			if ($stmt->num_rows == 1)
			{	
				while ($stmt->fetch())
				{				
					self::setMemberAttributes($nameOB, $posOB, $jnumbOB);
				}
			}			
			
			// Close the statement
			$stmt->close();
			unset($stmt);			

		} // End of pullMemberData function
					
		// Edit Member Method (currently only allows managers)
		function editMember($userID, $pos, $jnumb) 
		{
			if (self::isManager($this->id_player, $userID))
			{		
				// Update the user's info in the database
				$q = 'UPDATE players SET position=?, jersey_number=? 
					WHERE id_player=? LIMIT 1';
	
				// Prepare the statement
				$stmt = $this->dbc->prepare($q); 
	
				// Bind the inbound variables:
				$stmt->bind_param('ssi', $pos, $jnumb, $this->id_player);
					
				// Execute the query:
				$stmt->execute();
	
				// MAY NOT WANT THESE MESSAGES LATER ON
				if ($stmt->affected_rows == 1) // And update to the database was made
				{				
					echo '<p>The member has been edited.</p>';
				}
				else 
				{	// Either did not run ok or no updates were made
					echo '<p>No changes were made.</p>';
				}
				
				// Update attributes
				self::setMemberAttributes($this->mname, $pos, $jnumb);
				
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
		} // End of editMember function
		

		// Function to delete member
		function deleteMember($userID)
		{
			if (self::isManager($this->id_player, $userID))
			{
			// Make the query	
			$q = "DELETE FROM players WHERE id_player=? LIMIT 1";
	
				// Prepare the statement:
				$stmt = $this->dbc->prepare($q);
	
				// Bind the inbound variable:
				$stmt->bind_param('i', $this->id_player);
	
				// Execute the query:
				$stmt->execute();
				
				// If the query ran ok.
				if ($stmt->affected_rows == 1) 
				{	
					// Print a message
					echo '<p>The player has been deleted successfully.</p>';
					include '../includes/footer.html';
					exit();				
				}
				else 
				{	// If the query did not run ok.
					echo '<p class="error">The member could not be deleted due to a system errror.</p>';
					exit();
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
		} // End of deleteMember function
			
		// Function to check if user is authroized to view page
		function checkAuth($userID)
		{
			if (self::isManager($this->id_player, $userID) == False)
			{
				$url = BASE_URL . 'manager/manager_home.php';
				header("Location: $url");
				exit();
			}		
		}			
						
		// Function to check if user is manager
		function isManager($membID, $userID)
		{
			// Make the query to retreive manager id associated with player:		
			$q = "SELECT tm.id_manager
				FROM teams AS tm INNER JOIN players AS p
				USING (id_team)
				WHERE p.id_player=? LIMIT 1";
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables:
			$stmt->bind_param('i', $membID);
			
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