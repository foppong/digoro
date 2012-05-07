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