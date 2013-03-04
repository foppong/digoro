<?php
	/* This script:
	 * - define constatns and settings
	 * - dictates how errors are handled
	 * - defines useful functions
	 */
	
	// SETTINGS

	// Admin contact address:
	define('EMAIL', 'foppong@gmail.com');	
	
	// Determine whether we're working on a local server or on the real server
	if (stristr($_SERVER['HTTP_HOST'], 'local') || (substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168'))
	{
		$local = TRUE;
		
		// Flag variable for site status:
		define('LIVE', FALSE);
		
	} else {
		$local = FALSE;
		
		// Flag variable for site status:
		define('LIVE', TRUE);		
	}

	// Determine location of files and the URL of the site:
	// Allow for the development on different servers.
	if ($local) {
		
		// Always debug when running locally:
		$debug = TRUE;
		
		// Define the constants
		define ('BASE_URI', 'C:/xampp/htdocs/');
		define ('BASE_URL', 'https://localhost/');
		define ('MYSQL1', 'nonWeb/mysqli_connect_dev.php');
		define ('MYSQL2', '../nonWeb/mysqli_connect_dev.php');

		// Adjust the time zone
		date_default_timezone_set('America/Los_Angeles');

		
	} else {
		
		// Site URL (base for all redirections):
		define('BASE_URL', 'http://www.digoro.com/');
		
		// Relative location of the MySQL connection script:
		define('MYSQL1', '../nonWeb/mysqli_connect.php');
		
		// Relative location of the MySQL connection script:
		define('MYSQL2', '../../nonWeb/mysqli_connect.php');
		
		// Adjust the time zone
		date_default_timezone_set('America/Los_Angeles');		
	}
	
	

	
	//Constants for PasswordHash.php
	// Base-2 logarithm of the iteration count used for password stretching
	$hash_cost_log2 = 8;
	// Do we require the hashes to be portable to older systems (less secure)?
	$hash_portable = FALSE;	
	
	
	// ERROR MANAGEMENT
	
	// Create the error handler:
	function my_error_handler($e_number, $e_message, $e_file, $e_line, $e_vars)
	{
		// Build the error message:
		$message = "An error occurred in script '$e_file' on line $e_line:
			$e_message\n";
		
		// Add the date and time:
		$message .= "Date/Time: " . date('n-j-Y H:i:s') . "\n";
		
		if (!LIVE)
		{
			// Development (print the error)
			// Show the error message:
			echo '<div class="error">' . nl2br($message);
			
			// Add the variables and a backtrace
			echo '<pre>' . print_r($e_vars, 1) . "\n";
			debug_print_backtrace();
			echo '</pre></div>';
		}
		else 
		{
			// Send an email to the admin:
			$body = $message . "\n" . print_r($e_vars, 1);
			mail(EMAIL, 'Site Error!', $body, 'From: foppong@gmail.com');
			
			// Only print an error message if the error isn't a notice:
			if ($e_number != E_NOTICE)
			{
				echo '<div class="error"> A system error occured.
					We apologize for the inconvenience. </div><br />';
			}
			
		}
	}
	
	// Use my error handler:
	set_error_handler('my_error_handler');
