<?php
	$array = array('ben'=>'10', 'jim'=>'2');
	print_r($array);
	foreach($array as $key => $val){
		echo $key."\n";
	}
	
	/*for($i = 0; $i < count($array); $i++){
		echo key($array)."\n";
		next($array);
	}*/
?>