<?php
	require 'connect.incV2.php';

	
	##################################################################
	//QUERIES
	
	/*
		Executes a series of a queries that replace/remove all the last names in the database relating to a given id 
		Param1: The given id
		Param2: The name that will replace the previous one
		@return: The value inputed into the field
	*/
	
	function replaceLastNameQuery($id, $name){
		global $conn;
		$prev_name = getQueryAttr($id, 'last_name');
		$stmt = $conn->prepare("UPDATE general_info as g SET g.last_name = ? WHERE g.id = ?");	
		$stmt->bind_param('si', $name, $id);
		$stmt->execute();
		$stmt->close();
		$stmt = $conn->prepare("UPDATE country_cell_resp SET joint_enabling = ? WHERE joint_enabling = '$prev_name'");
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$stmt = $conn->prepare("UPDATE country_cell_resp SET land = ? WHERE land = '$prev_name'");
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$stmt = $conn->prepare("UPDATE country_cell_resp SET maritime = ? WHERE maritime = '$prev_name'");
		$stmt->bind_param('s', $name);
		$stmt->execute();
	    $stmt = $conn->prepare("UPDATE country_cell_resp SET air = ? WHERE air = '$prev_name'");
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$stmt = $conn->prepare("UPDATE country_cell_resp SET country_leader = ? WHERE country_leader = '$prev_name'");
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$stmt = $conn->prepare("UPDATE cell_leaders AS c SET c.last_name = ? WHERE c.id = ?");
		$stmt->bind_param('si', $name, $id);
		$stmt->execute();
		return $name;
	}
	function removeLastNameQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE country_cell_resp AS c JOIN general_info AS g SET c.country_leader = '' WHERE g.id = ? AND c.country_leader = g.last_name");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt = $conn->prepare("UPDATE country_cell_resp AS c JOIN general_info AS g SET c.joint_enabling = '' WHERE g.id = ? AND c.joint_enabling = g.last_name");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt = $conn->prepare("UPDATE country_cell_resp AS c JOIN general_info AS g SET c.air = '' WHERE g.id = ? AND c.air = g.last_name");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt = $conn->prepare("UPDATE country_cell_resp AS c JOIN general_info AS g SET c.maritime = '' WHERE g.id = ? AND c.maritime = g.last_name");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt = $conn->prepare("UPDATE country_cell_resp AS c JOIN general_info AS g SET c.land = '' WHERE g.id = ? AND c.land = g.last_name");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$conn->prepare("UPDATE general_info AS g SET g.last_name = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceSecurityClearanceQuery($id, $security_clearance){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.security_clearance = ? WHERE g.id = ?");
		$stmt->bind_param('si', $security_clearance, $id);
		$stmt->execute();
		return $security_clearance;
	}
	function removeSecurityClearanceQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.security_clearance = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceFirstNameQuery($id, $name){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.first_name = ? WHERE g.id = ?");
		$stmt->bind_param('si', $name, $id);
		$stmt->execute();
		return $name;
	}
	function removeFirstNameQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.first_name = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceNationalityQuery($id, $nationality){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.nationality = ? WHERE g.id = ?");
		$stmt->bind_param('si', $nationality, $id);
		$stmt->execute();
		return $nationality;
	}
	function removeNatQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.nationality = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceRankQuery($id, $rank){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.rank = ? WHERE g.id = ?");
		$stmt->bind_param('si', $rank, $id);
		$stmt->execute();
		return $rank;
	}
	function removeRankQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.rank = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceCreditCardCompanyQuery($id, $company){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.credit_card_company = ? WHERE g.id = ?");
		$stmt->bind_param('si', $company, $id);
		$stmt->execute();
		return $company;
	}
	function removeCreditCardCompanyQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.credit_card_company = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceCreditCardNumberQuery($id, $number){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.credit_card_number = ? WHERE g.id = ?");
		$stmt->bind_param('si', $number, $id);
		$stmt->execute();
		return $number;
	}
	function removeCreditCardNumberQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.credit_card_number = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceCreditCardExpiryQuery($id, $expiry){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.credit_card_expiry = ? WHERE g.id = ?");
		$stmt->bind_param('si', $expiry, $id);
		$stmt->execute();
		return $expiry;
	}
	function removeCreditCardExpiryQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.credit_card_expiry = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceSecondaryEmailQuery($id, $email){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.secondary_email = ? WHERE g.id = ?");
		$stmt->bind_param('si', $email, $id);
		$stmt->execute();
		return $email;
	}
	function removeSecondaryEmailQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.secondary_email = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceMobileQuery($id, $mobile){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.mobile = ? WHERE g.id = ?");
		$stmt->bind_param('si', $mobile, $id);
		$stmt->execute();
		return $mobile;
	}
	function removeMobileQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.mobile = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceAddressQuery($id, $address){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.address = ? WHERE g.id = ?");
		$stmt->bind_param('si', $address, $id);
		$stmt->execute();
		return $address;
	}
	function removeAddressQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.address = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceTasksQuery($id, $tasks){
		global $conn;
		$string_array = explode("\n", $tasks);
		$new_tasks_string = '';
		if(count($string_array) == 1){
			$new_tasks_string = $string_array[0];
		}else if(count($string_array) > 1){
			for($i = 0; $i < count($string_array)-1; $i++){
				$new_tasks_string .= "$string_array[$i]<br>";
			}
			$new_tasks_string .= end($string_array);
		}
		$new_tasks_string = str_replace("\n", '', $new_tasks_string);
		$new_tasks_string = str_replace("\r", '', $new_tasks_string);
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.tasks = ? WHERE g.id = ?");
		$stmt->bind_param('si', $new_tasks_string, $id);
		$stmt->execute();
	}
	function removeTasksQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.tasks = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	/*
		This function changes the environmental cell corresponding to the
		given id. NOTE: once the environmental cell changes, the person will
		be removed from all its previous country responsibilities corresponding to his/her
		previous environmental cell.
		Param1: The corresponding id
		Param2: The new environmental cell
	*/
	function replaceEnvironmentQuery($id, $environment){
		global $conn;
		$prev_EC = '';
		$name = '';
		$stmt = $conn->prepare("SELECT DISTINCT g.environment, g.last_name FROM general_info AS g WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
			while($row = mysqli_fetch_assoc($result)){
				$prev_EC = $row['environment'];
				$name = $row['last_name'];
			}
		$result->free();
		if($prev_EC != $environment){
			$stmt = $conn->prepare("UPDATE general_info AS g SET g.environment = ? WHERE g.id = ?");
			$stmt->bind_param('si', $environment, $id);
			$stmt->execute();
			$stmt = $conn->prepare("UPDATE cell_leaders AS c SET c.cell_type = ? WHERE c.id = ?");
			$stmt->bind_param('si', $environment, $id);
			$stmt->execute();
		}
		return $environment;
	}
	//WARNING: This will also delete the row of the country leader with the given id!
	function removeECQuery($id){
		global $conn;
		$prev_EC = '';
		$stmt = $conn->prepare("SELECT DISTINCT g.environment, g.last_name FROM general_info AS g WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
			while($row = mysqli_fetch_assoc($result)){
				$prev_EC = $row['environment'];
				$name = $row['last_name'];
			}
		$result->free();
		switch($prev_EC){
			case 'JOINT/ENABLING':
				$stmt = $conn->prepare("UPDATE country_cell_resp SET joint_enabling = '' WHERE joint_enabling = ?");
				$stmt->bind_param('s', $name);
				$stmt->execute();
				break;
			case 'MARITIME':
				$stmt = $conn->prepare("UPDATE country_cell_resp SET maritime = '' WHERE maritime = ?");
				$stmt->bind_param('s', $name);
				$stmt->execute();
				break;
			case 'AIR':
				$stmt = $conn->prepare("UPDATE country_cell_resp SET air = '' WHERE air = ?");
				$stmt->bind_param('s', $name);
				$stmt->execute();
				break;
			case 'LAND':
				$stmt = $conn->prepare("UPDATE country_cell_resp SET land = '' WHERE land = ?");
				$stmt->bind_param('s', $name);
				$stmt->execute();
				break;
		}
		$stmt = $conn->prepare("DELETE FROM cell_leaders WHERE id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt = $conn->prepare("UPDATE c_resp AS c SET c.environment = '' WHERE c.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.environment = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replacePeQuery($id, $pe_num){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.pe_number = ? WHERE g.id = ?");
		$stmt->bind_param('si', $pe_num, $id);
		$stmt->execute();
		return $pe_num;
	}
	function removePeNumQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.pe_number = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replacePeFlagQuery($id, $pe_flag){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.pe_flag = ? WHERE g.id = ?");
		$stmt->bind_param('si', $pe_flag, $id);
		$stmt->execute();
	}
	function removePeFlagQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.pe_flag = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceBoTQuery($id, $BoT){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.BoT = '$BoT' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		return $BoT;
	}
	function removeBoTQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.BoT = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceEoTQuery($id, $EoT){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.EoT = '$EoT' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		return $EoT;
	}
	function removeEoTQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.EoT = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceNatoPassQuery($id, $NATO_PASS){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.NATO_PASS = ? WHERE g.id = ?");
		$stmt->bind_param('si', $NATO_PASS, $id);
		$stmt->execute();
		return $NATO_PASS;
	}
	function removeNatoPassQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.NATO_PASS = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceShapeIdQuery($id, $SHAPE_ID){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.SHAPE_ID = ? WHERE g.id = ?");
		$stmt->bind_param('si', $SHAPE_ID, $id);
		$stmt->execute();
		return $SHAPE_ID;
	}
	function removeShapeIdQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.SHAPE_ID = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceDobQuery($id, $DoB){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.DoB = ? WHERE g.id = ?");
		$stmt->bind_param('si', $DoB, $id);
		$stmt->execute();
		return $DoB;
	}
	function removeDobQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.DoB = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replacePassportQuery($id, $passport){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.Passport = ? WHERE g.id = ?");
		$stmt->bind_param('si', $passport, $id);
		$stmt->execute();
		return $passport;
	}
	function removePassportQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.Passport = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replacePassportExpiryQuery($id, $passport_expiry){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.Passport_expiry = ? WHERE g.id = ?");
		$stmt->bind_param('si', $passport_expiry, $id);
		$stmt->execute();
		return $passport_expiry;
	}
	function removePassportExpiryQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.Passport_expiry = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceEmailQuery($id, $email){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.email = ? WHERE g.id = ?");
		$stmt->bind_param('si', $email, $id);
		$stmt->execute();
		return $email;
	}
	function replacePhoneQuery($id, $phone){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.phone = ? WHERE g.id = ?");
		$stmt->bind_param('si', $phone, $id);
		$stmt->execute();
		return $phone;
	}
	function removePhoneQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.phone = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	function replaceUsernameQuery($id, $user){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.username = ? WHERE g.id = ?'");
		$stmt->bind_param('si', $user, $id);
		$stmt->execute();
		return $user;
	}
	function replacePasswordQuery($id, $pass){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.password = ? WHERE g.id = ?");
		$stmt->bind_param('si', $pass, $id);
		$stmt->execute();
		return $pass;
	}
	function replaceCAGQuery($id, $CAG){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.CAG = ? WHERE g.id = ?");
		$stmt->bind_param('si', $CAG, $id);
		$stmt->execute();
		return $CAG;
	}
	function removeCagQuery($id){
		global $conn;
		$stmt = $conn->prepare("UPDATE general_info AS g SET g.CAG = '' WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	/*
		Replaces the country leader of country corresponding to the specified
		country code and will also insert the country leader into its corresponding environmental cell
		Param1: The id of the person
		Param2: The array of countries
	*/
	//function replaceLeaderQuery($id, $code){
	function replaceLeaderQuery($id, $countries){
		global $conn;
		$query = "UPDATE country_cell_resp AS c JOIN general_info AS g SET c.country_leader = '' WHERE c.country_leader = g.last_name AND g.id = ?";
		$stmt = $conn->prepare($query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$query = "SELECT g.last_name, g.environment FROM general_info AS g WHERE g.id = ?";
		$stmt = $conn->prepare($query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$ln = '';
		$EC = '';
		while($row = $result->fetch_assoc()){
			$ln = $row['last_name'];
			$EC = $row['environment'];
		}
		$result->free();
		if(!empty($countries)){
			foreach($countries as $val){
				$query = "UPDATE country_cell_resp SET country_leader = ? WHERE code = ?";
				$stmt = $conn->prepare($query);
				$stmt->bind_param('ss', $ln, $val);
				$stmt->execute();
				if(!empty($EC)){
					$row_name = '';
					switch($EC){
						case 'JOINT/ENABLING':
							$query = "SELECT c.joint_enabling FROM country_cell_resp AS c WHERE c.country_leader = ?";
							$row_name = 'joint_enabling';
							break;
						case 'MARITIME':	
							$query = "SELECT c.joint_enabling FROM country_cell_resp AS c WHERE c.country_leader = ?";
							$row_name = 'maritime';
							break;
						case 'AIR':
							$query = "SELECT c.joint_enabling FROM country_cell_resp AS c WHERE c.country_leader = ?";
							$row_name = 'air';
							break;
						case 'LAND':
							$query = "SELECT c.joint_enabling FROM country_cell_resp AS c WHERE c.country_leader = ?";
							$row_name = 'land';
							break;
					}
					if(!empty($row_name)){
						$stmt = $conn->prepare($query);
						$stmt->bind_param('s', $ln);
						$stmt->execute();
						$result = $stmt->get_result();
						while($row = $result->fetch_assoc()){
							$prev_guy = $row[$row_name];
							if(!empty($prev_guy)){
								$query = "SELECT g.id FROM general_info AS g WHERE g.last_name = '$prev_guy'";
								if($id_result = $conn->query($query)){
									while($id_row = $id_result->fetch_assoc()){
										$prev_id = $id_row['id'];
									}
								}else
									echo $conn->error;
								$id_result->free();
								$conn->query("DELETE FROM c_resp WHERE id = '$prev_id' AND country_responsibilities = '$val'");
							}
						}
						$result->free();
						$query = "UPDATE country_cell_resp AS c SET c.'$row_name' = '$ln' WHERE c.code = '$val'";
						$conn->query($query);
					}
				}
			}
		}else
			echo "<h2>$ln is no longer responsible for leading countries!</h2>";
	}
	
	/*
		Replaces the partner nation leading responsibilities with country corresponding to the specified
		Param1: The id of the person
		Param2: The array of countries
	*/
	function replacePartnerLeaderQuery($id, $countries){
		global $conn;
		$query = "DELETE FROM p_resp WHERE leading_nation != '' AND id = ?";
		$stmt = $conn->prepare($query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		if(!empty($countries)){
			foreach($countries as $val){
				$query = "INSERT INTO p_resp(id, leading_nation) VALUES(?, '$val')";
				$stmt = $conn->prepare($query);
				$stmt->bind_param('i', $id);
				$stmt->execute();
			}
			foreach($countries as $val){
				$query = "DELETE FROM p_resp WHERE id != ? AND leading_nation = '$val'";
				$stmt = $conn->prepare($query);
				$stmt->bind_param('i', $id);
				$stmt->execute();
			}
		}else
			echo "<h2>$ln is no longer responsible for leading countries!</h2>";
	}
	/*
		This function will replace the environmental cell leader with the given name
		Param1: The name that will take the new place of the given environment
		Param2: The selected environment
		Param3: The current id
	*/
	function replaceECLeaderQuery($name, $EC, $id){
		global $conn;
		$stmt = $conn->prepare("UPDATE cell_leaders SET last_name = ?, id = ? WHERE cell_type = ?");
		$stmt->bind_param('sis', $name, $id, $EC);
		$stmt->execute();
	}
	
	/*
		This function will change the country responsibilities belonging to
		the person with the corresponding id. 
		NOTE: the country responsibilities will change according to the
		person's environmental cell and if there is already a previous value,
		that spot will be replaced by the new person.
		Param1: The corresponding id
		Param2: The array of selected countries
	*/
	function changeCountryRespQuery($id, $countries){
		global $conn;
		$EC = '';
		$name = '';
		//get the name and environmental cell
		
		$stmt = $conn->prepare("SELECT DISTINCT g.environment, g.last_name FROM general_info AS g WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		while($row = mysqli_fetch_assoc($result)){
			$EC = $row['environment'];
			$name = $row['last_name'];
		}
		$stmt = $conn->prepare("DELETE FROM c_resp WHERE c_resp.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$conn->query("UPDATE country_cell_resp SET joint_enabling = '' WHERE joint_enabling = '$name'");
		$conn->query("UPDATE country_cell_resp SET maritime = '' WHERE maritime = '$name'");
		$conn->query("UPDATE country_cell_resp SET air = '' WHERE air = '$name'");
		$conn->query("UPDATE country_cell_resp SET land = '' WHERE land = '$name'");
		
		
		for($i = 0; $i < sizeof($countries); $i++){
			if($countries[$i] != NULL){
				if($EC == 'JOINT/ENABLING'){
					$conn->query("UPDATE country_cell_resp SET joint_enabling = '$name' WHERE code = '$countries[$i]'");
				}else if($EC == 'MARITIME'){
					$conn->query("UPDATE country_cell_resp SET maritime = '$name' WHERE code = '$countries[$i]'");
				}else if($EC == 'LAND'){
					$conn->query("UPDATE country_cell_resp SET land = '$name' WHERE code = '$countries[$i]'");
				}else if($EC == 'AIR'){
					$conn->query("UPDATE country_cell_resp SET air = '$name' WHERE code = '$countries[$i]'");
				}
				//adds the new rows
				$stmt = $conn->prepare("INSERT INTO c_resp VALUES(?, ?, ?)");
				$stmt->bind_param('iss', $id, $countries[$i], $EC);
				$stmt->execute();
			
				//deletes the rows with the same country responsibility and environmental cell
				$stmt = $conn->prepare("DELETE FROM c_resp WHERE id != ? AND country_responsibilities = ? AND environment = ?");
				$stmt->bind_param('iss', $id, $countries[$i], $EC);
				$stmt->execute();
			}
		}
		$result->free();
	}
	/*
		This function will change the partner country responsibilities belonging to
		the person with the corresponding id. 
		Param1: The corresponding id
		Param2: The array of selected countries
	*/
	function changePartnerCountryQuery($id, $partners){
		global $conn;
		$stmt = $conn->prepare("DELETE FROM p_resp WHERE p_resp.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		for($i = 0; $i < sizeof($partners); $i++){
			if($partners[$i] != null){
				$stmt = $conn->prepare("INSERT INTO p_resp VALUES(?, ?)");
				$stmt->bind_param('is', $id, $partners[$i]);
				$stmt->execute();
			}
		}
	}
	
	/*
		This function will remove all rows matching the last name of the given id
	*/
	function removeProfile($id){
		global $conn;
		$stmt = $conn->prepare("SELECT g.last_name FROM general_info AS g WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt->bind_result($ln);
		$stmt->fetch();
		$stmt->close();
		$stmt = $conn->prepare("DELETE FROM general_info WHERE id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt = $conn->prepare("DELETE FROM p_resp WHERE id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt = $conn->prepare("DELETE FROM c_resp WHERE id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$conn->query("UPDATE country_cell_resp SET country_leader = '' WHERE country_leader = '$ln'");
		$conn->query("UPDATE country_cell_resp SET joint_enabling = '' WHERE joint_enabling = '$ln'");
		$conn->query("UPDATE country_cell_resp SET air = '' WHERE air = '$ln'");
		$conn->query("UPDATE country_cell_resp SET maritime = '' WHERE maritime = '$ln'");
		$conn->query("UPDATE country_cell_resp SET land = '' WHERE land = '$ln'");
		$stmt = $conn->prepare("UPDATE cell_leaders SET id = '', last_name = '' WHERE id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	#######################################################
	#GET QUERIES
	
	/*
		This function will fill the task box with the user's tasks
		Param1: The id of the person's tasks
	*/
	function getTasks($id){
		global $conn;
		$result = $conn->query("SELECT g.tasks FROM general_info AS g WHERE g.id = $id");
		while($row = $result->fetch_assoc()){
			$tasks = $row['tasks'];
		}
		if(@!empty($tasks)){
			$tasks = str_replace("<br>", "\n", $tasks);
			echo "$tasks";
		}
	}
	
	/*
		This function will return the specified attribute associated with the 
		given id from the database
		Param1: Specified id
		Param2: Desired attribute associated with the specified id
	*/
	function getQueryAttr($id, $attr){
		global $conn;
		$stmt = $conn->prepare("SELECT g.$attr FROM general_info AS g WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		while($row = $result->fetch_assoc()){
			return $row["$attr"];
		}
		
	}
	/*
		This function will return 0 if the user is not an admin
		and will return a 1 elsewise
		Param1: the target's id
	*/
	function getAdmin($id){
		global $conn;
		$admin = '';
		$stmt = $conn->prepare("SELECT g.admin FROM general_info AS g WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		while($row = $result->fetch_assoc())
			$admin = $row['admin'];
		$result->free();
		return $admin;
	}
	
	/*
		This query will get the current EC leader
		Param1: The selected environment
	*/
	function getECLeader($EC){
		global $conn;
		$stmt = $conn->prepare("SELECT c.last_name FROM cell_leaders AS c WHERE c.cell_type = ?");
		$stmt->bind_param('s', $EC);
		$stmt->execute();
		$stmt->bind_result($last_name);
		$stmt->fetch();
		if(!empty($last_name))
			return $last_name;
		else
			return '';
	}
	########################################################
	#ADMIN QUERIES
	
	/*
		This function will promote the target to an admin
		Param1: the target's id
	*/
	function promoteAdmin($id){
		global $conn;
		$make_admin_query = "UPDATE general_info AS g SET g.admin = '1' WHERE g.id = ?";
		$stmt = $conn->prepare($make_admin_query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
	/*
		This function will demote the target from admin
		Param1: the target's id
	*/
	function demoteAdmin($id){
		global $conn;
		$make_admin_query = "UPDATE general_info AS g SET g.admin = '0' WHERE g.id = ?";
		$stmt = $conn->prepare($make_admin_query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}
?>