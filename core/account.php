<?php
	// account.php
	// This page is for users to change their account settings.
	
	require '../includes/config.php';
	$page_title = 'digoro : My Account';
	include '../includes/header.html';
	include '../includes/php-functions.php';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	} 

	// Assign user object from session variable
	if (isset($_SESSION['userObj']))
	{
		$user = $_SESSION['userObj'];
	}
	else 
	{
		redirect_to('index.php');
	}

	// Need the database connection:
	require_once MYSQL2;

	$user->pullUserData();
	$userID = $user->getUserID();

	// Grab users database birthday
	$bdOB = $user->getUserAttribute('bday');

	if ($_SERVER['REQUEST_METHOD'] == 'POST') // Confirmation that form has been submitted	
	{

		if ($_POST['edit-user-fname']) {
			$fname = $_POST['edit-user-fname'];
		}
		else {
			echo 'Please enter a valid first name';
			exit();
		}		
		
		if ($_POST['edit-user-lname']) {
			$lname = $_POST['edit-user-lname'];
		}
		else {
			echo 'Please enter a valid last name';
			exit();
		}		
		
		if ($_POST['edit-user-city']) {
			$city = $_POST['edit-user-city'];
		}
		else {
			$city = '';
		}				
		
		if ($_POST['edit-user-state']) {
			$state = $_POST['edit-user-state'];
		}
		else {
			$state = '';
		}				

		if ($_POST['edit-user-zip']) {
			$zip = $_POST['edit-user-zip'];
		}
		else {
			$zip = '';
		}		

		if ($_POST['edit-user-sel-sex']) {
			$sex = $_POST['edit-user-sel-sex'];
		}
		else {
			echo 'Please select your sex';
			exit();
		}	

		if ($_POST['edit-user-phone']) {
			$phone = $_POST['edit-user-phone'];
		}
		else {
			$phone = '';
		}

		if ($_POST['DateOfBirth_Day']) {
			$bdday = $_POST['DateOfBirth_Day'];
		}
		else {
			echo 'Please enter your birthday day';
			exit();
		}

		if ($_POST['DateOfBirth_Month']) {
			$bdmnth = $_POST['DateOfBirth_Month'];
		}
		else {
			echo 'Please enter your birthday month';
			exit();
		}

		if ($_POST['DateOfBirth_Year']) {
			$bdyr = $_POST['DateOfBirth_Year'];
		}
		else {
			echo 'Please enter your birthday year';
			exit();
		}

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
	
		// Check if user entered information is valid before continuing to edit event
		if ($bdfrmat && $fname && $lname && $sex) {
			$user->editAccount($fname, $lname, $city, $state, $zip, $sex, $phone, $bdfrmat);
		}
		else {	// Errors in the user entered information
			echo 'Please try again';
			exit();
		}
	}

	// Delete objects
	unset($user);
			
	// Close the connection:
	$db->close();
	unset($db);

?>

	<div class="container" id="contentWrapper">
		<div class="row"> <!-- Main row - for all content except footer -->	
			<div class="span2"> <!-- column for icons --> 
				<div class="well">
				<div class="side-nav">
				<ul class="nav nav-list">
					<li>
						<a href="../manager/home.php"><img src="../css/imgs/home-icon.png" 
							alt="home-icon" height="60" width="60"></a>
					</li>
					<li><p>Home</p></li>
					<li>
						<a href="../manager/profile.php"><img src="../css/imgs/user-icon.png" 
							alt="user-icon" height="60" width="60"></a>	
					</li>
					<li><p>Profiles</p></li>
					<li>
						<a href="../manager/my_teams.php"><img src="../css/imgs/clipboard-icon.png" 
							alt="clipboard-icon" height="60" width="60"></a>	
					</li>
					<li><p>My Teams</p></li>
					<li>
						<a href="../manager/find_players.php"><img src="../css/imgs/binoculars-icon.png" 
							alt="binoculars-icon" height="60" width="60"></a>
					</li>
					<li><p>Find Players</p></li>
					<li>
						<a href=""><img src="../css/imgs/world-icon.png" 
							alt="world-icon" height="60" width="60"></a>
					</li>
					<li><p>Find Teams</p></li>		
				</ul>
				</div>
				</div>
			</div> <!-- end of column for icons --> 
					
			<div class="span10"> <!-- column for main content --> 
				<div class="row"> <!-- Header row -->
					<div class="span10">
						<div class="page-header"><h1>Account</h1></div>
					</div>

				<!-- Edit Account Form -->
				<div id="EditAccountForm" title="Edit Account">	
					<form method="post" class="form-horizontal">
						<div class="control-group">			
							<label class="control-label" for="edit-user-fname">First name:</label>
							<div class="controls">
								<input type="text" name="edit-user-fname" id="edit-user-fname" size="20" maxlength="20" />
							</div>
						</div>
			
						<div class="control-group">			
							<label class="control-label" for="edit-user-lname">Last name:</label>
							<div class="controls">
									<input type="text" name="edit-user-lname" id="edit-user-lname" size="20" maxlength="40" />
							</div>
						</div>
			
						<div class="control-group">			
							<label class="control-label" for="edit-user-city">City:</label>
							<div class="controls">
								<input type="text" name="edit-user-city" id="edit-user-city" size="30" maxlength="40" />
							</div>
						</div>

						<div class="control-group">			
							<label class="control-label" for="edit-user-state">State:</label>
							<div class="controls">
								<input type="text" name="edit-user-state" id="edit-user-state" size="2" maxlength="2" />
							</div>
						</div>

						<div class="control-group">			
							<label class="control-label" for="edit-user-zip">Zipcode:</label>
							<div class="controls">
								<input type="text" name="edit-user-zip" id="edit-user-zip" size="5" maxlength="5" />
							</div>
						</div>
			
						<div class="control-group">	
							<label class="control-label" for="edit-user-sel-sex">Sex:</label>
							<div class="controls">
								<select class="input-medium" name="edit-user-sel-sex" id="edit-user-sel-sex">
									<option value="">-Select Sex-</option>
									<option value="1">Female</option>
									<option value="2">Male</option>
								</select>
							</div>
						</div>
						
						<div class="control-group">	
							<label class="control-label" for="edit-user-phone">Phone:</label>
							<div class="controls">
								<input type="text" name="edit-user-phone" id="edit-user-phone" size="15" maxlength="15" />
							</div>
						</div>						
			
						<div class="control-group">	
							<label class="control-label" for="bday">Select Your Birthdate:</label>
							<div class="controls controls-row">
								<select class="input-medium" name="DateOfBirth_Month" id="bdayM">
									<option value="">- Month -</option>
									<option value="01">January</option>
									<option value="02">Febuary</option>
									<option value="03">March</option>
									<option value="04">April</option>
									<option value="05">May</option>
									<option value="06">June</option>
									<option value="07">July</option>
									<option value="08">August</option>
									<option value="09">September</option>
									<option value="10">October</option>
									<option value="11">November</option>
									<option value="12">December</option>
								</select>
			
								<select class="input-small" name="DateOfBirth_Day" id="bdayD">
									<option value="">- Day -</option>
									<option value="01">1</option>
									<option value="02">2</option>
									<option value="03">3</option>
									<option value="04">4</option>
									<option value="05">5</option>
									<option value="06">6</option>
									<option value="07">7</option>
									<option value="08">8</option>
									<option value="09">9</option>
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
			
								<select class="input-small" name="DateOfBirth_Year" id="bdayY">
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
								<span class="help-inline"><a href="help.php">Why do I need to provide my birthday?</a></span>
							</div>
						</div>
						<button type="submit" id="editaccount" class="btn btn-primary">Save</button>
					</form>	<!-- End of Edit Account Form -->
				</div>
					<p><a href="change_password.php">Change Password</a></p>
					<p><a href="change_email.php">Change Email</a></p>
					<p><a href="delete_acct.php">Delete Account</a></p>

				</div> <!-- End of main row -->
				
	<!-- External javascript call -->
	<script type="text/javascript" src="../js/account.js"></script>

<?php include '../includes/footer.html'; ?>
