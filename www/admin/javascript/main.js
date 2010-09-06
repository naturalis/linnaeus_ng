function allTableColumnSort(col) {

	var e = document.getElementById('key');
	e.value = col;
	e = document.getElementById('postForm');
	e.submit();
}

function allToggleHelpVisibility() {

	var e = document.getElementById('body-visible');
	if (e.className=='body-collapsed') {
		e.className='body-visible';
	} else {
		e.className='body-collapsed';
	}

}

function allDoubleDeleteConfirm(element,name) {

	if (confirm(
		'Are you sure you want to delete the '+element+' "'+name+'"?\n'+
		'When you delete the '+element+', all corresponding data will be irreversibly deleted.'
		)) {
	
		return (confirm(
			'Final confirmation:\n'+
			'Are you sure you want to delete the '+element+' "'+name+'"?\n'+
			'All corresponding data will be irreversibly deleted.'
			));
	
	} else {

		return false;

	}

}

function userRemoteValueCheck(id,values,tests,idti) {

	$.ajax({ url:
		   		"ajax_interface.php?f="+
		   		encodeURIComponent(id)+"&v="+
				encodeURIComponent(values)+"&t="
				+encodeURIComponent(tests)+
				(idti ? "&i="+encodeURIComponent(idti) : "" ),
		success: function(data){
	        $('#'+id+'-message').html(data);
			error = data.search(/\<error\>/gi)!=-1;
			$('#'+id+'-message').removeClass().addClass(error ? 'message-error' : 'message-no-error');
      	}
	});

}

function moduleChangeModuleStatus(ele,removeIds) {

	var dummy = ele.id.replace('cell-','');
	var id = dummy.substr(0,dummy.length-1);
	var classname = ($('#'+ele.id).attr('class'));
	var modulename = $('#cell-'+id+'e').html();

	if (classname == 'cell-module-activate') var action = 'activate';
	else
	if (classname == 'cell-module-reactivate') var action = 'reactivate';
	else
	if (classname == 'cell-module-deactivate') var action = 'deactivate';
	else
	if (classname == 'cell-module-delete') var action = 'delete';
	else return;
	
	if (action == 'delete') {

		if (!allDoubleDeleteConfirm('module',modulename)) return;

	}

	$.ajax({ url:"ajax_interface.php?v=modules&a="+encodeURIComponent(action)+"&i="+encodeURIComponent(id),
		success: function(data){

			//alert(data);

			if (data=='<ok>') {
				switch(action) {
					case 'activate':
					case 'reactivate':
						var classa = 'cell-module-in-use';
						var classb = 'cell-module-deactivate';
						var classc = 'cell-module-invisible';	  
						var classd = 'cell-module-title-in-use';	  
						var titlea = 'in use in your project';
						var titleb = 'deactivate (no data will be deleted)';
						var titlec = '';
						break;
					case 'deactivate':
						var classa = 'cell-module-inactive';
						var classb = 'cell-module-reactivate';
						var classc = 'cell-module-delete';	  
						var classd = 'cell-module-title-inactive';	  
						var titlea = 'in use in your project, but inactive';
						var titleb = 're-activate';
						var titlec = 'delete module and data';
						break;
					case 'delete':
						var classa = 'cell-module-unused';
						var classb = 'cell-module-activate';	  
						var classc = 'cell-module-invisible';	  
						var classd = 'cell-module-title-unused';	  
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
									
					$('#new-input').removeClass().addClass('module-new-input');

				}

			}

		}

	});

}

function moduleToggleModuleUserBlock(i) {

	classname = $('#users-'+i).attr('class');
	
	if (classname=='modusers-block-hidden')
		$('#users-'+i).removeClass().addClass('modusers-block');
	else
		$('#users-'+i).removeClass().addClass('modusers-block-hidden');

}

function moduleChangeModuleUserStatus(ele) {

	if ($('#'+ele.id).attr('class') == 'cell-moduser-inactive')
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

			if (data=='<ok>') {

				switch(action) {
					case 'add':
						var classa = 'cell-module-title-in-use';
						var classb = 'cell-moduser-remove';
						break;
					case 'remove':
						var classa = '';
						var classb = 'cell-moduser-inactive';
						break;
				}			
	
				$('#'+ele.id).removeClass().addClass(classb);
				$('#'+ele.id.replace('b','a')).removeClass().addClass(classa);
				$('#cell-'+m+'n').html(parseFloat($('#cell-'+m+'n').html())+(action=='add' ? 1 : -1 ));
			}
		}
	});

}

var selectedLanguages = Array();

function projectSaveLanguage(action,lan) {

	if (action == 'delete') {

		if (!allDoubleDeleteConfirm('language',lan[1])) return;

	}

	$.ajax({ url:"ajax_interface.php?v=languages"+
				"&a="+encodeURIComponent(action)+
				"&i="+encodeURIComponent(lan[0]),
		success: function(data){
			//alert(data);
			if (data=='<ok>') {
		
				if (action=='add') {

					lan[4]=1;
					selectedLanguages[selectedLanguages.length] = lan;

				} else {

					for(var i=0;i<selectedLanguages.length;i++) {

						if (action=='default') selectedLanguages[i][3]=(selectedLanguages[i][0]==lan[0] ? '1' : '0');
						if (action=='deactivate' && selectedLanguages[i][0]==lan[0]) selectedLanguages[i][4]='0';
						if (action=='reactivate' && selectedLanguages[i][0]==lan[0]) selectedLanguages[i][4]='1';
						if (action=='delete' && selectedLanguages[i][0]==lan[0]) var n = i;
						
					}

					if (action=='delete') selectedLanguages.splice(n,1);

				}

				projectUpdateLanguageBlock();

			}
		}

	});

}

function projectAddLanguage(lan) {

	selectedLanguages[selectedLanguages.length] = lan;

	projectUpdateLanguageBlock();

}

function projectUpdateLanguageBlock() {

	$('#language-list').html('<table>');

	for (var i=0;i<selectedLanguages.length;i++) {

		$('#language-list').html(
			$('#language-list').html() +
			'<tr><td class="cell-language-name'+(selectedLanguages[i][4]!=1 ? '-unused' : '')+'">'+selectedLanguages[i][1]+'</td>'+
			'<td class="cell-language-'+
				(selectedLanguages[i][3]==1 ? 
					'default" title="default language"' : 
					'set-default" " title="make default language" onclick="projectSaveLanguage(\'default\',[\''+selectedLanguages[i][0]+'\'])"'
				)+
			'></td>'+
			'<td class="cell-language-'+
				(selectedLanguages[i][4]==1 ? 
					(selectedLanguages[i][3]==1 ?
						'active-inactive"' :
						'active" title="deactivate language" onclick="projectSaveLanguage(\'deactivate\',[\''+selectedLanguages[i][0]+'\'])"' 
					) : 
					'inactive" title="reactivate language" onclick="projectSaveLanguage(\'reactivate\',[\''+selectedLanguages[i][0]+'\'])"'
				)+
			'"></td>'+
			'<td class="cell-language-delete'+
				(selectedLanguages[i][4]==1 ? 
					'-inactive"' : 
					'" title="delete language" onclick="projectSaveLanguage(\'delete\',[\''+selectedLanguages[i][0]+'\',\''+selectedLanguages[i][1]+'\'])"'
				)+
			'"></td></tr>'
		);

		$('#language-list').html($('#language-list').html()+'</table>');

	}

}






















