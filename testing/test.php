<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="author" content="Frank" />
		<!-- External javascript call -->
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" charset="utf-8"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" charset="utf-8"></script>
		<script src="jquery.validate.min.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" src="projectblackstar.js"></script>
		<script type="text/javascript" src="scripts.js"></script>
		<!-- CSS Style Sheet -->
		<link rel="stylesheet" href="css/base.css" type="text/css" media="screen" charset="utf-8"/>
		<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" href="css/panel.css" type="text/css" media="screen" charset="utf-8"/>
	</head>
	<body>
		<div id="container">
		<div id="header">
			<h1>digoro</h1>
			<h2>Connecting teams and players!</h2>
		</div>
		<div id="content">

			<div class="cart">
			        <a href="#" title="Shopping Cart">View Cart</a>
			</div>

      <div id="signup">
        <h2>Sign up</h2>  
        <form action="">
          <div>
            <label for="name">Name:</label>
            <input name="name" id="name" type="text"/>
          </div>
          <div>
            <label for="email">Email:</label>
            <input name="email" id="email" type="text"/>
          </div>
          <div>
            <label for="website">Web site URL:</label>
            <input name="website" id="website" type="text" />
          </div>      
          <div>
            <label for="password">Password:</label>
            <input name="password" id="password" type="password" />
          </div>   
          <div>
            <label for="passconf">Confirm Password:</label>
            <input name="passconf" id="passconf" type="password" />
          </div>      
          <div>
          	<input type="submit" value="Submit" />
          </div>
        </form>
       </div>
			
		<div id="info">
	      <ul>
	        <li><a href="view_roster-j.php"><span>Roster</span></a></li>
	        <li><a href="view_soc_sch.php"><span>Schedule</span></a></li>
	        <li><a href="register.php"><span>Register</span></a></li>
	      </ul>
		</div><br />

	      <ul id="menu">
	        <li><a href="#">What's new?</a>
	          <ul class="active">
	            <li><a href="#">Weekly specials</a></li>
	            <li><a href="#">Last night's pics!</a></li>
	            <li><a href="#">Users' comments</a></li>
	          </ul>
	        </li>
	        <li><a href="#">Member extras</a>
	          <ul>
	            <li><a href="#">Premium Celebrities</a></li>
	            <li><a href="#">24-hour Surveillance</a></li>
	          </ul>
	        </li>
	        <li><a href="#">About Us</a>
	          <ul>
	            <li><a href="#">Privacy Statement</a></li>
	            <li><a href="#">Terms and Conditions</a></li>
	            <li><a href="#">Contact Us</a></li>
	          </ul>
	        </li>
	      </ul>
		
			<p class="test">Frank it's super late but you gotta keep coding - no sleep when you believe in an idea!</p>
	
			<p id="disclaimer" class="test">In order to use this site you must agree to our privacy and term of use policy.</p>
	
			<div id="bio">
				<h2>Click a Player Below</h2>
				
				<h3>Frank Oppong</h3>
				<div>
					<img src="css/imgs/FrankPort.jpg" width="100" height="100" alt="Frank Oppong" />
					<p>Content about Frank Oppong</p>
				</div>
				
				<h3>Jake Cody</h3>
				<div>
					<img src="css/imgs/FrankPort2.jpg" width="100" height="100" alt="Jake Cody" />
					<p>Content about Jake Cody</p>
				</div>
				
				<h3>Amy Winn</h3>
				<div>
					<img src="css/imgs/FrankSoccerKid 001.jpg" width="100" height="100" alt="Amy Winn" />
					<p>Content about Amy Winn</p><br />
				</div>
			</div>		
	
			<p class="test">The first thing you need to know about Hot Gloo is that it is 
			in Beta, the second thing you need to know is that it is currently free, 
			but with plans to charge a fee later on this year, so, grab your chance now while its free, its worth it.</p>
	
			<p class="test">Hey man, you gotta make this paragraph dance like Frank can dance.</p>
	
			<a href="css/imgs/FrankPort.jpg" class="lightbox">Picture</a>
	
		    <div id="news">
		      <h2>Latest News</h2>
		      <p>
		        Which member of the seminal calypso/lectro band <em>C&amp;C Music Sweatshop</em> was spotted last night at <em>Dirt</em>, the trendy New York restaurant that serves only food caught and retrieved by the chef's own hands?
		        <span class="spoiler">Yes! It's the ever-effervescent, <em>Glendatronix</em>!</span>
		      </p>
		      <p>Who lost their recording contract today? <span class="spoiler">The Zaxntines!</span></p>
		    </div>
		</div>
      
      <div id="comment">
        <h2>Leave a comment</h2>
        name:<br />
        <input type="text" /><br/>
        comment:<br/>
        <textarea rows="5" cols="30"></textarea>
      </div>
      
	 </div>
	 </body>
</html>