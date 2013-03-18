<?php 
	// register.php
	// This page is used to create new users to the site.

	require_once '../includes/config.php';
	$page_title = 'digoro : Register';	
	include '../includes/iheader.html';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Code to create a new user
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// Need the database connection:	
		require MYSQL2;
		
		// Assume invalid values:
		$fn = $ln = $e = $p = $zp = $bd = $mstatus = $gd = FALSE;
		
		// Validate firstname
		if (preg_match('/^[A-Z \'.-]{2,20}$/i', $_POST['first_name']))
		{
			$fn = $_POST['first_name'];
		}
		else 
		{
			echo '<p class="error">Please enter valid first name.</p>';
		}
	
		// Validate lastname
		if (preg_match('/^[A-Z \'.-]{2,40}$/i', $_POST['last_name']))
		{
			$ln = $_POST['last_name'];
		}
		else 
		{
			echo '<p class="error">Please enter valid last name.</p>';
		}

		// Validate email
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{
			$e = $_POST['email'];
		}
		else 
		{
			echo '<p class="error">Please enter valid email address.</p>';
		}

		// Validate password
		if (strlen($_POST['pass1']) > 5)
		{
			if ($_POST['pass1'] == $_POST['pass2'])
			{
				$p = $_POST['pass1'];
			}
			else 
			{
				echo '<p class="error">Your password did not match the confirmed password.</p>';
			}
		}
		else 
		{
			echo '<p class="error"> Please enter a valid password.</p>';
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

		// Validate a role is selected
		if ($_POST['role'])
		{
			$mstatus = $_POST['role'];
		}
		else
		{
			echo '<p class="error">Please select a role.</p>';
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

		// Set invited variable to False.
		$iv = 0;
		
		// Check if user entered values are valid before proceeding
		if ($fn && $ln && $e && $p && $zp && $bd && $mstatus && $gd)
		{
			$user = new UserAuth($dbObject);
			$user->createUser($e, $p, $fn, $ln, $mstatus, $zp, $gd, $bdfrmat, $iv);
			unset($user);
		}
		else
		{	// Form submitted information was not valid
			echo '<p class="error">Please try again.</p>';
		}

		// Close the connection:
		$db->close();
		unset($db);
		
	} // End of Form Submission
?>

	<!-- New Member Create Form -->
	<h2>Sign Up</h2>
	<form action="register.php" method="post" id="SignUpForm">
		<fieldset>
		<input type="hidden" name="op" value="new">

		<div>
			<label for="role"><b>Are You A Manager or Player/Free Agent?</b></label>
			<select name="role" id="role">
				<option value="">- Select Role -</option>
				<option value="M">Manager</option>
				<option value="P">Player/Free Agent</option>
			</select><br />
			<small>If you are both, just select manager. Click <a href="help.php">here</a> for more information.</small>
		</div>
		
		<div>
			<label for="first_name"><b>First Name:</b></label>
			<input type="text" name="first_name" id="first_name" size="20" maxlength="20"
			value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name']; ?>" />
		</div>
	
		<div>
			<label for="last_name"><b>Last Name:</b></label>
			<input type="text" name="last_name" id="last_name" size="20" maxlength="40"
			value="<?php if (isset($_POST['last_name'])) echo $_POST['last_name']; ?>" />
		</div>
	
		<div>
			<label for="email"><b>Email Address:</b></label>
			<input type="text" name="email" id="email" size="30" maxlength="60"
			value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" />
		</div>
	
		<div>
			<label for="pass1"><b>Enter A New Password:</b></label>
			<input type="password" name="pass1" id="pass1" size="20" maxlength="20"
			value="<?php if (isset($_POST['pass1'])) echo $_POST['pass1']; ?>" />
			<small>Password must be between 6 and 20 characters long.</small>
		</div>
	
		<div>
			<label for="pass2"><b>Confirm Password:</b></label>
			<input type="password" name="pass2" id="pass2" size="20" maxlength="20"
			value="<?php if (isset($_POST['pass2'])) echo $_POST['pass2']; ?>" />
		</div>
	
		<div>
			<label for="zipcode"><b>Enter Your Zip Code:</b></label>
			<input type="text" name="zipcode" id="zipcode" size="5" maxlength="5" 
			value="<?php if (isset($_POST['zipcode'])) echo $_POST['zipcode']; ?>" />
		</div>
		
		<div>
			<label for="sex"><b>Select Your Gender:</b></label>
			<select name="sex" id="sex">
				<option value="">- Select Sex -</option>
				<option value="F">Female</option>
				<option value="M">Male</option>
			</select>
		</div>
	
		<div>
			<label for="bday"><b>Select Your Birthdate:</b></label>
			<select name="DateOfBirth_Month" id="bdayM">
				<option value="">- Month -</option>
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
				<option value="">- Day -</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
				<option value="31">31</option>
			</select>

			<select name="DateOfBirth_Year" id="bdayY">
				<option value="">- Year -</option>
				<option value="2004">2004</option><option value="2003">2003</option><option value="2002">2002</option>
				<option value="2001">2001</option><option value="2000">2000</option><option value="1999">1999</option>
				<option value="1998">1998</option><option value="1997">1997</option><option value="1996">1996</option>
				<option value="1995">1995</option><option value="1994">1994</option><option value="1993">1993</option>
				<option value="1992">1992</option><option value="1991">1991</option><option value="1990">1990</option>
				<option value="1989">1989</option><option value="1988">1988</option><option value="1987">1987</option>
				<option value="1986">1986</option><option value="1985">1985</option><option value="1984">1984</option>
				<option value="1983">1983</option><option value="1982">1982</option><option value="1981">1981</option>
				<option value="1980">1980</option><option value="1979">1979</option><option value="1978">1978</option>
				<option value="1977">1977</option><option value="1976">1976</option><option value="1975">1975</option>
				<option value="1974">1974</option><option value="1973">1973</option><option value="1972">1972</option>
				<option value="1971">1971</option><option value="1970">1970</option><option value="1969">1969</option>
				<option value="1968">1968</option><option value="1967">1967</option><option value="1966">1966</option>
				<option value="1965">1965</option><option value="1964">1964</option><option value="1963">1963</option>
				<option value="1962">1962</option><option value="1961">1961</option><option value="1960">1960</option>
				<option value="1959">1959</option><option value="1958">1958</option><option value="1957">1957</option>
				<option value="1956">1956</option><option value="1955">1955</option><option value="1954">1954</option>
				<option value="1953">1953</option><option value="1952">1952</option><option value="1951">1951</option>
				<option value="1950">1950</option><option value="1949">1949</option><option value="1948">1948</option>
				<option value="1947">1947</option><option value="1946">1946</option><option value="1945">1945</option>
				<option value="1944">1944</option><option value="1943">1943</option><option value="1942">1942</option>
				<option value="1941">1941</option><option value="1940">1940</option><option value="1939">1939</option>
				<option value="1938">1938</option><option value="1937">1937</option><option value="1936">1936</option>
				<option value="1935">1935</option><option value="1934">1934</option><option value="1933">1933</option>
				<option value="1932">1932</option><option value="1931">1931</option><option value="1930">1930</option>
				<option value="1929">1929</option><option value="1928">1928</option><option value="1927">1927</option>
				<option value="1926">1926</option><option value="1925">1925</option><option value="1924">1924</option>
				<option value="1923">1923</option><option value="1922">1922</option><option value="1921">1921</option>
				<option value="1920">1920</option><option value="1919">1919</option><option value="1918">1918</option>
				<option value="1917">1917</option><option value="1916">1916</option><option value="1915">1915</option>
				<option value="1914">1914</option><option value="1913">1913</option><option value="1912">1912</option>
				<option value="1911">1911</option><option value="1910">1910</option><option value="1909">1909</option>
				<option value="1908">1908</option><option value="1907">1907</option><option value="1906">1906</option>
				<option value="1905">1905</option><option value="1904">1904</option><option value="1903">1903</option>
				<option value="1902">1902</option><option value="1901">1901</option><option value="1900">1900</option>
			</select>
			<small>You must be 18 years old to register. <a href="help.php">Why must I be 18?</a></small>
		</div>
		</fieldset>		
		<div align="center"><input type="submit" name="submit" id="submit" value="Sign Up" />
	</form>
	
	<p><a href="forgot_password.php">Forgot your password?</a></p>
		
<?php include '../includes/ifooter.html'; ?>