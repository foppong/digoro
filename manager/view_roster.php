<?php 
	/*
	 * view_roster.php
	 * This page allows user to view roster.
	 */
	
	ob_start();
	session_start();

	require '../includes/config.php';
	include '../includes/php-functions.php';
	
	// autoloading of classes
	function __autoload($class) {
		require_once('../classes/' . $class . '.php');
	}

	// Validate user
	checkSessionObject();	
	
	// Check user role
	checkRole('m');
	
	// Check for a $page_title value:
	if (!isset($page_title))
	{
		$page_title = 'digoro';
	}

	$page_title = 'digoro : Roster';

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $page_title; ?></title>
		<meta name="author" content="Frank" />
		<!-- CSS Style Sheet -->

		<!-- External javascript call -->
		<script type="text/javascript" src="../js/roster.js"></script>
	</head>
	<body>

			<div class="row"> <!-- row for Team Name header -->
				<div class="span6">
					<h3><span class="page-header teamdisplay"></span> Roster</h3> <!-- Name dynamically inserted here -->
				</div>
				<div class="span2">
					<button type="button" id="add-member" class="btn btn-small btn-primary">Add Member</button>
				</div>
			</div>



		<!-- Modal Dialog Forms -->
		<div id="AddMemberForm" title="Add New Member">		
			<form method="post" class="form-horizontal">
				
				<div class="control-group">			
					<label class="control-label" for="add-member-fname">First name*</label>
					<div class="controls">
						<input type="text" class="input-medium" name="add-member-fname" id="add-member-fname" size="20" maxlength="20" />
					</div>
				</div>

				<div class="control-group">			
					<label class="control-label" for="add-member-lname">Last name*</label>
					<div class="controls">
						<input type="text" class="input-medium" name="add-member-lname" id="add-member-lname" size="20" maxlength="40" />
					</div>
				</div>

				<div class="control-group">	
					<label class="control-label" for="add-member-sel-sex">Member's sex is*</label>
					<div class="controls">
						<select class="input-medium" name="add-member-sel-sex" id="add-member-sel-sex">
							<option value="">-Select Sex-</option>
							<option value="1">Female</option>
							<option value="2">Male</option>
						</select>
					</div>
				</div>

				<div class="control-group">			
					<label class="control-label" for="add-member-email">Member's email is*</label>
					<div class="controls">
						<input type="text" class="input-medium" name="add-member-email" id="add-member-email" size="30" maxlength="60" />
					</div>
				</div>

				<div class="control-group">				
					<label class="control-label" for="add-member-ppos">Primary position*</label>
					<div class="controls">
						<input type="text" class="input-medium" name="add-member-ppos" id="add-member-ppos" size="20" maxlength="30" 
							placeholder="ex. striker"/>			
					</div>
				</div>

				<div class="control-group">	
					<label class="control-label" for="add-member-spos">Secondary position</label>
					<div class="controls">
						<input type="text" class="input-medium" name="add-member-spos" id="add-member-spos" size="20" maxlength="30"
							placeholder="ex. goalkeeper" />	
					</div>
				</div>

				<div class="control-group">	
					<label class="control-label" for="add-member-jernum">Jersey Number</label>
					<div class="controls">
						<input type="text" class="input-medium" name="add-member-jernum" id="add-member-jernum" size="4" maxlength="4" />
					</div>
				</div>

				<div class="control-group">	
					<label class="control-label">Send Invite?</label>
					<div class="controls">
						<label class="radio">
						  <input type="radio" name="add-member-invite" id="add-member-inviteY" value="1" checked>
						  Yes
						</label>
						<label class="radio">
						  <input type="radio" name="add-member-invite" id="add-member-inviteN" value="0">
						  No
						</label>
					</div>
				</div>
				<small>* Required Fields</small>
			</form>
		</div>
		
		<div id="EditMemberForm" title="Edit Member">	
			<form method="post" class="form-horizontal">
				<div class="control-group">
					<label class="control-label" for="edit-member-fname">First name</label>
					<div class="controls">
						<input type="text" class="input-medium" name="edit-member-fname" id="edit-member-fname" size="20" maxlength="20" />
					</div>
				</div>

				<div class="control-group">		
					<label class="control-label" for="add-member-lname">Last name</label>
					<div class="controls">
						<input type="text" class="input-medium" name="edit-member-lname" id="edit-member-lname" size="20" maxlength="40" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="edit-member-sel-sex">Member's sex is</label>
					<div class="controls">
						<select class="input-medium" name="edit-member-sel-sex" id="edit-member-sel-sex">
							<option value="">-Select Sex-</option>
							<option value="1">Female</option>
							<option value="2">Male</option>
						</select>
					</div>
				</div>

				<div class="control-group">			
					<label class="control-label" for="edit-member-ppos">Primary position:</label>
					<div class="controls">
						<input type="text" class="input-medium" name="edit-member-ppos" id="edit-member-ppos" size="20" maxlength="30" />			
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="edit-member-spos">Secondary position:</label>
					<div class="controls">
						<input type="text" class="input-medium" name="edit-member-spos" id="edit-member-spos" size="20" maxlength="30" />	
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="edit-member-jernum">Jersey Number:</label>
					<div class="controls">
						<input type="text" class="input-medium" name="edit-member-jernum" id="edit-member-jernum" size="4" maxlength="4" />
					</div>
				</div>
			</form>
		</div>

		<div id="DelMemberForm" title="Delete Member">
			<form method="post">
				<p>Are you sure you want to remove <span id="member_name"></span> this member?</p>
			</form>
		</div>
		<!-- End of Modal Dialog Form -->


		<div id="content">
			<!-- Load ajax roster data here -->
			<table class="table table-striped table-bordered table-condensed" id="roster" width="100%">
				<caption>
					Current Members
				</caption>
		</table>
		</div>

	</body>
</html>

<?php
	ob_end_flush();
?>		

