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
	 *  setTeamAttributes()
	 *  getTeamAttribute()
	 *  pullTeamData()
	 *  pullSpecificData()
	 *  addTeam()
	 *  removeTeam()
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

		// Function to add team to user
		function addTeam($teamID)
		{
			
		}

		// Function to remove team from user (for players)
		function removeTeam($teamID)
		{
			
		}

	} // End of Class
?>