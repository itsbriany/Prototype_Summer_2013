<?php
	
	$db = new mysqli('localhost', 'root', '', '1st home project');
	if($db->connect_errno > 0){
		die('Unable to connect to database ['.$db->connect_error.']');
	}
	
	$sql = "Select nation from country_cell_resp";
	
	
	if($result = $db->query($sql)){
		while($row = $result->fetch_assoc()){
			echo $row['nation'].'<br/>';
		}
		echo $result->num_rows.' results were found.';
		//use $db->affected_rows to find how many rows were affected
		$result->free(); //frees up system memory so that result does not take up so much RAM
	}else
		die('There was an error running the query ['.$conn->error.']');
		
		
	if($result = mysqli_query($db, $sql)){
		while($row = mysqli_fetch_assoc($result)){
			echo $row['nation']."\t";
		}
		echo mysqli_num_rows($result).' rows were found.';
		mysqli_free_result($result);
	}else
		die('There was an error! ['.mysqli_error($db).']');
	
	$db->close(); //closes db connection
	
	
	
?>
