<?php
	// view_users.php
	// This script retrieves all the records from the users table.
	
	require 'includes/config.php';
	$page_title = 'digoro : View Current Users';
	include 'includes/header.html';	

	// Authorized Login Check
	// If not an administrator, or no session value is present, redirect the user. Also validate the HTTP_USER_AGENT
	if (($_SESSION['role'] != 'A') OR !isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();
	}

	// Page header:
	echo '<h1>Registered Users</h1>';

	// Need the database connection:	
	require_once MYSQL;

	// Number of records to show per page:
	$display = 3;
	
	// Determine how many pages there are...
	if (isset($_GET['p']) && is_numeric($_GET['p']))
	{	// Already been determined
		$pages = $_GET['p'];
	}
	else 
	{	// Need to determine
		// Make the query to count the number of records
		$q = "SELECT COUNT(id_user) FROM users";

		// Prepare the statement:
		$stmt = $db->prepare($q);

		// Execute the query:
		$stmt->execute();

		//Store results:
		$stmt->store_result();

		// Bind the outbound variable:
		$stmt->bind_result($recOB);

		while ($stmt->fetch())
		{
			$records = $recOB;
		}

		// Calculate the number of pages...
		if ($records > $display)
		{	// More than 1 page
			$pages = ceil ($records/$display);
		}
		else 
		{
			$pages = 1;
		}
	}
	
	// Determine where in the database to start returning results...
	if (isset($_GET['s']) && is_numeric($_GET['s']))
	{
		$start = $_GET['s'];
	}
	else 
	{
		$start = 0;
	}

	// Determine the sort...
	// Default is by registration date.
	$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'rd'; // Ternary operator style syntax
	
	// Determine the sorting order:
	switch ($sort)
	{
		case 'fn':
			$order_by = 'first_name ASC';
			break;
		case 'ln':
			$order_by = 'last_name ASC';
			break;
		case 'rd':
			$order_by = 'registration_date ASC';
			break;
		default:
			$order_by = 'registration_date ASC';
			$sort = 'rd';
			break;
	}
	
	// Define the query:
	$q = "SELECT first_name, last_name, DATE_FORMAT(registration_date, '%M %d, %Y') AS dr, id_user
			FROM users ORDER BY $order_by LIMIT ?,?";
	
	// Prepare the statement:
	$stmt = $db->prepare($q);

	// Bind the inbound variable:
	$stmt->bind_param('ii', $start, $display);
	
	// Execute the query:
	$stmt->execute();		
		
	// Store results:
	$stmt->store_result();
	
	// Bind the outbound variable:
	$stmt->bind_result($fnOB, $lnOB, $drOB, $idOB);
	
	// If there are results to show.
	if ($stmt->num_rows > 0)
	{
		// Table Header
		echo '<table align="left" cellspacing="0" cellpadding="5" width="75%"
			<tr>
			<td align="left"><b><a href="view_users.php?sort=fn">First Name</a></b></td>
			<td align="left"><b><a href="view_users.php?sort=ln">Last Name</a></b></td>
			<td align="left"><b><a href="view_users.php?sort=rd">Date Registered</a></b></td>
			<td align="left"><b>Edit</b></td>
			<td align="left"><b>Delete</b></td>
			</tr>';
		
		// Fetch and print all records...
		$bg = '#eeeeee'; // Set the initial background color
		
		while ($stmt->fetch())
		{
			// Switch the background color.
			$bg = ($bg == '#eeeeee' ? '#ffffff' : '#eeeeee'); // Ternary operator style syntax
			
			echo '<tr bgcolor="' . $bg . '">
				<td align="left">' . $fnOB . '</td>
				<td align="left">' . $lnOB . '</td>
				<td align="left">' . $drOB . '</td>
				<td align="left"><a href="edit_user.php?id=' . $idOB . '">Edit</a></td>
				<td align="left"><a href="delete_user.php?id=' .$idOB . '">Delete</a></td>
				</td></tr>';	
		}	// End of WHILE loop
		
		echo '</table><br />';
		
		// Close the statement:
		$stmt->close();
		unset($stmt);

		// Close the connection:
		$db->close();
		unset($db);

	}
	else 
	{	// No registered users
		echo '<p class="error">There are no registered users.</p>';
	}

	// Make the links to other pages, if necessary.
	if ($pages > 1)
	{
		// Add some spacing and start a paragraph:
		echo '<br /><p><br />';

		// Determine what page the script is on:
		$current_page = ($start/$display) + 1;
		
		// If it's not the first page, make a Previous Link:
		if ($current_page != 1)
		{
			echo '<a href="view_users.php?s=' . ($start - $display) . '&p=' . $pages . 
				'$sort=' . $sort . '">Previous</a> ';
		}
		
		// Make all the numbered pages:
		for ($i = 1; $i <= $pages; $i++)
		{
			if ($i != $current_page)
			{
				echo '<a href="view_users.php?s=' . (($display * ($i - 1))) . '&p=' . $pages . 
					'$sort=' . $sort . '">' . $i . '</a> ';
			}
			else
			{
				echo $i . ' ';
			}
		}	// End of FOR loop
		
		// If it's not the last page, make a Next button:
		if ($current_page != $pages)
		{
			echo '<a href="view_users.php?s=' . ($start + $display) . '&p=' . $pages . 
				'$sort=' . $sort . '">Next</a>';
		}
		echo '</p><br />'; // Close the paragraph
	} // End of links secton.	
			
	include 'includes/footer.html';
?>
