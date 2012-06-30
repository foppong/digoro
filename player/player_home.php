<?php 
	// manager_homepage.php
	// This is the Manager Homepage
	require '../includes/config.php';
	$page_title = 'Welcome to digoro!';
	include '../includes/f_header.html';

	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

?>
  <h3>Stuffed Cookies</h3>
  <p>
    <img title="Stuffed Cookies" 
         src="../css/imgs/digoro_splash 800x600.jpg" 
         width="550"/>
  </p>

  <br>
  <form>
    <input type="button" value="Cook" onclick="postCook()" />
  </form>


<?php include '../includes/footer.html'; ?>