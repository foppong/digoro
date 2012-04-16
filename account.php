<?php
	// account.php
	// This page is for users to change their account settings.
	
	require 'includes/config.php';
	$page_title = 'digoro : My Account';
	include 'includes/header.html';

	// Authorized Login Check
	// If no session value is present, redirect the user. Also validate the HTTP_USER_AGENT	
	if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
	{
		session_unset();
		session_destroy();
		$url = BASE_URL . 'index.php';
		ob_end_clean();
		header("Location: $url");
		exit();
	}
	
	echo '<h2>Edit Account</h2>';

	// Check for a valid user ID, through GET or POST:
	if ( (isset($_SESSION['userID'])) && (is_numeric($_SESSION['userID'])) )
	{
		// Point A in Code Flow
		// Assign variable from index.php using session variables
		$id = $_SESSION['userID'];
	}
	else 
	{
		// No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error.</p>';
		include 'includes/footer.html';
		exit();
	}

	// Establish database connection
	require_once MYSQL;

	// Confirmation that form has been submitted:	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{	// Point D in Code Flow

		// Trim all the incoming data:
		$trimmed = array_map('trim', $_POST);
		
		// Assume invalid values:
		$fn = $ln = $e = $cty = $st = $zp = $bd = $gd = $pnumb = FALSE;
		
		// Validate firstname
		if (preg_match('/^[A-Z \'.-]{2,20}$/i', $trimmed['first_name']))
		{
			$fn = $trimmed['first_name'];
		}
		else 
		{
			echo '<p class="error">Please enter valid first name.</p>';
		}
	
		// Validate lastname
		if (preg_match('/^[A-Z \'.-]{2,40}$/i', $trimmed['last_name']))
		{
			$ln = $trimmed['last_name'];
		}
		else 
		{
			echo '<p class="error">Please enter valid last name.</p>';
		}

		// Validate email
		if (filter_var($trimmed['email'], FILTER_VALIDATE_EMAIL))
		{
			$e = $trimmed['email'];
		}
		else 
		{
			echo '<p class="error">Please enter valid email address.</p>';
		}

		// Validate city
		if (preg_match('/^[A-Z \'.-]{2,40}$/i', $trimmed['city']))
		{
			$cty = $trimmed['city'];
		}
		else 
		{
			$cty = '';
		}

		// Validate city
		if (preg_match('/^[A-Z \'.-]{2,2}$/i', $trimmed['state']))
		{
			$st = $trimmed['state'];
		}
		else 
		{
			$st = '';
		}

		// Validate zipcode
		if (filter_var($_POST['zipcode'], FILTER_VALIDATE_INT))
		{
			$zp = $_POST['zipcode'];
		}
		else 
		{
			echo '<p class="error">Please enter a valid zip code.</p>';
		}
		
		// Validate a gender is selected
		if ($_POST['sex'])
		{
			$gd = $_POST['sex'];
		}
		else 
		{
			echo '<p class="error">Please select your gender.</p>'; 
		}

		// Validate phone number
		if (is_string($_POST['phone']))
		{
			$pnumb = $_POST['phone'];
		}
		else 
		{
			$pnumb = '';
		}

		// Assign form birthdate inputs to variables
		$bdmnth = $_POST['DateOfBirth_Month'];
		$bdday = $_POST['DateOfBirth_Day'];
		$bdyr = $_POST['DateOfBirth_Year'];
		$bdarray = array($bdyr, $bdmnth, $bdday);

		// Validate if date entered is actually a date
		if (checkdate($bdmnth, $bdday, $bdyr))
		{
			$bdstring = implode("-", $bdarray);
			$bd = new DateTime($bdstring);
			$bdfrmat = $bd->format('Y-m-d');
		}
		else 
		{
			echo '<p class="error">Please enter a valid birthdate.</p>';
		}

		// Check if user entered information is valid before continuing to edit user
		if ($fn && $ln && $e && $zp && $bdfrmat && $gd)
		{
			// Make the query to make sure User's new email is available	
			$q = 'SELECT id_user,email FROM users WHERE email=? AND id_user !=? LIMIT 1';

			// Prepare the statement
			$stmt = $db->prepare($q);

			// Bind the inbound variable:
			$stmt->bind_param('si', $e, $id);

			// Execute the query:
			$stmt->execute();
			
			// Store result
			$stmt->store_result();

			// User login available, i.e. querey found nothing
			if ($stmt->num_rows == 0) 
			{
				// Update the user's info in the database
				$q = 'UPDATE users SET email=?, first_name=?, last_name=?, city=?, state=?, zipcode=?, gender=?, birth_date=?, phone_num=?
					WHERE id_user=? LIMIT 1';

				// Prepare the statement
				$stmt = $db->prepare($q); 

				// Bind the inbound variables:
				$stmt->bind_param('sssssissii', $e, $fn, $ln, $cty, $st, $zp, $gd, $bdfrmat, $pnumb, $id);
				
				// Execute the query:
				$stmt->execute();

				if ($stmt->affected_rows == 1) // And update to the database was made
				{				
					echo '<p>The users account has been edited.</p>';
				}
				else 
				{	// Either did not run ok or no updates were made
					echo '<p>No changes were made.</p>';
				}
			}
			else
			{	// Email is already registered
				echo '<p class="error">The email address has already been registered.</p>';
			}	
		}
		else
		{	// Errors in the user entered information
			echo '<p class="error">Please try again.</p>';
		}
	}	// End of submit conditional.

	// Point B in Code Flow
	// Always show the form...
	
	// Make the query to retreive user information:
	$q = 'SELECT email, first_name, last_name, city, state, zipcode, gender, birth_date, phone_num FROM users 
			WHERE id_user=? LIMIT 1';		

	// Prepare the statement:
	$stmt = $db->prepare($q);

	// Bind the inbound variable:
	$stmt->bind_param('i', $id);
		
	// Execute the query:
	$stmt->execute();		
		
	// Store results:
	$stmt->store_result();
	
	// Bind the outbound variable:
	$stmt->bind_result($emailOB, $fnOB, $lnOB, $cityOB, $stOB, $zpOB, $gdOB, $bdOB, $pnumOB);	
		
	// Valid user ID, show the form.
	if ($stmt->num_rows == 1)
	{
		while ($stmt->fetch())
		{

			// Set up for sticky birthday form
			$bdarrayOB = explode("-", $bdOB);
			$bdyrOB = $bdarrayOB[0];
			$bdmnthOB = $bdarrayOB[1];
			$bddayOB = $bdarrayOB[2];
			
			switch ($bdmnthOB)
			{
				case '1':
					$mth = 'January';
					break;
				case '2':
					$mth = 'Febuary';
					break;
				case '3':
					$mth = 'March';
					break;
				case '4':
					$mth = 'April';
					break;
				case '5':
					$mth = 'May';
					break;
				case '6':
					$mth = 'June';
					break;
				case '7':
					$mth = 'July';
					break;
				case '8':
					$mth = 'August';
					break;
				case '9':
					$mth = 'September';
					break;
				case '10':
					$mth = 'October';
					break;
				case '11':
					$mth = 'November';
					break;
				case '12':
					$mth = 'December';
					break;																							
				default:
					$mth = NULL;
					break;
			}

			// Set up for sticky gender select in form
			if ($gdOB == "F")
			{
				$Fsel = ' selected="selected"';
				$Msel = NULL;
			}
			else 
			{
				$Fsel = NULL;
				$Msel = ' selected="selected"';				
			}

			// Create the form:
			echo '<form action ="account.php" method="post" id="EditAccountForm">
				<fieldset>
				<div>
					<label for="first_name"><b>First Name:</b></label>
					<input type="text" name="first_name" id="first_name" size="20" maxlength="20" 
					value="' . $fnOB . '" />				
				</div>

				<div>
					<label for="last_name"><b>Last Name:</b></label>
					<input type="text" name="last_name" id="last_name" size="20" maxlength="40" 
					value="' . $lnOB . '" />
				</div>

				<div>
					<label for="email"><b>Email Address:</b></label>
					<input type="text" name="email" id="email" size="30" maxlength="60" 
					value="' . $emailOB . '" />
				</div>
				
				<div>
					<label for="city"><b>City:</b></label>
					<input type="text" name="city" id="city" size="10" maxlength="40" 
					value="' . $cityOB . '" />
				</div>

				<div>
					<label for="state"><b>State:</b></label>
					<input type="text" name="state" id="state" size="2" maxlength="2" 
					value="' . $stOB . '" /><i><small>Ex: CA</small></i>
				</div>

				<div>
					<label for="zipcode"><b>Zip Code:</b></label>
					<input type="text" name="zipcode" id="zipcode" size="5" maxlength="5" 
					value="' . $zpOB . '" />
				</div>
				
				<div>
					<label for="phone"><b>Phone:</b></label>
					<input type="text" name="phone" id="phone" size="15" maxlength="15" 
					value="' . $pnumOB . '" /><i><small>Ex: 1112223333</small></i>
				</div>				

				<div>
					<label for="ssex"><b>Sex:</b></label>
					<select name="sex" id="sex"> 
						<option value=""> - Select Sex - </option>
						<option' . $Fsel . ' value="F">Female</option>
						<option' . $Msel . ' value="M">Male</option>
					</select>
				</div>

				<div>
					<label for="bdayM"><b>Birthdate:</b></label>
					<select name="DateOfBirth_Month" id="bdayM">
						<option value="'. $bdmnthOB . '">' . $mth . '</option>
						<option value="1">January</option>
						<option value="2">Febuary</option>
						<option value="3">March</option>
						<option value="4">April</option>
						<option value="5">May</option>
						<option value="6">June</option>
						<option value="7">July</option>
						<option value="8">August</option>
						<option value="9">September</option>
						<option value="10">October</option>
						<option value="11">November</option>
						<option value="12">December</option>
					</select>

					<select name="DateOfBirth_Day" id="bdayD">
						<option value="'. $bddayOB . '">' . $bddayOB . '</option>
						<option value="1">1</option><option value="2">2</option>
						<option value="3">3</option><option value="4">4</option>
						<option value="5">5</option><option value="6">6</option>
						<option value="7">7</option><option value="8">8</option>
						<option value="9">9</option><option value="10">10</option>
						<option value="11">11</option><option value="12">12</option>
						<option value="13">13</option><option value="14">14</option>
						<option value="15">15</option><option value="16">16</option>
						<option value="17">17</option><option value="18">18</option>
						<option value="19">19</option><option value="20">20</option>
						<option value="21">21</option><option value="22">22</option>
						<option value="23">23</option><option value="24">24</option>
						<option value="25">25</option><option value="26">26</option>
						<option value="27">27</option><option value="28">28</option>
						<option value="29">29</option><option value="30">30</option>
						<option value="31">31</option>
					</select>
				
					<select name="DateOfBirth_Year" id="bdayY">
						<option value="' . $bdyrOB . '">' . $bdyrOB . '</option><option value="2004">2004</option><option value="2003">2003</option>
						<option value="2002">2002</option><option value="2001">2001</option><option value="2000">2000</option>
						<option value="1999">1999</option><option value="1998">1998</option><option value="1997">1997</option>
						<option value="1996">1996</option><option value="1995">1995</option><option value="1994">1994</option>
						<option value="1993">1993</option><option value="1992">1992</option><option value="1991">1991</option>
						<option value="1990">1990</option><option value="1989">1989</option><option value="1988">1988</option>
						<option value="1987">1987</option><option value="1986">1986</option><option value="1985">1985</option>
						<option value="1984">1984</option><option value="1983">1983</option><option value="1982">1982</option>
						<option value="1981">1981</option><option value="1980">1980</option><option value="1979">1979</option>
						<option value="1978">1978</option><option value="1977">1977</option><option value="1976">1976</option>
						<option value="1975">1975</option><option value="1974">1974</option><option value="1973">1973</option>
						<option value="1972">1972</option><option value="1971">1971</option><option value="1970">1970</option>
						<option value="1969">1969</option><option value="1968">1968</option><option value="1967">1967</option>
						<option value="1966">1966</option><option value="1965">1965</option><option value="1964">1964</option>
						<option value="1963">1963</option><option value="1962">1962</option><option value="1961">1961</option>
						<option value="1960">1960</option><option value="1959">1959</option><option value="1958">1958</option>
						<option value="1957">1957</option><option value="1956">1956</option><option value="1955">1955</option>
						<option value="1954">1954</option><option value="1953">1953</option><option value="1952">1952</option>
						<option value="1951">1951</option><option value="1950">1950</option><option value="1949">1949</option>
						<option value="1948">1948</option><option value="1947">1947</option><option value="1946">1946</option>
						<option value="1945">1945</option><option value="1944">1944</option><option value="1943">1943</option>
						<option value="1942">1942</option><option value="1941">1941</option><option value="1940">1940</option>
						<option value="1939">1939</option><option value="1938">1938</option><option value="1937">1937</option>
						<option value="1936">1936</option><option value="1935">1935</option><option value="1934">1934</option>
						<option value="1933">1933</option><option value="1932">1932</option><option value="1931">1931</option>
						<option value="1930">1930</option><option value="1929">1929</option><option value="1928">1928</option>
						<option value="1927">1927</option><option value="1926">1926</option><option value="1925">1925</option>
						<option value="1924">1924</option><option value="1923">1923</option><option value="1922">1922</option>
						<option value="1921">1921</option><option value="1920">1920</option><option value="1919">1919</option>
						<option value="1918">1918</option><option value="1917">1917</option><option value="1916">1916</option>
						<option value="1915">1915</option><option value="1914">1914</option><option value="1913">1913</option>
						<option value="1912">1912</option><option value="1911">1911</option><option value="1910">1910</option>
						<option value="1909">1909</option><option value="1908">1908</option><option value="1907">1907</option>
						<option value="1906">1906</option><option value="1905">1905</option><option value="1904">1904</option>
						<option value="1903">1903</option><option value="1902">1902</option><option value="1901">1901</option>
						<option value="1900">1900</option>
					</select>
					<small>You must be 18 years old to use this site. <a href="help.php">Why must I be 18?</a></small>
				</div>

				<input type="submit" name="submit" value="Save" />
				</fieldset>
				</form><br />';
			echo '<a href="change_password.php">Change Password</a><br />';
			echo '<a href="delete_acct.php">Delete Account</a><br />';
		}
	}
	else 
	{	//Not a valid user ID, kill the script
		echo '<p class="error">This page has been accessed in error.</p>';
		include 'includes/footer.html';
		exit();
	}
		
	// Close the statement:
	$stmt->close();
	unset($stmt);
			
	// Close the connection:
	$db->close();
	unset($db);
					
	include 'includes/footer.html';
?>