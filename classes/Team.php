<?php
	/* This page defines the Team class.
	 * Attributes:
	 * 	protected tmname
	 *  protected city
	 *  protected state
	 *  protected about
	 *  protected id_team
	 * 	protected id_league
	 *  protected id_sport
	 *  protected id_manager
	 *  protected dbc
	 * 
	 * Methods:
	 *  setDB()
	 *  setTeamID()
	 *  setTeamNM()
	 * 	setTeamABT()
	 *  setTeamAttributes()
	 *  getTeamAttribute()
	 *  pullTeamData()
	 *  pullSpecificData()
	 *  createTeam()
	 *  editTeam()
	 *  transferTeam()
	 *  deleteTeam()
	 *  isManager()
	 *  removeMember()
	 */
	
	class Team {
	 	
		// Declare the attributes
		protected $tmname, $city, $state, $about, $id_team, $id_league,
			$id_sport, $id_manager, $dbc;

		// Constructor
		function __construct() {}

		// Set database connection attribute
		function setDB($db)
		{
			$this->dbc = $db;
		}

		// Function to set team ID attribute
		function setTeamID($teamID)
		{
			$this->id_team = $teamID;
		}

		// Function to set team name
		function setTeamNM($attribute)
		{
			$this->tmname = $attribute;
		}

		// Function to set team about me
		function setTeamABT($attribute)
		{
			$this->about = $attribute;
		}
		
		// Function to set Team attributes
		function setTeamAttributes($lgID = 0, $sprtID = 0, $manID = 0, $tmname ='', $cty = '', 
			$st = '', $abtm = '')
		{
			$this->id_league = $lgID;
			$this->id_sport = $sprtID;
			$this->id_manager = $manID;						
			$this->tmname = $tmname;
			$this->city = $cty;
			$this->state = $st;
			$this->about = $abtm;
		}

		// Function to get specific class attribute
		function getTeamAttribute($attribute)
		{
			return $this->$attribute;
		}

		// Function to pull complete team data from database and set attributes
		function pullTeamData()
		{
			// Make the query
			$q = 'SELECT id_league,id_sport,id_manager,team_name,city,state,about
				FROM teams WHERE id_team=? LIMIT 1';
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables
			$stmt->bind_param('i', $this->id_team);
			
			// Execute the query
			$stmt->execute();
			
			// Store result
			$stmt->store_result();
			
			// Bind the outbound variables
			$stmt->bind_result($lgIDOB, $sprtIDOB, $manIDOB, $tmnameOB, $ctyOB, $stOB, $abtmIN);

			// Found result
			if ($stmt->num_rows == 1)
			{	
				while ($stmt->fetch())
				{				
					self::setTeamAttributes($lgIDOB, $sprtIDOB, $manIDOB, $tmnameOB, $ctyOB, $stOB, $abtmIN);
				}
			}			
			
			// Close the statement
			$stmt->close();
			unset($stmt);
			
		} // End of pullTeamData function

		// Function to pull specific user data from database
		function pullSpecificData($datacolumn)
		{
			// Make the query
			$q = "SELECT $datacolumn FROM teams WHERE id_team=? LIMIT 1";
					
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variable:
			$stmt->bind_param('i', $this->id_team);
			
			// Execute the query:
			$stmt->execute();
			
			// Store result
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($OB);	

			// Valid user ID
			if ($stmt->num_rows == 1)
			{
				while ($stmt->fetch())
				{				
					return $OB;
				}
				
			}
			
			// Close the statement:
			$stmt->close();
			unset($stmt);	
					
		} // End of pullSpecificData function		

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
				$q = 'INSERT INTO members (id_user, id_team) VALUES (?,?)';
					
				// Prepare the statement:
				$stmt2 = $this->dbc->prepare($q);
						
				// Bind the inbound variables:
				$stmt2->bind_param('ii', $manID, $tmID);
					
				// Execute the query:
				$stmt2->execute();
						
				if ($stmt2->affected_rows !== 1) // It didn't run ok
				{
					echo 'Manager was not added to roster. Please contact the service administrator.';
					exit();
				}
			
				// Close the statement:
				$stmt2->close();
				unset($stmt2);				
				
				// Set boolean logic to true
				$bl = 1;
				
				// Update the user's info in the database
				// This is wher the LOGIC of "logged in before" is set
				// NOTE: When develop player portion, will need to have a different trigger
				$q = 'UPDATE users SET default_teamID=?, login_before=? WHERE id_user=? LIMIT 1';
	
				// Prepare the statement
				$stmt2 = $this->dbc->prepare($q); 
	
				// Bind the inbound variables:
				$stmt2->bind_param('iii', $tmID, $bl, $manID);
					
				// Execute the query:
				$stmt2->execute();
					
				if ($stmt2->affected_rows !== 1) // It didn't run ok
				{
					echo 'Team was not added. Please contact the service administrator.';
					exit();
				}

				echo 'Team was added successfully!';
					
				// Close the statement:
				$stmt2->close();
				unset($stmt2);
			}
			else
			{
				echo 'Your team was not added. Please contact the service administrator.';
				exit();
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);

		} // End of createTeam function
		
		// Function to edit team
		function editTeam($tmname, $abtm)
		{
			// Update the user's info in the members' table in database
			$q = 'UPDATE teams SET team_name=?, about=?
				WHERE id_team=? LIMIT 1';

			// Prepare the statement
			$stmt = $this->dbc->prepare($q); 
			
			// Bind the inbound variables:
			$stmt->bind_param('ssi', $tmname, $abtm, $this->id_team);
				
			// Execute the query:
			$stmt->execute();

			// Print a message based upon result:
			if ($stmt->affected_rows == 1)
			{
				self::setTeamNM($tmname);
				self::setTeamABT($abtm);
				echo 'Your team was edited succesfully. ';
			}
			else
			{
				echo 'No changes made. ';
			}

		} // End of editTeam function

		// Function to transfer Manager role
		function transferTeam($newMangEmail)
		{
			// Make the query:
			$q = 'SELECT p.id_user
				FROM users AS u INNER JOIN members AS p
				USING (id_user) 
				WHERE u.email=? AND p.id_team=? LIMIT 1';

			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variable:
			$stmt->bind_param('si', $newMangEmail, $this->id_team);
			
			// Execute the query:
			$stmt->execute();
			
			// Store results:
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($iduserOB);
			
			// If there are results to show.
			if ($stmt->num_rows > 0) {
				
				while ($stmt->fetch()) {

					// Make the query to update the team record with another manager ID
					$q = 'UPDATE teams SET id_manager=? WHERE id_team=? LIMIT 1';
					
					// Prepare the statement
					$stmt2 = $this->dbc->prepare($q);
					
					// Bind the inbound variables:
					$stmt2->bind_param('ii', $iduserOB, $this->id_team);
					
					// Execute the query:
					$stmt2->execute();
					
					if ($stmt2->affected_rows == 1) // Update to database was made
					{
						echo 'The team has been transferred. ';
					}
					else 
					{	
						echo 'The team could not be transferred due to a system error. ';
						exit();
					}
					
					// Close the statement:
					$stmt2->close();
					unset($stmt2);
 				}
			}	
			else {
				echo 'Could not transfer because player is not on team roster. ';
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
				echo 'This team has been deleted successfully. ';
			}
			else 
			{	// If the query did not run ok.
				echo 'The team could not be deleted due to a system error. ';
				exit();
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);
		} // End of deleteTeam function	
		
		// Function to check if user is the manager
		function isManager($userID)
		{
			// Make the query to retreive manager id associated with team:		
			$q = "SELECT id_manager FROM teams
				WHERE id_team=? LIMIT 1";
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables:
			$stmt->bind_param('i', $this->id_team);
			
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
						return True; // User is the manager
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

		// Function to remove player from team if not manager
		function removeMember($userID) {
			// Make the query	
			$q = 'DELETE FROM members WHERE id_user=? LIMIT 1';
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables:
			$stmt->bind_param('i', $userID);
			
			// Execute the query
			$stmt->execute();
		
			if ($stmt->affected_rows == 1) {
				echo 'You have successfully removed yourself from ' . $this->tmname . '. ';
			}
			else {
				echo 'The removal did not work. Pleaes contact the system admistrator. ';
				exit();
			}
				
		} // End of removePlayer function
		
		
	} // End of Class
