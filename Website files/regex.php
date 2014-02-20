<?php
	require 'connect.incV2.php';
	
	//REGULAR EXPRESSIONS FOR VALIDATION
	//Param1: The string to validate
	//@Return: true if the string is validated by the regex
	
	//for more info on regular expressions, go to http://gskinner.com/RegExr/
	//how regex operators work http://metahtml.sourceforge.net/documentation/regex/regex_3.mhtml
	
	function validateName($name){
		return preg_match('/^([^(\W|\d)](\s*\-*\s*)){2,30}$/', $name);
	}
	function validateEmail($email){
		return preg_match('/^[\w\S]+@[\w\S]+\.[a-zA-Z]{2,4}$/', $email);
	}
	function validateUsername($user){
		return preg_match('/^[^(\W|\d)](\w{1,39})?$/', $user);
	}
	function validatePassword($pass){
		return preg_match('/^((?=\S*?[A-Z])(?=\S*?(!|\?|@|\^|))(?=\S*?[0-9])\S{3,32})+$/', $pass);
	}
	function validatePhone($phone){
		return preg_match('/^([\+]*?)(\s|\d)*$/', $phone);
	}
	function validateCreditCardNumber($number){
		return preg_match('/^(\s|\d)*$/', $number);
	}
	function validatePeNumber($pe){
		return preg_match('/^TSC FP[G|F|R] \d{4}$/', $pe);
	}
	function validateTerm($term){
		return preg_match('/^\d{1,2}\/\d{1,2}\/\d+$/', $term);
	}
	function validateCreditCardExpiry($term){
		return preg_match('/^\d{1,2}\/\d+$/', $term);
	}
	
	##########################################################################
	//Duplicates in db check functions will return true if there are duplicates
	
	function checkDupUsername($username){
		global $conn;
		$stmt = $conn->prepare("SELECT username FROM general_info WHERE username = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows >= 1)
			return true;
		return false;
	}
	function checkDupEmail($email){
		global $conn;
		$stmt = $conn->prepare("SELECT email FROM general_info WHERE email = ?");
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows >= 1)
			return true;
		return false;
	}
?>