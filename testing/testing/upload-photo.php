<html>
	<body>
		<?php
			if ((($_FILES["uploadedphoto"]["type"] == "image/gif")
			|| ($_FILES["uploadedphoto"]["type"] == "image/jpeg")
			|| ($_FILES["uploadedphoto"]["type"] == "image/pjpeg"))
			&& ($_FILES["uploadedphoto"]["size"] < 250000))	
			{
				if ($_FILES["uploadedphoto"]["error"] > 0)
				{
					echo "Return Code: " . $_FILES["uploadedphoto"]["error"] . "<br />";
				}
				else 
				{
					echo "Upload: " . $_FILES["uploadedphoto"]["name"] . "<br />";
					echo "Type: " . $_FILES["uploadedphoto"]["type"] . "<br />";
					echo "Size: " . ($_FILES["uploadedphoto"]["size"] / 1024) . " Kb<br />";
					echo "Stored in: " . $_FILES["uploadedphoto"]["tmp_name"];
					if (file_exists("user_uploads/" . $_FILES["uploadedphoto"]["name"]))
					{
						echo $_FILES["uploadedphoto"]["name"] . " already exists. Please try again.";
					}
					else 
					{
						move_uploaded_file($_FILES["uploadedphoto"]["tmp_name"], "user_uploads/" . $_FILES["uploadedphoto"]["name"]);
						echo "Stored in: " . "user_uploads/" . $_FILES["uploadedphoto"]["name"];	
					}
				}
			}
			else 
			{
				trigger_error("Invalid file. Please resubmit a photo that is smaller than 250kB and that is either a gif or jpg file.");
			}
		?>
	</body>
</html>