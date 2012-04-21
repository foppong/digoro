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
	 *  protected dftm (default team)
	 *  protected lb (login in before)
	 * 
	 * 	
	 * Methods:
	 *  addTeam()
	 *  deleteTeam()
	 *  viewTeams()
	 *  setdefaultTeam()
	 *  viewSchedule()
	 *  isnewUser()
	 *  editAccount()
	 *  chgPassword()
	 *  viewRoster()
	 *  getuserData()
	 */
	
	class User extends UserAuth {
	 	
		// Declare the attributes
		protected $fn, $ln, $role, $city, $state, $zp, $gd, $email,
			$pass, $rdate, $bday, $pnum, $rating, $dftm, $lb;

		// Constructor
		function __construct($userID) 
		{
			parent::setUserID($userID);
		}

		function setUserAttributes($fnIN='', $lnIN='', $roleIN='', $cityIN='', $stIN='', $zpIN=0, $gdIN='', $emailIN='',
			$passIN='', $rdateIN='', $bdayIN='', $pnumIN=0, $rateIN='', $dftmIN=0, $lbIN=0)
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
			$this->dftm = $dftmIN;
			$this->lb = $lbIN;
		}

		function getUserAttribute($attribute)
		{
			return $this->$attribute;
		}
		
		// Function to add team
		function addTeam($lg, $userID, $sp, $tn, $ct, $st, $abtm) 
		{
			// Make the query:
			$q = 'INSERT INTO teams (id_league, id_manager, id_sport, team_name, city, state, about) VALUES (?,?,?,?,?,?,?)';

			// Prepare the statement
			$stmt = $this->dbc->prepare($q);
			
			// Bind the variables
			$stmt->bind_param('iiissss', $lg, $userID, $sp, $tn, $ct, $st, $abtm);
			
			// Execute the query:
			$stmt->execute();
			
			// Successfully added team
			if ($stmt->affected_rows == 1)
			{
				// Set the default team ID
				$_SESSION['deftmID'] = $stmt->insert_id;
				$tmID = $_SESSION['deftmID'];

				// Make the new query to add manager to player table:
				$q = 'INSERT INTO players (id_user, id_team) VALUES (?,?)';
					
				// Prepare the statement:
				$stmt2 = $this->dbc->prepare($q);
						
				// Bind the inbound variables:
				$stmt2->bind_param('ii', $userID, $tmID);
					
				// Execute the query:
				$stmt2->execute();
						
				if ($stmt2->affected_rows !== 1) // It didn't run ok
				{
					echo '<p class="error">Manager was not added to roster. Please contact the service administrator.</p>';
				}

				// Redirect user to manager homepage after success
				$url = BASE_URL . 'manager/manager_home.php';
				header("Location: $url");
				exit();	
			
				// Close the statement:
				$stmt2->close();
				unset($stmt2);
				
				echo '<p>Your team was added succesfully.</p>';
			}
			else
			{
				echo '<p class="error">Your team was not added. Please contact the service administrator.</p>';
			}

			// Close the statement:
			$stmt->close();
			unset($stmt);			
		}
		
		// Function to delete team
		function deleteTeam()
		{
			
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

		// Function to change password
		function chgPassword()
		{
			
		}

		// Function to view roster
		function viewRoster()
		{
			
		}

		// Function to pull complete user data from database and set attributes
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
				
			// Valid user ID
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

		
	} // End of Class
?>