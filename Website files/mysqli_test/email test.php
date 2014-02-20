<?php
	
	$to = "itsbriany@gmail.com";
	$subject = "Test email";
	$body = "YES!\nThe email has sent!";
	$header = "From: fluffi <fuffi@nowhere.com>";
	
	if(mail($to, $subject, $body, $header))
		echo 'An email has been sent!';
	else
		echo 'An error has occurred when sending email';
?>

