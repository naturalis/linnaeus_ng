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

	if (confirm('Are you sure?')) { 
		var e = document.getElementById('delete'); 
		e.value = '1'; 
		e = document.getElementById('id'); 
		e.value = id; 
		e = document.getElementById('deleteForm'); 
		e.submit(); 
	}

}


function userRemoteValueCheck(id,values,tests,idti) {

	if (values[0].length==0) {

		$('#'+id+'-message').html('');
		return;

	}

	var action = false;

	switch(id) {
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
			if (id=='password') {
				if (data.match(/\<*\>/)) {
					$('#'+id+'-message').removeClass().addClass('password-'+data.replace(/[<>]/gi,''));
					$('#'+id+'-message').html('Password strength: '+data.replace(/[<>]/gi,''));
				} else {
					$('#'+id+'-message').removeClass().addClass('password-neutral');
					$('#'+id+'-message').html(data);
				}
			} else {
				$('#'+id+'-message').html(data);
			}
		}
	});

}