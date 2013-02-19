<?php
	/* This page defines the Member class.
	 * Attributes:
	 * 	protected primary position
	 *  protected secondary postion
	 * 	protected jersey_numb
	 * 	protected id_member
	 *  protected id_user
	 *  protected dbc
	 * 
	 * Methods:
	 * 	setMembID()
	 *  setDB()
	 *  setMemberAttributes()
	 * 	getMemberAttribute()
	 *  pullMemberData()
	 * 	createMember()
	 *  inUserDatabase()
	 *  editMember()
	 *  deleteMember()
	 *  checkAuth()
	 *  isManager()
	 */
		

	class Member {
	 	
		// Declare the attributes
		protected $prim_position, $sec_position, $jersey_numb, $id_member, $id_user, $dbc;

		// Constructor
		function __construct() {}
		
		// Function to set member ID attribute
		function setMembID($memberID)
		{
			$this->id_member = $memberID;
		}

		function setDB($db)
		{
			$this->dbc = $db;
		}
		
		function setMemberAttributes($ppos = '', $spos = '', $jernumb = 0, $userID = 0)
		{
			$this->prim_position = $ppos;
			$this->sec_position = $spos;
			$this->jersey_numb = $jernumb;
			$this->id_user = $userID;
		}		

		function getMemberAttribute($attribute)
		{
			return $this->$attribute;
		}
		
		// Function to pull complete member data from database and set attributes
		function pullMemberData()
		{
			// Make the query to retreive user information:		
			$q = "SELECT id_user,	primary_position, secondary_position, jersey_number
				FROM members WHERE id_member=? LIMIT 1";
		
			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);
		
			// Bind the inbound variable:
			$stmt->bind_param('i', $this->id_member);
				
			// Execute the query:
			$stmt->execute();		
				
			// Store results:
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($userIDOB, $pposOB, $sposOB, $jnumbOB);

			// Found result
			if ($stmt->num_rows == 1) {	
				while ($stmt->fetch())
				{				
					self::setMemberAttributes($pposOB, $sposOB, $jnumbOB, $userIDOB);
				}
			}			
			
			// Close the statement
			$stmt->close();
			unset($stmt);			

		} // End of pullMemberData function

		// Functon to create member and add to team
		function createMember($userEmail, $teamID, $fn, $ln, $sex, $ppos, $spos, $jnumb, $invite) {
			// Code checks users database to see if member is already in database. If not, enters a place holder until 
			// added player completes registeration.
			if (self::inUserDatabase($userEmail)) {
				// Make the query:
				$q = 'INSERT INTO members (id_user, id_team, primary_position, 
					secondary_position, jersey_number) VALUES (?,?,?,?,?)';
					
				// Prepare the statement:
				$stmt = $this->dbc->prepare($q);
						
				// Bind the inbound variables:
				$stmt->bind_param('iissi', $this->id_user, $teamID, $ppos, $spos, $jnumb);
					
				// Execute the query:
				$stmt->execute();
						
				if ($stmt->affected_rows == 1) { // It ran ok
					echo '<div class="alert alert-success">Member was added successfully</div>';
				}
				else {
					echo '<div class="alert alert-error">Oh Snap! Member was not added. Please contact the service administrator</div>';
				}
			
				// Close the statement:
				$stmt->close();
				unset($stmt);
				
			}
			else {
				// If player doesn't exist in user table, add skeleton informatoin to user table, add them to sport_table, & send invitation.		

				// Boolean used for invitation column, setting to True for invited
				$iv = 1;
			
				// Make the query to add new user to database
				$q = 'INSERT INTO users (first_name, last_name, email, sex, invited) VALUES (?,?,?,?,?)';
	
				// Prepare the statement
				$stmt = $this->dbc->prepare($q); 
	
				// Bind the inbound variables:
				$stmt->bind_param('sssii', $fn, $ln, $userEmail, $sex, $iv);
					
				// Execute the query:
				$stmt->execute();
				
				if ($stmt->affected_rows == 1) { // It ran OK.

					$newuserID = $stmt->insert_id;				
				
					// Make the query:
					$q = 'INSERT INTO members (id_user, id_team, primary_position, 
						secondary_position, jersey_number) VALUES (?,?,?,?,?)';
					
					// Prepare the statement
					$stmt2 = $this->dbc->prepare($q);
						
					// Bind the inbound variables:
					$stmt2->bind_param('iissi', $newuserID, $teamID, $ppos, $spos, $jnumb);
				
					// Execute the query:
					$stmt2->execute();
						
					if ($stmt2->affected_rows == 1) { // It ran ok
						echo '<div class="alert alert-success">Member was added successfully</div>';
					}
					else {
						echo '<div class="alert alert-error">Oh Snap! Member was not added. Please contact the service administrator</div>';
						exit();
					}

					// Send invitation if have permission
					if ($invite = 1) {
						// SEND GRID INSERT
						// Add conditional here somehow so that Manager has option to submit request or not
						// Send the invitation email
						$body = "Hello, you have been invited to join digoro!\n\nWe are a site devoted to connecting players and teams.\n\n 
							You can find more information at our website:";
						$body .= "\n" . BASE_URL;
						mail($userEmail, 'digoro.com - Digoro Invitation', $body);
							
						echo '<div class="alert alert-success">Invitation successfully sent!</div>';
					}
					else {
						echo '<div class="alert alert-error">Oh Snap! Invitation was not sent</div>';
					}
					
					// Close the statement:
					$stmt2->close();
					unset($stmt2);

				}
				else {	// Registration process did not run OK.
					echo '<div class="alert alert-error">Oh Snap! Registration process didnt work. Please contact service administrator</div>';
				}
				
				// Close the statement:
				$stmt->close();
				unset($stmt);
			}
		} // End of createMember function
				
		// Edit Member Method
		function editMember($memberid, $fn, $ln, $sex, $ppos, $spos, $jnumb) 
		{		
			// Update the user's info in the database
			$q = 'UPDATE members SET primary_position=?, secondary_position=?, jersey_number=? 
				WHERE id_member=? LIMIT 1';
	
			// Prepare the statement
			$stmt = $this->dbc->prepare($q); 
	
			// Bind the inbound variables:
			$stmt->bind_param('ssii', $ppos, $spos, $jnumb, $memberid);
					
			// Execute the query:
			$stmt->execute();
	
			if ($stmt->affected_rows == 1) { // And update to the database was made
				echo '<div class="alert alert-success">This member has been edited</div>';
			}
			else { // Either did not run ok or no updates were made
				echo '<div class="alert">No changes were made</div>';
			}
					
			// Close the statement:
			$stmt->close();
			unset($stmt);

			// If user hasn't registered with the site, update user table on their behalf
			if (!self::isRegistered($this->id_user)) {
				// Make the query
				$q = 'UPDATE users SET first_name=?, last_name=?, sex=? WHERE id_user=? LIMIT 1';
				
				// Prepare the statement
				$stmt = $this->dbc->prepare($q);
				
				// Bind the inbound variables
				$stmt->bind_param('ssii', $fn, $ln, $sex, $this->id_user);
				
				// Execute the statement
				$stmt->execute();

				// Close the statement:
				$stmt->close();
				unset($stmt);
			}


		} // End of editMember function
		
		// Function to delete member
		function deleteMember($memberid)
		{
			// Make the query	
			$q = "DELETE FROM members WHERE id_member=? LIMIT 1";
	
			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);
	
			// Bind the inbound variable:
			$stmt->bind_param('i', $memberid);
	
			// Execute the query:
			$stmt->execute();
				
			// If the query ran ok.
			if ($stmt->affected_rows == 1) {
				echo '<div class="alert alert-success">This member has been deleted successfully</div>';			
			}
			else {	// If the query did not run ok.
				echo '<div class="alert alert-error">Oh Snap! The member could not be deleted. Please contact the system administrator.</div>';
				exit();
			}
				
			// Close the statement:
			$stmt->close();
			unset($stmt);
			
		} // End of deleteMember function		

		// Function to check if user is the manager
		function isManager($userID, $memberid)
		{
			// Make the query to retrieve all teams associated with member and selected team
			$q = "SELECT tm.id_user
				FROM teams AS tm INNER JOIN members AS p
				USING (id_team)
				WHERE p.id_member=? LIMIT 1";
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables:
			$stmt->bind_param('i', $memberid);
			
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

		
		// Function to check if requested member is in database
		function inUserDatabase($userEmail) {
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

			// If player exists in user table
			if ($stmt->num_rows == 1) {
				// Assign the outbound variables			
				while ($stmt->fetch())
				{
					$this->id_user = $idOB;
				}
				return True;
			}
			else {
				return False;
			}
			
			// Close the statement:
			$stmt->close();
			unset($stmt);

		} // End of inUserDatabase function
		
		
		// Function to check if member has registered with site
		function isRegistered($memberID) {
			// Make the query
			$q = 'SELECT login_before FROM users WHERE id_user=? LIMIT 1';
			
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables
			$stmt->bind_param('i', $memberID);
			
			// Execute the query
			$stmt->execute();
			
			// Bind the outbound variables
			$stmt->bind_result($loginOB);
			
			// Found result
			if ($stmt->num_rows == 1) {
				while ($stmt->fetch()) {
					if ($loginOB == 1) {
						return TRUE;
					}
					else {
						return FALSE;
					}
				}
			}
						
			// Close the statement:
			$stmt->close();
			unset($stmt);		
		} // End of isRegistered function
			
} // End of Class
	 