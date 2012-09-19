<?php
	/* This page defines the Profile class.
	 * Attributes:
	 *  protected dbc
	 * 
	 * Methods:
	 *  setDB()
	 */
	
	class Profile {
	 	
		// Declare the attributes
		protected $id_profile, $id_user, $team_sex_preference, $id_region, $id_sport, $sport_experience,
			$primary_position, $secondary_position, $comments, $dbc;

		// Constructor
		function __construct() {}

		// Set database connection attribute
		function setDB($db) {
			$this->dbc = $db;
		}

		// Function to set Profile ID attribute
		function setProfileID($profileID) {
			$this->id_profile = $profileID;
		}
		
		// Function to set Profile object attributes
		function setPRAttributes($userID = 0, $tmSexPref = 0, $regID = 0, $sprtID = 0, 
			$sprtexp = 0, $ppos = '', $spos = '', $comm = '') {
			$this->$id_user = $userID;
			$this->$team_sex_preference = $tmSexPref;
			$this->$id_region = $regID;						
			$this->$id_sport = $sprtID;
			$this->$sport_experience = $sprtexp;
			$this->$primary_position = $ppos;
			$this->$secondary_position = $spos;
			$this->$comments = $comm;
		}

		// Function to get specific Profile object attribute
		function getPRAttribute($attribute) {
			return $this->$attribute;
		}

		// Function to create a Profile object
		function createProfile($userID, $tmSexPref, $regID, $sprtID, $sprtexp, $ppos, $spos, $comm) {
			// Make the query:
			$q = 'INSERT INTO profiles (id_user, team_sex_preference, id_region, id_sport, 
				sport_experience, primary_position, secondary_position, comments) VALUES (?,?,?,?,?,?,?,?)';
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('iiiiisss', $userID, $tmSexPref, $regID, $sprtID, $sprtexp, $ppos, $spos, $comm);
			
			// Execute the query
			$stmt->execute();

			// Successfully added subrequest
			if ($stmt->affected_rows == 1)
			{
				echo '<div class="alert alert-success">Your profile was created succesfully</div>';
			}
			else
			{
				echo '<div class="alert alert-error">Your profile was not added. Please contact the service administrator</div>';
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);		
			
		} // End of createProfile function

		
		// Function to edit a Profile object in database
		function editProfile($userID, $tmSexPref, $regID, $sprtID, $sprtexp, $ppos, $spos, $comm) {
			// Make the query:
			$q = 'UPDATE profiles SET id_user=?,team_sex_preference=?,id_region=?,id_sport=?, 
				sport_experience=?,primary_position=?,secondary_position=?,comments=? LIMIT 1';
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variables
			$stmt->bind_param('iiiiisss', $userID, $tmSexPref, $regID, $sprtID, $sprtexp, $ppos, $spos, $comm);
			
			// Execute the query
			$stmt->execute();

			// Successfully added subrequest
			if ($stmt->affected_rows == 1) {
				echo '<div class="alert alert-success">Your profile was edited succesfully</div>';
			}
			else {
				// Either did not run ok or no updates were made
				echo '<div class="alert">No changes were made</div>';
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);		
			
		} // End of editProfile function

		
		// Function to delete Profile
		function deleteProfile($profileID) {
			// Make the query	
			$q = "DELETE FROM profiles WHERE id_profile=? LIMIT 1";
	
			// Prepare the statement:
			$stmt = $this->dbc->prepare($q);
	
			// Bind the inbound variable:
			$stmt->bind_param('i', $profileID);
	
			// Execute the query:
			$stmt->execute();
				
			// If the query ran ok.
			if ($stmt->affected_rows == 1) 
			{
				// Print a message
				echo '<div class="alert alert-success">This profile has been deleted successfully</div>';
			}
			else 
			{	// If the query did not run ok.
				echo '<div class="alert alert-error">The profile was not deleted</div>';
			}
				
			// Close the statement:
			$stmt->close();
			unset($stmt);
		
		} // End of deleteProfile function
		
	} // End of Class
