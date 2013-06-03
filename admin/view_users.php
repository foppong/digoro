<?php
    // view_users.php
    // This script retrieves all the records from the users table.

    require_once('../includes/bootstrap.php');
    $page_title = 'digoro : View Current Users';
    require_once('../includes/header.html');

    // Site access level -> Administrator
    $lvl = 'A'; 

    // Assign user object from session variable
    if(isset($_SESSION['userObj'])) {
        $user = $_SESSION['userObj'];
    }
    else {
        session_unset();
        session_destroy();
        $url = BASE_URL . 'index.php';
        ob_end_clean();
        header("Location: $url");
        exit();
    }

    // Authorized Login Check
    if(!$user->valid($lvl)) {
        session_unset();
        session_destroy();
        $url = BASE_URL . 'index.php';
        ob_end_clean();
        header("Location: $url");
        exit();
    }

    // Page header:
    echo '<h1>Registered Users</h1>';
    
    // Number of records to show per page:
    $display = 3;

    // Determine how many pages there are...
    if(isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined
        $pages = $_GET['p'];
    }
    else { // Need to determine
        // Make the query to count the number of records
        $q = "SELECT COUNT(id_user) FROM users";

        // Execute the query & store result
        $records = $dbObject->getOne($q);

        // Calculate the number of pages...
        if($records > $display) { // More than 1 page
            $pages = ceil ($records/$display);
        }
        else {
            $pages = 1;
        }
    }

    // Determine where in the database to start returning results...
    if(isset($_GET['s']) && is_numeric($_GET['s'])) {
        $start = $_GET['s'];
    }
    else {
        $start = 0;
    }

    // Determine the sort...
    // Default is by registration date.
    $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'rd'; // Ternary operator style syntax

    // Determine the sorting order:
    switch($sort) {
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
          FROM users
          ORDER BY {$order_by}
          LIMIT {$start}, {$display}";

    // Execute the query & store result
    $results = $dbObject->getAll($q);

    // If there are results to show.
    if(count($result) > 0) {
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

        foreach($results as $result) {
            // Switch the background color.
            $bg = ($bg == '#eeeeee' ? '#ffffff' : '#eeeeee'); // Ternary operator style syntax

            echo '<tr bgcolor="' . $bg . '">
                <td align="left">' . $result['first_name'] . '</td>
                <td align="left">' . $result['last_name'] . '</td>
                <td align="left">' . $result['dr'] . '</td>
                <td align="left"><a href="edit_user.php?id=' . $result['id_user'] . '">Edit</a></td>
                <td align="left"><a href="delete_user.php?id=' .$result['id_user'] . '">Delete</a></td>
                </td></tr>';
        }    // End of WHILE loop

        echo '</table><br />';
    }
    else { // No registered users
        echo '<p class="error">There are no registered users.</p>';
    }

    // Make the links to other pages, if necessary.
    if($pages > 1) {
        // Add some spacing and start a paragraph:
        echo '<br /><p><br />';

        // Determine what page the script is on:
        $current_page = ($start/$display) + 1;

        // If it's not the first page, make a Previous Link:
        if($current_page != 1) {
            echo '<a href="view_users.php?s=' . ($start - $display) . '&p=' . $pages . 
                '$sort=' . $sort . '">Previous</a> ';
        }

        // Make all the numbered pages:
        for($i = 1; $i <= $pages; $i++) {
            if($i != $current_page) {
                echo '<a href="view_users.php?s=' . (($display * ($i - 1))) . '&p=' . $pages . 
                    '$sort=' . $sort . '">' . $i . '</a> ';
            }
            else {
                echo $i . ' ';
            }
        }    // End of FOR loop

        // If it's not the last page, make a Next button:
        if($current_page != $pages) {
            echo '<a href="view_users.php?s=' . ($start + $display) . '&p=' . $pages . 
                '$sort=' . $sort . '">Next</a>';
        }
        echo '</p><br />'; // Close the paragraph
    } // End of links secton.    

    require_once('../includes/footer.html');