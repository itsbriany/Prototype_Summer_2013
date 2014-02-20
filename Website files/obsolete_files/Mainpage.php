<?php
	require 'connect.inc.php';
	include 'GlobalVariables.php';
	
	//global variables
	$empty_field = false;
	$no_results = false;
	$CECR_visible = false;
	
	/*
		This function will check the username of which the user logged in with
		and will return the user's first name 
	*/
	function getFirstName(){
		global $username;
		if(!empty($username)){
			$query = "SELECT * FROM general_info WHERE username = '$username'";
			if($query_run = mysql_query($query)){
				if(mysql_num_rows($query_run) == NULL)
					return $username = "User";
				else{
					if ($query_row = mysql_fetch_assoc($query_run))
						return $query_row['first_name'];
				}
			}else
				echo mysql_error();
		}
	}
	
	##############################################################
	//PEOPLE SEARCH FUNCTIONS
	/*
		This function will work for any specific search.
		The parameters specified are:
		Param1:	the variable that is being searched for
		Param2:	the name of the database element that the searched variable must correspond to
	*/
	function specificSearch($searchElement, $dataBaseElement){
		global $empty_field, $no_results; //allows this function to access the specified variables outside of this function
		if(!empty($searchElement)){
			$query = "SELECT * FROM general_info WHERE $dataBaseElement='$searchElement'";
			if($query_run = mysql_query($query)){
				if(mysql_num_rows($query_run) == NULL)
					$no_results = true;
				else{
					fetchResults($query_run);
				}
			}else
				echo mysql_error();
		}else
			$empty_field = true;
	}
	
	/*
		This function will fetch the results from a query run and will output 
		a nice table displaying all the results.
		Param1:	the query run variable
	*/
	function fetchResults($query_run){
		echo "<table border='1' align='center' style='margin-top:20px'>";
		echo "<tr><td>Last name</td><td>First name</td><td>Nationality</td><td>Rank</td><td>Environmental cell</td></tr>";
		while($query_row = mysql_fetch_assoc($query_run)){
			$last_name = $query_row['last_name'];
			$first_name = $query_row['first_name'];
			$rank = $query_row['rank'];
			$nationality = $query_row['nationality'];
			$EC = $query_row['environment'];
			echo "<tr><td>".$last_name."</td><td>".$first_name."</td><td>".$nationality."</td><td>".$rank."</td><td>".$EC."</td></tr>";
		}
	}
	
	/*
		These if statements are brief searches for each different submission field
	*/
	if(isset($_POST['first_name_submit'])){
		$first_name_search = ucfirst(strtolower($_POST['first_name_search']));
		specificSearch($first_name_search, 'first_name');
	}
	
	if(isset($_POST['last_name_submit'])){
		$last_name_search = ucfirst(strtolower($_POST['last_name_search']));
		specificSearch($last_name_search, 'last_name');
	}
	
	if(isset($_POST['rank_submit'])){
		$rank_search = $_POST['rank_search'];
		specificSearch($rank_search, 'rank');
	}
	
	if(isset($_POST['nationality_submit'])){
		$nationality_search = $_POST['nationality_search'];
		specificSearch($nationality_search, 'nationality');
	}
	
	if(isset($_POST['EC_submit'])){
		$EC_search = $_POST['EC_search'];
		specificSearch($EC_search, 'environment');
	}
	
	/*
		This function checks for if all the search fields have been filled and will make an accurate check 
		to find the given record.
	*/
	function personSearch(){
		if(isset($_POST['submit_search'])){
			$first_name_search = ucfirst(strtolower($_POST['first_name_search']));
			$last_name_search = ucfirst(strtolower($_POST['last_name_search']));
			$rank_search = strtolower($_POST['rank_search']);
			$nationality_search = $_POST['nationality_search'];
			$EC_search = $_POST['EC_search'];
			if(!empty($_POST['first_name_search']) and !empty($_POST['last_name_search']) and !empty($_POST['rank_search']) and !empty($_POST['nationality_search']) and !empty($_POST['EC_search'])){
				$query = "SELECT * FROM general_info WHERE first_name = '$first_name_search' AND last_name = '$last_name_search' AND nationality = '$nationality_search' AND rank = '$rank_search' AND environment = '$EC_search'";
				//when doing this kind of search, make sure that ALL CORRESPONDING DATA HAS A VALUE!
				if($query_run = mysql_query($query)){
					if(mysql_num_rows($query_run) == NULL)
						echo 'No results found.';
					else
						fetchResults($query_run);
				}else
					echo mysql_error();
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
			$fn_search = ucfirst(strtolower($_POST['fn_res']));
			$ln_search = ucfirst(strtolower($_POST['ln_res']));
			if(!empty($fn_search) and !empty($ln_search)){
				$query = "SELECT general_info.first_name, general_info.last_name, c_resp.country_responsibilities FROM general_info, c_resp WHERE general_info.first_name='$fn_search' AND general_info.last_name='$ln_search' AND general_info.id=c_resp.id";
				if($query_run = mysql_query($query)){
					if(mysql_num_rows($query_run) == NULL)
						echo 'No results found.';
					else{
						echo "<h2 style='text-align:center'>Country Responsibilities</h2>";
						echo "<table border='1' align='center'><tr><th>$fn_search $ln_search</th></tr>";
						while($query_row = mysql_fetch_assoc($query_run)){
							$cr = $query_row['country_responsibilities'];
							echo "<tr><td>$cr</td></tr>";
						}
						echo "</table>";
					}
				}else
					echo mysql_error();
			}else
				echo "<h3 style='color:red;text-align:center'>Please fill in both individual responsibilities search fields.</h3>";
		}
	}
	
	#######################################################
	//ENVIRONMENTAL CELL RESPONSIBILITIES FUNCTIONS
	
	/*
		This function generates the table that represents all people in 
		a specified environmental cell
		Param1: The query
		Param2: The specified environmental cell
	*/
	function newECTable($query, $EC){
		if($query_run = mysql_query($query)){
			$EC_color = '';
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
				}
			if(mysql_num_rows($query_run) == NULL)
				echo "<h3 style='text-align:center;margin-top:50px'>Nobody participating in the environmental cell <span style='color:$EC_color'>$EC</span> could be found</h3>";
			else{
				echo "<h2 style='text-align:center;color:$EC_color'>$EC</h2><table border='1' align='center'>";
				echo "<tr><th>Last name</th><th>First name</th><th>Nationality</th><th colspan='10'>Country responsibilities</th></tr>";
				$checkDifferent = false;
				$checkLastRun = false;
				$idCheck = -1;
				//$countryCount = 0;
				//$maxCountryCount = 0;
				while($query_row = mysql_fetch_assoc($query_run)){
					$id = $query_row['id'];
					$environment = $query_row['environment'];
					$CR = $query_row['country_responsibilities'];
					if($environment != "$EC" and $checkDifferent){
						echo "</tr>";
						$checkDifferent = false;
						$checkLastRun = false;
						//$maxCountryCount = $countryCount;
						//$countryCount = 0;
					}else if($environment == "$EC" and $idCheck != $id){
						echo "<tr>";
						$ln = $query_row['last_name'];
						$fn = $query_row['first_name'];
						$nat = $query_row['nationality'];
						echo "<td>$ln</td><td>$fn</td><td>$nat</td><td>$CR</td>";
						$idCheck = $id;
						$checkDifferent = true;
						$checkLastRun = true;
						//$countryCount++;
					}else if($environment == "$EC" and $idCheck == $id){
						echo "<td>$CR</td>";
						//$countryCount++;
					}
				}
				if($checkLastRun)
					echo "</tr>";
				echo "</table>";
			}	
		}
	}

	/*
		These functions will display everyone's country responsibilities in the 
		corresponding environmental cell
	*/
	function landResponsibilities(){
		if(isset($_POST['LAND_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, c_resp.country_responsibilities FROM `general_info` AS g, c_resp WHERE environment = 'LAND' AND c_resp.id = g.id ORDER BY last_name, first_name";
			newECTable($query, 'LAND');
		}
	}
	function airResponsibilities(){
		if(isset($_POST['AIR_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, c_resp.country_responsibilities FROM `general_info` AS g, c_resp WHERE environment = 'AIR' AND c_resp.id = g.id ORDER BY last_name, first_name";
			newECTable($query, 'AIR');
		}
	}
	function maritimeResponsibilities(){
		if(isset($_POST['MARITIME_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, c_resp.country_responsibilities FROM `general_info` AS g, c_resp WHERE environment = 'MARITIME' AND c_resp.id = g.id ORDER BY last_name, first_name";
			newECTable($query, 'MARITIME');
		}
	}
	function jeResponsibilities(){
		if(isset($_POST['JE_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, c_resp.country_responsibilities FROM `general_info` AS g, c_resp WHERE environment = 'JOINT/ENABLING' AND c_resp.id = g.id ORDER BY last_name, first_name";
			newECTable($query, 'JOINT/ENABLING');
		}
	}
	
	/*
		This function will display everyone's country responsibilities and their environmental cell
	*/
	function allResponsibilities(){
		if(isset($_POST['EC_res'])){
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, c_resp.country_responsibilities FROM `general_info` AS g, c_resp WHERE environment = 'LAND' AND c_resp.id = g.id ORDER BY last_name, first_name";
			newECTable($query, 'LAND');
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, c_resp.country_responsibilities FROM `general_info` AS g, c_resp WHERE environment = 'AIR' AND c_resp.id = g.id ORDER BY last_name, first_name";
			newECTable($query, 'AIR');
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, c_resp.country_responsibilities FROM `general_info` AS g, c_resp WHERE environment = 'MARITIME' AND c_resp.id = g.id ORDER BY last_name, first_name";
			newECTable($query, 'MARITIME');
			$query = "SELECT g.first_name, g.last_name, g.id, g.nationality, g.environment, c_resp.country_responsibilities FROM `general_info` AS g, c_resp WHERE environment = 'JOINT/ENABLING' AND c_resp.id = g.id ORDER BY last_name, first_name";
			newECTable($query, 'JOINT/ENABLING');
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
		}
	}
	
	/*
		This function outputs the countries and
		environmental cells of which everyone is responsible for
		and will display their cell leaders. (CECR means country, environmental cell, responsibilities)
	*/
	function returnCECR($query){
		$cell_leader_query = "SELECT * FROM cell_leaders";
		if($query_run = mysql_query($cell_leader_query)){
			if(mysql_num_rows($query_run) == NULL)
				echo "No environmental cell leaders found.";
			else{
				echo "<table border='1' align='center' style='margin-top:20px;font-family:arial;text-align:center'>";
				echo "<tr><th bgcolor='#B8B8B8'>JOINT/ENABLING leader</th><th bgcolor='#33CC33'>LAND leader</th><th bgcolor='#1975FF'>MARITIME leader</th><th bgcolor='#66CCFF'>AIR leader</th></tr>";
				echo "<tr>";
				while($query_row = mysql_fetch_assoc($query_run)){
					$last_name = $query_row['last_name'];
					echo "<td>$last_name</td>";
				}
				echo "</tr></table>";
			}
		}
		if($query_run = mysql_query($query)){
			if(mysql_num_rows($query_run) == NULL){
				echo 'No results found.';
			}else{
				global $CECR_visible;
				$CECR_visible = true;
				$graySwitch = false;
				echo "<table border='1' align='center' style='margin-top:10px;font-family:arial;text-align:center'>";
				echo "<tr bgcolor='yellow'><th>Nation</th><th style='color:green'>Code</th><th>Capital</th><th>Country Leader</th><th>JOINT/ENABLING</th><th>LAND</th><th>MARITIME</th><th>AIR</th></tr>";
				while($query_row = mysql_fetch_assoc($query_run)){
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
			echo mysql_error();
	}
	
	################################################################
	//TABLE SORTING FUNCTIONS
	
	/*
		This function will sort the CECR table
		The sort will vary upon the parameter
		Param1: sorting method
	*/
	function sortCECR(){
		if(isset($_POST['sort_submit'])){
			$query = '';
			$sort = $_POST['sort_input'];
			if($sort == 'Name')
				$query = "SELECT * FROM country_cell_resp ORDER BY last_name";
			else if($sort == 'Country')
				$query = "SELECT * FROM country_cell_resp ORDER BY code";
			if($query != '')	
				returnCECR($query);
		}
	}
	
	################################################################
	//PEOPLE EDIT FUNCTIONS
	
	/*
		This function will modify the desired person's
		information in the database according to the given input
	*/
	function editPerson(){
		if(isset($_POST['people_edit_submit'])){
			$id = $_POST['select_db_id'];
			if($id == '')
				echo 'please enter an id.';
			else{
				$query = "SELECT general_info.id FROM general_info WHERE general_info.id='$id'";
				if($query_run = mysql_query($query)){
					if(mysql_num_rows($query_run) == NULL){
						echo 'the entered id does not exist in db.';
					}else{
						$ln = $_POST['ln_edit'];
						if($ln != ''){
							replaceLastNameQuery($id, $ln);
						}
						
					}
				}else
					echo mysql_error();
			}
		}
	}
	
	##################################################################
	//QUERIES
	
	/*
		Executes a series of a queries that replace all the last names in the database relating to a given id 
		Param1: The given id
		Param2: The name that will replace the previous one
	*/
	function replaceLastNameQuery($id, $name){
		mysql_query("UPDATE country_cell_resp as cr
			JOIN general_info
			SET cr.joint_enabling ='$name' WHERE cr.joint_enabling = general_info.last_name AND general_info.id = '$id'");
											
		mysql_query("UPDATE country_cell_resp as cr
			JOIN general_info
			SET cr.land ='$name' WHERE cr.land = general_info.last_name AND general_info.id = '$id'");

		mysql_query("UPDATE country_cell_resp as cr
			JOIN general_info
			SET cr.maritime ='$name' WHERE cr.maritime = general_info.last_name AND general_info.id = '$id'");

		mysql_query("UPDATE country_cell_resp as cr
			JOIN general_info
			SET cr.air ='$name' WHERE cr.air = general_info.last_name AND general_info.id = '$id'");

		mysql_query("UPDATE country_cell_resp as cr
			JOIN general_info
			SET cr.country_leader ='$name' WHERE cr.country_leader = general_info.last_name AND general_info.id = '$id'");

		mysql_query("UPDATE general_info as g
			SET g.last_name = '$name' WHERE g.id = '$id'");	

		mysql_query("UPDATE cell_leaders as c
			SET c.last_name = '$name' WHERE c.id = '$id'");
	}
	
	function replaceFirstNameQuery($id, $name){
		mysql_query("UPDATE general_info AS g SET g.first_name = '$name' WHERE g.id = '$id'");
	}
	
	function replaceNationalityQuery($id, $nationality){
		mysql_query("UPDATE general_info AS g SET g.nationality = '$nationality' WHERE g.id = '$id'");
	}
	
	function replaceRankQuery($id, $rank){
		mysql_query("UPDATE general_info AS g SET g.rank = '$rank' WHERE g.id = '$id'");
	}
	
	function replaceEnvironmentQuery($id, $environment){
		mysql_query("UPDATE general_info AS g SET g.environment = '$name' WHERE g.id = '$id'");
		//NOTE: If environment changes, then remove the guy from joint_enabling, land, maritime, and air columns in country_cell_resp???
	}
	
	function replaceRankQuery($id, $rank){
		mysql_query("UPDATE general_info AS g SET g.rank = '$rank' WHERE g.id = '$id'");
	}
	
	function replaceRankQuery($id, $rank){
		mysql_query("UPDATE general_info AS g SET g.rank = '$rank' WHERE g.id = '$id'");
	} 
?>

<head>
	<title>Project #1</title>
	<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
</head>

<form action='Mainpage.php' method='POST'>
	<body>
		<h1 style='text-align:center'>Welcome <?php echo getFirstName();?></h1>
		<fieldset>
			<legend>People search</legend>
			<ul>
				<li><label for='first_name_search'>First name: </label><input type='text' value='' name='first_name_search' id='first_name_search'>
				<input type='submit' value='Search only by first name' name='first_name_submit' id='first_name_submit'></li>
				<li><label for='last_name_search'>Last name: </label><input type='text' value='' name='last_name_search' id='last_name_search'>
				<input type='submit' value='Search only by last name' name='last_name_submit' id='last_name_submit'></li>
				<li><label for='rank_search'>Rank: </label>
				<select id='rank_search' name='rank_search'>
					<option value='' selected='selected'>Choose One</option>
					<option value='OF-2'>OF-2</option>
					<option value='OF-3'>OF-3</option>
					<option value='OF-4'>OF-4</option>
					<option value='OF-5'>OF-5</option>
					<option value='OF-6'>OF-6</option>
					<option value='CIV'>CIV</option>
				</select>
				<input type='submit' value='Search only by rank' name='rank_submit' id='rank_submit'></li>
				<li><label for='nationality_search'>Nationality: </label>
				<select id='nationality_search' name='nationality_search'>
					<option value='' selected='selected'>Choose One</option>
						<option value='ENG'>ENG</option>
						<option value='GER'>GER</option>
						<option value='CAN'>CAN</option>
						<option value='US'>US</option>
						<option value='FR'>FR</option>
						<option value='BEL'>BEL</option>
				</select><input style='margin-left:4px' type='submit' value='Search only by nationality' name='nationality_submit' id='nationality_submit'></li>
				<li><label for='EC_search'>Environmental cell: </label>
				<select id='EC_search' name='EC_search'>
					<option value='' selected='selected'>Choose One</option>
					<option value='LAND'>LAND</option>
					<option value='AIR'>AIR</option>
					<option value='MARITIME'>MARITIME</option>
					<option value='JOINT/ENABLING'>JOINT/ENABLING</option>
				</select><input style='margin-left:4px' type='submit' value='Search only by environmental cell' name='EC_submit' id='EC_submit'></li>
				<li><input type='submit' value='Submit complete search' id='submit_search' name='submit_search'></li>
				<li><?php if($empty_field){echo "<label style='color:red'>Field was left empty</label>";} if($no_results){echo "<label style='color:red'>No results found.</label>";} ?></li>
			</ul>
		</fieldset>
		
		<fieldset>
			<legend>People edit</legend>
			<ul>
				<li><label for='select_db_id'>Enter the id number of the person whom you wish to replace/make changes to: </label><input type='text' name='select_db_id' id='select_db_id'>
				<li><label for='ln_edit'>Change last name to: </label><input type='text' name='ln_edit' id='ln_edit'>
				<li><label for='fn_edit'>Change first name to: </label><input type='text' name='fn_edit' id='fn_edit'>
				<li><label for='nationality_edit'>Change nationality to: </label><input type='text' name='nationality_edit' id='nationality_edit'>
				<li><label for='rank_edit'>Change rank to: </label>
				<select name='rank_edit' id='rank_edit'>
					<option value='' selected='selected'>Choose one</option>
					<option value='OF-2'>OF-2</option>
					<option value='OF-3'>OF-3</option>
					<option value='OF-4'>OF-4</option>
					<option value='OF-5'>OF-5</option>
					<option value='OF-6'>OF-6</option>
					<option value='CIV'>CIV</option>
				</select></li>
				<li><label for='EC_edit'>Change environmental cell to: </label>
				<select name='EC_edit' id='EC_edit'>
					<option value='' selected='selected'>Choose One</option>
					<option value='LAND'>LAND</option>
					<option value='AIR'>AIR</option>
					<option value='MARITIME'>MARITIME</option>
					<option value='JOINT/ENABLING'>JOINT/ENABLING</option>
				</select></li>
				<li><label for='pe_num_edit'>Change PE number to: </label><input type='text' name='pe_num_edit' id='pe_num_edit'></li>
				<li><label for='BoT_edit'>Change BoT to: </label><input type='text' name='BoT_edit' id='BoT_edit'></li>
				<li><label for='EoT_edit'>Change EoT to: </label><input type='text' name='EoT_edit' id='EoT_edit'></li>
				<li><label for='email_edit'>Change email to: </label><input type='text' name='email_edit' id='email_edit'></li>
				<li><label for='phone_edit'>Change phone number to: </label><input type='text' name='phone_edit' id='phone_edit'></li>
				<li><label for='username_edit'>Change username to: </label><input type='text' name='username_edit' id='username_edit'></li>
				<li><label for='pass_edit'>Change password to: </label><input type='text' name='pass_edit' id='pass_edit'></li>
				<li><input type='submit' name='people_edit_submit' id='people_edit_submit' value='Submit changes'></li>
			</ul>
		</fieldset>
		<fieldset>
			<legend>Individual Responsibilities search</legend>
			<ul>
				<li><label for='fn_res'>First name: </label><input type='text' name='fn_res' id='fn_res'></li>
				<li><label for='ln_res'>Last name: </label><input type='text' name='ln_res' id='ln_res'></li>
				<li><input type='submit' value='Search!' name='res_submit' id='res_submit'></li>
			</ul>
		</fieldset>
		<fieldset>
			<legend>Environmental cell responsibilities search</legend>
			<ul>
				<li><label>Display people's country responsibilities in the <span style='color:green'>LAND</span> cell</label><input type='submit' class='ECR_search' value="Display results from this environmental cell" name='LAND_res' id='LAND_res'></li>
				<li><label>Display people's country responsibilities in the <span style='color:0099FF'>AIR</span> cell</label><input type='submit' class='ECR_search' value="Display results from this environmental cell" name='AIR_res' id='AIR_res'></li>
				<li><label>Display people's country responsibilities in the <span style='color:blue'>MARITIME</span> cell</label><input type='submit' class='ECR_search' value="Display results from this environmental cell" name='MARITIME_res' id='MARITIME_res'></li>
				<li><label>Display people's country responsibilities in the <span style='color:gray'>JOINT/ENABLING</span> cell</label><input type='submit' class='ECR_search' value="Display results from this environmental cell" name='JE_res' id='JE_res'></li>
				<li><label>Display everyone's country responsibilities</label><input type='submit' class='ECR_search' value='Display all responsibilities from all environmental cells' name='EC_res' id='EC_res'></li>
			</ul>
		</fieldset>
		<fieldset>
			<legend>CECR (Countries, environmental cells, responsibilities)</legend>
			<ul>
				<li><label>Display all people with their responsibilities corresponding to their countries and environmental cells</label><input type='submit' style='margin-left:20px' name='CECR_all' id='CECR_all' value='Get results'></li>
			</ul>
		</fieldset>
		<?php 
			personSearch();
			fetchIndividualResponsibilities();
			allResponsibilities();
			landResponsibilities();
			airResponsibilities();
			maritimeResponsibilities();
			jeResponsibilities();
			getCECR();
			sortCECR();
			editPerson();
		?>
		
		<?php if($CECR_visible): ?>
			<div id='CECR_options'>
				<label for='sort_input'>Sort table by: </label>
				<select name='sort_input' id='sort_input'>
					<option value='' selected='selected'>Choose one</option>
					<option value='Name'>Name</option>
					<option value='Country'>Country</option>
				</select>
				<input type='submit' value='Sort!' name='sort_submit' id='sort_submit'>
			</div>
		<?php endif;?>
	</body>
</form>