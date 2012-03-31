<?php

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

	/*Custom function to check whether magic_quotes_gpc was set
	//and undo its effect for specific inputs where this matters
	function get_post_var($var)
	{
		$val = $_POST[$var];
		if (get_magic_quotes_gpc())
			$val = stripslashes($val);
		return $val;
	}
	
	/* Example 1 - Function with an exception
	function checkNum($number)
	{
		if($number>1)
		{
			throw new Exception("Value must be 1 or below");
		}
		return true;
	}
	
	//trigger execption in a "try" block
	try
	{
		checkNum(2);
		//If the exception is thrown, this text below will not be shown
		echo 'If you see this, the number is 1 or below';
	}
		
	//catch exception
	catch(Exception $e)
	{
		echo 'Message: ' .$e->getMessage();
	}
	
	//Example 2
	class customException extends Exception
	{
		public function errorMessage()
			{
				//error message
				$errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile().': <b>'.
				$this->getMessage().'</b> is not a valid E-Mail address';
				return $errorMsg;
			}	
	}
	
	$email = "someone@example...com";
	
	try
	{
		try
		{
			//check for "example" in mail address
			if(strpos($email, "example") !== FALSE)
			{
				//throw exception if example string is found in email
				throw new Exception($email);
			}		
		}
		catch(Exception $e)
		{
			//re-throw exception so that we get a friendlier message to user
			throw new customException($email);
		}
	}
		
	catch(customException $e)
	{
		//display custom message using the error Message function we defined earlier
		echo $e->errorMessage();
	}
	*/

?>