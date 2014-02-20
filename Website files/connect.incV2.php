
<?php

	$conn = new mysqli('localhost', 'root', 'database_password', '1st home project');
	if($conn->connect_errno > 0){
		die('Unable to connect to database ['.$conn->connect_error.']');
	}
	echo "<script>var isMainpage = false;</script>";
?>