<?php
	/* This page defines the Team class.
	 * Attributes:
	 * 
	 * Methods:
	 *  createTeam()
	 *  editTeam()
	 *  transferTeam()
	 *  deleteTeam()
	 */
	
	class ManagerTeam extends Team {

		// Constructor
		function __construct($teamID) 
		{
			parent::setTeamID($teamID);
		}
			
		// Function to create team
		function createTeam($lgID, $sprtID, $manID, $tmname, $cty, $st, $abtm)
		{
			// Make the query:
			$q = 'INSERT INTO teams (id_league, id_sport, id_manager, team_name, city, state, about) VALUES (?,?,?,?,?,?,?)';

			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('iiissss', $lgID, $sprtID, $manID, $tmname, $cty, $st, $abtm);
			
			// Execute the query:
			$stmt->execute();
			
			// Successfully added team
			if ($stmt->affected_rows == 1)
			{
				// Assign id_team with setTeamID function
				self::setTeamID($stmt->insert_id);

				// Set the default team ID
				$_SESSION['deftmID'] = $this->id_team;
				$tmID = $_SESSION['deftmID'];

				// Make the new query to add manager to player table:
				$q = 'INSERT INTO players (id_user, id_team) VALUES (?,?)';
					
				// Prepare the statement:
				$stmt2 = $this->dbc->prepare($q);
						
				// Bind the inbound variables:
				$stmt2->bind_param('ii', $manID, $tmID);
					
				// Execute the query:
				$stmt2->execute();
						
				if ($stmt2->affected_rows !== 1) // It didn't run ok
				{
					echo '<p class="error">Manager was not added to roster. Please contact the service administrator.</p>';
					exit();
				}
			
				// Close the statement:
				$stmt2->close();
				unset($stmt2);				
				
				// Set boolean logic to true
				$bl = 1;
				
				// Update the user's info in the database
				$q = 'UPDATE users SET default_teamID=?, login_before=? WHERE id_user=? LIMIT 1';
	
				// Prepare the statement
				$stmt2 = $this->dbc->prepare($q); 
	
				// Bind the inbound variables:
				$stmt2->bind_param('iii', $tmID, $bl, $manID);
					
				// Execute the query:
				$stmt2->execute();
					
				if ($stmt2->affected_rows !== 1) // It didn't run ok
				{
					echo '<p class="error">Team was not added. Please contact the service administrator.</p>';
					exit();
				}

				// Redirect user to manager homepage after success
				$url = BASE_URL . 'manager/manager_home.php';
				header("Location: $url");
				exit();	
					
				// Close the statement:
				$stmt2->close();
				unset($stmt2);
			}
			else
			{
				echo '<p class="error">Your team was not added. Please contact the service administrator.</p>';
				exit();
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);

		} // End of createTeam function
		
		// Function to edit team
		function editTeam($tmname, $abtm)
		{
			// Update the user's info in the players' table in database
			$q = 'UPDATE teams SET team_name=?, about=?
				WHERE id_team=? LIMIT 1';

			// Prepare the statement
			$stmt = $this->dbc->prepare($q); 
			
			// Bind the inbound variables:
			$stmt->bind_param('ssi', $tmname, $abtm, $this->id_team);
				
			// Execute the query:
			$stmt->execute();

			if ($stmt->affected_rows == 1) // And update to the database was made
			{				
				//The team has been edited
				$result = True;
			}
			else 
			{	// Either did not run ok or no updates were made
				$result = False;
			}

			parent::setTeamNM($tmname);
			parent::setTeamABT($abtm);

			return $result;

		} // End of editTeam function

		// Function to transfer Manager role
		function transferTeam($newManID)
		{
			// Make the query to update the team record with another manager ID
			$q = 'UPDATE teams SET id_manager=? WHERE id_team=? LIMIT 1';
			
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables:
			$stmt->bind_param('ii', $newManID, $this->teamID);
			
			// Execute the query:
			$stmt->execute();
			
			if ($stmt->affected_rows == 1) // Update to database was made
			{
				return True; // Team was successfully updated with new manager ID
			}
			else 
			{	
				return False; // Either did not run ok or no updates were made
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);

		}

		// Functon to delete team from database
		function deleteTeam()
		{
			// Make the query	
			$q = "DELETE FROM teams WHERE id_team=? LIMIT 1";

			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('i', $this->id_team);

			// Execute the query:
			$stmt->execute();
			
			// If the query ran ok.
			if ($stmt->affected_rows == 1) 
			{	// Print a message
				echo '<p>This team has been deleted successfully.</p>';
			}
			else 
			{	// If the query did not run ok.
				echo '<p class="error">The team could not be deleted due to a system error.</p>';
				exit();
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);
		} // End of deleteTeam function

		// Function to check if user is authroized to view page
		function checkAuth($userID)
		{
			if (self::isManager($this->id_team, $userID) == False)
			{
				$url = BASE_URL . 'manager/manager_home.php';
				header("Location: $url");
				exit();
			}		
		}		
		
		// Function to check if user is manager
		function isManager($teamID, $userID)
		{
			// Make the query to retreive manager id associated with team:		
			$q = "SELECT id_manager FROM teams
				WHERE id_team=? LIMIT 1";
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables:
			$stmt->bind_param('i', $teamID);
			
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