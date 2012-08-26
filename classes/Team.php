<?php
	/* This page defines the Team class.
	 * Attributes:
	 * 	protected tmname
	 *  protected about
	 *  protected id_team
	 *  protected id_sport
	 *  protected id_manager
	 *  protected level
	 *  protected id_region
	 *  protected team_sex
	 *  protected team_email
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
		protected $tmname, $about, $id_team, $id_sport, $id_manager, $level, 
			$id_region, $team_sex, $team_email, $dbc;

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
/*
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
*/		
		// Function to set Team attributes
		function setTeamAttributes($sprtID = 0, $manID = 0, $tmname ='', 
			$abtm = '', $lvl = '', $sex = '', $reg = 0, $tmemail = '')
		{
			$this->id_sport = $sprtID;
			$this->id_manager = $manID;						
			$this->tmname = $tmname;
			$this->about = $abtm;
			$this->level = $lvl;
			$this->team_sex = $sex;
			$this->id_region = $reg;
			$this->team_email = $tmemail;
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
			$q = 'SELECT id_sport,id_manager,team_name,about,level_of_play,id_region,team_sex,team_email
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
			$stmt->bind_result($sprtIDOB, $manIDOB, $tmnameOB, $abtmOB, $lvlOB, $regOB, $sexOB, $tmemailOB);

			// Found result
			if ($stmt->num_rows == 1)
			{	
				while ($stmt->fetch())
				{				
					self::setTeamAttributes($sprtIDOB, $manIDOB, $tmnameOB, $abtmOB, 
						$lvlOB, $sexOB, $regOB, $tmemailOB);
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
		function createTeam($sprtID, $manID, $tmname, $abtm, $lvl, $reg, $sex, $tmemail)
		{
			// Make the query:
			$q = 'INSERT INTO teams (id_sport, id_manager, team_name, about, 
				level_of_play, id_region, team_sex, team_email) 
				VALUES (?,?,?,?,?,?,?,?)';

			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('iissiiis', $sprtID, $manID, $tmname, $abtm, $lvl, $reg, $sex, $tmemail);
			
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
		function editTeam($sprtID, $tmname, $abtm, $lvl, $reg, $sex, $tmemail, $teamid)
		{
			// Update the user's info in the members' table in database
			$q = 'UPDATE teams SET id_sport=?, team_name=?, about=?, level_of_play=?, id_region=?,
				team_sex=?, team_email=?
				WHERE id_team=? LIMIT 1';
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q); 
			
			// Bind the inbound variables:
			$stmt->bind_param('issiiisi', $sprtID, $tmname, $abtm, $lvl, $reg, $sex, $tmemail, $teamid);
				
			// Execute the query:
			$stmt->execute();

			// Print a message based upon result:
			if ($stmt->affected_rows == 1)
			{
				echo 'Your team was edited succesfully. ';
			}
			else
			{
				echo 'No changes made. ';
			}

		} // End of editTeam function

		// Function to transfer Manager role
		function transferTeam($newMangEmail, $teamid)
		{
			// Make the query:
			$q = 'SELECT p.id_user
				FROM users AS u INNER JOIN members AS p
				USING (id_user) 
				WHERE u.email=? AND p.id_team=? LIMIT 1';

			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variable:
			$stmt->bind_param('si', $newMangEmail, $teamid);
			
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
					$stmt2->bind_param('ii', $iduserOB, $teamid);
					
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
		function deleteTeam($teamid)
		{
			// Make the query	
			$q = "DELETE FROM teams WHERE id_team=? LIMIT 1";

			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('i', $teamid);

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
		function isManager($userID, $teamid)
		{
			// Make the query to retreive manager id associated with team:		
			$q = "SELECT id_manager FROM teams
				WHERE id_team=? LIMIT 1";
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables:
			$stmt->bind_param('i', $teamid);
			
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
				echo 'You have successfully removed yourself from the team';
			}
			else {
				echo 'The removal did not work. Pleaes contact the system admistrator. ';
				exit();
			}
				
		} // End of removePlayer function
		
		
	} // End of Class
