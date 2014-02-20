<?php
	#NOTE:
	#Functions cannot be called if they are already included in another file!!
	#It is better to declare javascript/jquery functions through php echo since php loads before javascript
	#If some CSS/JS links are not working, try clearing cache or disabling it for that given website!
	require 'connect.incV2.php';
	include 'GlobalVariables.php';
	include 'regex.php';
	include 'queries.php';
	
	//Sessions are used to access variables throughout the website
	session_start();
	
	//fixes JavaScript errors
	#NOTE: Since PHP loads before JavaScript, we can echo out variables so that they can be applied to future imported scripts
	echo "<script>var isMainpage = true;</script>";
	
	
	###################################################################
	//SESSION FUNCTIONS
	
	//Gets the user's first name based on the session
	function getSessionFirstName(){
		global $conn;
		$id = $_SESSION['id'];
		$result = $conn->query("SELECT g.first_name FROM general_info AS g WHERE g.id = '$id'");
		while($row = $result->fetch_assoc()){
			return $_SESSION['first_name'] = $row['first_name'];
		}
		$result->free();
	}
	
	####################################################################
	//ADMIN FUNCTIONS
	
	/*
		Checks if the logged in user is an administrator
	*/
	function checkForAdmin(){
		global $conn, $isAdmin;
		$user = $_SESSION['username'];
		//prepares the statement
		$stmt = $conn->prepare("SELECT g.admin FROM general_info AS g WHERE g.username = ?");
		//binds the ? parameters with user of type String
		$stmt->bind_param('s', $user);
		//executes the statement since it is now prepared
		$stmt->execute();
		//store the result(s) in a new variable
		$stmt->bind_result($isAdmin);
		//fetches the results
		$stmt->fetch();
		if($isAdmin)
			$_SESSION['admin'] = true;
		else
			$_SESSION['admin'] = false;
		$stmt->close();
	}
	
	########################################################
	//OPTIONS FUNCTIONS
	
	/*
		This function will log the current user out
	*/
	function logout(){
		if(isset($_POST['logout'])){
			session_destroy();
			header("Location:http://localhost:8000/First%20project/Homepage.php");
		}
	}
	
	/*
		This function will bring the user to their profile page
	*/
	function profile(){
		if(isset($_POST['profile'])){
			header("Location:http://localhost:8000/First%20project/My_Profile.php");
		}
	}
	
	##############################################################
	//PEOPLE SEARCH FUNCTIONS

	/*
		These functions are brief searches for each different submission field
	*/
	function firstNameSearch(){
		if(isset($_POST['first_name_submit'])){
			$first_name_search = ucfirst(strtolower($_POST['first_name_search']));
			specificSearch($first_name_search, 'first_name');
		}
	}
	function lastNameSearch(){
		if(isset($_POST['last_name_submit'])){
			$last_name_search = ucfirst(strtolower($_POST['last_name_search']));
			specificSearch($last_name_search, 'last_name');
		}
	}
	function rankSearch(){
		if(isset($_POST['rank_submit'])){
			$rank_search = $_POST['rank_search'];
			specificSearch($rank_search, 'rank');
		}
	}
	function nationalitySearch(){
		if(isset($_POST['nationality_submit'])){
			$nationality_search = $_POST['nationality_search'];
			specificSearch($nationality_search, 'nationality');
		}
	}
	function environmentSearch(){
		if(isset($_POST['EC_submit'])){
			$EC_search = $_POST['EC_search'];
			specificSearch($EC_search, 'environment');
		}
	}
	function cagSearch(){
		if(isset($_POST['CAG_submit'])){
			$cag_search = $_POST['CAG_search'];
			specificSearch($cag_search, 'CAG');
		}
	}
	
	/*
		This function will work for any specific search.
		The parameters specified are:
		Param1:	the variable that is being searched for
		Param2:	the name of the database element that the searched variable must correspond to
	*/
	function specificSearch($searchElement, $dataBaseElement){
		global $empty_field, $no_results, $conn, $global_query, $row_count, $temp_var; //allows this function to access the specified variables outside of this function
		if(!empty($searchElement)){
			$temp_var = $searchElement;
			$global_query = "SELECT * FROM general_info WHERE $dataBaseElement = ?";
			$stmt = $conn->prepare($global_query);
			$stmt->bind_param('s', $searchElement);
			$stmt->execute();
			$query_run = $stmt->get_result();
			if($query_run->num_rows == NULL)
				$no_results = true;
			else{
				if($query_run->num_rows == 1)
					$row_count++;
				fetchResults($query_run);
			}
				$query_run->free();
		}else
			$empty_field = true;
	}
	
	/*
		This function will fetch the results from a query run and will output 
		a nice table displaying all the results.
		Param1:	the query run variable
	*/
	function fetchResults($query_run){
		echo "<table border='1' align='center' style='margin-top:20px;margin-bottom:20px;'>";
		if($_SESSION['admin'])
			echo "<tr bgcolor='yellow'><th>Id</th><th>Last name</th><th>First name</th><th>Nationality</th><th>Rank</th><th>Cell</th><th>CAG</th><th>PE Number</th><th>Beginning of Term</th><th>End of Term</th>
			<th>NATO PASS</th><th>SHAPE ID</th><th>Date of Birth</th><th>Passport</th><th>Passport expiry date</th><th>Email</th><th>Phone</th><th>Admin</th></tr>";
		else{
			echo "<tr bgcolor='yellow'><th>Last name</th><th>First name</th><th>Nationality</th><th>Rank</th><th>Cell</th><th>CAG</th><th>PE Number</th><th>Beginning of Term</th><th>End of Term</th><th>NATO PASS</th><th>SHAPE ID</th><th>Date of Birth</th><th>Passport</th><th>Passport expiry date</th><th>Email</th><th>Phone</th><th>Admin</th></tr>";
		}
		while($query_row = mysqli_fetch_assoc($query_run)){
			$id = $query_row['id'];
			$last_name = $query_row['last_name'];
			$first_name = $query_row['first_name'];
			$rank = $query_row['rank'];
			$nationality = $query_row['nationality'];
			$EC = $query_row['environment'];
			$CAG = $query_row['CAG'];
			$pe = $query_row['pe_number'];
			$BoT = $query_row['BoT'];
			$EoT = $query_row['EoT'];
			$NATO_PASS = $query_row['NATO_PASS'];
			$SHAPE_ID = $query_row['SHAPE_ID'];
			$DoB = $query_row['DoB'];
			$passport = $query_row['Passport'];
			$passport_expiry = $query_row['Passport_expiry'];
			$email = $query_row['email'];
			$phone = $query_row['phone'];
			$admin = $query_row['admin'];
			if($_SESSION['admin']){
				echo "<tr bgcolor='green' style='color:yellow'><td>$id</td><td>".$last_name."</td><td>".$first_name."</td><td>".$nationality."</td><td>".$rank."</td><td>".$EC."</td><td>$CAG</td><td>$pe</td><td>$BoT</td><td>$EoT</td><td>$NATO_PASS</td><td>$SHAPE_ID</td><td>$DoB</td><td>$passport</td><td>$passport_expiry</td><td>$email</td><td>$phone</td>";
			}else
				echo "<tr bgcolor='green' style='color:yellow'><td>".$last_name."</td><td>".$first_name."</td><td>".$nationality."</td><td>".$rank."</td><td>".$EC."</td><td>$CAG</td><td>$pe</td><td>$BoT</td><td>$EoT</td><td>$NATO_PASS</td><td>$SHAPE_ID</td><td>$DoB</td><td>$passport</td><td>$passport_expiry</td><td>$email</td><td>$phone</td>";
			if($admin == 1)
					echo "<td>yes</td></tr>";
				else
					echo "<td>no</td></tr>";
		}
		echo "</table>";
	}
	
	/*
		This function checks for if all the search fields have been filled and will make an accurate check 
		to find the given record.
	*/
	function personSearch(){
		if(isset($_POST['submit_search'])){
			global $conn, $no_results;
			$first_name_search = ucfirst(strtolower($_POST['first_name_search']));
			$last_name_search = ucfirst(strtolower($_POST['last_name_search']));
			$rank_search = strtolower($_POST['rank_search']);
			$nationality_search = $_POST['nationality_search'];
			$EC_search = $_POST['EC_search'];
			$CAG_search = $_POST['CAG_search'];
			if(!empty($_POST['first_name_search']) and !empty($_POST['last_name_search']) and !empty($_POST['rank_search']) and !empty($_POST['nationality_search']) and !empty($_POST['EC_search']) and !empty($_POST['CAG_search'])){
				$query = "SELECT * FROM general_info WHERE first_name = ? AND last_name = ? AND nationality = ? AND rank = ? AND environment = ? AND CAG = ?";
				$stmt->$conn->prepare($query);
				$stmt->bind_param('ssssss', $first_name_search, $last_name_search, $nationality_search, $rank_search, $EC_search, $CAG_search);
				$stmt->execute();
				$query_run = $stmt->get_result();
				//when doing this kind of search, make sure that ALL CORRESPONDING DATA HAS A VALUE!
				if($query_run->num_rows == NULL){
					$no_results = true;
				}else
					fetchResults($query_run);
				$query_run->free();
			}else
				echo "<h3 style='color:red;text-align:center'>Please fill in all people search fields.</h3>";
		}
	}	
	/*
		This function checks for if all individual responsibility search fields have been filled
		and will make sure that all data is inputed and will 
		give a table result of a person's given country responsibilities
	*/
	function fetchIndividualResponsibilities(){
		if(isset($_POST['res_submit'])){
			global $conn;
			$ln_search = ucfirst(strtolower($_POST['ln_res']));
			if(!empty($ln_search)){
				$query = "SELECT DISTINCT general_info.first_name, general_info.last_name
						FROM general_info
						WHERE general_info.last_name = '$ln_search'";
				if($query_run = mysqli_query($conn, $query)){
					if(mysqli_num_rows($query_run) == NULL)
						echo 'No results found.';
					else{
						$countries = array();
						$partners = array();
						$leading = array();
						$leading_partners = array();
						echo "<h2 style='text-align:center'>Responsibilities</h2>";
						$header = false;
						while($query_row = mysqli_fetch_assoc($query_run)){
							$country_check = true;
							$partner_check = true;
							$leading_check = true;
							$partner_leading_check = true;
							if(!$header){
								$fn = $query_row['first_name'];
								echo "<table border='1' align='center'><tr><th colspan='4' bgcolor='yellow'>$fn $ln_search</th></tr>";
								echo "<tr><th>NATO country responsibilities</th><th>Partner nation responsibilities</th><th>Country leadership responsibilities</th><th>Partner nation leadership responsibilities</th></tr>";
								$header = true;
							}
							$loop = true;
							//fills the arrays with the responsibilities
							if($country_result = $conn->query("SELECT c.country_responsibilities FROM c_resp AS c, general_info AS g WHERE g.last_name = '$ln_search' AND g.id = c.id ORDER BY c.country_responsibilities DESC")){
								while($country_row = $country_result->fetch_assoc()){
									$country = $country_row['country_responsibilities'];
									array_push($countries, $country);
								}
							}else
								echo $conn->error;
							$country_result->free();
							if($partner_result = $conn->query("SELECT DISTINCT p.partner_responsibilities FROM p_resp AS p, general_info AS g WHERE g.last_name = '$ln_search' AND p.partner_responsibilities != '' AND g.id = p.id ORDER BY p.partner_responsibilities DESC")){
								while($partner_row = $partner_result->fetch_assoc()){
									$partner = $partner_row['partner_responsibilities'];
									array_push($partners, $partner);
								}
							}else
								echo $conn->error;
							if($partner_leader_result = $conn->query("SELECT DISTINCT p.leading_nation FROM p_resp AS p, general_info AS g WHERE g.last_name = '$ln_search' AND p.leading_nation != '' AND g.id = p.id ORDER BY p.leading_nation DESC")){
								while($partner_leader_row = $partner_leader_result->fetch_assoc()){
									$partner_leader = $partner_leader_row['leading_nation'];
									array_push($leading_partners, $partner_leader);
								}
							}else
								echo $conn->error;
							$partner_leader_result->free();
							if($leader_result = $conn->query("SELECT DISTINCT c.code FROM country_cell_resp AS c, general_info AS g	WHERE c.country_leader = '$ln_search' ORDER BY c.code DESC")){
								while($leader_row = $leader_result->fetch_assoc()){
									$leader = $leader_row['code'];
									array_push($leading, $leader);
								}
							}else
								echo $conn->error;
							$leader_result->free();
							while($loop){
								echo "<tr style='color:blue;font-weight:bold' bgcolor='orange'>";
								if(!empty($countries)){
									$country = $countries[count($countries)-1];
									echo "<td>$country</td>";
									array_pop($countries);
								}else
									echo "<td></td>";
								if(!empty($partners)){
									$partner = $partners[count($partners)-1];
									echo "<td>$partner</td>";
									array_pop($partners);
								}else
									echo "<td></td>";
								if(!empty($leading)){
									$leader = $leading[count($leading)-1];
									echo "<td>$leader</td>";
									array_pop($leading);
								}else
									echo "<td></td>";
								if(!empty($leading_partners)){
									$partner_leader = $leading_partners[count($leading_partners)-1];
									echo "<td>$partner_leader</td>";
									array_pop($leading_partners);
								}else
									echo "<td></td>";
								echo "</tr>";
								
								if(empty($countries) and empty($partners) and empty($leading) and empty($leading_partners)){
									$loop = false;
								}
							}
						}
						echo "</table>";
					}
				}else
					echo $conn->error;
				mysqli_free_result($query_run);
			}else
				echo "<h3 style='color:red;text-align:center'>Please fill in the responsibility search field</h3>";
		}
	}
	
	/*
		This function will fill a select field with options,
		creating a drop-down menu of given elements
		Param1: The field in the database that is being searched
	*/
	function getOptions($field){
		global $conn;
		if($result = $conn->query("SELECT DISTINCT $field FROM general_info ORDER BY $field")){
			while($row = $result->fetch_assoc()){
				$option = $row["$field"];
				echo "<option value='$option'>$option</option>";
			}
		}else
			echo $conn->error;
		$result->free();
	}
	
	#######################################################
	//ENVIRONMENTAL CELL AND CAG RESPONSIBILITIES FUNCTIONS
	
	/*
		This function generates the table that represents all people in 
		a specified environmental cell
		Param1: The query
		Param2: The specified environmental cell
		Param3: The specified branch
	*/
	function newECTable($query, $EC = '', $branch = ''){
		global $conn; 
		if($query_run = mysqli_query($conn, $query)){
			$EC_color = '';
			$branch_color = '';
				switch($EC){
					case 'LAND':
						$EC_color = 'green';
						break;
					case 'AIR':
						$EC_color = '0099FF';
						break;
					case 'MARITIME':
						$EC_color = 'blue';
						break;
					case 'JOINT/ENABLING':
						$EC_color = 'gray';
						break;
					case 'CTR BRANCH':
						$EC_color = 'red';
						break;
				}
				switch($branch){
					case 'BRANCH HEAD':
						$branch_color = '009999';
						break;
					case 'ENGAGEMENT':
						$branch_color = '660066';
						break;
					case 'COMMAND SUPPORT':
						$branch_color = 'FF9900';
						break;
					case 'ENABLING':
						$branch_color = 'red';
						break;
					case 'COORDINATOR':
						$branch_color = '0000FF';
						break;
					case 'JOINT COORDINATOR':
						$branch_color = 'FF00FF';
						break;
				}
			if(mysqli_num_rows($query_run) == NULL and $EC != '')
				echo "<h3 style='text-align:center;margin-top:50px'>Nobody participating in the environmental cell <span style='color:$EC_color'>$EC</span> could be found</h3>";
			else if(mysqli_num_rows($query_run) == NULL and $branch != ''){
				echo "<h3 style='text-align:center;margin-top:50px'>Nobody participating in the <span style='color:$branch_color'>$branch</span> branch could be found</h3>";
			}else{
				if($EC != '')
					echo "<h2 style='text-align:center;color:$EC_color'>$EC</h2><table border='1' align='center'>";
				else if($branch != '')
					echo "<h2 style='text-align:center;color:$branch_color'>$branch</h2><table border='1' align='center'>";
				$colspan = 10;
				$col_count = 0;
				if($EC == 'CTR BRANCH')
					echo "<tr bgcolor='yellow'><th>Rank</th><th>First name</th><th>Last name</th><th>Nationality</th><th>PE Number</th><th></th><th colspan='$colspan'>NATO country responsibilities</th><th colspan='$colspan'>Partner nation responsibilities</th></tr>";
				else{
					if(!empty($branch))
						echo "<tr bgcolor='yellow'><th>Rank</th><th>First name</th><th>Last name</th><th>Nationality</th><th>PE Number</th><th>Environment</th><th colspan='$colspan'>NATO country responsibilities</th><th colspan='$colspan'>Partner nation responsibilities</th></tr>";
					else
						echo "<tr bgcolor='yellow'><th>Rank</th><th>First name</th><th>Last name</th><th>Nationality</th><th>PE Number</th><th>CAG</th><th colspan='$colspan'>NATO country responsibilities</th><th colspan='$colspan'>Partner nation responsibilities</th></tr>";
				}
				$checkDifferent = false;
				$checkLastRun = false;
				$idCheck = -1;
				while($row = $query_run->fetch_assoc()){
					$id = $row['id'];
					if($idCheck != $id){
						$idCheck = $id;
						$environment = $row['environment'];
						$ln = $row['last_name'];
						$fn = $row['first_name'];
						$nat = $row['nationality'];
						$rank = $row['rank'];
						$pe = $row['pe_number'];
						$CAG = $row['CAG'];
						if(!empty($branch))
							echo "<tr bgcolor='green' style='color:yellow'><td>$rank</td><td>$fn</td><td>$ln</td><td>$nat</td><td>$pe</td><td>$environment</td>";
						else
							echo "<tr bgcolor='green' style='color:yellow'><td>$rank</td><td>$fn</td><td>$ln</td><td>$nat</td><td>$pe</td><td>$CAG</td>";
						//echo "<script>$('#main_content').css('margin-left', '1%')</script>";
						$country_query = "SELECT c.country_responsibilities FROM c_resp AS c WHERE c.id = '$id'";
						if($country_result = $conn->query($country_query)){
							if($country_result->num_rows != NULL){
								while($country_row = $country_result->fetch_assoc()){
									$country = $country_row['country_responsibilities'];
									echo "<td>$country</td>";
									$col_count++;
								}
							}
						}else
							echo $conn->error;
						$country_result->free();
						while($col_count < $colspan){
							echo "<td></td>";
							$col_count++;
						}
						$col_count = 0;
						$partner_query = "SELECT p.partner_responsibilities FROM p_resp AS p WHERE p.id = '$id'";
						if($partner_result = $conn->query($partner_query)){
							if($partner_result->num_rows != NULL){
								$current_partner_nations = array();
								$found_dup = false;
								while($partner_row = $partner_result->fetch_assoc()){
									$nation = $partner_row['partner_responsibilities'];
									foreach($current_partner_nations as $val){
										if($val == $nation){
											$found_dup = true;
											break;
										}
									}
									if(!$found_dup){
										array_push($current_partner_nations, $nation);
										echo "<td>$nation</td>";
										$col_count++;
									}
								}
							}
						}
						while($col_count < $colspan){
							echo "<td></td>";
							$col_count++;
						}
						$col_count = 0;
						echo "</tr>";
					}
				}
				echo "</table>";
			}
		}else
			echo $conn->error;
		mysqli_free_result($query_run);
	}

	/*
		This function generates the table that represents all people's
		profiles in a specified Cell/CAG
		Param1: The query
		Param2: The specified cell
		Param3: The specified CAG/branch
	*/
	function newECProfile($query, $EC = '', $branch = ''){
		global $conn; 
		if($query_run = mysqli_query($conn, $query)){
			$EC_color = '';
			$branch_color = '';
				switch($EC){
					case 'LAND':
						$EC_color = 'green';
						break;
					case 'AIR':
						$EC_color = '0099FF';
						break;
					case 'MARITIME':
						$EC_color = 'blue';
						break;
					case 'JOINT/ENABLING':
						$EC_color = 'gray';
						break;
					case 'CTR BRANCH':
						$EC_color = 'red';
						break;
				}
				switch($branch){
					case 'BRANCH HEAD':
						$branch_color = '009999';
						break;
					case 'ENGAGEMENT':
						$branch_color = '660066';
						break;
					case 'COMMAND SUPPORT':
						$branch_color = 'FF9900';
						break;
					case 'ENABLING':
						$branch_color = 'red';
						break;
					case 'COORDINATOR':
						$branch_color = '0000FF';
						break;
					case 'JOINT COORDINATOR':
						$branch_color = 'FF00FF';
						break;
				}
			if(mysqli_num_rows($query_run) == NULL and $EC != '')
				echo "<h3 style='text-align:center;margin-top:50px'>Nobody participating in the environmental cell <span style='color:$EC_color'>$EC</span> could be found</h3>";
			else if(mysqli_num_rows($query_run) == NULL and $branch != ''){
				echo "<h3 style='text-align:center;margin-top:50px'>Nobody participating in the <span style='color:$branch_color'>$branch</span> branch could be found</h3>";
			}else{
				if($EC != '')
					echo "<h2 style='text-align:center;color:$EC_color'>$EC</h2><table border='1' align='center'>";
				else if($branch != '')
					echo "<h2 style='text-align:center;color:$branch_color'>$branch</h2><table border='1' align='center'>";
				if($EC == 'CTR BRANCH')
					echo "<tr bgcolor='yellow'><th>Rank</th><th>First name</th><th>Last name</th><th>Nationality</th><th>PE number</th><th></th></tr>";
				else{
					if($EC == NULL)
						echo "<tr bgcolor='yellow'><th>Rank</th><th>First name</th><th>Last name</th><th>Nationality</th><th>PE number</th><th>Environment</th></tr>";
					else
						echo "<tr bgcolor='yellow'><th>Rank</th><th>First name</th><th>Last name</th><th>Nationality</th><th>PE number</th><th>CAG</th></tr>";
				}
				while($row = $query_run->fetch_assoc()){
					$rank = $row['rank'];
					$last_name = $row['last_name'];
					$first_name = $row['first_name'];
					$nat = $row['nationality'];
					$pe = $row['pe_number'];
					$CAG = $row['CAG'];
					$cell = $row['environment'];
					if($EC == NULL)
						echo "<tr bgcolor='green' style='color:yellow'><td>$rank</td><td>$first_name</td><td>$last_name</td><td>$nat</td><td>$pe</td><td>$cell</td></tr>";
					else
						echo "<tr bgcolor='green' style='color:yellow'><td>$rank</td><td>$first_name</td><td>$last_name</td><td>$nat</td><td>$pe</td><td>$CAG</td></tr>";
				}
				echo "</table>";
			}
		}else
			echo $conn->error;
		mysqli_free_result($query_run);
	}
	/*
		These functions will display everyone's country responsibilities in the 
		corresponding environmental cell/branch
	*/
	function landResponsibilities(){
		if(isset($_POST['LAND_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.environment = 'LAND' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, 'LAND');
		}
	}
	function airResponsibilities(){
		if(isset($_POST['AIR_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.environment = 'AIR' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, 'AIR');
		}
	}
	function maritimeResponsibilities(){
		if(isset($_POST['MARITIME_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.environment = 'MARITIME' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, 'MARITIME');
		}
	}
	function jeResponsibilities(){
		if(isset($_POST['JE_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.environment = 'JOINT/ENABLING' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, 'JOINT/ENABLING');
		}
	}
	function coordinatorResponsibilities(){
		if(isset($_POST['COORDINATOR_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'COORDINATOR' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'COORDINATOR');
		}
	}
	function ctrBranchResponsibilities(){
		if(isset($_POST['CTR_BRANCH_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.environment = 'CTR BRANCH' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, 'CTR BRANCH');
		}
	}
	function jointCoordinatorResponsibilities(){
		if(isset($_POST['JOINT_COORDINATOR_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'JOINT COORDINATOR' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'JOINT COORDINATOR');
		}
	}
	function branchHeadResponsibilities(){
		if(isset($_POST['BRANCH_HEAD_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'BRANCH HEAD' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'BRANCH HEAD');
		}
	}
	function engagementResponsibilities(){
		if(isset($_POST['ENGAGEMENT_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'ENGAGEMENT' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'ENGAGEMENT');
		}
	}
	function commandSupportResponsibilities(){
		if(isset($_POST['COMMAND_SUPPORT_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'COMMAND SUPPORT' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'COMMAND SUPPORT');
		}
	}
	function sustainResponsibilities(){
		if(isset($_POST['SUSTAIN_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'ENABLING' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'ENABLING');
		}
	}
	
	/*
		These functions will display everyone's profile in the 
		corresponding environmental cell/CAG
	*/
	function landProfiles(){
		if(isset($_POST['LAND_profiles'])){
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.environment = 'LAND' ORDER BY g.last_name, g.first_name";
			newECProfile($query, 'LAND');
		}
	}
	function airProfiles(){
		if(isset($_POST['AIR_profiles'])){
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.environment = 'AIR' ORDER BY g.last_name, g.first_name";
			newECProfile($query, 'AIR');
		}
	}
	function maritimeProfiles(){
		if(isset($_POST['MARITIME_profiles'])){
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.environment = 'MARITIME' ORDER BY g.last_name, g.first_name";
			newECProfile($query, 'MARITIME');
		}
	}
	function jointEnablingProfiles(){
		if(isset($_POST['JE_profiles'])){
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.environment = 'JOINT/ENABLING' ORDER BY g.last_name, g.first_name";
			newECProfile($query, 'JOINT/ENABLING');
		}
	}
	function ctrBranchProfiles(){
		if(isset($_POST['CTR_BRANCH_profiles'])){
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.environment = 'CTR BRANCH' ORDER BY g.last_name, g.first_name";
			newECProfile($query, 'CTR BRANCH');
		}
	}
	function engagementProfiles(){
		if(isset($_POST['ENGAGEMENT_profiles'])){
			$query = "SELECT g.last_name, g.first_name, g.environment, g.rank, g.nationality, g.CAG, g.pe_number, g.CAG FROM general_info AS g WHERE g.CAG = 'ENGAGEMENT' ORDER BY g.last_name, g.first_name";
			newECProfile($query, '', 'ENGAGEMENT');
		}
	}
	function commandSupportProfiles(){
		if(isset($_POST['COMMAND_SUPPORT_profiles'])){
			$query = "SELECT g.last_name, g.first_name, g.rank, g.environment, g.nationality, g.CAG, g.pe_number, g.CAG FROM general_info AS g WHERE g.CAG = 'COMMAND SUPPORT' ORDER BY g.last_name, g.first_name";
			newECProfile($query, '', 'COMMAND SUPPORT');
		}
	}
	function enablingProfiles(){
		if(isset($_POST['ENABLING_profiles'])){
			$query = "SELECT g.last_name, g.first_name, g.rank, g.environment, g.nationality, g.CAG, g.pe_number, g.CAG FROM general_info AS g WHERE g.CAG = 'ENABLING' ORDER BY g.last_name, g.first_name";
			newECProfile($query, '', 'ENABLING');
		}
	}
	
	
	/*
		This function will display everyone's country responsibilities and their environmental cell
	*/
	function allResponsibilities(){
		if(isset($_POST['EC_res'])){
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.environment = 'CTR BRANCH' ORDER BY g.last_name, g.first_name";
			newECProfile($query, 'CTR BRANCH');
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.environment = 'LAND' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, 'LAND');
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.environment = 'AIR' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, 'AIR');
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.environment = 'MARITIME' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, 'MARITIME');
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, g.rank, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.environment = 'JOINT/ENABLING' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, 'JOINT/ENABLING');
		}
	}
	
	/*
		This function will display everyone's profiles and their environmental cell
	*/
	function allECProfiles(){
		if(isset($_POST['EC_profiles'])){
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.environment = 'CTR BRANCH' ORDER BY g.last_name, g.first_name";
			newECProfile($query, 'CTR BRANCH');
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.environment = 'LAND' ORDER BY g.last_name, g.first_name";
			newECProfile($query, 'LAND');
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.environment = 'AIR' ORDER BY g.last_name, g.first_name";
			newECProfile($query, 'AIR');
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.environment = 'MARITIME' ORDER BY g.last_name, g.first_name";
			newECProfile($query, 'MARITIME');
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.environment = 'JOINT/ENABLING' ORDER BY g.last_name, g.first_name";
			newECProfile($query, 'JOINT/ENABLING');
		}
	}
	
	/*
		This function will display everyone's profiles and their CAG
	*/
	function allCAGProfiles(){
		if(isset($_POST['CAG_profiles'])){
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.CAG = 'ENGAGEMENT' ORDER BY g.last_name, g.first_name";
			newECProfile($query, '', 'ENGAGEMENT');
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.CAG = 'COMMAND SUPPORT' ORDER BY g.last_name, g.first_name";
			newECProfile($query, '', 'COMMAND SUPPORT');
			$query = "SELECT g.last_name, g.first_name, g.rank, g.nationality, g.environment, g.pe_number, g.CAG FROM general_info AS g WHERE g.CAG = 'ENABLING' ORDER BY g.last_name, g.first_name";
			newECProfile($query, '', 'ENABLING');
		}
	}
	
	/*
		This function will display everyone's NATO country responsibilities and their CAG branch
	*/
	function allCagResponsibilities(){
		if(isset($_POST['ALL_CAG_res'])){
			$query = "SELECT g.first_name, g.rank, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'ENGAGEMENT' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'ENGAGEMENT');
			$query = "SELECT g.first_name, g.rank, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'COMMAND SUPPORT' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'COMMAND SUPPORT');
			$query = "SELECT g.first_name, g.rank, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'ENABLING' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'ENABLING');
			/*$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'COORDINATOR' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'COORDINATOR');
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'BRANCH HEAD' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'BRANCH HEAD');
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, g.pe_number, g.CAG, c.country_responsibilities, p.partner_responsibilities FROM `general_info` AS g, c_resp AS c, p_resp AS p WHERE g.CAG = 'JOINT COORDINATOR' AND c.id = g.id AND g.id = p.id ORDER BY last_name, first_name";
			newECTable($query, '', 'JOINT COORDINATOR');
			*/
		}
	}
	
	####################################################################
	//CECR FUNCTIONS
	
	/*
		This function will display all the countries and
		environmental cells of which everyone is responsible for
		and will display their cell leaders. (CECR means country, environmental cell, responsibilities)
		When CECR_all submit button is pressed.
	*/
	function getCECR(){
		if(isset($_POST['CECR_all'])){
			$query = "SELECT * FROM country_cell_resp";
			returnCECR($query);
			if(@$_POST['CECR_get_csv'] == 'y')
				getCSV($query);
		}
	}
	
	/*
		This function outputs the countries and
		environmental cells of which everyone is responsible for
		and will display their cell leaders. (CECR means country, environmental cell, responsibilities)
	*/
	function returnCECR($query){
		global $conn;
		$cell_leader_query = "SELECT * FROM cell_leaders";
		if($query_run = mysqli_query($conn, $cell_leader_query)){
			if(mysqli_num_rows($query_run) == NULL)
				echo "No environmental cell leaders found.";
			else{
				echo "<table border='1' align='center' style='margin-top:20px;font-family:arial;text-align:center;font-size:16px'>";
				echo "<tr><th bgcolor='#33CC33'>LAND leader</th><th bgcolor='#1975FF'>MARITIME leader</th><th bgcolor='#66CCFF'>AIR leader</th><th bgcolor='#B8B8B8'>JOINT/ENABLING leader</th></tr>";
				echo "<tr>";
				while($query_row = mysqli_fetch_assoc($query_run)){
					$last_name = $query_row['last_name'];
					echo "<td>$last_name</td>";
				}
				echo "</tr></table>";
			}
		}else
			echo $conn->error;
		$query_run->free();
		if($query_run = $conn->query($query)){
			if(mysqli_num_rows($query_run) == NULL){
				echo 'No results found.';
			}else{
				global $CECR_visible;
				$CECR_visible = true;
				$graySwitch = false;
				echo "<table border='1' align='center' style='margin-top:10px;font-family:arial;text-align:center;font-size:16px'>";
				echo "<tr bgcolor='yellow'><th>Nation</th><th style='color:green'>Code</th><th>Capital</th><th>Country Leader</th><th>JOINT/ENABLING</th><th>LAND</th><th>MARITIME</th><th>AIR</th></tr>";
				while($query_row = mysqli_fetch_assoc($query_run)){
					$nation = $query_row['nation'];
					$code = $query_row['code'];
					$capital = $query_row['capital'];
					$country_leader = $query_row['country_leader'];
					$joint_enabling = $query_row['joint_enabling'];
					$land = $query_row['land'];
					$maritime = $query_row['maritime'];
					$air = $query_row['air'];
					if(!$graySwitch){
						echo "<tr style='text-align:center'><td style='font-weight:bold'>$nation</td><td style='color:green;font-weight:bold'>$code</td><td>$capital</td><td bgcolor='orange' style='color:blue;font-weight:bold;font-family:arial'>$country_leader</td><td>$joint_enabling</td><td>$land</td><td>$maritime</td><td>$air</td></tr>";
						$graySwitch = true;
					}else{
						echo "<tr style='text-align:center'><td bgcolor='#B8B8B8' style='font-weight:bold'>$nation</td><td bgcolor='#B8B8B8'  style='color:green;font-weight:bold'>$code</td><td>$capital</td><td bgcolor='orange' style='color:blue;font-weight:bold;font-family:arial'>$country_leader</td><td bgcolor='#CCCCCC' >$joint_enabling</td><td bgcolor='#CCCCCC' >$land</td><td bgcolor='#CCCCCC' >$maritime</td><td bgcolor='#CCCCCC' >$air</td></tr>";
						$graySwitch = false;
					}
				}
				echo "</table>";
			}
		}else
			echo $conn->error;
		$query_run->free();
	}

	
	################################################################
	//PEOPLE EDIT FUNCTIONS
	
	/*
		This function will modify the desired person's
		information in the database according to the given input
	*/
	function editPerson(){
		global $conn, $display_responsibilities, $edit_people_confirm, $display_partner_nations, $display_partner_leader_nations, $display_leading_nations, $display_leader_responsibilities;
		if(isset($_POST['people_edit_submit'])){
			$id = $_POST['select_db_id'];
			if($id == '')
				echo "<h2 style='text-align:center;color:red'>Please enter an id</h2>";
			else{
				$query = "SELECT general_info.id FROM general_info WHERE general_info.id = ?";
				$stmt = $conn->prepare($query);
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$query_run = $stmt->get_result();
				if(mysqli_num_rows($query_run) == NULL){
					echo "<h2 style='text-align:center;color:red'>The entered id does not exist in database</h2>";
				}else{
					global $edit_error;
					$update = false;
					$removed_fields = array();
					$prev = array_fill(0, 30, NULL);//fills an empty array with values
					$ln = ucfirst(strtolower($_POST['ln_edit']));
					$fn = ucfirst(strtolower($_POST['fn_edit']));
					$nat = $_POST['nationality_edit'];
					$rank = $_POST['rank_edit'];
					$EC = $_POST['EC_edit'];
					$pe = $_POST['pe_input1'].' '.$_POST['pe_input2'].' '.$_POST['pe_num_edit'];
					$BoT = $_POST['edit_BoT'].'/'.$_POST['edit_BoT2'].'/'.$_POST['edit_BoT3'];
					$EoT = $_POST['edit_EoT'].'/'.$_POST['edit_EoT2'].'/'.$_POST['edit_EoT3'];
					$NATO_PASS = $_POST['NATO_PASS_edit'];
					$SHAPE_ID = $_POST['SHAPE_ID_edit'];
					$DoB = $_POST['DoB_edit'].'/'.$_POST['DoB_edit2'].'/'.$_POST['DoB_edit3'];
					$passport = $_POST['passport_edit'];
					$passport_expiry = $_POST['passport_expiry_edit'].'/'.$_POST['passport_expiry_edit2'].'/'.$_POST['passport_expiry_edit3'];
					$email = $_POST['email_edit'];
					$phone = $_POST['phone_edit'];
					$user = $_POST['username_edit'];
					$pass = $_POST['pass_edit'];
					$CAG = strtoupper($_POST['CAG_edit']);
					$EC_leader = $_POST['make_EC_leader'];
					$security_clearance = $_POST['security_clearance_edit'];
					$credit_card_company = $_POST['credit_card_company_edit'];
					$credit_card_number = $_POST['credit_card_number_edit'];
					$credit_card_expiry = $_POST['credit_card_expiry_edit'].'/'.$_POST['credit_card_expiry_edit2'];
					$secondary_email = $_POST['secondary_email_edit'];
					$mobile = $_POST['mobile_edit'];
					$address = $_POST['address_edit'];
					$tasks = $_POST['tasks_edit_area'];
					$pe_flag = $_POST['pe_flag_edit'];
					
					if(@$_POST['delete_profile_by_admin'] == 'x'){
						$ln = getQueryAttr($id, 'last_name');
						removeProfile($id);
						echo "<h2 style='text-align:center;color:blue'>All records belonging to $ln have been removed from the database!</h2>";
						return;
					}
					if($ln != ''){
						$prev[0] = getQueryAttr($id, 'last_name');
							if($ln != $prev[0]){
								if(validateName($ln)){
									$ln = replaceLastNameQuery($id, $ln);
									$update = true;
								}else
									$edit_error[0] = true;
						}
					}
					if(@$_POST['remove_fn'] == 'x'){
						removeFirstNameQuery($id);
						$removed_fields['fn'] = 'first name';
						$update = true;
					}else if($fn != ''){
						$prev[1] = getQueryAttr($id, 'first_name');
							if($fn != $prev[1]){
								if(validateName($fn)){
								$fn = replaceFirstNameQuery($id, $fn);
								$update = true;
							}else
								$edit_error[1] = true;
						}
					}
					if(@$_POST['remove_nat'] == 'x'){
						removeNatQuery($id);
						$removed_fields['nat'] = 'nationality';
						$update = true;
					}else if($nat != ''){
						$prev[2] = getQueryAttr($id, 'nationality');
						if($nat != $prev[2]){
							$nat = replaceNationalityQuery($id, $nat);
							$update = true;
						}
					}
					if(@$_POST['remove_rank'] == 'x'){
						removeRankQuery($id);
						$removed_fields['rank'] = 'rank';
						$update = true;
					}else if($rank != ''){
						$prev[3] = getQueryAttr($id, 'rank');
						if($rank != $prev[3]){
							$rank = replaceRankQuery($id, $rank);
							$update = true;
						}
					}
					if(@$_POST['remove_EC'] == 'x'){
						removeECQuery($id);
						$removed_fields['EC'] = 'environmental cell';
						$update = true;
					}else if($EC != ''){
						$prev[4] = getQueryAttr($id, 'environment');
						if($EC != $prev[4]){
							$EC = replaceEnvironmentQuery($id, $EC);
							$update = true;
						}
					}
					if(@$_POST['remove_pe_num'] == 'x'){
						removePeNumQuery($id);
						removePeFlagQuery($id);
						$removed_fields['pe'] = 'PE number';
						$update = true;
					}else if($pe != '  '){
						$prev[5] = getQueryAttr($id, 'pe_number');
						$prev[29] = getQueryAttr($id, 'pe_flag');
						if($pe != $prev[5] or $pe_flag != $prev[29]){
							if(validatePeNumber($pe)){
							replacePeFlagQuery($id, $pe_flag);
							$pe = replacePeQuery($id, $pe);
							$update = true;
						}else
							$edit_error[5] = true;
						}
					}
					if(@$_POST['remove_BoT'] == 'x'){
						removeBoTQuery($id);
						$removed_fields['BoT'] = 'beginning of term date';
						$update = true;
					}else if($BoT != '//'){
						$prev[6] = getQueryAttr($id, 'BoT');
						if($BoT != $prev[6]){
							if(validateTerm($BoT)){
								$BoT = replaceBoTQuery($id, $BoT);
								$update = true;
							}else
								$edit_error[13] = true;
						}
					}
					if(@$_POST['remove_EoT'] == 'x'){
						removeEoTQuery($id);
						$removed_fields['EoT'] = 'end of term date';
						$update = true;
					}else if($EoT != '//'){
						$prev[7] = getQueryAttr($id, 'EoT');
						if(@$EoT != $prev[7]){
							if(validateTerm($EoT)){
								$EoT = replaceEoTQuery($id, $EoT);
								$update = true;
							}else
								$edit_error[14] = true;
						}
					}
					if(@$_POST['remove_CAG'] == 'x'){
						removeCagQuery($id);
						$removed_fields['CAG'] = 'CAG';
						$update = true;
					}else if($CAG != ''){
						$prev[8] = getQueryAttr($id, 'CAG');
						if($CAG != $prev[8]){
							$CAG = replaceCAGQuery($id, $CAG);
							$update = true;
						}
					}
					if($email != ''){
						$prev[9] = getQueryAttr($id, 'email');
						if($prev[9] != $email){
							if(validateEmail($email)){
								$emailQuery = "SELECT g.email FROM general_info AS g WHERE g.email = ?";
								$stmt = $conn->prepare($emailQuery);
								$stmt->bind_param('s', $email);
								$stmt->execute();
								$result = $stmt->get_result();
								if(mysqli_num_rows($result) == NULL){
									$email = replaceEmailQuery($id, $email);
									$update = true;
								}else
									$edit_error[9] = true;
								$result->free();
							}else
								$edit_error[9] = true;
						}
					}
					if(@$_POST['remove_phone'] == 'x'){
						removePhoneQuery($id);
						$removed_fields['phone'] = 'phone number';
						$update = true;
					}else if($phone != ''){
						$prev[10] = getQueryAttr($id, 'phone');
						if($phone != $prev[10]){
							if(validatePhone($phone)){
								$phone = replacePhoneQuery($id, $phone);
								$update = true;
							}else
								$edit_error[10] = true;
						}
					}
					if($user != ''){
						$prev[11] = getQueryAttr($id, 'username');
						if($user != $prev[11]){
							if(validateUsername($user)){
								$userQuery = "SELECT g.username FROM general_info AS g WHERE g.username = ?";
								$user_stmt = $conn->prepare($userQuery);
								$user_stmt->bind_param('s', $user);
								$user_stmt->execute();
								$user_result = $user_stmt->get_result();
								if(mysqli_num_rows($user_result) == NULL){
									$user = replaceUsernameQuery($id, $user);
									$update = true;
								}else
									$edit_error[11] = true;
								$user_result->free();
							}else
								$edit_error[11] = true;
						}
					}
					if($pass != ''){
						$prev[12] = getQueryAttr($id, 'password');
						if($pass != $prev[12]){
							if(validatePassword($pass)){
								$pass = md5($pass);
								$pass = replacePasswordQuery($id, $pass);
								$update = true;
							}else
								$edit_error[12] = true;
						}
					}
					$prev[13] = getAdmin($id);
					if(@$_POST['make_admin'] == 'y'){	
						promoteAdmin($id);
					}else if(@$_POST['make_admin'] == 'n'){
						demoteAdmin($id);
					}
					if(getAdmin($id) != $prev[13])
						$update = true;
					$current_ln = '';
					if($EC_leader != ''){
						$prev[14] = getECLeader($EC_leader);
						$current_ln = getQueryAttr($id, 'last_name');
						if($prev[14] != $current_ln){
							replaceECLeaderQuery($current_ln, $EC_leader, $id);
							$update = true;
						}
					}
					if(@$_POST['remove_NATO_PASS'] == 'x'){
						removeNatoPassQuery($id);
						$removed_fields['NATO_PASS'] = 'NATO pass';
						$update = true;
					}else if($NATO_PASS != ''){
						$prev[15] = getQueryAttr($id, 'NATO_PASS');
						if($NATO_PASS != $prev[15]){
							$NATO_PASS = replaceNatoPassQuery($id, $NATO_PASS);
							$update = true;
						}
					}
					if(@$_POST['remove_SHAPE_ID'] == 'x'){
						removeShapeIdQuery($id);
						$removed_fields['SHAPE_ID'] = 'SHAPE ID';
						$update = true;
					}else if($SHAPE_ID != ''){
						$prev[16] = getQueryAttr($id, 'SHAPE_ID');
						if($SHAPE_ID != $prev[16]){
							$SHAPE_ID = replaceShapeIdQuery($id, $SHAPE_ID);
							$update = true;
						}
					}
					if(@$_POST['remove_DoB'] == 'x'){
						removeDobQuery($id);
						$removed_fields['DoB'] = 'Date of Birth';
						$update = true;
					}else if($DoB != '//'){
						$prev[17] = getQueryAttr($id, 'DoB');
						if($DoB != $prev[17]){
							if(validateTerm($DoB)){
								$DoB = replaceDobQuery($id, $DoB);
								$update = true;
							}else
								$edit_error[15] = true;
						}
					}
					if(@$_POST['remove_passport'] == 'x'){
						removePassportQuery($id);
						$removed_fields['passport'] = 'passport';
						$update = true;
					}else if($passport != ''){
						$prev[18] = getQueryAttr($id, 'Passport');
						if($passport != $prev[18]){
							$passport = replacePassportQuery($id, $passport);
							$update = true;
						}
					}
					if(@$_POST['remove_passport_expiry'] == 'x'){
						removePassportExpiryQuery($id);
						$removed_fields['passport_expiry'] = 'passport expiry date';
						$update = true;
					}else if($passport_expiry != '//'){
						$prev[19] = getQueryAttr($id, 'Passport_expiry');
						if($passport_expiry != $prev[19]){
							if(validateTerm($passport_expiry)){
								$passport_expiry = replacePassportExpiryQuery($id, $passport_expiry);
								$update = true;
							}else
								$edit_error[16] = true;
						}
					}
					if(@$_POST['remove_security_clearance'] == 'x'){
						removeSecurityClearanceQuery($id);
						$removed_fields['security_clearance'] = 'security clearance';
						$update = true;
					}else if(!empty($security_clearance)){
						$prev[20] = getQueryAttr($id, 'security_clearance');
						if($security_clearance != $prev[20]){
							$security_clearance = replaceSecurityClearanceQuery($id, $security_clearance);
							$update = true;
						}
					}
					if(@$_POST['remove_credit_card_company'] == 'x'){
						removeCreditCardCompanyQuery($id);
						$removed_fields['credit_card_company'] = 'credit card company';
						$update = true;
					}else if(!empty($credit_card_company)){
						$prev[21] = getQueryAttr($id, 'credit_card_company');
						if($credit_card_company != $prev[21]){
							$credit_card_company = replaceCreditCardCompanyQuery($id, $credit_card_company);
							$update = true;
						}
					}
					if(@$_POST['remove_credit_card_number'] == 'x'){
						removeCreditCardNumberQuery($id);
						$removed_fields['credit_card_number'] = 'credit card number';
						$update = true;
					}else if(!empty($credit_card_number)){
						if(validateCreditCardNumber($credit_card_number)){
							$prev[22] = getQueryAttr($id, 'credit_card_number');
							if($credit_card_number != $prev[22]){
								$credit_card_number = replaceCreditCardNumberQuery($id, $credit_card_number);
								$update = true;
							}
						}else
							$edit_error[17] = true;
					}
					if(@$_POST['remove_credit_card_expiry'] == 'x'){
						removeCreditCardExpiryQuery($id);
						$removed_fields['credit_card_expiry'] = 'credit card expiry';
						$update = true;
					}else if($credit_card_expiry != '/'){
						if(validateCreditCardExpiry($credit_card_expiry)){
							$prev[23] = getQueryAttr($id, 'credit_card_expiry');
							if($credit_card_expiry != $prev[23]){
								$credit_card_expiry = replaceCreditCardExpiryQuery($id, $credit_card_expiry);
								$update = true;
							}
						}else
							$edit_error[18] = true;
					}
					if(@$_POST['remove_secondary_email'] == 'x'){
						removeSecondaryEmailQuery($id);
						$removed_fields['secondary_email'] = 'secondary email';
						$update = true;
					}else if(!empty($secondary_email)){
						if(validateEmail($secondary_email)){
							$prev[24] = getQueryAttr($id, 'secondary_email');
							if($secondary_email != $prev[24]){
								$secondary_email = replaceSecondaryEmailQuery($id, $secondary_email);
								$update = true;
							}
						}else
							$edit_error[19] = true;
					}
					if(@$_POST['remove_mobile'] == 'x'){
						removeMobileQuery($id);
						$removed_fields['mobile'] = 'mobile';
						$update = true;
					}else if(!empty($mobile)){
						if(validatePhone($mobile)){
							$prev[25] = getQueryAttr($id, 'mobile');
							if($mobile != $prev[25]){
								$mobile = replaceMobileQuery($id, $mobile);
								$update = true;
							}
						}else
							$edit_error[20] = true;
					}
					if(@$_POST['remove_address'] == 'x'){
						removeAddressQuery($id);
						$removed_fields['address'] = 'address';
						$update = true;
					}else if(!empty($address)){
						$prev[26] = getQueryAttr($id, 'address');
						if($address != $prev[26]){
							$address = replaceAddressQuery($id, $address);
							$update = true;
						}
					}
					if(@$_POST['remove_tasks'] == 'x'){
						removeTasksQuery($id);
						$removed_fields['tasks'] = 'tasks';
						$update = true;
					}else if($tasks != ''){
						//$prev[27] = getQueryAttr($id, 'tasks');
						//if($tasks != $prev[27]){
							replaceTasksQuery($id, $tasks);
							//$update = true;
						//}
					}
					
					
					@$countries = $_POST['country_group'];
					@$leading_countries = $_POST['leader_group'];
					@$partner_nations = $_POST['partner_group'];
					@$partner_leading_nations = $_POST['partner_leading_group'];
					
					if(@$_POST['confirm_NATO_countries'] == 'y')
						$country_check = updateByCountryCategory('country_group', $countries, $id);
					if(@$_POST['confirm_leader_responsibilities'] == 'y')
						$country_leader_check = updateByCountryCategory('leader_group', $leading_countries, $id);
					if(@$_POST['confirm_partner_nations'] == 'y')
						$partner_check = updateByCountryCategory('partner_group', $partner_nations, $id);
					if(@$_POST['confirm_partner_leader_nations'] == 'y')
						$partner_leader_check = updateByCountryCategory('partner_leading_group', $partner_leading_nations, $id);
						
					
						if(@$_POST['confirm_NATO_countries'] == 'y' and !$country_check){
							$display_responsibilities = true;
							$update = true;
						}
						if(@$_POST['confirm_partner_nations'] == 'y' and !$country_leader_check){
							$display_partner_nations = true;
							$update = true;
						}
						if(@$_POST['confirm_partner_leader_nations'] == 'y' and !$partner_check){
							$display_partner_leader_nations = true;
							$update = true;
						}
						if(@$_POST['confirm_leader_responsibilities'] == 'y' and !$partner_leader_check){
							$display_leading_nations = true;
							$update = true;
						}
					
					if(@$update)
						echo "<h2 style='text-align:center;color:blue'>The database has been updated!</h2>";
					$previously_empty = false;
					if(!empty($prev)){
						for($i = 0; $i < sizeof($prev); $i++){
							if($prev[$i] == ''){
								$previously_empty = true;
							}else{
								$previously_empty = false;
								break;
							}
						}
					}
					$ln = getQueryAttr($id, 'last_name');
					if(@$update){
						if(!$previously_empty){
							echo "<table border='1' align='center' style='margin-top:20px;text-align:center' id='person_changes'>";
							echo "<tr><th colspan='4'>Changes made</th></tr>";
							echo "<script>$('table#person_changes').hide();</script>";
							if($prev[0] != NULL and $prev[0] != $ln and !$edit_error[0]){
								echo "<tr><td>Last name</td><td>$prev[0]</td><td>-></td><td>$ln</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[1] != NULL and $prev[1] != $fn and !$edit_error[1]){
								echo "<tr><td>First name</td><td>$prev[1]</td><td>-></td><td>$fn</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[2] != NULL and $prev[2] != $nat){
								echo "<tr><td>Nationality</td><td>$prev[2]</td><td>-></td><td>$nat</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[3] != NULL and $prev[3] != $rank){
								echo "<tr><td>Rank</td><td>$prev[3]</td><td>-></td><td>$rank</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[4] != NULL and $prev[4] != $EC){
								echo "<tr><td>Environment</td><td>$prev[4]</td><td>-></td><td>$EC</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[5] != NULL and ($prev[5] != $pe or $prev[29] != $pe_flag) and !$edit_error[5]){
								echo "<tr><td>PE number</td><td>$prev[5] $prev[29]</td><td>-></td><td>$pe $pe_flag</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[6] != NULL and $prev[6] != $BoT and !$edit_error[13]){
								echo "<tr><td>Beginning of Term</td><td>$prev[6]</td><td>-></td><td>$BoT</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[7] != NULL and $prev[7] != $EoT and !$edit_error[14]){
								echo "<tr><td>End of Term</td><td>$prev[7]</td><td>-></td><td>$EoT</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[15] != NULL and $prev[15] != $NATO_PASS){
								echo "<tr><td>NATO pass</td><td>$prev[15]</td><td>-></td><td>$NATO_PASS</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[16] != NULL and $prev[16] != $SHAPE_ID){
								echo "<tr><td>SHAPE ID</td><td>$prev[16]</td><td>-></td><td>$SHAPE_ID</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[17] != NULL and $prev[17] != $DoB and !$edit_error[15]){
								echo "<tr><td>Date of Birth</td><td>$prev[17]</td><td>-></td><td>$DoB</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[18] != NULL and $prev[18] != $passport){
								echo "<tr><td>Passport ID</td><td>$prev[18]</td><td>-></td><td>$passport</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[19] != NULL and $prev[19] != $passport_expiry and !$edit_error[16]){
								echo "<tr><td>Passport expiry date</td><td>$prev[19]</td><td>-></td><td>$passport_expiry</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[8] != NULL and $prev[8] != $CAG){
								echo "<tr><td>CAG</td><td>$prev[8]</td><td>-></td><td>$CAG</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[9] != NULL and $prev[9] != $email and !$edit_error[9]){
								echo "<tr><td>Email</td><td>$prev[9]</td><td>-></td><td>$email</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[10] != NULL and $prev[10] != $phone and !$edit_error[10]){
								echo "<tr><td>Phone</td><td>$prev[10]</td><td>-></td><td>$phone</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[11] != NULL and $prev[11] != $user and !$edit_error[11]){
								echo "<tr><td>Username</td><td>$prev[11]</td><td>-></td><td>$user</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[12] != NULL and $prev[12] != $pass and !$edit_error[12]){
								echo "<tr><td>Password</td><td>$prev[12]</td><td>-></td><td>$pass</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[13] != NULL and $prev[13] != getAdmin($id) and $prev[13] == 0){
								echo "<tr><td colspan='4'>$ln has been promoted to admin!</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}else if($prev[13] != NULL and $prev[13] != getAdmin($id) and $prev[13] == 1){
								echo "<tr><td colspan='4'>$ln has been demoted from admin!</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[14] != $current_ln){
								echo "<tr><td colspan='4'>$current_ln is now the leading the $EC_leader cell!</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[20] != NULL and $prev[20] != $security_clearance){
								echo "<tr><td>Security clearance</td><td>$prev[20]</td><td>-></td><td>$security_clearance</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[21] != NULL and $prev[21] != $credit_card_company){
								echo "<tr><td>Credit card company</td><td>$prev[21]</td><td>-></td><td>$credit_card_company</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[22] != NULL and $prev[22] != $credit_card_number and !$edit_error[17]){
								echo "<tr><td>Credit card number</td><td>$prev[22]</td><td>-></td><td>$credit_card_number</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[23] != NULL and $prev[23] != $credit_card_expiry and !$edit_error[18]){
								echo "<tr><td>Credit card expiry</td><td>$prev[23]</td><td>-></td><td>$credit_card_expiry</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[24] != NULL and $prev[24] != $secondary_email and !$edit_error[19]){
								echo "<tr><td>Personal email</td><td>$prev[24]</td><td>-></td><td>$secondary_email</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[25] != NULL and $prev[25] != $mobile and !$edit_error[20]){
								echo "<tr><td>Mobile</td><td>$prev[25]</td><td>-></td><td>$mobile</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[26] != NULL and $prev[26] != $address){
								echo "<tr><td>Address</td><td>$prev[26]</td><td>-></td><td>$address</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}if($prev[27] != NULL and $prev[27] != $tasks){
								echo "<tr><td colspan='4'>$prev[0]'s tasks have been updated!</td></tr>";
								echo "<script>$('table#person_changes').show();</script>";
							}
							$show_table_from_removed = false;
							foreach($removed_fields as $var){
								if(!empty($var)){
									echo "<tr><td colspan='4'>$prev[0]'s $var has been removed!</td></tr>";
									$show_table_from_removed = true;
								}
							}
							if($show_table_from_removed)
								echo "<script>$('table#person_changes').show();</script>";
							echo "</table>";
						}
					}
				}
			$query_run->free();
			}
		}
	}
	
	/*
		This function will display the errors that the admin
		might encounter when editing someone's profile
		Param1: The array of errors
	*/
	function displayEditErrors($errors){
		global $conn;
		$display_div = false;
		foreach($errors as $val){
			if($val){
				$display_div = true;
				break;
			}
		}
		if($display_div){
			echo "<div id='edit_errors_div'>";
			echo "<h3>The following fields did not affect the database</h3>";
			echo "<ul>";
			for($i = 0; $i < count($errors); $i++){
				if($i == 0 and $errors[$i])
					 echo "<li>Invalid last name</li>";
				else if($i == 1 and $errors[$i])
					echo "<li>Invalid first name</li>";
				else if($i == 5 and $errors[$i])	
					echo "<li>Invalid PE number</li>";
				else if($i == 13 and $errors[$i])		
					echo "<li>Invalid beginning of term</li>";
				else if($i == 14 and $errors[$i])
					echo "<li>Invalid end of term</li>";
				else if($i == 15 and $errors[$i])
					echo "<li>Invalid date of birth</li>";
				else if($i == 16 and $errors[$i])
					echo "<li>Invalid passport expiry date</li>";
				else if($i == 10 and $errors[$i])
					echo "<li>Invalid email</li>";
				else if($i == 11 and $errors[$i])
					echo "<li>Username is already taken or is invalid</li>";
				else if($i == 12 and $errors[$i])
					echo "<li>Invalid password</li>";
				else if($i == 17 and $errors[$i])
					echo "<li>Invalid credit card number</li>";
				else if($i == 18 and $errors[$i])
					echo "<li>Invalid credit card expiry date</li>";
				else if($i == 19 and $errors[$i])
					echo "<li>Invalid personal email</li>";
				else if($i == 20 and $errors[$i])
					echo "<li>Invalid mobile</li>";
			}
			echo "</ul>";
			echo "</div>";
		}
	}
	
	/*
		This function will return true if the inputed countries have updated
		Param1: The name of the country category
		Param2: The array of countries
		Param3: The target id
	*/
	function updateByCountryCategory($cat, $countries, $id){
		//check if the countries stored in the db match up with those checked
		//if the checked countries are the same with those found in db, then
		//the table displaying the person's country responsibilities will not display
		$col_select = '';
		global $conn;
		//$country_stmt = '';
		switch($cat){
			case 'country_group':
				$country_stmt = $conn->prepare("SELECT c.country_responsibilities FROM c_resp AS c WHERE c.id = ?");
				$col_select = 'country_responsibilities';
				break;
			case 'leader_group':
				$ln = getQueryAttr($id, 'last_name');
				$conn->prepare("SELECT c.country_leader FROM country_cell_resp AS c WHERE c.country_leader = $ln");
				$col_select = 'country_leader';
				break;
			case 'partner_group':
				$country_stmt = $conn->prepare("SELECT p.partner_responsibilities FROM p_resp AS p WHERE p.id = ?");
				$col_select = 'partner_responsibilities';
				break;
			case 'partner_leader_group':
				$country_stmt = $conn->prepare("SELECT p.leading_nation FROM p_resp AS p WHERE p.id = ?");
				$col_select = 'leading_nation';
				break;
		}
		$check = true;
		if(!empty($col_select) and $col_select != 'country_leader'){
			$country_stmt->bind_param('i', $id);
			$country_stmt->execute();
			$country_result = $country_stmt->get_result();
				if(sizeof($countries) == $country_result->num_rows){
					while($row = mysqli_fetch_assoc($country_result)){
						$curr = $row[$col_select];
						for($i = 0; $i < sizeof($countries) and $check; $i++){
							if($countries[$i] == $curr){
								$check = true;
								break;
							}else if($i == sizeof($countries)-1){
								$check = false;
							}
						}
					}
				}else
					$check = false;
			$country_result->free();
		}
		return $check;
	}
	/*
		This function will display the updated country responsibilities
		and will execute the query that will change the person's country
		responsibilities.
		Param1: The matching person's id
		Param2: The array of selected country responsibilities
	*/
	function crespDisplay($id, $countries){
		global $conn, $c_check;
		$c_check = true;
		$last_name = '';
		changeCountryRespQuery($id, $countries);
		$stmt = $conn->prepare("SELECT DISTINCT g.last_name FROM general_info AS g WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		while($row = mysqli_fetch_assoc($result)){
			$last_name = $row['last_name'];
		}
		$result->free();
		echo "<table border='1' align='center' style='margin-top:10px;margin-bottom:10px'>";
		echo "<tr><th>$last_name's current NATO country responsibilities</th></tr>";
		$country_query = "SELECT c.country_responsibilities FROM c_resp AS c WHERE c.id = ?";
		$stmt = $conn->prepare($country_query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$country_result = $stmt->get_result();
		while($row = mysqli_fetch_assoc($country_result)){
			$country = $row['country_responsibilities'];
			echo "<tr><td>$country</td></tr>";
		}
		$country_result->free();
		echo "</table>";
	}
	
	/*
		This function will display the updated partner country responsibilities
		and will execute the query that will change the person's partner country responsibilities
		Param1: The matching person's id
		Param2: The array of selected partner country responsibilities
	*/
	function partnerCountryDisplay($id, $partners){
		global $conn, $c_check;
		$last_name = '';
		changePartnerCountryQuery($id, $partners);
		$c_check = true;
		$stmt = $conn->prepare("SELECT DISTINCT g.last_name FROM general_info AS g WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
			while($row = mysqli_fetch_assoc($result)){
				$last_name = $row['last_name'];
			}
		$result->free();
		echo "<table border='1' align='center' style='margin-top:10px;margin-bottom:10px'>";
		echo "<tr><th>$last_name's current partner country responsibilities</th></tr>";
		$partner_query = "SELECT p.partner_responsibilities FROM p_resp AS p WHERE p.id = ?";
		$stmt = $conn->prepare($partner_query);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$partner_result = $stmt->get_result();
		while($row = mysqli_fetch_assoc($partner_result)){
			$country = $row['partner_responsibilities'];
			echo "<tr><td>$country</td></tr>";
		}
		$partner_result->free();
		echo "</table>";
	}
	
	/*
		This function will check if the leader has changed and will
		display the new countries the given person is now leading
		Param1: The corresponding person's id
	*/
	function leaderUpdate($id){
		global $conn;
		@$countries = $_POST['leader_group'];
		replaceLeaderQuery($id, $countries);
		$stmt = $conn->prepare("SELECT c.nation, c.country_leader FROM country_cell_resp AS c, general_info AS g WHERE c.country_leader = g.last_name AND g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$header = true;
		while($row = mysqli_fetch_assoc($result)){
			$nation = $row['nation'];
			$leader = $row['country_leader'];
			if($header){
				echo "<table border='1' align='center' style='margin-top:10px;margin-bottom:10px'>";
				echo "<tr><th>$leader is now leading the following countries</th></tr>";
				$header = false;
			}
			echo "<tr><td>$nation</td></tr>";
		}
		$result->free();
		echo "</table>";
	}
	
	/*
		This function will check if the partner nation leader has changed and will
		display the new countries the given person is now leading
		Param1: The corresponding person's id
	*/
	function partnerLeaderUpdate($id){
		global $conn;
		@$partner_leader_countries = $_POST['partner_leader_group'];
		replacePartnerLeaderQuery($id, $partner_leader_countries);
		$stmt = $conn->prepare("SELECT g.last_name FROM general_info AS g WHERE g.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt->bind_result($ln);
		$stmt->fetch();
		$stmt->close();
		$stmt = $conn->prepare("SELECT p.leading_nation FROM p_resp AS p WHERE p.id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$header = true;
		while($row = mysqli_fetch_assoc($result)){
			$nation = $row['leading_nation'];
			if($header){
				echo "<table border='1' align='center' style='margin-top:10px;margin-bottom:10px'>";
				echo "<tr><th>$ln is now leading the following partner nations</th></tr>";
				$header = false;
			}
			echo "<tr><td>$nation</td></tr>";
		}
		$result->free();
		echo "</table>";
	}
	/*
		This function will fill in the people editing text input fields
		with the given information of the searched person.
		Param1: the input query
	*/
	function fillPeopleEdit($query){
		global $conn, $row_count, $temp_var;
		if($row_count == 1){
			$stmt = $conn->prepare($query);
			$stmt->bind_param('s', $temp_var);
			$stmt->execute();
			$other_result = $stmt->get_result();
			while($people_row = mysqli_fetch_assoc($other_result)){
				$id = $people_row['id'];
				$last_name = $people_row['last_name'];
				$first_name = $people_row['first_name'];
				$nat = $people_row['nationality'];
				$rank = $people_row['rank'];
				$EC = $people_row['environment'];
				$pe = $people_row['pe_number'];
				$BoT = $people_row['BoT'];
				$EoT = $people_row['EoT'];
				$NATO_PASS = $people_row['NATO_PASS'];
				$SHAPE_ID = $people_row['SHAPE_ID'];
				$DoB = $people_row['DoB'];
				$passport = $people_row['Passport'];
				$passport_expiry = $people_row['Passport_expiry'];
				$CAG = $people_row['CAG'];
				$email = $people_row['email'];
				$phone = $people_row['phone'];
				$user = $people_row['username'];
				$security_clearance = $people_row['security_clearance'];
				$credit_card_company = $people_row['credit_card_company'];
				$credit_card_number = $people_row['credit_card_number'];
				$credit_card_expiry = $people_row['credit_card_expiry'];
				$secondary_email = $people_row['secondary_email'];
				$mobile = $people_row['mobile'];
				$address = $people_row['address'];
				$tasks = $people_row['tasks'];
				$pe_flag = $people_row['pe_flag'];
				
				$countryQuery = "SELECT c.country_responsibilities FROM c_resp AS c WHERE c.id = ?";
				$stmt = $conn->prepare($countryQuery);
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$country_result = $stmt->get_result();
				echo "<script>$('input.c_edit_div_sub').prop('checked', false);</script>";
				while($row = mysqli_fetch_assoc($country_result)){
					$country = $row['country_responsibilities'];
					if($country == 'ALB'){
						echo "<script>$('#Albania').prop('checked', true);</script>";
					}if($country == 'BEL'){
						echo "<script>$('#Belgium').prop('checked', true);</script>";
					}if($country == 'BGR'){
						echo "<script>$('#Bulgaria').prop('checked', true);</script>";
					}if($country == 'CAN'){
						echo "<script>$('#Canada').prop('checked', true);</script>";
					}if($country == 'HRV'){
						echo "<script>$('#Croatia').prop('checked', true);</script>";
					}if($country == 'CZE'){
						echo "<script>$('#Czech_Republic').prop('checked', true);</script>";
					}if($country == 'DNK'){
						echo "<script>$('#Denmark').prop('checked', true);</script>";
					}if($country == 'EST'){
						echo "<script>$('#Estonia').prop('checked', true);</script>";
					}if($country == 'FRA'){
						echo "<script>$('#France').prop('checked', true);</script>";
					}if($country == 'DEU'){
						echo "<script>$('#Germany').prop('checked', true);</script>";
					}if($country == 'GRC'){
						echo "<script>$('#Greece').prop('checked', true);</script>";
					}if($country == 'HUN'){
						echo "<script>$('#Hungary').prop('checked', true);</script>";
					}if($country == 'ISL'){
						echo "<script>$('#Iceland').prop('checked', true);</script>";
					}if($country == 'ITA'){
						echo "<script>$('#Italy').prop('checked', true);</script>";
					}if($country == 'LVA'){
						echo "<script>$('#Latvia').prop('checked', true);</script>";
					}if($country == 'LTU'){
						echo "<script>$('#Lithuania').prop('checked', true);</script>";
					}if($country == 'LUX'){
						echo "<script>$('#Luxembourg').prop('checked', true);</script>";
					}if($country == 'NLD'){
						echo "<script>$('#Netherlands').prop('checked', true);</script>";
					}if($country == 'NOR'){
						echo "<script>$('#Norway').prop('checked', true);</script>";
					}if($country == 'POL'){
						echo "<script>$('#Poland').prop('checked', true);</script>";
					}if($country == 'PRT'){
						echo "<script>$('#Portugal').prop('checked', true);</script>";
					}if($country == 'ROU'){
						echo "<script>$('#Romania').prop('checked', true);</script>";
					}if($country == 'SVK'){
						echo "<script>$('#Slovakia').prop('checked', true);</script>";
					}if($country == 'SVN'){
						echo "<script>$('#Slovenia').prop('checked', true);</script>";
					}if($country == 'ESP'){
						echo "<script>$('#Spain').prop('checked', true);</script>";
					}if($country == 'TUR'){
						echo "<script>$('#Turkey').prop('checked', true);</script>";
					}if($country == 'GBR'){
						echo "<script>$('#United_Kingdom').prop('checked', true);</script>";
					}if($country == 'USA'){
						echo "<script>$('#United_States').prop('checked', true);</script>";
					}
				}
				$partner_query = "SELECT p.partner_responsibilities FROM p_resp AS p WHERE p.id = ?";
				$stmt = $conn->prepare($partner_query);
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$partner_result = $stmt->get_result();
				echo "<script>$('input.partner_n_edit_div_sub').prop('checked', false);</script>";
				while($row = mysqli_fetch_assoc($partner_result)){
					$country = $row['partner_responsibilities'];
					if($country == 'ARM'){
						echo "<script>$('#p_Armenia').prop('checked', true);</script>";
					}if($country == 'AUT'){
						echo "<script>$('#p_Austria').prop('checked', true);</script>";
					}if($country == 'AZE'){
						echo "<script>$('#p_Azerbaijan').prop('checked', true);</script>";
					}if($country == 'BLR'){
						echo "<script>$('#p_Belarus').prop('checked', true);</script>";
					}if($country == 'BIH'){
						echo "<script>$('#p_Bosnia_Herzegovina').prop('checked', true);</script>";
					}if($country == 'FIN'){
						echo "<script>$('#p_Finland').prop('checked', true);</script>";
					}if($country == 'FYR'){
						echo "<script>$('#p_FYROM').prop('checked', true);</script>";
					}if($country == 'GEO'){
						echo "<script>$('#p_Georgia').prop('checked', true);</script>";
					}if($country == 'IRL'){
						echo "<script>$('#p_Ireland').prop('checked', true);</script>";
					}if($country == 'KAZ'){
						echo "<script>$('#p_Kazakhstan').prop('checked', true);</script>";
					}if($country == 'KGZ'){
						echo "<script>$('#p_Kyrgyztan').prop('checked', true);</script>";
					}if($country == 'MLT'){
						echo "<script>$('#p_Malta').prop('checked', true);</script>";
					}if($country == 'MDA'){
						echo "<script>$('#p_Moldova').prop('checked', true);</script>";
					}if($country == 'MNE'){
						echo "<script>$('#p_Montenegro').prop('checked', true);</script>";
					}if($country == 'RUS'){
						echo "<script>$('#p_Russian_Federation').prop('checked', true);</script>";
					}if($country == 'SRB'){
						echo "<script>$('#p_Serbia').prop('checked', true);</script>";
					}if($country == 'SWE'){
						echo "<script>$('#p_Sweden').prop('checked', true);</script>";
					}if($country == 'CHE'){
						echo "<script>$('#p_Switzerland').prop('checked', true);</script>";
					}if($country == 'TJK'){
						echo "<script>$('#p_Tajikistan').prop('checked', true);</script>";
					}if($country == 'TKM'){
						echo "<script>$('#p_Turkmenistan').prop('checked', true);</script>";
					}if($country == 'UKR'){
						echo "<script>$('#p_Ukraine').prop('checked', true);</script>";
					}if($country == 'UZB'){
						echo "<script>$('#p_Uzbekistan').prop('checked', true);</script>";
					}
				}
				$partner_leader_query = "SELECT leading_nation FROM p_resp WHERE id = ?";
				$stmt = $conn->prepare($partner_leader_query);
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$partner_leader_result = $stmt->get_result();
				echo "<script>$('input.partner_leader_edit_div_sub').prop('checked', false);</script>";
				while($row = mysqli_fetch_assoc($partner_leader_result)){
					$country = $row['leading_nation'];
					if($country == 'ARM'){
						echo "<script>$('#p_leader_Armenia').prop('checked', true);</script>";
					}if($country == 'AUT'){
						echo "<script>$('#p_leader_Austria').prop('checked', true);</script>";
					}if($country == 'AZE'){
						echo "<script>$('#p_leader_Azerbaijan').prop('checked', true);</script>";
					}if($country == 'BLR'){
						echo "<script>$('#p_leader_Belarus').prop('checked', true);</script>";
					}if($country == 'BIH'){
						echo "<script>$('#p_leader_Bosnia_Herzegovina').prop('checked', true);</script>";
					}if($country == 'FIN'){
						echo "<script>$('#p_leader_Finland').prop('checked', true);</script>";
					}if($country == 'FYR'){
						echo "<script>$('#p_leader_FYROM').prop('checked', true);</script>";
					}if($country == 'GEO'){
						echo "<script>$('#p_leader_Georgia').prop('checked', true);</script>";
					}if($country == 'IRL'){
						echo "<script>$('#p_leader_Ireland').prop('checked', true);</script>";
					}if($country == 'KAZ'){
						echo "<script>$('#p_leader_Kazakhstan').prop('checked', true);</script>";
					}if($country == 'KGZ'){
						echo "<script>$('#p_leader_Kyrgyztan').prop('checked', true);</script>";
					}if($country == 'MLT'){
						echo "<script>$('#p_leader_Malta').prop('checked', true);</script>";
					}if($country == 'MDA'){
						echo "<script>$('#p_leader_Moldova').prop('checked', true);</script>";
					}if($country == 'MNE'){
						echo "<script>$('#p_leader_Montenegro').prop('checked', true);</script>";
					}if($country == 'RUS'){
						echo "<script>$('#p_leader_Russian_Federation').prop('checked', true);</script>";
					}if($country == 'SRB'){
						echo "<script>$('#p_leader_Serbia').prop('checked', true);</script>";
					}if($country == 'SWE'){
						echo "<script>$('#p_leader_Sweden').prop('checked', true);</script>";
					}if($country == 'CHE'){
						echo "<script>$('#p_leader_Switzerland').prop('checked', true);</script>";
					}if($country == 'TJK'){
						echo "<script>$('#p_leader_Tajikistan').prop('checked', true);</script>";
					}if($country == 'TKM'){
						echo "<script>$('#p_leader_Turkmenistan').prop('checked', true);</script>";
					}if($country == 'UKR'){
						echo "<script>$('#p_leader_Ukraine').prop('checked', true);</script>";
					}if($country == 'UZB'){
						echo "<script>$('#p_leader_Uzbekistan').prop('checked', true);</script>";
					}
				}
				$leader_query = "SELECT c.code FROM country_cell_resp AS c, general_info AS g WHERE c.country_leader = g.last_name AND g.id = ?";
				$stmt = $conn->prepare($leader_query);
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$leader_result = $stmt->get_result();
				echo "<script>$('input.leader_edit_div_sub').prop('checked', false);</script>";
				while($row = mysqli_fetch_assoc($leader_result)){
					$country = $row['code'];
					if($country == 'ALB'){
						echo "<script>$('#l_Albania').prop('checked', true);</script>";
					}if($country == 'BEL'){
						echo "<script>$('#l_Belgium').prop('checked', true);</script>";
					}if($country == 'BGR'){
						echo "<script>$('#l_Bulgaria').prop('checked', true);</script>";
					}if($country == 'CAN'){
						echo "<script>$('#l_Canada').prop('checked', true);</script>";
					}if($country == 'HRV'){
						echo "<script>$('#l_Croatia').prop('checked', true);</script>";
					}if($country == 'CZE'){
						echo "<script>$('#l_Czech_Republic').prop('checked', true);</script>";
					}if($country == 'DNK'){
						echo "<script>$('#l_Denmark').prop('checked', true);</script>";
					}if($country == 'EST'){
						echo "<script>$('#l_Estonia').prop('checked', true);</script>";
					}if($country == 'FRA'){
						echo "<script>$('#l_France').prop('checked', true);</script>";
					}if($country == 'DEU'){
						echo "<script>$('#l_Germany').prop('checked', true);</script>";
					}if($country == 'GRC'){
						echo "<script>$('#l_Greece').prop('checked', true);</script>";
					}if($country == 'HUN'){
						echo "<script>$('#l_Hungary').prop('checked', true);</script>";
					}if($country == 'ISL'){
						echo "<script>$('#l_Iceland').prop('checked', true);</script>";
					}if($country == 'ITA'){
						echo "<script>$('#l_Italy').prop('checked', true);</script>";
					}if($country == 'LVA'){
						echo "<script>$('#l_Latvia').prop('checked', true);</script>";
					}if($country == 'LTU'){
						echo "<script>$('#l_Lithuania').prop('checked', true);</script>";
					}if($country == 'LUX'){
						echo "<script>$('#l_Luxembourg').prop('checked', true);</script>";
					}if($country == 'NLD'){
						echo "<script>$('#l_Netherlands').prop('checked', true);</script>";
					}if($country == 'NOR'){
						echo "<script>$('#l_Norway').prop('checked', true);</script>";
					}if($country == 'POL'){
						echo "<script>$('#l_Poland').prop('checked', true);</script>";
					}if($country == 'PRT'){
						echo "<script>$('#l_Portugal').prop('checked', true);</script>";
					}if($country == 'ROU'){
						echo "<script>$('#l_Romania').prop('checked', true);</script>";
					}if($country == 'SVK'){
						echo "<script>$('#l_Slovakia').prop('checked', true);</script>";
					}if($country == 'SVN'){
						echo "<script>$('#l_Slovenia').prop('checked', true);</script>";
					}if($country == 'ESP'){
						echo "<script>$('#l_Spain').prop('checked', true);</script>";
					}if($country == 'TUR'){
						echo "<script>$('#l_Turkey').prop('checked', true);</script>";
					}if($country == 'GBR'){
						echo "<script>$('#l_United_Kingdom').prop('checked', true);</script>";
					}if($country == 'USA'){
						echo "<script>$('#l_United_States').prop('checked', true);</script>";
					}
				}
				$partner_result->free();
				$leader_result->free();
				$country_result->free();
				$partner_leader_result->free();
			}
			
			$pe_parts = array();
			$tok = strtok($pe, " ");
			while($tok != false){
				array_push($pe_parts, $tok);
				$tok = strtok(" ");
			}
			
			$bot_parts = array();
			$tok = strtok($BoT, '/');
			while($tok != false){
				array_push($bot_parts, $tok);
				$tok = strtok('/');
			}
			
			$eot_parts = array();
			$tok = strtok($EoT, '/');
			while($tok != false){
				array_push($eot_parts, $tok);
				$tok = strtok('/');
			}
			
			$dob_parts = array();
			$tok = strtok($DoB, '/');
			while($tok != false){
				array_push($dob_parts, $tok);
				$tok = strtok('/');
			}
			
			$passport_expiry_parts = array();
			$tok = strtok($passport_expiry, '/');
			while($tok != false){
				array_push($passport_expiry_parts, $tok);
				$tok = strtok('/');
			}
			
			$credit_card_expiry_parts = array();
			$tok = strtok($credit_card_expiry, '/');
			while($tok != false){
				array_push($credit_card_expiry_parts, $tok);
				$tok = strtok('/');
			}
			
			echo "<script>
					$('input#select_db_id').val('$id');
					$('input#ln_edit').val('$last_name');
					$('input#fn_edit').val('$first_name');
					$('select#nationality_edit').val('$nat');
					$('select#rank_edit').val('$rank');//select, not text
					$('select#EC_edit').val('$EC');//select, not text
					$('input#NATO_PASS_edit').val('$NATO_PASS');
					$('input#SHAPE_ID_edit').val('$SHAPE_ID');
					$('input#passport_edit').val('$passport');
					$('select#CAG_edit').val('$CAG');
					$('select#pe_flag_edit').val('$pe_flag');
					$('input#email_edit').val('$email');
					$('input#phone_edit').val('$phone');
					$('input#username_edit').val('$user');
					$('select#security_clearance_edit').val('$security_clearance');
					$('input#credit_card_company_edit').val('$credit_card_company');
					$('input#credit_card_number_edit').val('$credit_card_number');
					$('input#secondary_email_edit').val('$secondary_email');
					$('input#mobile_edit').val('$mobile');
					$('input#address_edit').val('$address');
					valid_clear = true;
				</script>";
			if(!empty($pe_parts)){
				echo "<script>
				$('select#pe_input1').val('$pe_parts[0]');
				$('select#pe_input2').val('$pe_parts[1]');
				$('input#pe_num_edit').val('$pe_parts[2]');
				</script>";
			}
			if(!empty($bot_parts)){
				echo "<script>
				$('select#edit_BoT').val('$bot_parts[0]');
				$('select#edit_BoT2').val('$bot_parts[1]');
				$('select#edit_BoT3').val('$bot_parts[2]');
				</script>";
			}
			if(!empty($eot_parts)){
				echo "<script>
				$('select#edit_EoT').val('$eot_parts[0]');
				$('select#edit_EoT2').val('$eot_parts[1]');
				$('select#edit_EoT3').val('$eot_parts[2]');
				</script>";
			}
			if(!empty($dob_parts)){
				echo "<script>
				$('select#DoB_edit').val('$dob_parts[0]');
				$('select#DoB_edit2').val('$dob_parts[1]');
				$('select#DoB_edit3').val('$dob_parts[2]');
				</script>";
			}
			if(!empty($passport_expiry_parts)){
				echo "<script>
				$('select#passport_expiry_edit').val('$passport_expiry_parts[0]');
				$('select#passport_expiry_edit2').val('$passport_expiry_parts[1]');
				$('select#passport_expiry_edit3').val('$passport_expiry_parts[2]');
				</script>";
			}
			if(!empty($credit_card_expiry_parts)){
				echo "<script>
				$('select#credit_card_expiry_edit').val('$credit_card_expiry_parts[0]');
				$('select#credit_card_expiry_edit2').val('$credit_card_expiry_parts[1]');
				</script>";
			}
			$other_result->free();
			//reset the global variable
			$row_count = 0;
		}
	}
	/*
		This function will fill in the person's tasks with the given id of
		the searched person
		Param1: The searched person's query
	*/
	function fillPeopleTasks($query){
		global $conn, $row_count, $temp_var;
		if($row_count == 1){
			if($stmt = $conn->prepare($query)){
				$stmt->bind_param('s', $temp_var);
				$stmt->execute();
				$other_result = $stmt->get_result();
				while($row = $other_result->fetch_assoc()){
					$id = $row['id'];
				}
				getTasks($id);
			}
		}
	}
	
	/*
		This function will create a new account by the admin
	*/
	function createAccByAdmin(){
		if(isset($_POST['new_acc_by_admin_submit'])){
			global $conn, $validations, $confirm_account, $create_acc_error;
			$fn = $_POST['new_fn'];
			$ln = $_POST['new_ln'];
			$nat = $_POST['new_nat'];
			$rank = $_POST['new_rank'];
			$cell = $_POST['new_cell'];
			$CAG = $_POST['new_CAG'];
			$pe = $_POST['new_pe'].' '.$_POST['new_pe2'].' '.$_POST['new_pe3'];
			$BoT = $_POST['new_BoT'].'/'.$_POST['new_BoT2'].'/'.$_POST['new_BoT3'];
			$EoT = $_POST['new_EoT'].'/'.$_POST['new_EoT2'].'/'.$_POST['new_EoT3'];
			$email = $_POST['new_email'];
			$phone = $_POST['new_phone'];
			$username = $_POST['new_username'];
			$password = $_POST['new_password'];
			if(empty($fn) or !validateName($fn)){
				$fn = '';
			}
			if(empty($ln) or !validateName($ln)){
				$validations[0] = false;
			}
			if(empty($pe) or !validatePeNumber($pe))
				$pe = '';
			if(empty($BoT) or !validateTerm($BoT))
				$BoT = '';
			if(empty($EoT) or !validateTerm($EoT))
				$EoT = '';
				
			if(!validateEmail($email) or empty($email))
				$validations[1] = false;
			else if(checkDupEmail($email))
				$validations[1] = false;
				
			if(empty($username) or !validateUsername($username))
				$validations[2] = false;
			else if(checkDupUsername($username))
				$validations[2] = false;
				
			if(empty($password) or !validatePassword($password))
				$validations[3] = false;
			else
				$password = md5($password);
			if($_POST['new_admin'] == 'yes')
				$admin = 1;
			else
				$admin = 0;
			foreach($validations as $val){
				if(!$val){
					$confirm_account = false;
					$create_acc_error = true;
					break;
				}
				$confirm_account = true;
			}
			if($confirm_account){
				$stmt= $conn->prepare("INSERT INTO general_info (last_name, first_name, nationality, rank, environment, CAG, pe_number, BoT, EoT, email, phone, username, password, admin) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
				$stmt->bind_param('sssssssssssssi', $ln, $fn, $nat, $rank, $cell, $CAG, $pe, $BoT, $EoT, $email, $phone, $username, $password, $admin);
				$stmt->execute();
			}
		}
	}
	##################################################################
	//PHP TO CSV FUNCTIONS
	
	/*
		This function will turn the according table into a csv file
		Param1: The name of the table within the database
	*/
	function tableToCsv($table){
		global $conn;
		$query = "SELECT * FROM $table";
		$result = $conn->query($query);
		if($result->num_rows == NULL){
			echo "<h3>Could not retrieve any data from $table due to the table being empty</h3>";
		}else{
			$file_name = 'uploads/'."$table".'.csv';
			$file = fopen($file_name, "w");
			//csv formatting
			$text = '';
			$table_header = "SELECT `COLUMN_NAME`
							FROM `INFORMATION_SCHEMA`.`COLUMNS`
							WHERE `TABLE_SCHEMA` = '1st home project'
							AND `TABLE_NAME` = '$table'";
			$header_result = $conn->query($table_header);
			while($row = $header_result->fetch_assoc()){
				foreach($row as $val)
					$text .= "$val,";
			}
			$header_result->free();
			$text .= "\n";
			//specific field exceptions
			if($table == 'general_info'){
				while($row = $result->fetch_assoc()){
					foreach($row as $key => $val){
						if($key == 'tasks'){
							$text .= str_replace("\n", '', $row['tasks']).',';
							/*$string_array = explode("<br>", $row['tasks']);
							$task_string = '';
							foreach($string_array as $x){
								$task_string .= "$x<br>";
							}
							$text .= "$task_string";*/
						}else
							$text .= "$val,";
						//advance the pointer because key() only proceeds by pointer
						//next($row);
					}
					$text .= "\n";
				}
			}else{
				while($row = $result->fetch_assoc()){
					foreach($row as $val)
						$text .= "$val,";
					$text .= "\n";
				}
			}
			$result->free();
			fputs($file, $text);
			fclose($file);
		}
	}
	
	
	/*
		This function will dump all database tables into separate csv files
	*/
	function dumpDatabase(){
		global $conn;
		if($_SESSION['admin']){
			if(isset($_POST['confirm_database_dump'])){
				tableToCsv('cell_leaders');
				tableToCsv('country_cell_resp');
				tableToCsv('c_resp');
				tableToCsv('general_info');
				tableToCsv('p_resp');
				echo "<h2>The csv files have been created</h2>";
			}
		}
	}
	
	/*
		This function will download the CSV file according to the query
		Param1: The query
	*/
	function getCSV($query){
		if($_SESSION['admin']){
			global $conn;
			$result = $conn->query($query);
			if($result->num_rows == NULL){
				echo "Could not retrieve any data due to empty result.";
			}else{
				$file_name = 'uploads/'.strtotime("now").'.csv';
				$file = fopen($file_name, "w");
				$text = ",,CELL LEADERS,\n\n";
				//csv formatting
				$get_cell_leaders_col_names = "SELECT cell_type FROM cell_leaders";
				$header_result = $conn->query($get_cell_leaders_col_names);
				while($row = $header_result->fetch_assoc()){
					foreach($row as $val){
						$text .= "$val,";
					}
				}
				$text .= "\n";
				$header_result->free();
				$cell_leaders_query = "SELECT last_name FROM cell_leaders";
				$leader_result = $conn->query($cell_leaders_query);
				while($row = $leader_result->fetch_assoc()){
					foreach($row as $val)
						$text .= "$val,";
				}
				$text .= "\n\n\n";
				$leader_result->free();
				$get_country_cell_resp_col_names = "SELECT `COLUMN_NAME`
							FROM `INFORMATION_SCHEMA`.`COLUMNS`
							WHERE `TABLE_SCHEMA` = '1st home project'
							AND `TABLE_NAME` = 'country_cell_resp'";
				$col_count = 0;
				$header_result = $conn->query($get_country_cell_resp_col_names);
				while($row = $header_result->fetch_assoc()){
					foreach($row as $val){
						$text .= "$val,";
						$col_count++;
					}
				}
				$text .= "\n";
				for($i = 0; $i < $col_count; $i++){
					$text .= ",";
				}
				$text .= "\n";
				$header_result->free();
				while($row = $result->fetch_assoc()){
					foreach($row as $val){
						$text .= "$val,";
					}
					$text .= "\n";
				}
				fputs($file, $text);
				fclose($file);
				echo "<h2 style='text-align:center;color:blue'>A csv file has been created</h2>";
			}
		}
	}
	
	##################################################################
	//ACCOUNT CREATING FUNCTION
	
	##################################################################
	#Call functions before HTML here
	
	logout();
	profile();
	
	##################################################################
	//LOGIN SECURITY
	if(!$_SESSION['username'])
		header("Location:http://localhost:8000/First%20project/Homepage.php");
	
?>

<head>
	<!--<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta content="utf-8" http-equiv="encoding"/> -->
	<title>Project #1</title>
	<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
</head>

<form action='Mainpage_v2.php' method='POST'>
	<body>
		<?php checkForAdmin();?>
		<div id='header_area'>
			<div id='admin_header'>
				<?php if(@$_SESSION['admin']): ?><h3>Admin</h3><?php endif; ?>
				<?php if(@$_SESSION['admin']){echo "<script>var isAdmin = true;</script>";}?>
			</div>
			<div id='welcome'>
				<h1 style='text-align:center<?php if(!$_SESSION['admin']): ?>;margin-left:22%<?php endif; ?>'>Welcome <?php echo getSessionFirstName();?></h1>
				<?php if(!empty($_SESSION['first_login'])){echo "<h2>Check your email</h2>";unset($_SESSION['first_login']);} ?>
			</div>
			<div id='options_area'>
				<input type='submit' class='mainpage_options' value='Log out' name='logout' id='logout'>
				<input type='submit' style='margin-top:5%' class='mainpage_options' value='Profile' name='profile' id='profile'>
			</div>
		</div>
		
		<div id='main_content'>
			<?php
				personSearch();
				lastNameSearch();
				firstNameSearch();
				rankSearch();
				nationalitySearch();
				environmentSearch();
				cagSearch();
				fetchIndividualResponsibilities();
				engagementResponsibilities();
				commandSupportResponsibilities();
				sustainResponsibilities();
				allCagResponsibilities();
				allResponsibilities();
				landResponsibilities();
				airResponsibilities();
				maritimeResponsibilities();
				jeResponsibilities();
				coordinatorResponsibilities();
				ctrBranchResponsibilities();
				branchHeadResponsibilities();
				jointCoordinatorResponsibilities();
				landProfiles();
				airProfiles();
				maritimeProfiles();
				jointEnablingProfiles();
				ctrBranchProfiles();
				engagementProfiles();
				commandSupportProfiles();
				enablingProfiles();
				allECProfiles();
				allCAGProfiles();
				getCECR();
				createAccByAdmin();
				dumpDatabase();
			?>
			<?php if($confirm_account): ?>
				<h2>Account successfully created!</h2>
			<?php endif; ?>
			<div class='fset' id='people_search_div'>
				<h2 style='color:yellow'>People search</h2>
				<ul style='color:white'>
					<li><label for='first_name_search'>First name: </label><input type='text' value='' name='first_name_search' id='first_name_search'>
					<input type='submit' class='people_search_items' value='Search by first name' name='first_name_submit' id='first_name_submit'></li>
					<li><label for='last_name_search'>Last name: </label><select name='last_name_search' id='last_name_search'>
					<option value=''>Choose one</option>
					<?php getOptions('last_name'); ?>
					</select><input type='submit' class='people_search_items' style='margin-left:.4%' value='Search by last name' name='last_name_submit' id='last_name_submit'></li>
					<li><label for='rank_search'>Rank: </label>
					<select id='rank_search' name='rank_search'>
						<option value='' selected='selected'>Choose One</option>
						<option value='COL'>COL</option>
						<option value='CAPT(N)'>CAPT(N)</option>
						<option value='LTC'>LTC</option>
						<option value='CDR'>CDR</option>
						<option value='LCDR'>LCDR</option>
						<option value='MAJ'>MAJ</option>
						<option value='CAPT'>CAPT</option>
						<option value='CTR'>CTR</option>
						<option value='CIV'>CIV</option>
					</select>
					<input type='submit' class='people_search_items' value='Search by rank' name='rank_submit' id='rank_submit'></li>
					<li><label for='CAG_search'>CAG: </label><select name='CAG_search' id='CAG_search'>
						<option value=''>Choose one</option>
						<option value='ENGAGEMENT'>Engagement</option>
						<option value='COMMAND SUPPORT'>Command Support</option>
						<option value='ENABLING'>Enabling</option>
						<option value='COORDINATOR'>Coordinator</option>
						<option value='BRANCH HEAD'>Branch head</option>
						<option value='JOINT COORDINATOR'>Joint Coordinator</option>
					</select><input type='submit' class='people_search_items' value='Search by CAG' name='CAG_submit' id='CAG_submit'/></li>
					<li><label for='nationality_search'>Nationality: </label>
					<select id='nationality_search' name='nationality_search'>
						<option value='' selected='selected'>Choose One</option>
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
					</select><input style='margin-left:4px' type='submit' class='people_search_items' value='Search by nationality' name='nationality_submit' id='nationality_submit'></li>
					<li><label for='EC_search'>Cell: </label>
					<select id='EC_search' name='EC_search'>
						<option value='' selected='selected'>Choose One</option>
						<option value='LAND'>Land</option>
						<option value='AIR'>Air</option>
						<option value='MARITIME'>Maritime</option>
						<option value='JOINT/ENABLING'>Joint Enabling</option>
						<option value='CTR BRANCH'>CTR Branch</option>
					</select><input style='margin-left:4px' type='submit' class='people_search_items' value='Search by cell' name='EC_submit' id='EC_submit'></li>
					<!--<li><input type='submit' style='margin-left:0' class='people_search_items' value='Submit complete search' id='submit_search' name='submit_search'></li>-->
					<?php 	if($empty_field){echo "<li><label style='color:red'>Field was left empty</label></li>";} if($no_results){echo "<li><label style='color:red'>No results found.</label></li>";} ?>
				</ul>
			</div>
			<?php if($_SESSION['admin']): ?>
			<div class='fset' id='people_edit_div'>
					<h2>People edit</h2>
					<div id='people_edit'>
						<?php 
								editPerson();
								if($display_responsibilities)
									if(@$_POST['confirm_NATO_countries'] == 'y')
										crespDisplay(@$_POST['select_db_id'], @$_POST['country_group']);
								if($display_partner_nations)
									if(@$_POST['confirm_partner_nations'] == 'y')
										partnerCountryDisplay(@$_POST['select_db_id'], @$_POST['partner_group']);
								if($display_leading_nations){
									if(isset($_POST['confirm_leader_responsibilities']))
										leaderUpdate(@$_POST['select_db_id']);
								}
								if($display_partner_leader_nations){
									if(isset($_POST['confirm_partner_leader_nations']) == 'y'){
										partnerLeaderUpdate(@$_POST['select_db_id']);
									}
								}
								
							
						?>
						<ul>
							<li><label for='select_db_id' style='color:black'>Enter the id number of the person whom you wish to replace/make changes to: </label><input type='text' name='select_db_id' id='select_db_id'/></li>
							<li><label style='font-style:italic;color:black'>Check the boxes to remove the field</label></li>
							<li><label for='ln_edit'>Change last name to: </label><input type='text' name='ln_edit' id='ln_edit'/></li>
							<li><label for='fn_edit'>Change first name to: </label><input type='text' name='fn_edit' id='fn_edit'/><input type='checkbox' class='remove_person_field' name='remove_fn' id='remove_fn' value='x'/></li>
							<li><label for='nationality_edit'>Change nationality to: </label>
							<select name='nationality_edit' id='nationality_edit'>
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
							</select><input type='checkbox' class='remove_person_field' name='remove_nat' id='remove_nat' value='x'/></li>
							<li><label for='rank_edit'>Change rank to: </label>
							<select name='rank_edit' id='rank_edit'>
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
							</select><input type='checkbox' class='remove_person_field' name='remove_rank' id='remove_rank' value='x'/></li>
							<li><label for='EC_edit'>Change Cell to: </label>
							<select name='EC_edit' id='EC_edit'>
								<option value='' selected='selected'>Choose One</option>
								<option value='LAND'>Land</option>
								<option value='AIR'>Air</option>
								<option value='MARITIME'>Maritime</option>
								<option value='JOINT/ENABLING'>Joint Enabling</option>
								<option value='CTR BRANCH'>CTR Branch</option>
							</select><!-- <input type='checkbox' class='remove_person_field' name='remove_EC' id='remove_EC' value='x'/></li> -->
							<li><label for='pe_num_edit'>Change PE number to: </label><select name='pe_input1' id='pe_input1'>
								<option value='' selected='selected'></option>
								<option value='TSC'>TSC</option>
							</select>
							<select name='pe_input2' id='pe_input2'>
								<option value='' selected='selected'></option>
								<option value='FPF'>FPF</option>
								<option value='FPG'>FPG</option>
								<option value='FPR'>FPR</option>
								<option value='PFP'>PFP</option>
							</select><input style='margin-left:1%' type='text' name='pe_num_edit' id='pe_num_edit'/><select name='pe_flag_edit' id='pe_flag_edit'>
								<option value=''>Flag</option>
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
							</select><input type='checkbox' class='remove_person_field' name='remove_pe_num' id='remove_pe_num' value='x'/><?php if($edit_error[5]): ?><label class='edit_error' style='margin-left:55%'>Invalid PE Number</label><?php endif; ?></li>
							<li><label for='edit_BoT'>Beginning of Term: </label><select name='edit_BoT' id='edit_BoT'>
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
								</select><select name='edit_BoT2' id='edit_BoT2'>
									<option value='' selected='selected'>Day</option>
									<?php for($i = 1; $i <= 31; $i++){
											$op_value = $i;
											if($i <= 9)
												$op_value = '0'.$i;
											echo "<option value='$op_value'>$op_value</option>";
										}
									?>
								</select><select name='edit_BoT3' id='edit_BoT3'>
									<option value='' selected='selected'>Year</option>
									<?php for($i = 2000; $i <= 2050; $i++)
											echo "<option value='$i'>$i</option>";
									?>
								</select><input type='checkbox' class='remove_person_field' name='remove_BoT' id='remove_BoT' value='x'/><?php if($edit_error[13]): ?><label class='edit_error'>Invalid term</label><?php endif; ?></li>
							<li><label for='edit_EoT'>End of Term: </label><select name='edit_EoT' id='edit_EoT'>
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
								</select><select name='edit_EoT2' id='edit_EoT2'>
									<option value='' selected='selected'>Day</option>
									<?php for($i = 1; $i <= 31; $i++){
											$op_value = $i;
											if($i <= 9)
												$op_value = '0'.$i;
											echo "<option value='$op_value'>$op_value</option>";
										}
									?>
								</select><select name='edit_EoT3' id='edit_EoT3'>
									<option value='' selected='selected'>Year</option>
									<?php for($i = 2000; $i <= 2050; $i++)
											echo "<option value='$i'>$i</option>";
									?>
								</select><input type='checkbox' class='remove_person_field' name='remove_EoT' id='remove_EoT' value='x'/><?php if($edit_error[14]): ?><label class='edit_error'>Invalid term</label><?php endif; ?></li>
							<li><label for='CAG_edit'>Change CAG to: </label><select name='CAG_edit' id='CAG_edit'>
								<option value=''>Choose one</option>
								<option value='ENGAGEMENT'>Engagement</option>
								<option value='COMMAND SUPPORT'>Command Support</option>
								<option value='ENABLING'>Enabling</option>
								<option value='COORDINATOR'>Coordinator</option>
								<option value='BRANCH HEAD'>Branch head</option>
								<option value='JOINT COORDINATOR'>Joint Coordinator</option>
							</select><input type='checkbox' class='remove_person_field' name='remove_CAG' id='remove_CAG' value='x'/></li>
							<li><label for='NATO_PASS_edit'>Change the NATO PASS to: </label><input type='text' id='NATO_PASS_edit' name='NATO_PASS_edit'/><input type='checkbox' class='remove_person_field' name='remove_NATO_PASS' id='remove_NATO_PASS' value='x'/></li>
							<li><label for='SHAPE_ID_edit'>Change the SHAPE ID to: </label><input type='text' id='SHAPE_ID_edit' name='SHAPE_ID_edit'/><input type='checkbox' class='remove_person_field' name='remove_SHAPE_ID' id='remove_SHAPE_ID' value='x'/></li>
							<li><label for='DoB_edit'>Change the date of birth to: </label><select name='DoB_edit' id='DoB_edit'>
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
								</select><select name='DoB_edit2' id='DoB_edit2'>
									<option value='' selected='selected'>Day</option>
									<?php for($i = 1; $i <= 31; $i++){
											$op_value = $i;
											if($i <= 9)
												$op_value = '0'.$i;
											echo "<option value='$op_value'>$op_value</option>";
										}
									?>
								</select><select name='DoB_edit3' id='DoB_edit3'>
									<option value='' selected='selected'>Year</option>
									<?php for($i = 1945; $i <= 2013; $i++)
											echo "<option value='$i'>$i</option>";
									?>
								</select><input type='checkbox' class='remove_person_field' name='remove_DoB' id='remove_DoB' value='x'/><?php if($edit_error[15]): ?><label class='edit_error'>Invalid date</label><?php endif; ?></li>
							<li><label for='passport_edit'>Change the passport ID to: </label><input type='text' id='passport_edit' name='passport_edit'/><input type='checkbox' class='remove_person_field' name='remove_passport' id='remove_passport' value='x'/></li>
							<li><label for='passport_expiry_edit'>Change the passport expiry date to: </label><select name='passport_expiry_edit' id='passport_expiry_edit'>
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
								</select><select name='passport_expiry_edit2' id='passport_expiry_edit2'>
									<option value='' selected='selected'>Day</option>
									<?php for($i = 1; $i <= 31; $i++){
											$op_value = $i;
											if($i <= 9)
												$op_value = '0'.$i;
											echo "<option value='$op_value'>$op_value</option>";
										}
									?>
								</select><select name='passport_expiry_edit3' id='passport_expiry_edit3'>
									<option value='' selected='selected'>Year</option>
									<?php for($i = 2000; $i <= 2050; $i++)
											echo "<option value='$i'>$i</option>";
									?>
								</select><input type='checkbox' class='remove_person_field' style='margin-left:1px' name='remove_passport_expiry' id='remove_passport_expiry' value='x'/><?php if($edit_error[16]): ?><label class='edit_error'>Invalid date</label><?php endif; ?></li>
							<li><label for='security_clearance_edit'>Change the security clearance to: </label><select id='security_clearance_edit' name='security_clearance_edit'>
									<option value='' selected='selected'></option>
									<option value='ACTS'>ACTS</option>
									<option value='NATS'>NATS</option>
									<option value='CTS'>CTS</option>
									<option value='NS'>NS</option>
									<option value='NC'>NC</option>
									<option value='NR'>NR</option>
									<option value='NR'>NTS</option>
								</select><input type='checkbox' class='remove_person_field' name='remove_security_clearance' id='remove_security_clearance' value='x'/></li>
							<li><label for='credit_card_company_edit'>Change the credit card company to: </label><input type='text' id='credit_card_company_edit' name='credit_card_company_edit'><input type='checkbox' class='remove_person_field' name='remove_credit_card_company' id='remove_credit_card_company' value='x'/></li>
							<li><label for='credit_card_number_edit'>Change the credit card number to: </label><input type='text' id='credit_card_number_edit' name='credit_card_number_edit'><input type='checkbox' class='remove_person_field' name='remove_credit_card_number' id='remove_credit_card_number' value='x'/></li>
							<li><label for='credit_card_expiry_edit'>Change the credit card expiry date to: </label><select name='credit_card_expiry_edit' id='credit_card_expiry_edit'>
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
							</select><select id='credit_card_expiry_edit2' name='credit_card_expiry_edit2'>
								<option value='' selected='selected'>Year</option>
									<?php for($i = 2013; $i <= 2050; $i++)
											echo "<option value='$i'>$i</option>";
									?>
							</select><input type='checkbox' class='remove_person_field' name='remove_credit_card_expiry' id='remove_credit_card_expiry' value='x'/></li>
							<li><label for='email_edit'>Change email to: </label><input type='text' name='email_edit' id='email_edit'/></li>
							<li><label for='secondary_email_edit'>Change personal email to: </label><input type='text' id='secondary_email_edit' name='secondary_email_edit'/><input type='checkbox' class='remove_person_field' name='remove_secondary_email' id='remove_secondary_email' value='x'/></li>
							<li><label for='phone_edit'>Change phone number to: </label><input type='text' name='phone_edit' id='phone_edit'/><input type='checkbox' class='remove_person_field' name='remove_phone' id='remove_phone' value='x'/></li>
							<li><label for='mobile_edit'>Change mobile to: </label><input type='text' name='mobile_edit' id='mobile_edit'/><input type='checkbox' class='remove_person_field' name='remove_mobile' id='remove_mobile' value='x'/>
							<li><label for='address_edit'>Change address to: </label><input type='text' name='address_edit' id='address_edit'><input type='checkbox' class='remove_person_field' name='remove_address' id='remove_address' value='x'/>
							<li><label for='username_edit'>Change username to: </label><input type='text' autocomplete='off' value ='' name='username_edit' id='username_edit'/></li>
							<li><label for='pass_edit'>Change password to: </label><input type='password' autocomplete='off' value ='' name='pass_edit' id='pass_edit'/></li>
							<li><label for='make_EC_leader'>Make this person the Cell leader of: </label><select name='make_EC_leader' id='make_EC_leader'>
								<option value='' selected='selected'>Choose One</option>
								<option value='LAND'>Land</option>
								<option value='AIR'>Air</option>
								<option value='MARITIME'>Maritime</option>
								<option value='JOINT/ENABLING'>Joint Enabling</option>
								<option value='CTR BRANCH'>CTR Branch</option>
							</select></li>
							<li><label for='delete_profile_by_admin' style='color:red'>Check the box to delete this person's profile </label><input type='checkbox' id='delete_profile_by_admin' name='delete_profile_by_admin' value='x'>
							<li><label for='make_admin' style='color:black'>Check the box to promote to admin</label><input type='checkbox' class='make_admin' name='make_admin' id='make_admin' value='y'></li>
							<li><label for='demote_admin' style='color:black'>Check the box to demote from admin</label><input type='checkbox' class='make_admin' name='make_admin' id='demote_admin' value='n'></li>
						</ul>
					</div>
					<div id='country_edit'>
						<label style='color:#007A29;font-weight:bold;font-size:15.5px'>Select the NATO country responsibilities you wish to change</label>
						<div id='c_edit_big_div'>
							<div class='c_edit_div'>
								<div class='c_edit_div_sub'>
									<ul class='c_edit_row'>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Albania' value='ALB'/><label for='Albania'>Albania</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Belgium' value='BEL'/><label for='Belgium'>Belgium</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Bulgaria' value='BGR'/><label for='Bulgaria'>Bulgaria</label></li>
									</ul>
								</div>
								<div class='c_edit_div_sub'>
									<ul class='c_edit_row'>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Canada' value='CAN'/><label for='Canada'>Canada</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Croatia' value='HRV'/><label for='Croatia'>Croatia</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Czech_Republic' value='CZE'/><label for='Czech_Republic'>Czech Republic</label></li>
									</ul>
								</div>
							</div>
							<div class='c_edit_div'>
								<div class='c_edit_div_sub'>
									<ul class='c_edit_row'>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Denmark' value='DNK'/><label for='Denmark'>Denmark</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Estonia' value='EST'/><label for='Estonia'>Estonia</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='France' value='FRA'/><label for='France'>France</label></li>
									</ul>
								</div>
								<div class='c_edit_div_sub'>
									<ul class='c_edit_row'>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Germany' value='DEU'/><label for='Germany'>Germany</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Greece' value='GRC'/><label for='Greece'>Greece</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Hungary' value='HUN'/><label for='Hungary'>Hungary</label></li>
									</ul>
								</div>
							</div>
							<div class='c_edit_div'>
								<div class='c_edit_div_sub'>
									<ul class='c_edit_row'>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Iceland' value='ISL'/><label for='Iceland'>Iceland</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Italy' value='ITA'/><label for='Italy'>Italy</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Latvia' value='LVA'/><label for='Latvia'>Latvia</label></li>
									</ul>
								</div>
								<div class='c_edit_div_sub'>
									<ul class='c_edit_row'>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Lithuania' value='LTU'/><label for='Lithuania'>Lithuania</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Luxembourg' value='LUX'/><label for='Luxembourg'>Luxembourg</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Netherlands' value='NLD'/><label for='Netherlands'>Netherlands</label></li>
									</ul>
								</div>
							</div>
							<div class='c_edit_div'>
								<div class='c_edit_div_sub'>
									<ul class='c_edit_row'>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Norway' value='NOR'/><label for='Norway'>Norway</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Poland' value='POL'/><label for='Poland'>Poland</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Portugal' value='PRT'/><label for='Portugal'>Portugal</label></li>
									</ul>
								</div>
								<div class='c_edit_div_sub'>
									<ul class='c_edit_row'>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Romania' value='ROU'/><label for='Romania'>Romania</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Slovakia' value='SVK'/><label for='Slovakia'>Slovakia</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Slovenia' value='SVN'/><label for='Slovenia'>Slovenia</label></li>
									</ul>
								</div>
							</div>
							<div class='c_edit_div'>
								<div class='c_edit_div_sub'>
									<ul class='c_edit_row'>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Spain' value='ESP'/><label for='Spain'>Spain</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='Turkey' value='TUR'/><label for='Turkey'>Turkey</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='United_Kingdom' value='GBR'/><label for='United_Kingdom'>United Kingdom</label></li>
										<li><input type='checkbox' class='countries' name='country_group[]' id='United_States' value='USA'/><label for='United_States'>United States</label></li>
									</ul>
								</div>
							</div>
						</div>
						<div id='NATO_c_resp_confirm'>
							<span class='NATO_c_resp_confirm_info'><label style='margin-top:2%;color:#007A29;font-size:17px' for='confirm_NATO_countries'>Confirm NATO country responsibilities</label><input type='checkbox' style='' value='y' name='confirm_NATO_countries' id='confirm_NATO_countries'></span>
						</div>
					</div>
					<div id='tasks_edit'>
						<h2>Edit Tasks</h2>
						<span id='task_remove_header'><label for='remove_tasks'>Check the box to remove tasks</label><input type='checkbox' value='x' id='remove_tasks' name='remove_tasks'/></span>
						<textarea name='tasks_edit_area' id='tasks_edit_area'><?php fillPeopleTasks($global_query); ?></textarea>
					</div>
					<?php  displayEditErrors($edit_error); ?>
					<div id='people_submit_div'>
						<input type='submit' name='people_edit_submit' id='people_edit_submit' value='Submit changes'/>
					</div>
					<div id='country_leader_responsibility_edit'>
						<h3 style='text-align:center;color:#164F29'>Select the country leadership responsibilities you wish to change</h3>
						<div class='leader_edit_div_sub'>
							<ul class='leader_edit_row'>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Albania' value='ALB'/><label for='l_Albania'>Albania</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Belgium' value='BEL'/><label for='l_Belgium'>Belgium</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Bulgaria' value='BGR'/><label for='l_Bulgaria'>Bulgaria</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Canada' value='CAN'/><label for='l_Canada'>Canada</label></li>
							</ul>
						</div>
						<div class='leader_edit_div_sub'>
							<ul class='leader_edit_row'>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Croatia' value='HRV'/><label for='l_Croatia'>Croatia</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Czech_Republic' value='CZE'/><label for='l_Czech_Republic'>Czech Republic</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Denmark' value='DNK'/><label for='l_Denmark'>Denmark</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Estonia' value='EST'/><label for='l_Estonia'>Estonia</label></li>
							</ul>
						</div>
						<div class='leader_edit_div_sub'>
							<ul class='leader_edit_row'>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_France' value='FRA'/><label for='l_France'>France</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Germany' value='DEU'/><label for='l_Germany'>Germany</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Greece' value='GRC'/><label for='l_Greece'>Greece</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Hungary' value='HUN'/><label for='l_Hungary'>Hungary</label></li>
							</ul>
						</div>
						<div class='leader_edit_div_sub'>
							<ul class='leader_edit_row'>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Iceland' value='ISL'/><label for='l_Iceland'>Iceland</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Italy' value='ITA'/><label for='l_Italy'>Italy</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Latvia' value='LVA'/><label for='l_Latvia'>Latvia</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Lithuania' value='LTU'/><label for='l_Lithuania'>Lithuania</label></li>
							</ul>
						</div>
						<div class='leader_edit_div_sub'>
							<ul class='leader_edit_row'>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Luxembourg' value='LUX'/><label for='l_Luxembourg'>Luxembourg</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Netherlands' value='NLD'/><label for='l_Netherlands'>Netherlands</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Norway' value='NOR'/><label for='l_Norway'>Norway</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Poland' value='POL'/><label for='l_Poland'>Poland</label></li>
							</ul>
						</div>
						<div class='leader_edit_div_sub'>
							<ul class='leader_edit_row'>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Portugal' value='PRT'/><label for='l_Portugal'>Portugal</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Romania' value='ROU'/><label for='l_Romania'>Romania</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Slovakia' value='SVK'/><label for='l_Slovakia'>Slovakia</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Slovenia' value='SVN'/><label for='l_Slovenia'>Slovenia</label></li>
							</ul>
						</div>
						<div class='leader_edit_div_sub'>
							<ul class='leader_edit_row'>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Spain' value='ESP'/><label for='l_Spain'>Spain</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_Turkey' value='TUR'/><label for='l_Turkey'>Turkey</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_United_Kingdom' value='GBR'/><label for='l_United_Kingdom'>United Kingdom</label></li>
								<li><input type='checkbox' class='countries' name='leader_group[]' id='l_United_States' value='USA'/><label for='l_United_States'>United States</label></li>
							</ul>
						</div>
						<div id='confirm_leader_div' class='partner_nation_confirm_class'>
							<label for='confirm_leader_responsibilities' style='color:#164F29'>Confirm country leader responsibilities<label>
							<input type='checkbox' name='confirm_leader_responsibilities' id='confirm_leader_responsibilities' value='y'>
						</div>
					</div>
				<div id='partner_nation_responsibilities_edit' class='partner_nation_class'>
					<h3 style='text-align:center'>Select the partner nation responsibilities you wish to change</h3>
					<div class='partner_n_edit_div_sub'>
						<ul class='partner_n_edit_row'>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Armenia' value='ARM'/><label for='p_Armenia'>Armenia</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Austria' value='AUT'/><label for='p_Austria'>Austria</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Azerbaijan' value='AZE'/><label for='p_Azerbaijan'>Azerbaijan</label></li>
						</ul>
					</div>
					<div class='partner_n_edit_div_sub'>
						<ul class='partner_n_edit_row'>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Belarus' value='BLR'/><label for='p_Belarus'>Belarus</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Bosnia_Herzegovina' value='BIH'/><label for='p_Bosnia_Herzegovina'>Bosnia-Herzegovina</label></li> <!-- Check this one out -->
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Finland' value='FIN'/><label for='p_Finland'>Finland</label></li>
						</ul>
					</div>
					<div class='partner_n_edit_div_sub'>
						<ul>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_FYROM' value='FYR'/><label for='p_FYROM'>FYROM</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Georgia' value='GEO'/><label for='p_Georgia'>Georgia</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Ireland' value='IRL'/><label for='p_Ireland'>Ireland</label></li>
						</ul>
					</div>
					<div class='partner_n_edit_div_sub'>
						<ul class='partner_n_edit_row'>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Kazakhstan' value='KAZ'/><label for='p_Kazakhstan'>Kazakhstan</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Kyrgyzstan' value='KGZ'/><label for='p_Kyrgyzstan'>Kyrgyzstan</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Malta' value='MLT'/><label for='p_Malta'>Malta</label></li>
						</ul>
					</div>
					<div class='partner_n_edit_div_sub'>
						<ul class='partner_n_edit_row'>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Moldova' value='MDA'/><label for='p_Moldova'>Moldova</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Montenegro' value='MNE'/><label for='p_Montenegro'>Montenegro</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Russian_Federation' value='RUS'/><label for='p_Russian_Federation'>Russian Federation</label></li>
						</ul>
					</div>
					<div class='partner_n_edit_div_sub'>
						<ul class='partner_n_edit_row'>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Serbia' value='SRB'/><label for='p_Serbia'>Serbia</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Sweden' value='SWE'/><label for='p_Sweden'>Sweden</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Switzerland' value='CHE'/><label for='p_Switzerland'>Switzerland</label></li>
						</ul>
					</div>
					<div class='partner_n_edit_div_sub'>
						<ul>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Tajikistan' value='TJK'/><label for='p_Tajikistan'>Tajikistan</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Turkmenistan' value='TKM'/><label for='p_Turkmenistan'>Turkmenistan</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Ukraine' value='UKR'/><label for='p_Ukraine'>Ukraine</label></li>
							<li><input type='checkbox' class='countries' name='partner_group[]' id='p_Uzbekistan' value='UZB'/><label for='p_Uzbekistan'>Uzbekistan</label></li>
						</ul>
					</div>
					<div id='confirm_partner_nations_div' class='partner_nation_confirm_class'>
						<label for='confirm_partner_nations'>Confirm partner nation responsibilities<label>
						<input type='checkbox' name='confirm_partner_nations' id='confirm_partner_nations' value='y'>
					</div>
				</div>
				<div id='partner_nation_leader_responsibilities_edit' class='partner_nation_class' style='background-color:black;border:5px dotted yellow'>
					<h3 style='text-align:center'>Select the partner nation leader responsibilities you wish to change</h3>
					<div class='partner_leader_edit_div_sub'>
						<ul class='partner_n_edit_row'>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Armenia' value='ARM'/><label for='p_leader_Armenia'>Armenia</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Austria' value='AUT'/><label for='p_leader_Austria'>Austria</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Azerbaijan' value='AZE'/><label for='p_leader_Azerbaijan'>Azerbaijan</label></li>
						</ul>
					</div>
					<div class='partner_leader_edit_div_sub'>
						<ul class='partner_n_edit_row'>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Belarus' value='BLR'/><label for='p_leader_Belarus'>Belarus</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Bosnia_Herzegovina' value='BIH'/><label for='p_leader_Bosnia_Herzegovina'>Bosnia-Herzegovina</label></li> <!-- Check this one out -->
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Finland' value='FIN'/><label for='p_leader_Finland'>Finland</label></li>
						</ul>
					</div>
					<div class='partner_leader_edit_div_sub'>
						<ul>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_FYROM' value='FYR'/><label for='p_leader_FYROM'>FYROM</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Georgia' value='GEO'/><label for='p_leader_Georgia'>Georgia</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Ireland' value='IRL'/><label for='p_leader_Ireland'>Ireland</label></li>
						</ul>
					</div>
					<div class='partner_leader_edit_div_sub'>
						<ul class='partner_n_edit_row'>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Kazakhstan' value='KAZ'/><label for='p_leader_Kazakhstan'>Kazakhstan</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Kyrgyzstan' value='KGZ'/><label for='p_leader_Kyrgyzstan'>Kyrgyzstan</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Malta' value='MLT'/><label for='p_leader_Malta'>Malta</label></li>
						</ul>
					</div>
					<div class='partner_leader_edit_div_sub'>
						<ul class='partner_n_edit_row'>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Moldova' value='MDA'/><label for='p_leader_Moldova'>Moldova</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Montenegro' value='MNE'/><label for='p_leader_Montenegro'>Montenegro</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Russian_Federation' value='RUS'/><label for='p_leader_Russian_Federation'>Russian Federation</label></li>
						</ul>
					</div>
					<div class='partner_leader_edit_div_sub'>
						<ul class='partner_n_edit_row'>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Serbia' value='SRB'/><label for='p_leader_Serbia'>Serbia</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Sweden' value='SWE'/><label for='p_leader_Sweden'>Sweden</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Switzerland' value='CHE'/><label for='p_leader_Switzerland'>Switzerland</label></li>
						</ul>
					</div>
					<div class='partner_leader_edit_div_sub'>
						<ul>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Tajikistan' value='TJK'/><label for='p_leader_Tajikistan'>Tajikistan</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Turkmenistan' value='TKM'/><label for='p_leader_Turkmenistan'>Turkmenistan</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Ukraine' value='UKR'/><label for='p_leader_Ukraine'>Ukraine</label></li>
							<li><input type='checkbox' class='countries' name='partner_leader_group[]' id='p_leader_Uzbekistan' value='UZB'/><label for='p_leader_Uzbekistan'>Uzbekistan</label></li>
						</ul>
					</div>
					<div id='confirm_partner_nations_leaders_div' class='partner_nation_confirm_class' style='margin-left:30%'>
						<label for='confirm_partner_leader_nations'>Confirm partner nation leadership responsibilities<label>
						<input type='checkbox' name='confirm_partner_leader_nations' id='confirm_partner_leader_nations' value='y'>
					</div>
				</div>
				<?php fillPeopleEdit($global_query); ?>
				<?php if($_SESSION['admin']): ?>
					<div id='create_new_person_big_div'>
						<div id='create_new_person'>
							<h2 style='color:green'>Create a new account</h2>
							<ul>
								<li><label for='new_ln'>Last name: </label><input type='text' name='new_ln' id='new_ln'/></li>
								<li><label for='new_fn'>First name: </label><input type='text' name='new_fn' id='new_fn'/></li>
								<li><label for='new_nat'>Nationality: </label><select name='new_nat' id='new_nat'>
									<option value ='' selected='selected'>Choose one</option>
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
								<li><label for='new_rank'>Rank: </label><select name='new_rank' id='new_rank'>
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
								<li><label for='new_cell'>Cell: </label><select name='new_cell' id='new_cell'>
									<option value='' selected='selected'>Choose One</option>
									<option value='LAND'>Land</option>
									<option value='AIR'>Air</option>
									<option value='MARITIME'>Maritime</option>
									<option value='JOINT/ENABLING'>Joint Enabling</option>
									<option value='CTR BRANCH'>CTR Branch</option>
								</select></li>
								<li><label for='new_CAG'>CAG: </label><select name='new_CAG' id='new_CAG'>
									<option value=''>Choose one</option>
									<option value='ENGAGEMENT'>Engagement</option>
									<option value='COMMAND SUPPORT'>Command Support</option>
									<option value='ENABLING'>Enabling</option>
									<option value='COORDINATOR'>Coordinator</option>
									<option value='BRANCH HEAD'>Branch head</option>
									<option value='JOINT COORDINATOR'>Joint Coordinator</option>
								</select></li>
								<li><label for='new_pe'>PE number: </label><select name='new_pe' id='new_pe'>
									<option value='TSC' selected='selected'>TSC</option>
								</select><select name='new_pe2' id='new_pe2'>
									<option value='' selected='selected'></option>
									<option value='FPF'>FPF</option>
									<option value='FPG'>FPG</option>
									<option value='FPR'>FPR</option>
									<option value='PFP'>PFP</option>
								</select><input type='text' name='new_pe3' id='new_pe3'/></li>
								<li><label for='new_BoT'>Beginning of Term: </label><select type='text' name='new_BoT' id='new_BoT'>
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
								</select><select name='new_BoT2' id='new_BoT2'>
									<option value='' selected='selected'>Day</option>
									<?php for($i = 1; $i <= 31; $i++){
											$op_value = $i;
											if($i <= 9)
												$op_value = '0'.$i;
											echo "<option value='$op_value'>$op_value</option>";
										}
									?>
								</select><select name='new_BoT3' id='new_BoT3'>
									<option value='' selected='selected'>Year</option>
									<?php for($i = 2000; $i <= 2050; $i++)
											echo "<option value='$i'>$i</option>";
									?>
								</select></li>
								<li><label for='new_EoT'>End of Term: </label><select name='new_EoT' id='new_EoT'>
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
								</select><select name='new_EoT2' id='new_EoT2'>
									<option value='' selected='selected'>Day</option>
									<?php for($i = 1; $i <= 31; $i++){
											$op_value = $i;
											if($i <= 9)
												$op_value = '0'.$i;
											echo "<option value='$op_value'>$op_value</option>";
										}
									?>
								</select><select name='new_EoT3' id='new_EoT3'>
									<option value='' selected='selected'>Year</option>
									<?php for($i = 2000; $i <= 2050; $i++)
											echo "<option value='$i'>$i</option>";
									?>
								</select></li>
								<li><label for='new_email'>Email: </label><input type='text' name='new_email' id='new_email'/></li>
								<li><label for='new_phone'>Phone: </label><input type='text' name='new_phone' id='new_phone'/></li>
								<li><label for='new_username'>Username: </label><input type='text' name='new_username' id='new_username'/></li>
								<li><label for='new_password'>Password: </label><input type='password' name='new_password' id='new_password'/></li>
								<li><label for='new_admin'>Admin: </label><select name='new_admin' id='new_admin'>
									<option value='no' selected='selected'>No</option>
									<option value='yes'>Yes</option>
								</select></li>
							</ul>
							<div id='new_acc_by_admin_submit_div'><input type='submit' name='new_acc_by_admin_submit' id='new_acc_by_admin_submit' value='Create the new account'/></div>
						</div>
						<div id='create_new_person_right_div'>
							<div id='create_new_person_help'>
								<strong style='text-align:center;margin-left:1%'>NOTE: The following fields must be valid and filled before account approval</strong>
								<ul>
									<li>Last name cannot contain digits</li>
									<li>Username cannot already be taken and must start with an alphabetical character</li>
									<li>Password must contain at least a digit, a capital letter and a special character such as !@?^</li>
								</ul>
							</div>
							<?php if($create_acc_error): ?>
								<div id='create_new_person_errors'>
									<h2 style='text-align:center'>Errors:</h2>
									<ul>
										<?php for($i = 0; $i < count($validations); $i++){
												if(!$validations[$i]){
													if($i == 0)
														echo "<li>Last name missing/invalid last name</li>";
													else if($i == 1)
														echo "<li>Invalid email/email already taken</li>";
													else if($i == 2)
														echo "<li>Username missing/invalid username/username already taken</li>";
													else if($i == 3)
														echo "<li>Password missing/invalid password</li>";
												}
											}
										?>
									</ul>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<div class='fset' id='individual_country_responsibilities_div'>
				<h2 style='color:green'>Individual Country Responsibilities</h2>
				<ul>
					<li><label for='ln_res'>Last name: </label><select name='ln_res' id='ln_res'>
						<option value=''>Choose one</option>
						<?php getOptions('last_name'); ?>
					</select></li>
					<li><input type='submit' value='Search!' name='res_submit' id='res_submit'/></li>
				</ul>
			</div>
			<div class='fset' id='cell_responsibilities'>
				<h2 style='color:yellow'>Cell Responsibilities and Profiles</h2>
				<div id='EC_people_responsibilities' class='cell_CAG_profile_responsibilities'>
					<h2>Responsibilities</h2>
					<ul>
						<li><label>Display the <span style='color:green'>LAND</span> Cell</label><input type='submit' class='ECR_search' value="Submit" name='LAND_res' id='LAND_res'/></li>
						<li><label>Display the <span style='color:0099FF'>AIR</span> Cell</label><input type='submit' class='ECR_search' value="Submit" name='AIR_res' id='AIR_res'/></li>
						<li><label>Display the <span style='color:blue'>MARITIME</span> Cell</label><input type='submit' class='ECR_search' value="Submit" name='MARITIME_res' id='MARITIME_res'/></li>
						<li><label>Display the <span style='color:gray'>JOINT/ENABLING</span> Cell</label><input type='submit' class='ECR_search' value="Submit" name='JE_res' id='JE_res'/></li>
						<!--<li><label>Display people's country responsibilities in the <span style='color:66FF66'>CTR BRANCH</span> Cell</label><input type='submit' class='ECR_search' value="Display results from this Cell" name='CTR_BRANCH_res' id='CTR_BRANCH_res'/></li>-->
					</ul>
				</div>
				<div id='EC_people_profiles' class='cell_CAG_profile_responsibilities'>
					<h2>Profiles</h2>
					<ul>
						<li><label>Display the profiles in the <span style='color:red'>CTR BRANCH</span> Cell</label><input type='submit' class='ECR_search' value="Submit" name='CTR_BRANCH_profiles' id='CTR_BRANCH_profiles'/></li>
						<li><label>Display the profiles in the<span style='color:green'>LAND</span> Cell</label><input type='submit' class='ECR_search' value="Submit" name='LAND_profiles' id='LAND_profiles'/></li>
						<li><label>Display the profiles in the<span style='color:0099FF'>AIR</span> Cell</label><input type='submit' class='ECR_search' value="Submit" name='AIR_profiles' id='AIR_profiles'/></li>
						<li><label>Display the profiles in the<span style='color:blue'>MARITIME</span> Cell</label><input type='submit' class='ECR_search' value="Submit" name='MARITIME_profiles' id='MARITIME_profiles'/></li>
						<li><label>Display the profiles in the<span style='color:gray'>JOINT/ENABLING</span> Cell</label><input type='submit' class='ECR_search' value="Submit" name='JE_profiles' id='JE_profiles'/></li>
					</ul>
				</div>
				<div id='all_EC_responsibilities' class='cell_CAG_profile_responsibilities'>
					<h2>All Cell profiles and responsibilities</h2>
					<ul>
						<li><label>Display everyone's country responsibilities</label><input type='submit' class='ECR_search' value='Submit' name='EC_res' id='EC_res'/></li>
						<li><label>Display all Cell profiles</label><input type='submit' class='ECR_search' value='Submit' name='EC_profiles' id='EC_profiles'></li>
					</ul>
				</div>
			</div>
			<div class='fset' id='CAG_responsibilities'>
				<h2 style='color:green'>CAG Responsibilities and Profiles</h2>
				<div id='CAG_people_responsibilities' class='cell_CAG_profile_responsibilities'>
					<h2>Responsibilities</h2>
					<ul>
						<li><label>Display the <span style='color:660066'>ENGAGEMENT</span> CAG</label><input type='submit' class='ECR_search' value="Submit" name='ENGAGEMENT_res' id='ENGAGEMENT_res'/></li>
						<li><label>Display the <span style='color:FF9900'>COMMAND SUPPORT</span> CAG</label><input type='submit' class='ECR_search' value="Submit" name='COMMAND_SUPPORT_res' id='COMMAND_SUPPORT_res'/></li>
						<li><label>Display the <span style='color:red'>ENABLING</span> CAG</label><input type='submit' class='ECR_search' value="Submit" name='SUSTAIN_res' id='SUSTAIN_res'/></li>
					<!--<li><label>Display people's country responsibilities in the <span style='color:0000FF'>COORDINATOR</span> branch</label><input type='submit' class='ECR_search' value="Display results from this branch" name='COORDINATOR_res' id='COORDINATOR_res'/></li>-->
					<!--<li><label>Display people's country responsibilities in the <span style='color:009999'>BRANCH HEAD</span> branch</label><input type='submit' class='ECR_search' value="Display results from this branch" name='BRANCH_HEAD_res' id='BRANCH_HEAD_res'/></li>-->
					<!--<li><label>Display people's country responsibilities in the <span style='color:#FF00FF'>JOINT COORDINATOR</span> branch</label><input type='submit' class='ECR_search' value="Display results from this branch" name='JOINT_COORDINATOR_res' id='JOINT_COORDINATOR_res'/></li>-->
					</ul>
				</div>
				<div id='CAG_people_profiles' class='cell_CAG_profile_responsibilities'>
					<h2>Profiles</h2>
					<ul>
						<li><label>Display the profiles in the <span style='color:660066'>ENGAGEMENT</span> CAG</label><input type='submit' class='ECR_search' value="Submit" name='ENGAGEMENT_profiles' id='ENGAGEMENT_profiles'/></li>
						<li><label>Display the profiles in the <span style='color:FF9900'>COMMAND SUPPORT</span> CAG</label><input type='submit' class='ECR_search' value="Submit" name='COMMAND_SUPPORT_profiles' id='COMMAND_SUPPORT_profiles'/></li>
						<li><label>Display the profiles in the <span style='color:red'>ENABLING</span> CAG</label><input type='submit' class='ECR_search' value="Submit" name='ENABLING_profiles' id='ENABLING_profiles'/></li>
					</ul>
				</div>
				<div id='all_CAG_responsibilities' class='cell_CAG_profile_responsibilities'>
					<h2>All CAG profiles and responsibilities</h2>
					<ul>
						<li><label>Display everyone's country responsibilities in ALL branches</label><input type='submit' class='ECR_search' value="Submit" name='ALL_CAG_res' id='ALL_CAG_res'/></li>
						<li><label>Display all CAG profiles</label><input type='submit' class='ECR_search' value="Submit" name='CAG_profiles' id='CAG_profiles'/></li>
					</ul>
				</div>
			</div>
			<div class='fset' id='countries_cells_responsibilities_div'>
				<h2 style='color:yellow'>Countries, Cells, and Responsibilities</h2>
				<ul style='color:white'>
					<li><label>Display all people with their responsibilities corresponding to their countries and Cells</label><input type='submit' style='margin-left:20px' name='CECR_all' id='CECR_all' value='Get results'/></li>
					<?php if($_SESSION['admin']): ?>
					<li><label for='CECR_get_csv'>Create csv file </label><input type='checkbox' name='CECR_get_csv' id='CECR_get_csv' value='y'></li>
					<li><label for='dump_database'>Dump database tables into csv file </label><input type='submit' value='Get csv files' id='confirm_database_dump' name='confirm_database_dump'/></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		
	</body>
</form>
