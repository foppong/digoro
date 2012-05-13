<?php
	/* This page defines the User class and extends the UserAuth class.
	 * Attributes:
	 *  protected fn (first name)
	 *  protected ln (last name)
	 *  protected role
	 *  protected city
	 *  protected state
	 *  protected zp (zip code)
	 *  protected gd (gender)
	 * 	protected email
	 *  protected pass
	 *  protected rdate (registration date)
	 *  protected bday (birthday)
	 *  protected pnum (phone number)
	 *  protected rating
	 *  protected invited
	 *  protected dftmID (default team id)
	 *  protected lb (login in before)
	 * 
	 * 	
	 * Methods:
	 *  setUserAttributes()
	 *  getUserAttribute()
	 *  viewTeams()
	 *  setDefaultTeam()
	 *  viewSchedule()
	 *  isNewUser()
	 *  editAccount()
	 *  viewRoster()
	 *  pullUserData()
	 *  pullSpecificData()
	 *  updateUserAcct()
	 */

	class User extends UserAuth {
	 	
		// Declare the attributes
		protected $fn, $ln, $role, $city, $state, $zp, $gd, $email,
			$pass, $rdate, $bday, $pnum, $rating, $invited, $dftmID, $lb;

		// Constructor
		function __construct($userID) 
		{
			parent::setUserID($userID);
			//self::pullUserData(); // Pull current database information and set attributes
		}

		// Function to set the User attributes
		function setUserAttributes($fnIN='', $lnIN='', $roleIN='', $cityIN='', $stIN='', $zpIN=0, $gdIN='', $emailIN='',
			$passIN='', $rdateIN='', $bdayIN='', $pnumIN=0, $rateIN='', $invIN=0, $dftmIN=0, $lbIN=0)
		{
			$this->fn = $fnIN;
			$this->ln = $lnIN;
			$this->role = $roleIN;
			$this->city = $cityIN;
			$this->state = $stIN;
			$this->zp = $zpIN;
			$this->gd = $gdIN;
			$this->email = $emailIN;
			$this->pass = $passIN;
			$this->rdate = $rdateIN;
			$this->bday = $bdayIN;
			$this->pnum = $pnumIN;
			$this->rating = $rateIN;
			$this->invited = $invIN;
			$this->dftmID = $dftmIN;
			$this->lb = $lbIN;
		}


		function getUserAttribute($attribute)
		{
			return $this->$attribute;
		}
		
		// Function to view associated teams
		function viewTeams()
		{
			
		}

		// Function to set default team
		function setDefaultTeam()
		{
			
		}

		// Function to view schedule of team
		function viewSchedule()
		{
			
		}

		// Function to check if new user
		function isnewUser()
		{
			
		}

		// Function to edit account settings
		function editAccount()
		{
			
		}

		// Function to view roster
		function viewRoster()
		{
			
		}

		// Function to pull complete user data from database and set all attributes
		function pullUserData()
		{
			// Make the query
			$q = 'SELECT first_name,last_name,role,city,state,zipcode,
				gender,email,pass,registration_date,birth_date,phone_num,
				rating,invited,default_teamID,login_before
				FROM users WHERE id_user=? LIMIT 1';
					
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variable:
			$stmt->bind_param('i', $this->id_user);
			
			// Execute the query:
			$stmt->execute();
			
			// Store result
			$stmt->store_result();
		
			// Bind the outbound variable:
			$stmt->bind_result($fnOB, $lnOB, $roleOB, $cityOB, $stOB, $zpOB, $gdOB, $emailOB,
				$passOB, $rdateOB, $bdOB, $pnumOB, $ratingOB, $invOB, $dftmOB, $lbOB);	
				
			// Found result
			if ($stmt->num_rows == 1)
			{	
				while ($stmt->fetch())
				{				
					self::setuserAttributes($fnOB, $lnOB, $roleOB, $cityOB, $stOB, $zpOB, $gdOB, $emailOB,
				$passOB, $rdateOB, $bdOB, $pnumOB, $ratingOB, $invOB, $dftmOB, $lbOB);
				
				}
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);
						
		} // End of pullUserData function

		// Function to pull specific user data from database
		function pullSpecificData($datacolumn)
		{
			// Make the query
			$q = "SELECT $datacolumn FROM users WHERE id_user=? LIMIT 1";
					
			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the inbound variable:
			$stmt->bind_param('i', $this->id_user);
			
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

		// Function to update user informatin in database
		function updateUserAcct($e, $fn, $ln, $cty, $st, $zp, $gd, $bdfrmat, $pnumb) {
			// Update the user's info in the database
			$q = 'UPDATE users SET email=?, first_name=?, last_name=?, city=?, state=?, zipcode=?, gender=?, birth_date=?, phone_num=?
				WHERE id_user=? LIMIT 1';

			// Prepare the statement
			$stmt = $this->dbc->prepare($q); 

			// Bind the inbound variables:
			$stmt->bind_param('sssssissii', $e, $fn, $ln, $cty, $st, $zp, $gd, $bdfrmat, $pnumb, $this->id_user);
				
			// Execute the query:
			$stmt->execute();

			if ($stmt->affected_rows == 1) // And update to the database was made
			{				
				echo '<p>The users account has been edited.</p>';
				self::pullUserData(); // Update object attributes
			}
			else 
			{	// Either did not run ok or no updates were made
				echo '<p>No changes were made.</p>';
			}
		} // End of updateUserAcct function
		
		
	} // End of Class
?>