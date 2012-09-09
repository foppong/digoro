<?php
	/* This page defines the SubResponse class.
	 * Attributes:
	 * 
	 * 
	 * Methods:
	 * 
	 *  
	 */
	
	class SubResponse {
	 	
		// Declare the attributes
		protected $id_sr_response, $id_subrequest, $id_user, $id_event, $manager_respond, 
			$manager_response, $comments, $did_showup, $dbc;

		// Constructor
		function __construct() {}


		// Set database connection attribute
		function setDB($db) {
			$this->dbc = $db;
		}


		// Function to set SubResponse ID attribute
		function setSRRespID($IDsubresponse) {
			$this->id_sr_response = $IDsubresponse;
		}
		
				
		// Function to set SubRequest ID attribute
		function setSubReqID($IDsubrequest) {
			$this->id_subrequest = $IDsubrequest;
		}
		
		
		// Function to set SubRequest object attributes
		function setSRRAttributes($userID = 0,$evntID = 0,$mrespond = 0,$mresponse = '',$com = '',$didsh = 0) {
			$this->id_user = $userID;
			$this->id_event = $evntID;
			$this->manager_respond = $mrespond;						
			$this->manager_response = $mresponse;
			$this->comments = $com;
			$this->did_showup = $didsh;
		}

		// Function to get specific SubRequest object attribute
		function getSRRAttribute($attribute) {
			return $this->$attribute;
		}

		// Function to create a SubResponse object
		function createSubReqResp($userID, $evntID, $com) {
			
			// Make the query
			$q = 'INSERT INTO subreq_responses (id_user, id_subrequest, id_event, comments)
				VALUES (?,?,?,?)';
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('iiis', $userID, $this->id_subrequest, $evntID, $com);
			
			// Execute the query
			$stmt->execute();
			
			// Successfully added subResponse
			if ($stmt->affected_rows == 1) {
				echo 'SubResponse was created successfully';
			}
			else {
				echo "SubResponse was not added. Please contact the service administrator";
			}
			
			// Close the statement
			$stmt->close();
			unset($stmt);
			
		} // End of createSubReqResp function

		// Function for manager to confirm SubResponse
		function confirmSubReqResp($subresponseID, $man_comments) {

			$didrespond = 1;
			
			// Make the query
			$q = 'UPDATE subreq_responses SET manager_respond=?, manager_response=?
				WHERE id_sr_response=?';
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('isi', $didrespond, $man_comments, $subresponseID);
			
			// Execute the query
			$stmt->execute();
			
			// Successfully added subResponse
			if ($stmt->affected_rows == 1) {
				echo 'SubResponse was confirmed';
				
				// SEND COMMENTS VIA EMAIL VIA SENDGRID HERE -write separate 
				// function to query database and retrieve manager comments and email
			}
			else {
				echo "SubResponse was not confirmed. Please contact the service administrator";
			}
			
			// Close the statement
			$stmt->close();
			unset($stmt);
			
		} // End of confirmSubReqResp function


		// Function for manager to confirm SubResponse
		function declineSubReqResp($subresponseID, $man_comments) {

			$didrespond = 2;
			
			// Make the query
			$q = 'UPDATE subreq_responses SET manager_respond=?, manager_response=?
				WHERE id_sr_response=?';
				
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('isi', $didrespond, $man_comments, $subresponseID);
			
			// Execute the query
			$stmt->execute();
			
			// Successfully added subResponse
			if ($stmt->affected_rows == 1) {
				echo 'SubResponse was declined';
				
				// SEND COMMENTS VIA EMAIL VIA SENDGRID HERE -write separate 
				// function to query database and retrieve manager comments and email
			}
			else {
				echo "SubResponse was not declined. Please contact the service administrator";
			}
			
			// Close the statement
			$stmt->close();
			unset($stmt);
			
		} // End of confirmSubReqResp function		
		
		// Function to cancel a SubResponse object
		function cancelSubResponse($subresponseID, $user_comments) {
			
		} // End of deleteSubReqResp function
		
		
		// Function to pull current data from database and set attributes
		function pullSubReqRespData() {
			
		} // End of pullSubReqRespData function


		// Function to check if SubResponse is current
		function isSRcurrent() {
			
		}


		
	} // End of Class
