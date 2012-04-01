<?php
	/* This page defines the UserAuth class.
	 * Attributes:
	 * 	protected email
	 * 	protected id_user
	 * 	protected invite
	 * 	protected q
	 *  protected a 
	 * 
	 * 	
	 * Methods:
	 * 	editGame()
	 * 	displayGame()
	 */
	
	require_once 'includes/config.php';	
	require 'includes/PasswordHash.php';
	
	class UserAuth {
	 	
		// Declare the attributes
		protected $email, $id_user, $invite, $q, $a;

		// Constructor
		function __construct() {
			
			// Need the database connection:	
			require MYSQL;		
		}
		
		// Method to check if username is avialable
		function checkUser($userEmail) {
			
			$this->email = $userEmail;	

			// Make the query to make sure New User's email is available	
			$this->q = 'SELECT id_user,invited FROM users WHERE email=? LIMIT 1';

			// Prepare the statement
			$stmt = $db->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('s', $email);

			// Execute the query:
			$stmt->execute();
			
			// Store result
			$stmt->store_result();
			
			// Bind the outbound variable:
			$stmt->bind_result($idOB, $inviteOB);

			//Assign the outbound variables			
			while ($stmt->fetch()) {
				$this->id_user = $idOB;
				$this->invite = $inviteOB;
			}

			if ($stmt->num_rows == 0) {
				return True; // User login available & not invited by manager
			}
			else {
				return False;
			}
			
			// Close the statement:
			$stmt->close();
			unset($stmt);	
			
		}	

		// Function to create user
		function createUser() {
			
			// Create the activation code
			$a = md5(uniqid(rand(), TRUE));	

			// Make the query to add new user to database
			$q = 'INSERT INTO users (email, pass, first_name, last_name, role, zipcode, gender, activation, birth_date, invited, registration_date) 
				VALUES (?,?,?,?,?,?,?,?,?,?,NOW())';

			// Prepare the statement
			$stmt = $db->prepare($q); 

			// Bind the inbound variables:
			$stmt->bind_param('sssssssssi', $e, $hash, $fn, $ln, $mstatus, $zp, $gd, $a, $bdfrmat, $iv);
				
			// Execute the query:
			$stmt->execute();


		}


	}	 
?>