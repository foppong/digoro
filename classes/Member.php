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
		function __construct() {}
		
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
			if ($stmt->num_rows == 1) {	
				while ($stmt->fetch())
				{				
					self::setMemberAttributes($nameOB, $posOB, $jnumbOB);
				}
			}			
			
			// Close the statement
			$stmt->close();
			unset($stmt);			

		} // End of pullMemberData function

		// Functon to create member and add to team
		function createMember($userEmail, $teamID, $fn, $ln)
		{
			// Code checks users database to see if player is already registered. If not, enters a place holder until 
			// added player completes registeration.
			if (self::isRegistered($userEmail)) {
				// Make the query:
				$q = 'INSERT INTO players (id_user, id_team) VALUES (?,?)';
					
				// Prepare the statement:
				$stmt = $this->dbc->prepare($q);
						
				// Bind the inbound variables:
				$stmt->bind_param('ii', $this->id_player, $teamID);
					
				// Execute the query:
				$stmt->execute();
						
				if ($stmt->affected_rows == 1) { // It ran ok
					echo '<p>' . $fn . ' ' . $ln . ' was added successfully.</p>';
				}
				else {
					echo '<p class="error">Player ' . $fn . ' ' . $ln . ' was not added. Please contact the service administrator.</p>';
				}
			
				// Close the statement:
				$stmt->close();
				unset($stmt);
				
			}
			else {
				// If player doesn't exist in user table, add skeleton informatoin to user table, add them to sport_table, & send invitation.		

				// Boolean used for invitation column, setting to True for invited
				$iv = True;
			
				// Make the query to add new user to database
				$q = 'INSERT INTO users (email, invited) VALUES (?,?)';
	
				// Prepare the statement
				$stmt = $this->dbc->prepare($q); 
	
				// Bind the inbound variables:
				$stmt->bind_param('ss', $userEmail, $iv);
					
				// Execute the query:
				$stmt->execute();
				
				$newID = $stmt->insert_id;
	
				if ($stmt->affected_rows == 1) { // It ran OK.
					// Make the query:
					$q = 'INSERT INTO players (id_user, id_team) VALUES (?,?)';
					
					// Prepare the statement
					$stmt = $this->dbc->prepare($q);
						
					// Bind the inbound variables
					$stmt->bind_param('ii', $newID, $teamID);
					
					// Execute the query:
					$stmt->execute();
						
					if ($stmt->affected_rows == 1) { // It ran ok
						echo '<p>' . $fn . ' ' . $ln . ' was added successfully.</p>';
					}
					else {
						echo '<p class="error">Player' . $fn . ' ' . $ln . ' was not added. Please contact the service administrator.</p>';
					}

					// Add conditional here somehow so that Manager has option to submit request or not
					// Send the invitation email
					$body = "Hello, you have been invited to join digoro!\n\nWe are a site devoted to connecting players and teams.\n\n 
						You can find more information at our website:";
					$body .= "\n" . BASE_URL;
					mail($userEmail, 'digoro.com - Digoro Invitation', $body);
						
					echo '<h3>Invitation successfully sent.</h3>';
	
					// Close the statement:
					$stmt->close();
					unset($stmt);

				}
				else {	// Registration process did not run OK.
					echo '<p class="error">Invitation could not be sent. We apologize
						for any inconvenience.</p>';
				}
			}
		} // End of createMember function

				
		// Function to check if requested member is registered with site
		function isRegistered($userEmail)
		{
			// Make the query	
			$q = 'SELECT id_user FROM users WHERE email=? LIMIT 1';

			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('s', $userEmail);

			// Execute the query:
			$stmt->execute();
			
			// Store result:
			$stmt->store_result();

			// Bind the outbound variable:
			$stmt->bind_result($idOB);

			// Assign the outbound variables			
			while ($stmt->fetch())
			{
				$this->id_player = $idOB;
			}

			// If player exists in user table
			if ($stmt->num_rows == 1) {
				return True;
			}
			else {
				return False;
			}
		} // End of isRegistered function

							
		// Edit Member Method (currently only allows managers)
		function editMember($userID, $pos, $jnumb) 
		{
			if (self::isManager($this->id_player, $userID)) {		
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
				if ($stmt->affected_rows == 1) { // And update to the database was made
					echo '<p>The member has been edited.</p>';
				}
				else { // Either did not run ok or no updates were made
					echo '<p>No changes were made.</p>';
				}
				
				// Update attributes
				self::setMemberAttributes($this->mname, $pos, $jnumb);
				
				// Close the statement:
				$stmt->close();
				unset($stmt);
			}
			else {
				echo '<p class="error">This page has been accessed in error.</p>';
				include '../includes/footer.html';
				exit();				
			}
		} // End of editMember function
		

		// Function to delete member
		function deleteMember($userID)
		{
			if (self::isManager($this->id_player, $userID)) {
				// Make the query	
				$q = "DELETE FROM players WHERE id_player=? LIMIT 1";
	
				// Prepare the statement:
				$stmt = $this->dbc->prepare($q);
	
				// Bind the inbound variable:
				$stmt->bind_param('i', $this->id_player);
	
				// Execute the query:
				$stmt->execute();
				
				// If the query ran ok.
				if ($stmt->affected_rows == 1) {	
					// Print a message
					echo '<p>The player has been deleted successfully.</p>';
					include '../includes/footer.html';
					exit();				
				}
				else {	// If the query did not run ok.
					echo '<p class="error">The member could not be deleted due to a system errror.</p>';
					exit();
				}
				
				// Close the statement:
				$stmt->close();
				unset($stmt);
			}
			else {
				echo '<p class="error">This page has been accessed in error.</p>';
				include '../includes/footer.html';
				exit();				
			}			
		} // End of deleteMember function
			
		// Function to check if user is authroized to view page
		function checkAuth($userID)
		{
			if (self::isManager($this->id_player, $userID) == False) {
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
			if ($stmt->num_rows == 1) {
				while ($stmt->fetch())
				{				
					if ($manIDOB == $userID) {
						return True;
					}
					else {
						return False;
					}
				}
			}
			else {
				return False;
			}
			
			// Close the statement:
			$stmt->close();
			unset($stmt);
	
		} // End of isManager function
	
} // End of Class
	 
?>