
<?php

	$conn_error = 'Could not connect.';

	$mysql_host = 'localhost';
	$mysql_user = 'root';
	$mysql_pass = '';

	$mysql_db = '1st home project';
	
	# The @ symbol gets rid of the error messages for the given line

	if(!@mysql_connect($mysql_host, $mysql_user, $mysql_pass) or !@mysql_select_db($mysql_db))
		die($conn_error);
	
?>
