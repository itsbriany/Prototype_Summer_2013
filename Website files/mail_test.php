<?php
	require 'connect.incV2.php';
	$to = 'itsbriany@gmail.com';
	$subject = 'Test';
	$message = 'This email was successfully sent!';
	$header = 'From: Fuzzy@fuffy.org';
	
	mail($to, $subject, $message, $header);
	
?>