function userConnectExistingUser() {

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'connect_existing' ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			//alert(data);
			if(data=='<ok>') {
				window.open('index.php','_self');
			}
		}
	});

}

function userCreateUserFromSession() {

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'create_from_session' ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			//alert(data);
			if(data=='<ok>') {
				window.open('index.php','_self');
			}
		}
	});

}

function userDeleteUser(id) {

	if (confirm(_('Are you sure?'))) { 
		$('#delete').val('1'); 
		$('#id').val(id); 
		$('#deleteForm').submit(); 
	}

}


function userRemoteValueCheck(ele,values,tests,idti) {

	if (values[0].length==0) {

		$('#'+ele+'-message').html(ele=='password' || ele=='password_2' ? _('(leave blank to leave unchanged)') : '');
		$('#'+ele+'-message').removeClass().addClass('password-neutral');
		return;

	}

	var action = false;

	switch(ele) {
		case 'username' : 
			action = 'check_username';
			break;
		case 'password' :
			action = 'check_password';
			break;
		case 'password_2' :
			action = 'check_passwords';
			break;
		case 'first_name' :
			action = 'check_first_name';
			break;
		case 'last_name' :
			action = 'check_last_name';
			break;
		case 'email_address' :
			action = 'check_email_address';
			break;
	}

	if (!action) return;

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : action ,
			'values' : values ,
			'tests' : tests ,
			'id_to_ignore' : idti ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			if (ele=='password') {
				if (data.match(/\<*\>/)) {
					$('#'+ele+'-message').removeClass().addClass('password-'+data.replace(/[<>]/gi,''));
					$('#'+ele+'-message').html(_('Password strength:')+' '+data.replace(/[<>]/gi,''));
				} else {
					$('#'+ele+'-message').removeClass().addClass('password-neutral');
					$('#'+ele+'-message').html(data);
				}
			} else {
				$('#'+ele+'-message').html(data);
			}
		}
	});

}

function userChangeRoleRight(ele) {

	if (confirm(_('Are you sure?'))) {
		
		var d = ele.id.split('-');
				  
		$('#right').val(d[1]);
		$('#wrong').val(d[2]);
		$('#theForm').submit();
				  
	}

}


function userAddToProject(uid,returnUrl) {

	showDialog('',_('Add collaborator'));
	$('#dialog-content-inner').load('add_user.php?uid='+uid+(returnUrl?'&returnUrl='+returnUrl:''));

}


function userRemoveFromProject(uid,returnUrl) {

	showDialog('',_('Remove collaborator'));
	$('#dialog-content-inner').load('remove_user.php?uid='+uid+(returnUrl?'&returnUrl='+returnUrl:''));

}

function userAddToModule(uid,modId,returnUrl) {

	showDialog('',_('Assign collaborator to module'));
	$('#dialog-content-inner').load('add_user_module.php?uid='+uid+'&modId='+modId+(returnUrl?'&returnUrl='+returnUrl:''));

}

function userRemoveFromModule(uid,modId,returnUrl) {

	showDialog('',_('Remove collaborator from module'));
	$('#dialog-content-inner').load('remove_user_module.php?uid='+uid+'&modId='+modId+(returnUrl?'&returnUrl='+returnUrl:''));

}

function userAddToFreeModule(uid,modId,returnUrl) {

	showDialog('',_('Assign collaborator to module'));
	$('#dialog-content-inner').load('add_user_module.php?uid='+uid+'&modId='+modId+'&type=free'+(returnUrl?'&returnUrl='+returnUrl:''));

}

function userRemoveFromFreeModule(uid,modId,returnUrl) {

	showDialog('',_('Remove collaborator from module'));
	$('#dialog-content-inner').load('remove_user_module.php?uid='+uid+'&modId='+modId+'&type=free'+(returnUrl?'&returnUrl='+returnUrl:''));

}





