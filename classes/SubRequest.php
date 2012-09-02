<?php
	/* This page defines the SubRequest class.
	 * Attributes:
	 * 	protected id_subrequest
	 * 	protected id_user
	 *  protected id_team
	 *  protected id_event
	 *  protected sex_needed
	 *  protected experience_needed
	 *  protected id_region
	 *  protected dbc
	 * 
	 * Methods:
	 *  setDB()
	 *  setSubReqID()
	 *  setSRAttributes()
	 *  getSRAttribute()
	 *  createSubReq()
	 *  editSubReq()
	 *  deleteSubReq()
	 *  isManager()
	 *  pullSubReqData()
	 */
	
	class SubRequest {
	 	
		// Declare the attributes
		protected $id_subrequest, $id_user, $id_team, $id_event, $sex_needed, 
			$experience_needed, $id_region, $dbc;

		// Constructor
		function __construct() {}

		// Set database connection attribute
		function setDB($db) {
			$this->dbc = $db;
		}

		// Function to set SubRequest ID attribute
		function setSubReqID($srID) {
			$this->id_subrequest = $srID;
		}
		
		// Function to set SubRequest object attributes
		function setSRAttributes($manID = 0, $tmID = 0, $evntID = 0, $sex ='', $exp = 0, $reg = 0) {
			$this->id_user = $manID;
			$this->id_team = $tmID;
			$this->id_event = $evntID;						
			$this->sex_needed = $sex;
			$this->experience_needed = $exp;
			$this->id_region = $reg;
		}

		// Function to get specific SubRequest object attribute
		function getSRAttribute($attribute) {
			return $this->$attribute;
		}

		// Function to create a SubRequest object
		function createSubReq($manID, $tmID, $evntID, $sex, $exp, $reg) {
			// Make the query:
			$q = 'INSERT INTO subrequests (id_user, id_team, id_event, sex_needed, 
				experience_needed, id_region) VALUES (?,?,?,?,?,?)';
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('iiisii', $manID, $tmID, $evntID, $sex, $exp, $reg);
			
			// Execute the query
			$stmt->execute();

			// Successfully added subrequest
			if ($stmt->affected_rows == 1)
			{
				echo 'Your subrequest was created succesfully';
			}
			else
			{
				echo 'Your subrequest was not added. Please contact the service administrator';
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);		
			
		} // End of createSubReq function

		
		// Function to edit a SubRequest object in database
		function editSubReq($manID, $subReqid, $tmID, $evntID, $sex, $exp, $reg) {
			// Make the query:
			$q = 'UPDATE subrequests SET id_team=?,id_event=?,sex_needed=?,experience_needed=?,id_region=? 
				WHERE id_subrequest=? LIMIT 1';
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables
			$stmt->bind_param('iisiii', $tmID, $evntID, $sex, $exp, $reg, $subReqid);
			
			// Execute the query
			$stmt->execute();

			// Successfully added subrequest
			if ($stmt->affected_rows == 1) {
				echo 'Your subrequest was edited succesfully';
			}
			else {
				// Either did not run ok or no updates were made
				echo 'No changes were made';
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);		
			
		} // End of editSubReq function

		
		// Function to delete subrequest
		function deleteSubReq($subReqid) {
			// Make the query	
			$q = "DELETE FROM subrequests WHERE id_subrequest=? LIMIT 1";
	
			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);
	
			// Bind the inbound variable:
			$stmt->bind_param('i', $subReqid);
	
			// Execute the query:
			$stmt->execute();
				
			// If the query ran ok.
			if ($stmt->affected_rows == 1) 
			{
				// Print a message
				echo 'This subrequest has been deleted successfully';
			}
			else 
			{	// If the query did not run ok.
				echo 'The subrequest was not deleted';
			}
				
			// Close the statement:
			$stmt->close();
			unset($stmt);
		
		} // End of deleteSubReq function


		// Function to check if user is the manager
		function isManager($userID) {
			// Make the query to retreive manager id associated with team:		
			$q = "SELECT id_user FROM teams
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


		// Function to pull current SubRequest data from database & set attributes
		function pullSubReqData() {
			// Make the query
			$q = 'SELECT id_user,id_team,id_event,sex_needed,experience_needed,id_region
				FROM subrequests WHERE id_subrequest=? LIMIT 1';
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables
			$stmt->bind_param('i', $this->id_subrequest);
			
			// Execute the query
			$stmt->execute();
			
			// Store result
			$stmt->store_result();
			
			// Bind the outbound variables
			$stmt->bind_result($manIDOB, $tmIDOB, $evntIDOB, $sexOB, $expOB, $regOB);

			// Found result
			if ($stmt->num_rows == 1)
			{	
				while ($stmt->fetch())
				{				
					self::setSRAttributes($manIDOB, $tmIDOB, $evntIDOB, $sexOB, $expOB, $regOB);
				}
			}			
			
			// Close the statement
			$stmt->close();
			unset($stmt);
			
		} // End of pullSubReqData function



		
	} // End of Class
