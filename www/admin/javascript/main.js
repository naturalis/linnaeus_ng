function tableColumnSort(col) {
	var e = document.getElementById('key');
	e.value = col;
	e = document.getElementById('postForm');
	e.submit();
}

function toggleHelpVisibility() {
	var e = document.getElementById('inlineHelp-body');
	if (e.className=='inlineHelp-body-hidden') {
		e.className='inlineHelp-body';
	} else {
		e.className='inlineHelp-body-hidden';
	}
}

function setErrorClass(id,error) {

	$('#'+id+'-message').removeClass().addClass(error ? 'admin-message-error' : 'admin-message-no-error');

}

function remoteValueCheck(id,values,tests,idti) {

	$.ajax({ url:
		   		"ajax_interface.php?f="+
		   		encodeURIComponent(id)+"&v="+
				encodeURIComponent(values)+"&t="
				+encodeURIComponent(tests)+
				(idti ? "&i="+encodeURIComponent(idti) : "" ),
		success: function(data){
	        $('#'+id+'-message').html(data);
			setErrorClass(id,data.search(/\<error\>/gi)!=-1);
      	}
	});

}

function moduleAction(ele,removeIds) {

	var dummy = ele.id.replace('cell-','');
	var id = dummy.substr(0,dummy.length-1);
	var classname = ($('#'+ele.id).attr('class'));
	var modulename = $('#cell-'+id+'e').html();

	if (classname == 'admin-td-module-activate') var action = 'activate';
	else
	if (classname == 'admin-td-module-reactivate') var action = 'reactivate';
	else
	if (classname == 'admin-td-module-deactivate') var action = 'deactivate';
	else
	if (classname == 'admin-td-module-delete') var action = 'delete';
	else return;
	
	if (action == 'delete') {

		if (confirm(
			'Are you sure you want to delete the module "'+modulename+'"?\n'+
			'When you delete the module, all corresponding data will be irreversibly deleted.'
			)) {

			if (!confirm(
				'Final confirmation:\n'+
				'Are you sure you want to delete the module "'+modulename+'"?\n'+
				'All corresponding data will be irreversibly deleted.'
				)) {
	
				return;

			}

		}

	}

	$.ajax({ url:"ajax_interface.php?v=modules&a="+encodeURIComponent(action)+"&i="+encodeURIComponent(id),
		success: function(data){

			if (data=='<ok>') {
				switch(action) {
					case 'activate':
					case 'reactivate':
						var classa = 'admin-td-module-inuse';
						var classb = 'admin-td-module-deactivate';
						var classc = 'admin-td-module-invisible';	  
						var classd = 'admin-td-module-title-inuse';	  
						var titlea = 'in use in your project';
						var titleb = 'deactivate (no data will be deleted)';
						var titlec = '';
						break;
					case 'deactivate':
						var classa = 'admin-td-module-inactive';
						var classb = 'admin-td-module-reactivate';
						var classc = 'admin-td-module-delete';	  
						var classd = 'admin-td-module-title-deactivated';	  
						var titlea = 'in use in your project, but inactive';
						var titleb = 're-activate';
						var titlec = 'delete module and data';
						break;
					case 'delete':
						var classa = 'admin-td-module-unused';
						var classb = 'admin-td-module-activate';	  
						var classc = 'admin-td-module-invisible';	  
						var classd = 'admin-td-module-title-unused';	  
						var titlea = 'not in use in your project';
						var titleb = 'activate';
						var titlec = '';
						break;
				}

				$('#cell-'+id+'a').removeClass().addClass(classa);
				$('#cell-'+id+'b').removeClass().addClass(classb);
				$('#cell-'+id+'c').removeClass().addClass(classc);
				$('#cell-'+id+'d').removeClass().addClass(classd);

				$('#cell-'+id+'a').attr('title',titlea);
				$('#cell-'+id+'b').attr('title',titleb);
				$('#cell-'+id+'c').attr('title',titlec);
				
				if (action == 'delete' && removeIds) {
					for(var i=0;i<=removeIds.length;i++) {

						$('#'+removeIds[i]).remove();

					}

				}
				
				if (id.substr(0,1)=='f') {
									
					$('#new-input').removeClass().addClass('admin-module-new-input');

				}

			}

		}

	});

}


function toggleModuleUsers(i) {

	classname = $('#users-'+i).attr('class');
	
	if (classname=='admin-modusers-hidden')
		$('#users-'+i).removeClass().addClass('admin-modusers');
	else
		$('#users-'+i).removeClass().addClass('admin-modusers-hidden');
}

function moduleUserAction(ele) {

	if ($('#'+ele.id).attr('class') == 'admin-td-moduser-inactive')
		action = 'add';
	else
		action = 'remove';

	m = ele.id.replace('cell-','');
	m = m.substr(0,m.length-1);
	u = m.substr(m.indexOf('-')+1);
	m = m.substr(0,m.indexOf('-'));

	$.ajax({ url:"ajax_interface.php?v=collaborators"+
				"&a="+encodeURIComponent(action)+
				"&i="+encodeURIComponent(m)+
				"&u="+encodeURIComponent(u),
		success: function(data){

			switch(action) {
				case 'add':
					var classa = 'admin-td-module-title-inuse';
					var classb = 'admin-td-moduser-remove';
					break;
				case 'remove':
					var classa = '';
					var classb = 'admin-td-moduser-inactive';
					break;
			}			

			$('#'+ele.id).removeClass().addClass(classb);
			$('#'+ele.id.replace('b','a')).removeClass().addClass(classa);
			$('#cell-'+m+'n').html(parseFloat($('#cell-'+m+'n').html())+(action=='add' ? 1 : -1 ));
      	}
	});

}