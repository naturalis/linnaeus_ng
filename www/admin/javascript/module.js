var moduleUsers = Array();
var moduleModules = Array();
var moduleFreeModules = Array();
var moduleModuleUsers = Array();
var moduleFreeModuleUsers = Array();

function moduleAddProjectModule(module) {
	//[id,name,desc,[active,[id in project]]]
	moduleModules[moduleModules.length] = module;
}

function moduleAddProjectFreeModule(module) {
	//[id,name,active]
	moduleFreeModules[moduleFreeModules.length] = module;
}


function moduleDrawModuleBlock() {

	var b = '<table>'+
			'<tr>'+
				'<td class="cell-module-header-module">'+_('Module')+'</td>'+
				'<td class="cell-module-header-status">'+_('Status')+'</td>'+
				'<td class="cell-module-header-actions" colspan="2">'+_('Actions')+'</td>'+
			'</tr>';
	
	for(var i=0;i<moduleModules.length;i++) {

		b = b +
			'<tr class="tr-highlight">'+
				'<td class="cell-module-title">'+
					'<span class="module-title-'+(moduleModules[i][4]=='-' ? 'unused' : (moduleModules[i][3]=='y' ? 'in-use' : 'inactive'))+'" id="cell-'+moduleModules[i][0]+'d">'+
					'<span class="module-title">'+moduleModules[i][1]+'</span> - '+moduleModules[i][2]+'</span>'+
				'</td>'+
				'<td>'+
					(moduleModules[i][4]=='-' ? _('not part of the project') : _('part of the project') )+
					(moduleModules[i][3]=='y' ? '; '+_('published') : (moduleModules[i][4]!='-' ? '; '+_('unpublished') : '') )+
				'</td>'+
				'<td'+(moduleModules[i][4]=='-' ? '' : ' onclick="'+
						(moduleModules[i][3]=='y' ? 'moduleUnpublishModule('+moduleModules[i][0]+')' : 'modulePublishModule('+moduleModules[i][0]+')')+'"')+'>'+
						(moduleModules[i][4]=='-' ? '' : '[<span class="a">'+(moduleModules[i][3]=='y' ? _('unpublish') : _('publish') )+'</span>]')+
				'</td>'+
				'<td onclick="'+(moduleModules[i][4]=='-' ? 'moduleActivateModule('+moduleModules[i][0]+')' : 'moduleDeleteModule('+moduleModules[i][0]+')' )+'">'+
					'[<span class="a">'+(moduleModules[i][4]=='-' ? _('add') : _('delete') )+'</span>]'+
				'</td>'+
			'</tr>';
	}

	b = b + '</table>';

	$('#module-table-div').html(b);

}

function moduleDrawFreeModuleBlock() {



	var b = '<table>'+
			'<tr>'+
				'<td class="cell-module-header-module">'+_('Module')+'</td>'+
				'<td class="cell-module-header-status">'+_('Status')+'</td>'+
				'<td class="cell-module-header-actions" colspan="2">'+_('Actions')+'</td>'+
			'</tr>';
	
	for(var i=0;i<moduleFreeModules.length;i++) {

		b = b +
			'<tr class="tr-highlight">'+
				'<td class="cell-module-title">'+
					'<span class="module-title-'+(moduleFreeModules[i][2]=='y' ? 'in-use' : 'inactive')+'" id="cell-'+moduleFreeModules[i][0]+'d">'+
					'<span class="module-title">'+moduleFreeModules[i][1]+'</span>'+
				'</td>'+
				'<td>part of the project; '+
					(moduleFreeModules[i][2]=='y' ? _('published') : _('unpublished') )+
				'</td>'+
				'<td onclick="'+(moduleFreeModules[i][2]=='y' ? 'moduleUnpublishFreeModule('+moduleFreeModules[i][0]+')' : 'modulePublishFreeModule('+moduleFreeModules[i][0]+')')+'">'+
					'[<span class="a">'+(moduleFreeModules[i][2]=='y' ? _('unpublish') : _('publish') )+'</span>]'+
				'</td>'+
				'<td onclick="moduleDeleteFreeModule('+moduleFreeModules[i][0]+');">'+
					'[<span class="a">'+_('delete')+'</span>]'+
				'</td>'+
			'</tr>';
	}

	b = b + '</table>';

	$('#free-module-table-div').html(b);
	
	$('#new-input').removeClass();

	if (moduleFreeModules.length >= 5) $('#new-input').addClass('module-new-input-hidden');

}

function moduleActivateModule(id) {

	moduleChangeStatus(id,'module_activate','regular');

}

function moduleDeleteModule(id) {

	moduleChangeStatus(id,'module_delete','regular');

}

function moduleUnpublishModule(id) {

	moduleChangeStatus(id,'module_unpublish','regular');
}

function modulePublishModule(id) {

	moduleChangeStatus(id,'module_publish','regular');

}

function moduleUnpublishFreeModule(id) {

	moduleChangeStatus(id,'module_unpublish','free');

}

function modulePublishFreeModule(id) {

	moduleChangeStatus(id,'module_publish','free');

}

function moduleDeleteFreeModule(id) {

	moduleChangeStatus(id,'module_delete','free');

}

function moduleChangeStatus(id,action,type)
{
	if (action=='module_delete')
	{
		if (type=='regular')
		{
			for (var j=0;j<moduleModules.length;j++)
			{
				if (moduleModules[j][0]==id) var n = moduleModules[j][1];
			}
		} 
		else
		{
			for (var j=0;j<moduleFreeModules.length;j++)
			{
				if (moduleFreeModules[j][0]==id) var n = moduleFreeModules[j][1];
			}
		}
		if (!altKeyDown)
		{
			if (!allDoubleDeleteConfirm('the module',n)) return;
		}
	}

	$.post(
		"ajax_interface.php", 
		{
			'id' : id ,
			'action' : action ,
			'type' : type ,
			'view' : 'modules' ,
			'time' : allGetTimestamp()			
		},
		function(data)
		{
			if (data=='<ok>')
			{
				if (type=='regular')
				{
					//[id,name,desc,[active,[id in project]]]
					for(var i=0;i<moduleModules.length;i++) {
						if (id==moduleModules[i][0]) {
							switch (action) {
								case 'module_activate' :
									moduleModules[i][3] = 'n';
									moduleModules[i][4] = '99';
									break;
								case 'module_delete' :
									moduleModules[i][3] = '-';
									moduleModules[i][4] = '-';
									break;
								case 'module_unpublish' :
									moduleModules[i][3] = 'n';
									break;
								case 'module_publish' :
									moduleModules[i][3] = 'y';
									break;
							}
						}
					}
					moduleDrawModuleBlock();
				} 
				else
				if (type=='free')
				{
					//[id,name,active]
					for(var i=0;i<moduleFreeModules.length;i++)
					{
						if (id==moduleFreeModules[i][0])
						{
							switch (action)
							{
								case 'module_delete' :
									var t = Array();
									for (var j=0;j<moduleFreeModules.length;j++)
									{
										if (moduleFreeModules[j][0]!=id) t[t.length]=moduleFreeModules[j];
									}
									moduleFreeModules = t;
									break;
								case 'module_unpublish' :
									moduleFreeModules[i][2] = 'n';
									break;
								case 'module_publish' :
									moduleFreeModules[i][2] = 'y';
									break;
							}
						}
					}
					moduleDrawFreeModuleBlock();
				}
			}
		}
	);

}

function moduleAddUser(userId,user,role) {
	moduleUsers[moduleUsers.length]=[userId,user,role];
}


function moduleAddModule(type,moduleId,module,state,collaborators) {
	if (type=='regular')
		moduleModules[moduleModules.length]=[moduleId,module,state,collaborators,'hidden'];
	else
		moduleFreeModules[moduleFreeModules.length]=[moduleId,module,state,collaborators,'hidden'];
}

function moduleAddModuleUser(type,moduleId,userId,state) {
	if (type=='regular')
		moduleModuleUsers[moduleModuleUsers.length]=[moduleId,userId,state];
	else
		moduleFreeModuleUsers[moduleFreeModuleUsers.length]=[moduleId,userId,state];
}

function moduleBuildModuleUserBlock(type) {
	var b = '';

	if (type=='free') {
		var theseModules = moduleFreeModules;
		var theseModuleUsers = moduleFreeModuleUsers;
		var q = 'f';
	} else {
		var theseModules = moduleModules;
		var theseModuleUsers = moduleModuleUsers;
		var q = '';
	}

	for (var i=0;i<theseModules.length;i++) {
	
		b = b +'<tr id="row-'+q+theseModules[i][0]+'" class="tr-highlight">';

		if (theseModules[i][2]=='y') {
			b = b + '<td class="cell-module-title-in-use">';
		} else {
			b = b + '<td class="cell-module-title-inactive">';
		}
		
		b = b +'<span id="cell-'+theseModules[i][0]+'d"><span class="cell-module-title">'+theseModules[i][1]+'</span></span></td>'+
				'<td><span onclick="moduleToggleModuleUserBlock(\''+q+theseModules[i][0]+'\');" class="modusers-block-toggle" id="toggle-'+q+theseModules[i][0]+'">+</span> '+
				'<span id="cell-'+q+theseModules[i][0]+'n">'+theseModules[i][3]+'</span> collaborators</td></tr>'+
				'<tr id="users-'+q+theseModules[i][0]+'" class="modusers-block'+
				(theseModules[i][4]=='hidden' ? '-hidden' : '')+
				'"><td colspan="2"><table>';
						
		for (var j=0;j<moduleUsers.length;j++) {
	
			b = b +'<tr><td class="modusers-block-buffercell"></td>';

			for(var k=0;k<theseModuleUsers.length;k++) {
	
				if (theseModuleUsers[k][0]==theseModules[i][0] && theseModuleUsers[k][1]==moduleUsers[j][0]) {

					if (theseModuleUsers[k][2]==1) {

						b = b +'<td id="cell-'+q+theseModules[i][0]+'-'+moduleUsers[j][0]+'a" class="cell-module-user-active">'+
							moduleUsers[j][1]+'</td><td>'+moduleUsers[j][2]+'</td>'+
							'<td title="remove collaborator" class="cell-moduser-remove"'+
							'id="cell-'+q+theseModules[i][0]+'-'+moduleUsers[j][0]+'b"'+
							'onclick="moduleChangeModuleUserStatus('+theseModules[i][0]+','+moduleUsers[j][0]+',\'remove\',\''+type+'\')">'+
							'[<span class="a">'+_('remove as collaborator')+'</span>]</td>';
						
					} else {
					
						b = b +'<td id="cell-'+q+theseModules[i][0]+'-'+moduleUsers[j][0]+'a" class="cell-module-user-inactive">'+
							moduleUsers[j][1]+'</td><td>'+moduleUsers[j][2]+'</td>'+
							'<td title="add collaborator" class="cell-moduser-inactive"'+
							'id="cell-'+q+theseModules[i][0]+'-'+moduleUsers[j][0]+'b"'+
							'onclick="moduleChangeModuleUserStatus('+theseModules[i][0]+','+moduleUsers[j][0]+',\'add\',\''+type+'\')">'+
							'[<span class="a">'+_('add as collaborator')+'</span>]</td>';
					}
					
					b = b + '<td onclick="window.open(\'../users/edit.php?id='+moduleUsers[j][0]+'\',\'_self\');">'+
					'[<span class="a">'+_('edit user')+'</span>]</td>';

				}

			}

			b = b +'</tr>';

		}

		b = b + '<tr><td></td><td onclick="moduleChangeModuleUserStatus('+theseModules[i][0]+',moduleUsers,\'add\',\''+type+'\');">'+
			'[<span class="a">'+_('add all collaborators')+'</span>]</td></tr>'+		
			'</table></td></tr>';
	}
	
	if (b=='') {
		b = type == 'free' ? _('no free modules have been defined') : _('no modules have been defined') ;
		b = b + '; '+sprintf(_('go %shere%s to define modules'),'<a href="modules.php">','</a>')
	} else {
		b = '<table>'+b+'</table>';
	}

	if (type == 'free')
		$('#free-module-table').html(b);
	else
		$('#module-table').html(b);

}

function moduleChangeModuleUserStatus(module,user,action,type) {

	$.ajax({
		url:"ajax_interface.php",
		data: {
			view : 'collaborators' ,
			action : action ,
			id : module ,
			user : user ,
			type : type ,
			time : allGetTimestamp()
		},
		success: function(data){

			if (data=='<ok>') {

				if (type=='free') {
					var theseModules = moduleFreeModules;
					var theseModuleUsers = moduleFreeModuleUsers;
				} else {
					var theseModules = moduleModules;
					var theseModuleUsers = moduleModuleUsers;
				}

				for (var i=0;i<theseModuleUsers.length;i++) {
					if (isArray(user) && theseModuleUsers[i][0]==module) {
						theseModuleUsers[i][2]=1;
					} else {
						if (theseModuleUsers[i][0]==module && theseModuleUsers[i][1]==user) {
							if (theseModuleUsers[i][2]==1) {
								theseModuleUsers[i][2]=0;
							} else {
								theseModuleUsers[i][2]=1;
							}
						}
					}
				}

				for (var i=0;i<theseModules.length;i++) {
					if (theseModules[i][0]==module) {
						if (isArray(user)) {
							theseModules[i][3] = moduleUsers.length;
						} else {
							theseModules[i][3] = (action == 'add' ? theseModules[i][3] +1 : theseModules[i][3] - 1);	
						}
					}
				}

				moduleBuildModuleUserBlock(type);

			}

		} 
	});

}

function moduleToggleModuleUserBlock(id) {

	classname = $('#users-'+id).attr('class');
	
	if (classname=='modusers-block-hidden') {
		$('#users-'+id).removeClass().addClass('modusers-block');
		$('#toggle-'+id).html('-');
	} else {
		$('#users-'+id).removeClass().addClass('modusers-block-hidden');
		$('#toggle-'+id).html('+');
	}

	if (id.substr(0,1)=='f') {
		var theseModules = moduleFreeModules;
		var q = 'f';
	} else {
		var theseModules = moduleModules;
		var q = '';
	}

	for (var i=0;i<theseModules.length;i++) {
		if (q+theseModules[i][0]==id) {
			if (theseModules[i][4]=='hidden') {
				theseModules[i][4]='visible';
			} else {
				theseModules[i][4]='hidden';
			}		
		}
	}	



}