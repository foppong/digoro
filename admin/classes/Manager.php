<?php
	/* This page defines the Manager class and extends the User class.
	 * Attributes:
	 * 	
	 * Methods:
	 *  transferTeam()
	 *  addTeam()
	 *  removeTeam()
	 */
	
	class Manager extends User {
	 	
		// Function to transfer team ownership
		function changeManager($newManID, $teamID)
		{
			$team = new ManagerTeam();
			$team->setDB($this->dbc);
			$team->setTeamID($teamID);
			//$team->setTeamID($this->ctmID);
			
			if ($team->transferTeam($newManID))
			{
				return True;					
				echo '<p> Yay, we were able to transfer team ownership.</p>';
			}
			else 
			{
				return False;
				echo '<p class="error">We were not able to successfully transfer team ownership. Please try again.</p>';
			}
			
			unset($team);			
		}

		// Function to add team to profile
		function addTeam($lg, $sp, $userID, $tn, $ct, $st, $abtm)
		{
			$team = new ManagerTeam();
			$team->setDB($this->dbc);
			$team->createTeam($lg, $sp, $userID, $tn, $ct, $st, $abtm);
			unset($team);	
		}
		
		// Function to remove team from profile
		function removeTeam()
		{
			
		}

	} // End of Class
?>