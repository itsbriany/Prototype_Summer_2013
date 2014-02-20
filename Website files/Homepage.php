<?php
	require 'connect.incV2.php';
	include 'GlobalVariables.php';
	include 'queries.php';
	include 'regex.php';
?>
<?php
	session_start();

	function isInvalid(){
		global $username, $conn;
		if(isset($_POST['login'])){
			$username = strtolower($_POST['username']);
			$pass = md5($_POST['pass']);
			
			if(!empty($username) and !empty($pass)){
				$query = "SELECT g.id, g.username, g.password FROM general_info AS g WHERE username = ? AND password = ?";
				$stmt = $conn->prepare($query);
				$stmt->bind_param('ss', $username, $pass);
				$stmt->execute();
				$query_run = $stmt->get_result();
				if(mysqli_num_rows($query_run) != 1)
					return true;
				else{
					while($row = $query_run->fetch_assoc()){
						$db_pass = $row['password'];
						$_SESSION['username'] = $row['username'];
						$_SESSION['id'] = $row['id'];
					}
					if($db_pass == $pass){
						$query_run->free();
						header("Location:http://localhost:8000/First%20project/Mainpage_v2.php");
						return false;
					}else
						return true;
				}
				$query_run->free();
			}else
				echo '<p style="color:yellow">Please fill in both fields.</p>';
		}
	}
	
	########################################
	//PASSWORD RECOVERY FUNCTIONS
	
	/*
		This function will recover the user's username and password via email 
	*/
	function recover(){
		global $conn;
		if(isset($_POST['submit_recovery'])){
			if(empty($_POST['recover_by_email']))
				echo "<h2>The recovery field was left empty</h2>";
			else if(validateEmail($_POST['recover_by_email'])){
				$to = $_POST['recover_by_email'];
				$subject = 'Account recovery';
				$newPass = generatePassword();
				$stmt = $conn->prepare("SELECT username FROM general_info AS g WHERE email = ?");
				$stmt->bind_param('s', $to);
				$stmt->execute();
				$stmt->bind_result($user);
				$stmt->fetch();
				$stmt->close();
				if(empty($user))
					echo "<h2 style='text-align:center;margin-left:7%'>The email does not exist in the database</h2>";
				else{
					resetPassword($newPass, $user);
					$message = 'Your username: '.$user."\nYour new password: ".$newPass;
					$header = 'From: The NATO People Finder';
					mail($to, $subject, $message, $header);
					echo "<h2 style='text-align:center;margin-left:7%'>Check your email, your password has been reset</h2>";
				}
			}else
				echo "<h2 style='text-align:center;margin-left:7%'>Invalid email</h2>";
		}
	}
	
	/*
		This function will change the user's password in the database
		Param1: The new password
		Param2: The username
	*/
	function resetPassword($password, $username){
		global $conn;
		$db_pass = md5($password);
		$stmt = $conn->prepare("SELECT id FROM general_info WHERE username = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->bind_result($id);
		$stmt->fetch();
		$stmt->close();
		$stmt = $conn->prepare("UPDATE general_info SET password = ? WHERE id = ?");
		$stmt->bind_param('si', $db_pass, $id);
		$stmt->execute();
		$stmt->close();
	}
	
	
	/*
		This function will generate a random password and will return it
	*/
	function generatePassword(){
		$new_pass = '';
		$lc_count = 0;
		$uc_count = 0;
		$digit_count = 0;
		$sp_count = 0;
		for($i = 0; $i < 8; $i++){
			$loop = true;
			while($loop){
				$character = rand(0, 3);
				if($character == 0 and $lc_count < 3){
						$new_pass .= generateLowerChar();
						$lc_count++;
						$loop = false;
				}else if($character == 1 and $uc_count < 2){
						$new_pass .= generateUpperChar();
						$uc_count++;
						$loop = false;
				}else if($character == 2 and $digit_count < 2){
						$new_pass .= generateDigit();
						$digit_count++;
						$loop = false;
				}else if($sp_count < 1){
					$new_pass .= generateSpecialChar();
					$sp_count++;
					$loop = false;
				}
			}
		}
		return $new_pass;
	}
	
	/*
		This function will generate a lowercase character and returns it
	*/
	function generateLowerChar(){
		return $char = chr(rand(97, 122));
	}
	
	/*
		This function will generate an uppercase character and returns it
	*/
	function generateUpperChar(){
		return $char = chr(rand(65, 90));
	}
	
	/*
		This function will generate a digit and will return it
	*/
	function generateDigit(){
		return rand(0, 9);
	}
	
	/*
		This function will generate a !?@^ character and returns it
	*/
	function generateSpecialChar(){
		$select = rand(0, 3);
		switch($select){
			case 0: return '!';
			case 1: return '?';
			case 2: return '@';
			case 3: return '^';
		}
	}
?>
<head>
	<title>Homepage</title>
	<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
</head>

<form action='Homepage.php' method='POST'>
	<body>
		<div id='homepage_contain'>
			<div id='homepage'>
				<h1 style='text-align:center' id='home_header'>Welcome to the ACT/SEE Personnel Database</h1>
				<div id='homeInput'>
					<ul>
						<li><label for='username'>Username: </label><input type='text' autocomplete='off' id='username' name='username'/></li>
						<li><label for='pass'>Password: </label><input type='password' autocomplete='off' id='pass' name='pass'/></li>
					</ul>
					<?php if (isInvalid()){echo '<p id="invalid" style="color:yellow">Invalid username/password.</p>';}?>
					<input type='submit' style='margin-bottom:10px;' value='Log In' id='login' name='login'/>
				</div>
				<div id='accountlinks'>
					<input type='button' name='signup' id='signup' onclick='toSignup()' value='Sign up!'/>
					<input type='button' name='forgot' id='forgot' onclick='showRecovery()' value='Forgot username or password?'/>
				</div>
				<div id='recovery'>
					<ul>
						<li><label for='recover_by_email'>Enter your email address to recover your password</label></li>
						<li><input type='text' name='recover_by_email' id='recover_by_email'/></li>
						<li><input type='submit' name='submit_recovery' id='submit_recovery' value='Recover username/password'/></li>
					</ul>
				</div>
				<?php recover(); ?>
			</div>
		</div>
	</body>
</form>

