
var isMainPage = false;
var isAdmin = false;


	
function showRecovery(){
	$('#recovery').show();
}

function toSignup(){
	window.location.replace('http://localhost:8000/First%20project/Signup.php');
}

function createTaskSpace(){
	var current_task = document.getElementById('p_tasks').value.concat("\n");
	document.getElementById('p_tasks').value = current_task;
}

function createTasks(tasks){
	var current_task = document.getElementById('p_tasks').value.concat(tasks);
	document.getElementById('p_tasks').value = current_task;
}
$(document).ready(function(){
	
	$('#recovery').hide();
	
	var valid_clear = true;
	$('input#select_db_id').focus(function(){
		if(document.getElementById('select_db_id').value == ''){
			if(valid_clear){
				$('input#username_edit').val('');
				$('input#pass_edit').val('');
				valid_clear = false;
			}
		}else
			valid_clear = false;
	});
	
	
		if(isMainpage && isAdmin){
			$('input#make_admin').change(function(){
				if(document.getElementById('demote_admin').value == 'n'){
					$('input#demote_admin').prop('checked', false);
				}
			});
			$('input#demote_admin').change(function(){
				if(document.getElementById('make_admin').value == 'y'){
					$('input#make_admin').prop('checked', false);
				}
			});
				
			
			
			if(document.getElementById('username_edit').value == '' || document.getElementById('pass_edit').value == ''){
				$('input#username_edit').val('');
				$('input#pass_edit').val('');
			}
		}
	
	
	//$(document).load('http://localhost:8000/First%20project/Mainpage_v2.php', function(){
		//$("html, body").animate({ scrollTop: $(document).height()}, 'slow');
		//return false;
	//});
	
	$('input').keypress(function(e){
		if(e.keyCode == 13){
			return false;
		}
	});
	
	if(isMainpage && isAdmin){
	
		var has_checked = false;
		var currently_checked = ''; 
		
		$('select#leader').change(function(){
			var check_for_dup = false;
			var leader_element = document.getElementById('leader');
			var selected_leader = leader_element.options[leader_element.selectedIndex].value;
			$('input:checkbox:checked.countries').each(function(){
				if(selected_leader == this.value){
					check_for_dup = true;
				}
			});
			if(!check_for_dup){
				selected_leader = leader_element.options[leader_element.selectedIndex].text;
				if(selected_leader == 'United Kingdom')
					selected_leader = 'United_Kingdom';
				else if(selected_leader == 'United States')
					selected_leader = 'United_States';
				$('input:checkbox').each(function(){
					if(selected_leader == this.id){
						$(this).prop('checked',true);
						$('#confirm_NATO_countries').prop('checked', true);
						$('input:checkbox').each(function(){
							if(currently_checked == this.value){
								$(this).prop('checked', false);
								return false;
							}
						});
						currently_checked = this.value;
						return false;
					}
				});
			}
		});
	}
});