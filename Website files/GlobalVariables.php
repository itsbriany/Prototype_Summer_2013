<?php
	require 'connect.incV2.php';
	############################
	//VARIABLES USED BY Mainpage_v2.php
	$edit_error = array_fill(0, 21, false);
	$global_query = '';
	$empty_field = false;
	$no_results = false;
	$CECR_visible = false;
	$leader_resp_change = false;
	$c_check = true;
	$display_responsibilities = false;
	$display_leading_countries = false;
	$isAdmin = false;
	$row_count = 0;
	$edit_people_confirm = false;
	$display_partner_nations = false;
	$display_partner_leader_nations = false;
	$display_leading_nations = false;
	$display_leader_responsibilities = false;
	$temp_var = '';
	$validations = array_fill(0, 4, true);
	$confirm_account = false;
	$create_acc_error = false;
	$display_tasks = false;
	$target_id = -1;
	#############################
	//VARIABLE USED BY My_Profile.php
	$p_id = '';
	$p_last_name = '';
	$p_first_name = '';
	$p_nat = '';
	$p_rank = '';
	$p_EC = '';
	$p_CAG = '';
	$p_pe_num = '';
	$p_BoT = '';
	$p_EoT = '';
	$p_email = '';
	$p_phone = '';
	$p_user = '';
	$p_NATO_PASS = '';
	$p_SHAPE_ID = '';
	$p_DoB = '';
	$p_passport = '';
	$p_passport_expiry = '';
	$p_security_clearance = '';
	$p_pe_flag = '';
	$p_mobile = '';
	$p_secondary_email = '';
	$p_address = '';
	$p_credit_card_company = '';
	$p_credit_card_number = '';
	$p_credit_card_expiry = '';
	$p_tasks = '';
	$p_errors = array_fill(0,19, false);
	$pass_update = false;
	$p_update = false;
?>