<?php
	require 'connect.incV2.php';
	include 'regex.php';
	
	session_start();
	
	$empty_field = "\tThis field was left empty.";
	$hasFoundError = array();
	$tryAgain = false;
	
	
	
	/*
	Returns true if the field is empty
	Param1: The submission name
	Param2: The field name
*/
function foundEmptyField($submitName, $fieldName){
	if(isset($_POST["$submitName"]) and empty($_POST["$fieldName"])){
		return true;
	}
	return false;
}

/*
	Checks the query run and will send a confirmation email to the account creator.
	The database will then update with the input the user has given.
	Param1: query run variable
	Param2: last name
	Param3: first name
	Param4: rank
	Param5: nationality
	Param6: Environmental cell
	Param7: email
	Param8: username
	Param9: password
*/
function createAcc($query_run, $last_name, $first_name, $email, $username, $password, $understandable_pass){
	global $conn;
	if(mysqli_fetch_assoc($query_run) == NULL){
		$to = $email;
		$subject = 'Welcome to Brian\'s website!';
		$body = 'You have successfully created an account!<br>
		Your account username is '.$username.' and your password is '.$understandable_pass;
		$header = 'From: donotreply@fuffy.org';
		if(mail($to, $subject, $body, $header)){
			$queryUpdate = "INSERT INTO general_info (last_name, first_name, email, username, password) VALUES (?, ?, ?, ?, ?)";
			$stmt = $conn->prepare($queryUpdate);
			$stmt->bind_param('sssss', $last_name, $first_name, $email, $username, $password);
			$stmt->execute();
			$_SESSION['first_login'] = true;
			$_SESSION['first_name'] = $first_name;
			$_SESSION['username'] = $username;
			$stmt = $conn->prepare("SELECT g.id FROM general_info AS g WHERE g.username = ? AND g.password = ?");
			$stmt->bind_param('ss', $username, $password);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_assoc())
				$_SESSION['id'] = $row['id'];
			$result->free();
			header("Location:http://localhost:8000/First%20project/Mainpage_v2.php");
			return true;
		}else
			echo "There was an error when sending the email.";
	}
	return false;
}

/*
	Displays the appropriate error message
	Param1: The error message input
*/
function displayError($errMessage){
	echo "$errMessage";
}
	
/*
		This checks for the submission of the first form, allowing the user to create an "account"
		that will later be inputted into the database. Error checks and exceptions have been cleverly made.
*/	
function validateSubmission(){
	global $tryAgain, $hasFoundError, $conn;
	if(isset($_POST['signup_form_submit'])){
		for($i = 0; $i < 5; $i++)
			$hasFoundError[$i] = false;
		$last_name = ucfirst(strtolower($_POST['s_last_name']));
		$first_name = ucfirst(strtolower($_POST['s_first_name']));
		$email = strtolower($_POST['s_email']);
		$username = $_POST['s_usernamecreate'];
		$password = $_POST['s_userpass'];
		$check_pass = $_POST['s_check_userpass'];
		if(!empty($last_name) and !empty($first_name) and !empty($email) and !empty($username) and !empty($password) and !empty($check_pass)){
			$isLegit = true;
			if(!validateName($first_name) or !validateName($last_name)){
				$hasFoundError[0] = true;
				$isLegit = false;
				$tryAgain = true;
			}
			if(!validateEmail($email)){
				$hasFoundError[1] = true;
				$isLegit = false;
				$tryAgain = true;
			}
			if(!validateUsername($username)){
				$hasFoundError[2] = true;
				$isLegit = false;
				$tryAgain = true;
			}
			if(!validatePassword($password) or !validatePassword($check_pass)){
				$hasFoundError[3] = true;
				$isLegit = false;
				$tryAgain = true;
			}else if($password != $check_pass){
				$hasFoundError[4] = true;
				$isLegit = false;
				$tryAgain = true;
			}
			if($isLegit){
				$query = "SELECT * FROM general_info WHERE username = ? OR email = ?";
				$stmt = $conn->prepare("$query");
				$stmt->bind_param('ss', $username, $email);
				$stmt->execute();
				$query_run = $stmt->get_result();
				$prev_pass = $password;
				$password = md5($password);
				if(!createAcc($query_run, $last_name, $first_name, $email, $username, $password, $prev_pass)){
					echo 'Sorry, the username or email is already in use. Please try again.<br>';
					$tryAgain = true;
				}
				$query_run->free();
			}
		}else
			$tryAgain = true;
	}
}

//This function will adjust the css after submitting the form
function adjustCSS(){
	global $tryAgain;
	if(isset($_POST['signup_form_submit']) and $tryAgain){
		echo "<script>
			$('#signup_submit_field').css('margin-left', '30%');
			$('#signup_error_field').show();
		</script>";
	}	
}

//This function will bring the user back to the homepage after the button is clicked
function signupToHome(){
	if(isset($_POST['signup_to_home'])){
		header("Location:http://localhost:8000/First%20project/Homepage.php");
	}
}

?>

<head>
	<title>Project #1</title>
	<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
</head>



<form action='Signup.php' method='POST'>
	<body>
		<div id='signup_contain'>
			<?php 
					validateSubmission();
					adjustCSS(); 
					signupToHome();
			?>
			<div id='signup_body'>
				<div id='signup_options'>
					<input type='submit' value='Back' name='signup_to_home' id='signup_to_home'/>
				</div>
				<h1 style='margin-left:52%;color:black'>Sign up</h1>
				<?php if($tryAgain){echo "<h2 style='color:blue;margin-left:52%'>Please try again.</h2>";}?>
				<div id='signup_info'>
					<h2>Important!</h2>
					<ul>
						<li>The first and last name cannot contain any digits and should start with an alphabetical letter</li>
						<li>The username must start with an alphabetical character and cannot contain any special characters (such as %$#!?)</li>
						<li>Password must be between 8-32 characters and must contain at least a capital letter, a digit, and special character such as (!?@^) and cannot contain any spaces, tabs, or line breaks</li>
					</ul>
				</div>
				<div id='signup_area'>
					<div id='signup_labels'>
						<ul>
							<li><label for='s_last_name'>Last name: </label><input type='text' name='s_last_name' id='s_last_name'></li> 
							<li><label for='s_first_name'>First name: </label><input type='text' name='s_first_name' id='s_first_name'></li>
							<li><label for='s_email'>Email: </label><input type='text' id='s_email' name='s_email'></li>
							<li><label for='s_usernamecreate'>Username: </label><input type='text' autocomplete='off' id='s_usernamecreate' name='s_usernamecreate'></li>
							<li><label for='s_userpass'>Password: </label><input type='password' autocomplete='off' id='s_userpass' name='s_userpass'></li>
							<li><label for='s_check_userpass'>Confirm password: </label><input type='password' autocomplete='off' id='s_check_userpass' name='s_check_userpass'></li>
						</ul>
					</div>
					<div id='signup_submit_field'>
						<input type='submit' value='Submit' name='signup_form_submit' id='signup_form_submit'>
					</div>
					<?php if($tryAgain): ?>
						<div id='signup_error_field'>
							<ul>
								<?php if($hasFoundError[4]): ?><li><label>Passwords do not match</label></li><?php endif; ?>
								<?php if(foundEmptyField('signup_form_submit', 'last_name')): ?><li><label>Last name has been left empty!</label></li><?php endif; ?>
								<?php if($hasFoundError[0]): ?><li><label>Name cannot contain special characters or digits and must be between 2-30 characters</label></li><?php endif; ?>
								<?php if(foundEmptyField('signup_form_submit', 'first_name')): ?><li><label>First name has been left empty!</label></li><?php endif; ?>
								<?php if(foundEmptyField('signup_form_submit', 'email')): ?><li><label>Email has been left empty!</label></li><?php endif; ?>
								<?php if($hasFoundError[1]): ?><li><label>Invalid email address</label></li><?php endif; ?>
								<?php if(foundEmptyField('signup_form_submit', 'usernamecreate')): ?><li><label>Username has been left empty!</label></li><?php endif; ?>
								<?php if($hasFoundError[2]): ?><li><label>Username must start with an alphabetical character, must be between 2-40 characters, and cannot contain special characters</label></li><?php endif; ?>
								<?php if(foundEmptyField('signup_form_submit', 'userpass')): ?><li><label>Password has been left empty!</label></li><?php endif; ?>
								<?php if($hasFoundError[3]): ?><li><label>Password must be between 8-32 characters and must contain at least a capital letter, a digit, and special character such as (!?@^) and cannot contain any spaces, tabs, or line breaks</label></li><?php endif; ?>
								<?php if(foundEmptyField('signup_form_submit', 'check_userpass')): ?><li><label>Password confirmation field has been left empty!</label></li><?php endif; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</body>
</form>