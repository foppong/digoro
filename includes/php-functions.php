<?php

	// Custom function to translate subresponse manager status
	function translateSubResStatus($resp0B) {
		switch ($resp0B) {
			case 0:
				$status = 'Pending';
					return $status;
					break;
					
			case 1:
				$status = 'Confirmed';
					return $status;					
					break;
					
			case 2:
				$status = 'Declined';
					return $status;
					break;
						
			default: 
				$status = 'Pending';
					return $status;
					break;
		}		
	}

	// Custom function to translate level of play data from int to string
	function translateLevelofPlay($tmlvl) {
		switch ($tmlvl) {
			case 1: //  Recreational
				$tmlevel = 'Recreational';
					return $tmlevel;
					break;
					
			case 2: // Intermediate
				$tmlevel = 'Intermediate';
					return $tmlevel;					
					break;
					
			case 3: // Advanced
				$tmlevel = 'Advanced';
					return $tmlevel;
					break;
						
			default: 
				$tmlevel = 'Recreational';
					return $tmlevel;
					break;
		}		
	}
	

	// Custom function to translate event data from int to string
	function translateEventType($type) {
		switch ($type) {
			case 1: // Event is a game
				$type = 'Game';
				return $type;
				break;
				
			case 2: // Event is a practice
				$type = 'Practice';
				return $type;
				break;
				
			case 3: // Event is a scrimmage
				$type = 'Scrimmage';
				return $type;
				break;
					
			default: 
				$type = 'Game';
				return $type;
				break;
		}		
	}	


	// Custom function to translate sex data from int to string
	function translateSex($gen) {
		switch ($gen) {
			case 1: //  Female
				$sex = 'Female';
				return $sex;
				break;
				
			case 2: // Male
				$sex = 'Male';
				return $sex;
				break;
					
			default: 
				$sex = 'Undefined';
				return $sex;
				break;
		}	
	}	


	// Custom function to translate sex data from int to string
	function translateTmSex($sex) {
		switch ($sex) {
			case 1: //  Female
				$sex = 'Coed';
				return $sex;
				break;
				
			case 2: // Male
				$sex = 'All Female';
				return $sex;
				break;

			case 3: // Coed
				$sex = 'All Male';
				return $sex;
				break;
					
			default: 
				$sex = 'COED';
				return $sex;
				break;
		}	
	}	


	function translateRegion($reg) {
		switch ($reg) {
			case 1: //  San Francisco/ Bay Area
				$region = 'San Francisco/ Bay Area';
				return $region;
				break;

			default: 
				$region = 'San Francisco/ Bay Area';
				return $region;
				break;
		}	
		
	}
	
	
	function translateSport($sprt) {
		switch ($sprt) {
			case 1: 
				$sport = 'Soccer';
				return $sport;
				break;
				
			case 2: 
				$sport = 'Flag Football';
				return $sport;
				break;

			case 3: 
				$sport = 'Hockey';
				return $sport;
				break;

			case 4: 
				$sport = 'Softball';
				return $sport;
				break;

			case 5: 
				$sport = 'Basketball';
				return $sport;
				break;

			case 6: 
				$sport = 'Ultimate';
				return $sport;
				break;

			case 7: 
				$sport = 'Volleyball';
				return $sport;
				break;

			case 8: 
				$sport = 'Kickball';
				return $sport;
				break;

			case 9: 
				$sport = 'Rugby';
				return $sport;
				break;
					
			default: 
				$sport = 'Soccer';
				return $sport;
				break;
		}			
		
	}

	function translateExperience($exp) {
		switch ($exp) {
			case 2: 
				$experience = 'Beginner';
				return $experience;
				break;
				
			case 3: 
				$experience = 'Youth League';
				return $experience;
				break;

			case 4: 
				$experience = 'High School - Varsity/ Club';
				return $experience;
				break;

			case 5: 
				$experience = 'College - Varsity/ Club';
				return $experience;
				break;

			case 6: 
				$experience = 'Adult League/ Pick-up';
				return $experience;
				break;

			case 7: 
				$experience = 'Pro/ Semi-pro';
				return $experience;
				break;
					
			default: 
				$experience = 'Beginner';
				return $experience;
				break;
		}			
		
	}

	//Custom function to translate month
	function translateMonth($bdmnthIN) {
		switch ($bdmnthIN) {
			case '1':
				$mth = 'January';
				return $mth;
				break;
			case '2':
				$mth = 'Febuary';
				return $mth;				
				break;
			case '3':
				$mth = 'March';
				return $mth;
				break;
			case '4':
				$mth = 'April';
				return $mth;
				break;
			case '5':
				$mth = 'May';
				return $mth;
				break;
			case '6':
				$mth = 'June';
				return $mth;
				break;
			case '7':
				$mth = 'July';
				return $mth;
				break;
			case '8':
				$mth = 'August';
				return $mth;
				break;
			case '9':
				$mth = 'September';
				return $mth;
				break;
			case '10':
				$mth = 'October';
				return $mth;
				break;
			case '11':
				$mth = 'November';
				return $mth;
				break;
			case '12':
				$mth = 'December';
				return $mth;
				break;																							
			default:
				$mth = NULL;
				return $mth;
				break;
		}
	}


	//Custom function to display errors	
	function fail($pub, $pvt = '')
	{
		global $debug;
		$msg = $pub;
		if ($debug && $pvt !=='')
		{
			$msg .= ": $pvt";
		}
			exit("An error occured ($msg).\n");
	}

	//Function to redirect user and destroy session
	function redirect_to($page) {
	    session_unset();
	    session_destroy();
	    $url = BASE_URL . $page;
	    ob_end_clean();
	    header("Location: $url");
	    exit(); 
	}

?>