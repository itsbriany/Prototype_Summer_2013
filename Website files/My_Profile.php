<?php
	require 'connect.incV2.php';
	include 'GlobalVariables.php';
	include 'queries.php';
	include 'regex.php';
	session_start();
	
	//fixes JavaScript errors
	#NOTE: Since PHP loads before JavaScript, we can echo out variables so that they can be applied to future imported scripts
	echo "<script>var isMainpage = false;</script>";
	
	
	//File scope variables
	$display_error_box = false;
	
	
	#########################################
	//OPTION FUNCTIONS
	
	//This function will log the user out
	function logout(){
		if(isset($_POST['p_logout'])){
			session_destroy();
			header("Location:http://localhost:8000/First%20project/Homepage.php");
		}
	}
	
	//This function will bring the user back to the mainpage
	function backToMain(){
		if(isset($_POST['p_mainpage']))
			header("Location:http://localhost:8000/First%20project/Mainpage_v2.php");
	}
	
	#########################################
	//PAGE LOAD FUNCTIONS
	
	/*
		This function will get the user's current info
	*/
	function getInfo(){
		global $conn, $p_id, $p_last_name, $p_first_name, $p_nat, $p_rank, $p_EC, $p_CAG, $p_pe_num, $p_BoT, $p_EoT, $p_email, $p_phone, $p_user, $p_NATO_PASS,
		$p_SHAPE_ID, $p_DoB, $p_passport, $p_passport_expiry, $p_security_clearance, $p_pe_flag, $p_mobile, $p_secondary_email, $p_address,
		$p_credit_card_company, $p_credit_card_number, $p_credit_card_expiry, $p_tasks;
		$user = $_SESSION['username'];
		$query = "SELECT DISTINCT * FROM general_info AS g WHERE g.username = '$user'";
		if($result = $conn->query($query)){
			while($row = $result->fetch_assoc()){
				$p_id = $row['id'];
				$p_last_name = $row['last_name'];
				$p_first_name = $row['first_name'];
				$p_nat = $row['nationality'];
				$p_rank = $row['rank'];
				$p_EC = $row['environment'];
				$p_CAG = $row['CAG'];
				$p_pe_num = $row['pe_number'];
				$p_BoT = $row['BoT'];
				$p_EoT = $row['EoT'];
				$p_NATO_PASS = $row['NATO_PASS'];
				$p_SHAPE_ID = $row['SHAPE_ID'];
				$p_DoB = $row['DoB'];
				$p_passport = $row['Passport'];
				$p_passport_expiry = $row['Passport_expiry'];
				$p_email = $row['email'];
				$p_phone = $row['phone'];
				$p_user = $row['username'];
				$p_security_clearance = $row['security_clearance'];
				$p_pe_flag = $row['pe_flag'];
				$p_mobile = $row['mobile'];
				$p_secondary_email = $row['secondary_email'];
				$p_address = $row['address'];
				$p_credit_card_company = $row['credit_card_company'];
				$p_credit_card_number = $row['credit_card_number'];
				$p_credit_card_expiry = $row['credit_card_expiry'];
				$p_tasks = $row['tasks'];
			}
		}else
			echo $conn->error;
		$result->free();
	}
	
	##############################################
	//SUBMIT FUNCTIONS
	
	/*
		This function will update the user's profile info
	*/
	function updatePersonalInfo(){
		if(isset($_POST['p_submit'])){
			global $conn, $p_id, $p_errors, $pass_update, $p_update;
			$ln = ucfirst(strtolower($_POST['p_last_name_input']));
			$fn = ucfirst(strtolower($_POST['p_first_name_input']));
			$nat = $_POST['p_nat_input'];
			$rank = $_POST['p_rank_input'];
			$EC = $_POST['p_EC_input'];
			$CAG = $_POST['p_CAG_input'];
			$pe_num = $_POST['p_pe_input1'].' '.$_POST['p_pe_input2'].' '.$_POST['p_pe_num_input'];
			$BoT = $_POST['p_BoT_input'].'/'.$_POST['p_BoT_input2'].'/'.$_POST['p_BoT_input3'];
			$EoT = $_POST['p_EoT_input'].'/'.$_POST['p_EoT_input2'].'/'.$_POST['p_EoT_input3'];
			$NATO_PASS = $_POST['p_NATO_PASS_input'];
			$SHAPE_ID = $_POST['p_SHAPE_ID_input'];
			$DoB = $_POST['p_DoB_input'].'/'.$_POST['p_DoB_input2'].'/'.$_POST['p_DoB_input3'];
			$passport = $_POST['p_passport_input'];
			$passport_expiry = $_POST['p_passport_expiry_input'].'/'.$_POST['p_passport_expiry_input2'].'/'.$_POST['p_passport_expiry_input3'];
			$email = $_POST['p_email_input'];
			$phone = $_POST['p_phone_input'];
			$user = $_POST['p_username_input'];
			$old_pass = md5($_POST['old_pass']);
			$new_pass = $_POST['new_pass'];
			$confirm_pass = $_POST['confirm_pass'];
			$security_clearance = $_POST['p_security_clearance'];
			$secondary_email = $_POST['p_secondary_email_input'];
			$pe_flag = $_POST['p_pe_flag'];
			$mobile = $_POST['p_mobile_input'];
			$address = $_POST['p_street_input'].' '.$_POST['p_city_input'].' '.$_POST['p_postal_code_input'];
			$credit_card_company = $_POST['p_credit_card_company_input'];
			$credit_card_number = $_POST['p_credit_card_number_input'];
			$credit_card_expiry = $_POST['p_credit_card_expiry_input'].'/'.$_POST['p_credit_card_expiry_input2'].'/'.$_POST['p_credit_card_expiry_input2'];
			$tasks = $_POST['p_tasks'];
			if(!empty($secondary_email)){
				if(validateEmail($secondary_email)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.secondary_email = ? WHERE g.id = ?");
					$stmt->bind_param('si', $secondary_email, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[18] = true;
			}
			if(@$_POST['p_remove_tasks'] == 'x'){
				removeTasksQuery($p_id);
				$p_update = true;
			}else if(!empty($tasks)){
				replaceTasksQuery($p_id, $tasks);
				$p_update = true;
			}
			if($credit_card_expiry != '//'){
				if(validateTerm($credit_card_expiry)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.credit_card_expiry = ? WHERE g.id = ?");
					$stmt->bind_param('si', $credit_card_expiry, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[17] = true;
			}
			if(!empty($credit_card_company)){
				$stmt = $conn->prepare("UPDATE general_info AS g SET g.credit_card_company = ? WHERE g.id = ?");
				$stmt->bind_param('si', $credit_card_company, $p_id);
				$stmt->execute();
				$p_update = true;
			}
			if(!empty($credit_card_number)){
				if(validateCreditCardNumber($credit_card_number)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.credit_card_number = ? WHERE g.id = ?");
					$stmt->bind_param('si', $credit_card_number, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[16] = true;
			}
			if($address != '  '){
				$stmt = $conn->prepare("UPDATE general_info AS g SET g.address = ? WHERE g.id = ?");
				$stmt->bind_param('si', $address, $p_id);
				$stmt->execute();
				$p_update = true;
			}
			if(!empty($mobile)){
				if(validatePhone($mobile)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.mobile = ? WHERE g.id = ?");
					$stmt->bind_param('si', $mobile, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[15] = true;
			}
			if(!empty($pe_flag)){
				$stmt = $conn->prepare("UPDATE general_info AS g SET g.pe_flag = ? WHERE g.id = ?");
				$stmt->bind_param('si', $pe_flag, $p_id);
				$stmt->execute();
				$p_update = true;
			}
			if(!empty($security_clearance)){
				$stmt = $conn->prepare("UPDATE general_info AS g SET g.security_clearance = ? WHERE g.id = ?");
				$stmt->bind_param('si', $security_clearance, $p_id);
				$stmt->execute();
				$p_update = true;
			}
			if(!empty($ln)){
				if(validateName($ln)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.last_name = ? WHERE g.id = ?");
					$stmt->bind_param('si', $ln, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[8] = true;
			}
			if(!empty($fn)){
				if(validateName($fn)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.first_name = ? WHERE g.id = ?");
					$stmt->bind_param('si', $fn, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[9] = true;
			}
			if(!empty($nat)){
				$stmt = $conn->prepare("UPDATE general_info AS g SET g.nationality = ? WHERE g.id = ?");
				$stmt->bind_param('si', $nat, $p_id);
				$stmt->execute();
				$p_update = true;
			}
			if(!empty($rank)){
				$stmt = $conn->prepare("UPDATE general_info AS g SET g.rank = ? WHERE g.id = ?");
				$stmt->bind_param('si', $rank, $p_id);
				$stmt->execute();
				$p_update = true;
			}
			if(!empty($EC)){
				$stmt = $conn->prepare("UPDATE general_info AS g SET g.environment = ? WHERE g.id = ?");
				$stmt->bind_param('si', $EC, $p_id);
				$stmt->execute();
				$p_update = true;
			}
			if(!empty($CAG)){
				$stmt = $conn->prepare("UPDATE general_info AS g SET g.CAG = ? WHERE g.id = ?");
				$stmt->bind_param('si', $CAG, $p_id);
				$stmt->execute();
				$p_update = true;
			}
			if($pe_num != '  '){
				if(validatePeNumber($pe_num)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.pe_number = ? WHERE g.id = ?");
					$stmt->bind_param('si', $pe_num, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[10] = true;
			}
			if($BoT != '//' and !empty($BoT)){
				if(validateTerm($BoT)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.BoT = ? WHERE g.id = ?");
					$stmt->bind_param('si', $BoT, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[11] = true;
			}
			if(!empty($EoT) and $EoT != '//'){
				if(validateTerm($EoT)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.EoT = ? WHERE g.id = ?");
					$stmt->bind_param('si', $EoT, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[12] = true;
			}
			if(!empty($NATO_PASS)){
				$stmt = $conn->prepare("UPDATE general_info AS g SET g.NATO_PASS = ? WHERE g.id = ?");
				$stmt->bind_param('si', $NATO_PASS, $p_id);
				$stmt->execute();
				$p_update = true;
			}
			if(!empty($SHAPE_ID)){
				$stmt = $conn->prepare("UPDATE general_info AS g SET g.SHAPE_ID = ? WHERE g.id = ?");
				$stmt->bind_param('si', $SHAPE_ID, $p_id);
				$stmt->execute();
				$p_update = true;
			}
			if($DoB != '//' and !empty($DoB)){
				if(validateTerm($DoB)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.DoB = ? WHERE g.id = ?");
					$stmt->bind_param('si', $DoB, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[13] = true;
			}
			if(!empty($passport)){
				$stmt = $conn->prepare("UPDATE general_info AS g SET g.Passport = ? WHERE g.id = ?");
				$stmt->bind_param('si', $passport, $p_id);
				$stmt->execute();
				$p_update = true;
			}
			if($passport_expiry != '//' and !empty($passport_expiry)){
				if(validateTerm($passport_expiry)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.Passport_expiry = ? WHERE g.id = ?");
					$stmt->bind_param('si', $passport_expiry, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[14] = true;
			}
			if(!empty($email)){
				if(validateEmail($email)){
					$stmt = $conn->prepare("SELECT email FROM general_info AS g WHERE g.email = ?");
					$stmt->bind_param('s', $email);
					$stmt->execute();
					$result = $stmt->get_result();
						if($result->num_rows != 0){
							$p_errors[0] = true;
						}else{
							$stmt = $conn->prepare("UPDATE general_info AS g SET g.email = ? WHERE g.id = ?");
							$stmt->bind_param('si', $email, $p_id);
							$stmt->execute();
							$p_update = true;
						}
					$result->free();
				}else
					$p_errors[1] = true;
			}
			if(!empty($phone)){
				if(validatePhone($phone)){
					$stmt = $conn->prepare("UPDATE general_info AS g SET g.phone = ? WHERE g.id = ?");
					$stmt->bind_param('si', $phone, $p_id);
					$stmt->execute();
					$p_update = true;
				}else
					$p_errors[2] = true;
			}
			if(!empty($user)){
				if(validateUsername($user)){
					$stmt = $conn->prepare("SELECT username FROM general_info AS g WHERE g.username = ?");
					$stmt->bind_param('s', $user);
					$stmt->execute();
					$result = $stmt->get_result();
						if($result->num_rows != 0){
							$p_errors[3] = true;
						}else{
							$stmt = $conn->prepare("UPDATE general_info AS g SET g.username = ? WHERE g.id = ?");
							$stmt->bind_param('si', $user, $p_id);
							$stmt->execute();
							$_SESSION['username'] = $user;
							$p_update = true;
						}
					$result->free();
				}else
					$p_errors[4] = true;
			}
			if(!empty($old_pass) and !empty($new_pass) and !empty($confirm_pass)){
				$stmt = $conn->prepare("SELECT DISTINCT g.password FROM general_info AS g WHERE g.password = ? AND g.id = ?");
				$stmt->bind_param('si', $old_pass, $p_id);
				$stmt->execute();
				$stmt->bind_result($old_pass_check);
				$stmt->fetch();
				if($old_pass == $old_pass_check){
					if(validatePassword($confirm_pass) and validatePassword($new_pass)){
						$new_pass = md5($new_pass);
						$confirm_pass = md5($confirm_pass);
						if($confirm_pass == $new_pass){
							$stmt->prepare("UPDATE general_info AS g SET password = ? WHERE g.id= ?");
							$stmt->bind_param('si', $new_pass, $p_id);
							$stmt->execute();
							$pass_update = true;
						}else
							$p_errors[5] = true;
					}else
						$p_errors[6] = true;
				}else
					$p_errors[7] = true;
			}
		}
	}
	
	/*
		This function will delete the user's profile
	*/
	function deleteProfile(){
		if(isset($_POST['p_delete'])){
			removeProfile($_SESSION['id']);
			session_destroy();
			header("Location:http://localhost:8000/First%20project/goodbye.php");
		}
	}
	##################################################################
	//LOGIN SECURITY
	if(!$_SESSION['username'])
		header("Location:http://localhost:8000/First%20project/Homepage.php");
?>
<head>
	<!--<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta content="utf-8" http-equiv="encoding"/>-->
	<title>Project #1</title>
	<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
</head>

<body>
	<form action='My_Profile.php' method='POST'>
		<?php 
			deleteProfile();
			getInfo();
			updatePersonalInfo();
			getInfo();
			logout();
			backToMain();
		?>
		<div id = 'my_profile_body'>
			<div id='profile_header'>
				<div id='p_your_profile'>
					<h1>Your Profile</h1>
				</div>
				<div id='p_options'>
					<ul>
						<li><input type='submit' value='Log out' class='p_options_class' name='p_logout' id='p_logout'></li>
						<li><input type='submit' value='Mainpage' class='p_options_class' name='p_mainpage' id='p_mainpage'></li>
						<li><input type='submit' value='Delete profile' class='p_options_class' name='p_delete' id='p_delete'></li>
					</ul>
				</div>
				<?php 
						foreach($p_errors as $val){
							if($val){
								$display_error_box = true;
								break;
							}
						}
				?>
				<?php if($display_error_box): ?>
					<div id='p_error_box'>
						<h2 style='color:red'>Errors</h2>
						<ul>
							<?php if($p_errors[8]): ?><li><label class='p_errors'>Invalid last name</label></li><?php endif; ?>
							<?php if($p_errors[9]): ?><li><label class='p_errors'>Invalid first name</label></li><?php endif; ?>
							<?php if($p_errors[10]): ?><li><label class='p_errors'>Invalid PE number</label></li><?php endif; ?>
							<?php if($p_errors[0]): ?><li><label class='p_errors'>Sorry, the given is already taken</label></li><?php endif; ?>
							<?php if ($p_errors[1]): ?><li><label class='p_errors'>Invalid email</label></li><?php endif; ?>
							<?php if ($p_errors[2]): ?><li><label class='p_errors'>Invalid phone number</label></li><?php endif; ?>
							<?php if ($p_errors[3]): ?><li><label class='p_errors'>Sorry, the given username is already taken</label></li><?php endif; ?>
							<?php if ($p_errors[4]): ?><li><label class='p_errors'>Invalid username</label></li><?php endif; ?>
							<?php if($p_errors[5]): ?><li><label class='p_errors'>Passwords do not match!</label></li><?php endif; ?>
							<?php if($p_errors[6]): ?><li><label class='p_errors'>Invalid password</label></li><?php endif; ?>
							<?php if($p_errors[7]): ?><li><label class='p_errors'>Old password is incorrect</label></li><?php endif; ?>
							<?php if($p_errors[11]): ?><li><label class='p_errors'>Invalid beginning of term</label></li><?php endif; ?>
							<?php if($p_errors[12]): ?><li><label class='p_errors'>Invalid end of term</label></li><?php endif; ?>
							<?php if($p_errors[13]): ?><li><label class='p_errors'>Invalid date of birth</label></li><?php endif; ?>
							<?php if($p_errors[14]): ?><li><label class='p_errors'>Invalid passport expiry date</label></li><?php endif; ?>
							<?php if($p_errors[15]): ?><li><label class='p_errors'>Invalid mobile phone number</label></li><?php endif; ?>
							<?php if($p_errors[16]): ?><li><label class='p_errors'>Invalid credit card number</label></li><?php endif; ?>
							<?php if($p_errors[17]): ?><li><label class='p_errors'>Invalid credit card expiry date</label></li><?php endif; ?>
							<?php if($p_errors[18]): ?><li><label class='p_errors'>Invalid personal email</label></li><?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>
				<div id='p_updates'>
					<?php if($pass_update): ?><h2 style='color:orange;text-align:center'>Your password has been updated!</h2><?php endif; ?>
					<?php if($p_update): ?><h2 style='color:orange;text-align:center'>Your profile information has been updated!</h2><?php endif; ?>
				</div>
			</div>
			<div id='current_info'>
				<div id='p_sub_header'>
					<div id='p_current_info'>
						<h2 style='color:red;text-align:left;padding-left:14.5%'>Your current info</h2>
					</div>
				</div>
				<div id='profile_content1'>
					<div id='profile_display'>
						<div id='profile_category1'>
							<div class='profile_labels' id='profile_labels1'>
								<div class='profile_sub_labels'>
									<ul>
										<li><label class='profile_info'>Id</label><?php if(!empty($p_id)){echo "<label id='p_label1'> $p_id</label>";}else{echo "<label id='p_label1'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Last name</label><?php if(!empty($p_last_name)){echo "<label id='p_label2'> $p_last_name</label>";}else{echo "<label id='p_label2'> N/A<br/></label>";} ?> </li>
										<li><label class='profile_info'>First name</label><?php if(!empty($p_first_name)){echo "<label id='p_label3'> $p_first_name</label>";}else{echo "<label id='p_label3'> N/A<br/></label>";} ?></li>
									</ul>
								</div>
								<div class='profile_sub_labels'>
									<ul>
										<li><label class='profile_info'>Nationality</label><?php if(!empty($p_nat)){echo "<label id='p_label4'> $p_nat</label>";}else{echo "<label id='p_label4'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Rank</label><?php if(!empty($p_rank)){echo "<label id='p_label5'> $p_rank</label>";}else{echo "<label id='p_label5'> N/A<br/></label>";} ?> </li>
										<li><label class='profile_info'>Cell</label><?php if(!empty($p_EC)){echo "<label id='p_label6'> $p_EC</label>";}else{echo "<label id='p_label6'> N/A<br/></label>";} ?></li>
									</ul>
								</div>
							</div>
							<div class='profile_labels' id='profile_labels2'>
								<div class='profile_sub_labels'>
									<ul>
										<li><label class='profile_info'>CAG</label> <?php if(!empty($p_CAG)){echo "<label id='p_label7'> $p_CAG</label>";}else{echo "<label id='p_label7'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>PE number</label><?php if(!empty($p_pe_num)){echo "<label id='p_label8'> $p_pe_num</label>";}else{echo "<label id='p_label8'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>PE flag</label><?php if(!empty($p_pe_flag)){echo "<label id='p_label9'> $p_pe_flag</label>";}else{echo "<label id='p_label9'> N/A<br/></label>";} ?></li>
									</ul>
								</div>
								<div class='profile_sub_labels'>
									<ul>
										<li><label class='profile_info'>Beginning of term</label><?php if(!empty($p_BoT)){echo "<label id='p_label10'> $p_BoT</label>";}else{echo "<label id='p_label10'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>End of term</label><?php if(!empty($p_EoT)){echo "<label id='p_label11'> $p_EoT</label>";}else{echo "<label id='p_label11'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>NATO ID</label><?php if(!empty($p_NATO_PASS)){echo "<label id='p_label12'> $p_NATO_PASS</label>";}else{echo "<label id='p_label12'> N/A<br/></label>";} ?></li>
									</ul>
								</div>
							</div>
							<div class='profile_labels' id='profile_labels3'>
								<div class='profile_sub_labels'>
									<ul>
										<li><label class='profile_info'>SHAPE ID</label> <?php if(!empty($p_SHAPE_ID)){echo "<label id='p_label13'> $p_SHAPE_ID</label>";}else{echo "<label id='p_label13'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Date of Birth</label> <?php if(!empty($p_DoB)){echo "<label id='p_label14'> $p_DoB</label>";}else{echo "<label id='p_label14'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Passport ID</label> <?php if(!empty($p_passport)){echo "<label id='p_label15'> $p_passport</label>";}else{echo "<label id='p_label15'> N/A<br/></label>";} ?></li>
									</ul>
								</div>
								<div class='profile_sub_labels'>
									<ul>
										<li><label class='profile_info'>Passport expiry date</label><?php if(!empty($p_passport_expiry)){echo "<label id='p_label16'> $p_passport_expiry</label>";}else{echo "<label id='p_label16'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Security clearance</label><?php if(!empty($p_security_clearance)){echo "<label id='p_label17'> $p_security_clearance</label>";}else{echo "<label id='p_label17'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Credit card company</label><?php if(!empty($p_credit_card_company)){echo "<label id='p_label18'> $p_credit_card_company</label>";}else{echo "<label id='p_label18'> N/A<br/></label>";} ?></li>
									</ul>
								</div>
							</div>
						</div>
						<div id='profile_category2'>
							<div class='profile_labels' id='profile_labels4'>
								<div class='profile_sub_labels'>
									<ul>
										<li><label class='profile_info'>Credit card number</label><?php if(!empty($p_credit_card_number)){echo "<label id='p_label19'> $p_credit_card_number</label>";}else{echo "<label id='p_label19'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Credit card expiry date</label><?php if(!empty($p_credit_card_expiry)){echo "<label id='p_label20'> $p_credit_card_expiry</label>";}else{echo "<label id='p_label20'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Email</label> <?php if(!empty($p_email)){echo "<label id='p_label21'> $p_email</label>";}else{echo "<label id='p_label21'> N/A<br/></label>";} ?></li>
									</ul>
								</div>
								<div class='profile_sub_labels' id='profile_email_phone_div'>
									<ul>
										<li><label class='profile_info'>Personal email</label><?php if(!empty($p_secondary_email)){echo "<label id='p_label22'> $p_secondary_email</label>";}else{echo "<label id='p_label22'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Phone</label><?php if(!empty($p_phone)){echo "<label id='p_label23'> $p_phone</label>";}else{echo "<label id='p_label23'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Mobile</label><?php if(!empty($p_mobile)){echo "<label id='p_label24'> $p_mobile</label>";}else{echo "<label id='p_label24'> N/A<br/></label>";} ?></li>
									</ul>
								</div>
							</div>
							<div class='profile_labels' id='profile_labels5'>
								<div class='profile_sub_labels' id='profile_username_admin_div'>
									<ul>
										<li><label class='profile_info'>Address</label><?php if(!empty($p_address)){echo "<label id='p_label25'> $p_address</label>";}else{echo "<label id='p_label25'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Username</label><?php if(!empty($p_user)){echo "<label id='p_label26'> $p_user</label>";}else{echo "<label id='p_label26'> N/A<br/></label>";} ?></li>
										<li><label class='profile_info'>Admin</label><?php if($_SESSION['admin']){echo "<label id='p_label27'> Yes</label>";}else{echo "<label id='p_label27'> No</label>";} ?> </li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div id='profile_content3'>
						<h2>Tasks</h2>
						<label for='p_remove_tasks' id='p_remove_tasks_label'>Remove tasks</label><input type='checkbox' value='x' name='p_remove_tasks' id='p_remove_tasks'/>
						<textarea rows='4' cols='10' name='p_tasks' id='p_tasks'><?php getTasks($_SESSION['id']); ?></textarea>
					</div>
					<div id='big_profile_edit_display'>
						<div id='p_edit_info'>
							<h2 style='color:blue;text-align:right'>Edit profile info</h2>
						</div>
						<div id='profile_edit_display'>
							<ul>
								<li><label class='profile_edit_label' for='p_last_name_input'>Last name</label><input type='text' id='p_last_name_input' name='p_last_name_input'></li>
								<li><label class='profile_edit_label' for='p_first_name_input'>First name</label><input type='text' id='p_first_name_input' name='p_first_name_input'></li>
								<li><label class='profile_edit_label' for='p_nat_input'>Nationality</label><select id='p_nat_input' name='p_nat_input'>
									<option value='' selected='selected'>Choose One</option>
									<option value='ALB'>Albanian</option>
									<option value='BEL'>Belgian</option>
									<option value='BGR'>Bulgarian</option>
									<option value='CAN'>Canadian</option>
									<option value='HRV'>Croatian</option>
									<option value='CZE'>Czech Republican</option>
									<option value='DNK'>Danish</option>
									<option value='EST'>Estonian</option>
									<option value='FRA'>French</option>
									<option value='DEU'>German</option>
									<option value='GRC'>Greek</option>
									<option value='HUN'>Hungarian</option>
									<option value='ISL'>Icelandic</option>
									<option value='ITA'>Italian</option>
									<option value='LVA'>Latvian</option>
									<option value='LTU'>Lithuanian</option>
									<option value='LUX'>Luxembourg</option>
									<option value='NLD'>Netherlands</option>
									<option value='NOR'>Norwegian</option>
									<option value='POL'>Polish</option>
									<option value='PRT'>Portuguese</option>
									<option value='ROU'>Romanian</option>
									<option value='SVK'>Slovakian</option>
									<option value='SVN'>Slovenian</option>
									<option value='ESP'>Spanish</option>
									<option value='TUR'>Turkish</option>
									<option value='GBR'>English</option>
									<option value='USA'>American</option>
								</select></li>
								<li><label class='profile_edit_label' for='p_rank_input'>Rank</label><select id='p_rank_input' name='p_rank_input'>
									<option value='' selected='selected'>Choose one</option>
									<option value='COL'>COL</option>
									<option value='CAPT(N)'>CAPT(N)</option>
									<option value='LTC'>LTC</option>
									<option value='CDR'>CDR</option>
									<option value='LCDR'>LCDR</option>
									<option value='MAJ'>MAJ</option>
									<option value='CAPT'>CAPT</option>
									<option value='CTR'>CTR</option>
									<option value='CIV'>CIV</option>
								</select></li>
								<li><label class='profile_edit_label' for='p_EC_input'>Cell</label><select id='p_EC_input' name='p_EC_input'>
									<option value='' selected='selected'>Choose One</option>
									<option value='LAND'>Land</option>
									<option value='AIR'>Air</option>
									<option value='MARITIME'>Maritime</option>
									<option value='JOINT/ENABLING'>Joint Enabling</option>
									<option value='CTR BRANCH'>CTR Branch</option>
								</select></li>
								<li><label class='profile_edit_label' for='p_CAG_input'>CAG</label><select id='p_CAG_input' name='p_CAG_input'>
									<option value=''>Choose one</option>
									<option value='ENGAGEMENT'>Engagement</option>
									<option value='COMMAND SUPPORT'>Command Support</option>
									<option value='ENABLING'>Enabling</option>
									<option value='COORDINATOR'>Coordinator</option>
									<option value='BRANCH HEAD'>Branch head</option>
								</select></li>
								<li><label class='profile_edit_label' for='p_pe_input1'>PE number</label><select id='p_pe_input1' name='p_pe_input1'>
									<option value='' selected='selected'></option>
									<option value='TSC'>TSC</option>
								</select>
								<select id='p_pe_input2' name='p_pe_input2'>
									<option value='' selected='selected'></option>
									<option value='FPF'>FPF</option>
									<option value='FPG'>FPG</option>
									<option value='FPR'>FPR</option>
									<option value='PFP'>PFP</option>
								</select><input type='text' style='margin-left:1%' id='p_pe_num_input' name='p_pe_num_input'>
								<select id='p_pe_flag' name='p_pe_flag'>
									<option value='' selected='selected'>Flag</option>
									<option value='ALB'>Albania</option>
									<option value='BEL'>Belgium</option>
									<option value='BGR'>Bulgaria</option>
									<option value='CAN'>Canada</option>
									<option value='HRV'>Croatia</option>
									<option value='CZE'>Czech Republic</option>
									<option value='DNK'>Denmark</option>
									<option value='EST'>Estonia</option>
									<option value='FRA'>France</option>
									<option value='DEU'>Germany</option>
									<option value='GRC'>Greece</option>
									<option value='HUN'>Hungary</option>
									<option value='ISL'>Iceland</option>
									<option value='ITA'>Italy</option>
									<option value='LVA'>Latvia</option>
									<option value='LTU'>Lithuania</option>
									<option value='LUX'>Luxembourg</option>
									<option value='NLD'>Netherlands</option>
									<option value='NOR'>Norway</option>
									<option value='POL'>Poland</option>
									<option value='PRT'>Portugal</option>
									<option value='ROU'>Romania</option>
									<option value='SVK'>Slovakia</option>
									<option value='SVN'>Slovenia</option>
									<option value='ESP'>Spain</option>
									<option value='TUR'>Turkey</option>
									<option value='GBR'>United Kingdom</option>
									<option value='USA'>United States</option>
								</select></li>
								<li><label class='profile_edit_label' for='p_BoT_input'>Beginning of term</label><select id='p_BoT_input' name='p_BoT_input'>
									<option value='' selected='selected'>Month</option>
									<option value='01'>January</option>
									<option value='02'>February</option>
									<option value='03'>March</option>
									<option value='04'>April</option>
									<option value='05'>May</option>
									<option value='06'>June</option>
									<option value='07'>July</option>
									<option value='08'>August</option>
									<option value='09'>September</option>
									<option value='10'>October</option>
									<option value='11'>November</option>
									<option value='12'>December</option>
								</select><select id='p_BoT_input2' name='p_BoT_input2'>
									<option value='' selected='selected'>Day</option>
									<?php for($i = 1; $i <= 31; $i++){
											$op_value = $i;
											if($i <= 9)
												$op_value = '0'.$i;
											echo "<option value='$op_value'>$op_value</option>";
										}
									?>
								</select><select id='p_BoT_input3' name='p_BoT_input3'>
									<option value='' selected='selected'>Year</option>
									<?php for($i = 2000; $i <= 2050; $i++)
											echo "<option value='$i'>$i</option>";
									?>
								</select></li>
								<li><label class='profile_edit_label' for='p_EoT_input'>End of term</label><select id='p_EoT_input' name='p_EoT_input'>
									<option value='' selected='selected'>Month</option>
									<option value='01'>January</option>
									<option value='02'>February</option>
									<option value='03'>March</option>
									<option value='04'>April</option>
									<option value='05'>May</option>
									<option value='06'>June</option>
									<option value='07'>July</option>
									<option value='08'>August</option>
									<option value='09'>September</option>
									<option value='10'>October</option>
									<option value='11'>November</option>
									<option value='12'>December</option>
								</select><select id='p_EoT_input2' name='p_EoT_input2'>
									<option value='' selected='selected'>Day</option>
									<?php for($i = 1; $i <= 31; $i++){
											$op_value = $i;
											if($i <= 9)
												$op_value = '0'.$i;
											echo "<option value='$op_value'>$op_value</option>";
										}
									?>
								</select><select id='p_EoT_input3' name='p_EoT_input3'>
									<option value='' selected='selected'>Year</option>
									<?php for($i = 2000; $i <= 2050; $i++)
											echo "<option value='$i'>$i</option>";
									?>
								</select></li>
								<li><label class='profile_edit_label' for='p_NATO_PASS_input'>NATO ID</label><input type='text' id='p_NATO_PASS_input' name='p_NATO_PASS_input'/></li>
								<li><label class='profile_edit_label' for='p_SHAPE_ID_input'>SHAPE ID</label><input type='text' id='p_SHAPE_ID_input' name='p_SHAPE_ID_input'/></li>
								<li><label class='profile_edit_label' for='p_DoB_input'>Date of Birth</label><select id='p_DoB_input' name='p_DoB_input'>
									<option value='' selected='selected'>Month</option>
									<option value='01'>January</option>
									<option value='02'>February</option>
									<option value='03'>March</option>
									<option value='04'>April</option>
									<option value='05'>May</option>
									<option value='06'>June</option>
									<option value='07'>July</option>
									<option value='08'>August</option>
									<option value='09'>September</option>
									<option value='10'>October</option>
									<option value='11'>November</option>
									<option value='12'>December</option>
								</select><select id='p_DoB_input2' name='p_DoB_input2'>
									<option value='' selected='selected'>Day</option>
									<?php for($i = 1; $i <= 31; $i++){
											$op_value = $i;
											if($i <= 9)
												$op_value = '0'.$i;
											echo "<option value='$op_value'>$op_value</option>";
										}
									?>
								</select><select id='p_DoB_input3' name='p_DoB_input3'>
									<option value='' selected='selected'>Year</option>
									<?php for($i = 1945; $i <= 2013; $i++)
											echo "<option value='$i'>$i</option>";
									?>
								</select></label></li>
								<li><label class='profile_edit_label' for='p_passport_input'>Passport ID</label><input type='text' id='p_passport_input' name='p_passport_input'/></li>
								<li><label class='profile_edit_label' for='p_passport_expiry_input'>Passport expiry date</label><select id='p_passport_expiry_input' name='p_passport_expiry_input'>
									<option value='' selected='selected'>Month</option>
									<option value='01'>January</option>
									<option value='02'>February</option>
									<option value='03'>March</option>
									<option value='04'>April</option>
									<option value='05'>May</option>
									<option value='06'>June</option>
									<option value='07'>July</option>
									<option value='08'>August</option>
									<option value='09'>September</option>
									<option value='10'>October</option>
									<option value='11'>November</option>
									<option value='12'>December</option>
								</select><select id='p_passport_expiry_input2' name='p_passport_expiry_input2'>
									<option value='' selected='selected'>Day</option>
									<?php for($i = 1; $i <= 31; $i++){
											$op_value = $i;
											if($i <= 9)
												$op_value = '0'.$i;
											echo "<option value='$op_value'>$op_value</option>";
										}
									?>
								</select><select id='p_passport_expiry_input3' name='p_passport_expiry_input3'>
									<option value='' selected='selected'>Year</option>
									<?php for($i = 2000; $i <= 2050; $i++)
											echo "<option value='$i'>$i</option>";
									?>
								</select></li>
								<li><label class='profile_edit_label' for='p_security_clearance'>Security clearance</label><select id='p_security_clearance' name='p_security_clearance'>
									<option value='' selected='selected'></option>
									<option value='ACTS'>ACTS</option>
									<option value='NATS'>NATS</option>
									<option value='CTS'>CTS</option>
									<option value='NS'>NS</option>
									<option value='NC'>NC</option>
									<option value='NR'>NR</option>
								</select></li>
								<li><label class='profile_edit_label' for='p_credit_card_company_input'>Credit card company</label><input type='text' name='p_credit_card_company_input' id='p_credit_card_company_input'></li>
								<li><label class='profile_edit_label' for='p_credit_card_number_input'>Credit card number</label><input type='text' name='p_credit_card_number_input' id='p_credit_card_number_input'></li>
								<li><label class='profile_edit_label'>Credit card expiry date</label><select name='p_credit_card_expiry_input' id='p_credit_card_expiry_input'>
									<option value='' selected='selected'>Month</option>
									<option value='01'>January</option>
									<option value='02'>February</option>
									<option value='03'>March</option>
									<option value='04'>April</option>
									<option value='05'>May</option>
									<option value='06'>June</option>
									<option value='07'>July</option>
									<option value='08'>August</option>
									<option value='09'>September</option>
									<option value='10'>October</option>
									<option value='11'>November</option>
									<option value='12'>December</option>
								</select><select name='p_credit_card_expiry_input2' id='p_credit_card_expiry_input2'>
									<option value='' selected='selected'>Year</option>
									<?php for($i = 2013; $i <= 2050; $i++)
											echo "<option value='$i'>$i</option>";
									?>
								</select></li>
								<li><label class='profile_edit_label' for='p_email_input'>Email</label><input type='text' id='p_email_input' name='p_email_input'></li>
								<li><label class='profile_edit_label' for='p_secondary_email_input'>Personal email</label><input type='text' id='p_secondary_email_input' name='p_secondary_email_input'></li>
								<li><label class='profile_edit_label' for='p_phone_input'>Phone</label><input type='text' id='p_phone_input' name='p_phone_input'></li>
								<li><label class='profile_edit_label' for='p_mobile_input'>Mobile</label><input type='text' id='p_mobile_input' name='p_mobile_input'></li>
								<li><label class='profile_edit_label' >Address</label><input type='text' placeholder='Street and number' name='p_street_input' id='p_street_input'><input type='text' placeholder='Town/city' id='p_city_input' name='p_city_input'><input type='text' placeholder='Postal code' id='p_postal_code_input' name='p_postal_code_input'></li>
								<li><label class='profile_edit_label' for='p_username_input'>Username</label><input type='text' autocomplete='off' id='p_username_input' name='p_username_input'></li>
							</ul>
						</div>
					</div>
				</div>
				<div id='profile_content2'>
					<div id='sub_profile_content2'>
						<h2>Change your password</h2>
							<ul>
								<li><label for='old_pass'>Old password</label><input type='password' autocomplete='off' name='old_pass' id='old_pass' ></li>
								<li><label for='new_pass'>New password</label><input type='password' name='new_pass' id='new_pass'></li>
								<li><label for='confirm_pass'>Confirm new password</label><input type='password' name='confirm_pass' id='confirm_pass'></li>
							</ul>
					</div>
					<div id='p_submit_changes'>
						<input type='submit' value='Submit changes' name='p_submit' id='p_submit'>
					</div>
				</div>
			</div>
		</div>
	</form>
</body>