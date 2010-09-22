function q(m) {
	var e = document.getElementById('debug-message');
	e.innerHTML = m;
}

function allGetTimestamp() {

	var tsTimeStamp= new Date().getTime();

	return tsTimeStamp;

}

function allTableColumnSort(col) {

	var e = document.getElementById('key');
	e.value = col;
	e = document.getElementById('sortForm');
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
		'Are you sure you want to delete '+element+' "'+name+'"?\n'+
		'All corresponding data will be irreversibly deleted.'
		)) {
	
		return (confirm(
			'Final confirmation:\n'+
			'Are you sure you want to delete '+element+' "'+name+'"?\n'+
			'ALL CORRESPONDING DATA WILL BE IRREVERSIBLY DELETED.'
			));
	
	} else {

		return false;

	}

}

function allSetMessage(msg,err) {

	$('#message-container').show();
	$('#message-container').html(msg).delay(1000).fadeOut(500);

}

var heartbeatUserId = false;
var heartbeatApp = false;
var heartbeatCtrllr = false;
var heartbeatView = false;
var heartbeatParams = Array();
var heartbeatFreq = 120000;
var autosaveFreq = 120000;

function allSetHeartbeatFreq(freq) {

	heartbeatFreq = freq;

}

function allSetHeartbeat(userid,app,ctrllr,view,params) {

	if (userid) heartbeatUserId = userid;
	if (app) heartbeatApp = app;
	if (ctrllr) heartbeatCtrllr = ctrllr;
	if (view) heartbeatView = view;
	if (params) heartbeatParams = params;

	$.ajax({
		url : "../utilities/ajax_interface.php",
		type: "GET",
		data : ({
			'user_id' : heartbeatUserId ,
			'app' : heartbeatApp ,
			'ctrllr' : heartbeatCtrllr ,
			'view' : heartbeatView ,
			'params' : heartbeatParams ,
			'action' : 'heartbeat',
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			//alert(data);
		}
	});

	setTimeout ("allSetHeartbeat()", heartbeatFreq);

}

function allSetAutoSaveFreq(freq) {

	autosaveFreq = freq;

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

	$.ajax({ url:
				"ajax_interface.php?f="+encodeURIComponent(id)+
				"&v="+encodeURIComponent(values)+
				"&t="+encodeURIComponent(tests)+
				"&time="+allGetTimestamp()+
				(idti ? "&i="+encodeURIComponent(idti) : "" ),
			success: function(data){
				$('#'+id+'-message').html(data);
				error = data.search(/\<error\>/gi)!=-1;
				$('#'+id+'-message').removeClass().addClass(error ? 'message-error' : 'message-no-error');
      	}
	});

}

var moduleModules = Array();
var moduleFreeModules = Array();

function moduleAddModule(module) {
	//[id,name,desc,[active,[id in project]]]
	moduleModules[moduleModules.length] = module;
}

function moduleAddFreeModule(module) {
	//[id,name,active]
	moduleFreeModules[moduleFreeModules.length] = module;
}

function moduleDrawModuleBlock() {

	var b = '<table>'+
			'<tr>'+
				'<td class="cell-module-header-module">Module</td>'+
				'<td class="cell-module-header-status">Status</td>'+
				'<td class="cell-module-header-actions" colspan="2">Actions</td>'+
			'</tr>';
	
	for(var i=0;i<moduleModules.length;i++) {

		b = b +
			'<tr class="tr-highlight">'+
				'<td class="cell-module-title">'+
					'<span class="module-title-'+(moduleModules[i][4]=='-' ? 'unused' : (moduleModules[i][3]=='y' ? 'in-use' : 'inactive'))+'" id="cell-'+moduleModules[i][0]+'d">'+
					'<span class="module-title">'+moduleModules[i][1]+'</span> - '+moduleModules[i][2]+'</span>'+
				'</td>'+
				'<td>'+
					(moduleModules[i][4]=='-' ? 'not' : '' )+
					' part of the project'+
					(moduleModules[i][3]=='y' ? '; published' : (moduleModules[i][4]!='-' ? '; unpublished' : '') )+
				'</td>'+
				'<td'+(moduleModules[i][4]=='-' ? '' : ' onclick="'+
						(moduleModules[i][3]=='y' ? 'moduleUnpublishModule('+moduleModules[i][0]+')' : 'modulePublishModule('+moduleModules[i][0]+')')+'"')+'>'+
						(moduleModules[i][4]=='-' ? '' : '[<span class="pseudo-a">'+(moduleModules[i][3]=='y' ? 'unpublish' : 'publish')+'</span>]')+
				'</td>'+
				'<td onclick="'+(moduleModules[i][4]=='-' ? 'moduleActivateModule('+moduleModules[i][0]+')' : 'moduleDeleteModule('+moduleModules[i][0]+')' )+'">'+
					'[<span class="pseudo-a">'+(moduleModules[i][4]=='-' ? 'add' : 'delete' )+'</span>]'+
				'</td>'+
			'</tr>';
	}

	b = b + '</table>';

	$('#module-table-div').html(b);

}

function moduleDrawFreeModuleBlock() {

	var b = '<table>'+
			'<tr>'+
				'<td class="cell-module-header-module">Module</td>'+
				'<td class="cell-module-header-status">Status</td>'+
				'<td class="cell-module-header-actions" colspan="2">Actions</td>'+
			'</tr>';
	
	for(var i=0;i<moduleFreeModules.length;i++) {

		b = b +
			'<tr class="tr-highlight">'+
				'<td class="cell-module-title">'+
					'<span class="module-title-'+(moduleFreeModules[i][2]=='y' ? 'in-use' : 'inactive')+'" id="cell-'+moduleFreeModules[i][0]+'d">'+
					'<span class="module-title">'+moduleFreeModules[i][1]+'</span>'+
				'</td>'+
				'<td>'+
					(moduleFreeModules[i][2]=='y' ? 'published' : 'unpublished')+
				'</td>'+
				'<td onclick="'+(moduleFreeModules[i][2]=='y' ? 'moduleUnpublishFreeModule('+moduleFreeModules[i][0]+')' : 'modulePublishFreeModule('+moduleFreeModules[i][0]+')')+'">'+
					'[<span class="pseudo-a">'+(moduleFreeModules[i][2]=='y' ? 'unpublish' : 'publish')+'</span>]'+
				'</td>'+
				'<td onclick="moduleDeleteFreeModule('+moduleFreeModules[i][0]+');">'+
					'[<span class="pseudo-a">delete</span>]'+
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

function moduleChangeStatus(id,action,type) {

	if (action == 'module_delete') {
		
		if (type=='regular') {
		
			for (var j=0;j<moduleModules.length;j++) {
				if (moduleModules[j][0]==id) var n = moduleModules[j][1];
			}
	
		} else {

			for (var j=0;j<moduleFreeModules.length;j++) {
				if (moduleFreeModules[j][0]==id) var n = moduleFreeModules[j][1];
			}

		}

		if (!allDoubleDeleteConfirm('the module',n)) return;

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
		function(data){
			if (data=='<ok>') {
				if (type=='regular') {
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
				} else
				if (type=='free') {
					//[id,name,active]
					for(var i=0;i<moduleFreeModules.length;i++) {
						if (id==moduleFreeModules[i][0]) {
							switch (action) {
								case 'module_delete' :
									var t = Array();
									for (var j=0;j<moduleFreeModules.length;j++) {
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
				"&u="+encodeURIComponent(u)+
				"&time="+allGetTimestamp(),
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

		if (!allDoubleDeleteConfirm('the language',lan[1])) return;

	}

	$.ajax({ url:"ajax_interface.php?v=languages"+
				"&a="+encodeURIComponent(action)+
				"&i="+encodeURIComponent(lan[0])+
				"&time="+allGetTimestamp(),
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


var taxonActivePageTitle = false;
var taxonActiveLanguage = false;
var taxonNewLanguage = false;
var taxonLanguages = Array();
var taxonActivePage = false;
var taxonPages = Array();
var taxonPageStates = Array();
var taxonPublishState = false;
var taxonInitAutoSave = true;
var taxonSaveType = 'auto';


function taxonSetActivePageTitle(page) {

	taxonActivePageTitle = page;

}

function taxonPageDelete(page,name) {

	if (!allDoubleDeleteConfirm('the page',name)) return;

	$.post(
		"ajax_interface.php", 
		{
			'id' : page ,
			'action' : 'delete_page' ,
			'time' : allGetTimestamp()
		},
		function() {
			$('#theForm').submit();
		}
	);

}

function taxonPageTitleSave(page) {

	title = $('#name-'+page[0]+'-'+page[1]).val();

	$.post(
		"ajax_interface.php", 
		{
			'id' : page[0],
			'action' : 'save_page_title' ,
			'title' : title , 
			'language' : page[1] ,
			'time' : allGetTimestamp()
		},
		function(data){
			allSetMessage(data);
		}
	);

}

function taxonAddLanguage(lan) {
	//[id,name,default?]
	taxonLanguages[taxonLanguages.length] = lan;

}

function taxonUpdateLanguageBlock() {

	$buffer = '<table class="taxon-language-table"><tr>';

	for (var i=0;i<taxonLanguages.length;i++) {
	
		$buffer = $buffer+
			'<td class="taxon-language-cell'+
			(taxonLanguages[i][0]==taxonActiveLanguage ? '-active' : '" onclick="taxonSwitchLanguage('+taxonLanguages[i][0]+');' )+
			'">'+
			taxonLanguages[i][1]+
			(taxonLanguages[i][2]==1 ?  ' *' : '')+
			'</td>';
	}

	$buffer = $buffer + '</tr></table>';

	$('#taxon-language-table-div').html($buffer);

}

function taxonSwitchLanguage(language) {

	taxonSaveData('taxonGetData('+language+','+taxonActivePage+')');

}

function taxonAddPage(page) {
	//[id,[names],default?]
	taxonPages[taxonPages.length] = page;
}

function taxonDrawPageBlock() {

	$buffer = '<table class="taxon-pages-table"><tr>';

	for (var i=0;i<taxonPages.length;i++) {

		$buffer = $buffer+
			'<td class="taxon-page-cell' +
			(taxonPages[i][0]==taxonActivePage ? '-active' : '" onclick="taxonSwitchPage('+taxonPages[i][0]+');' ) +
			'">' +
			(taxonPages[i][1][taxonActiveLanguage].length == 0 ? '('+taxonPages[i][1][-1]+')' : taxonPages[i][1][taxonActiveLanguage] ) +
			(taxonPages[i][2]==1 ?  ' *' : '') +
			'<br /><span class="taxon-page-publish-state">' +
			(taxonPageStates[taxonPages[i][0]]==1 ? 'published' : (taxonPageStates[taxonPages[i][0]] == 0 ? 'unpublished' : 'empty')) +
			'</span></td>';
	}

	$buffer = $buffer + '</tr></table>';

	$('#taxon-pages-table-div').html($buffer);

}


function taxonUpdatePageBlock() {

	$.ajax({
		url: "ajax_interface.php",
		data: {
			'id' : $('#taxon_id').val() ,
			'action' : 'get_page_states' ,
			'language' : taxonActiveLanguage ,
			'time' : allGetTimestamp()
		},
		success:
			function(data){
				for(var i=0;i<taxonPageStates.length;i++) {
					taxonPageStates[i]=-1;
				}
				if (data) {
					obj = $.parseJSON(data);
					for(var i=0;i<obj.length;i++) {
						taxonPageStates[obj[i].page_id] = obj[i].publish;
					}
				}
				taxonDrawPageBlock();
			}
			
		}
	);

}

function taxonSwitchPage(page) {

	taxonSaveData('taxonGetData('+taxonActiveLanguage+','+page+')');

}

function taxonClose() {

	taxonSaveData("window.open('list.php','_top')");

}

function taxonSaveDataManual() {

	taxonSaveType = 'manual';

	taxonSaveData();

}

function taxonSaveData(execafter,sync) {

	/*
	prompt('url',
		'ajax_interface.php?id='+$('#taxon_id').val()+
		'&action=save_taxon'+
		'&name='+$('#taxon-name-input').val()+
		'&content=content'+
		'&language='+taxonActiveLanguage +
		'&page='+taxonActivePage+
		'&save_type='+taxonSaveType);
	*/

	$.ajax({
		url : "ajax_interface.php",
		data : ({
			'id' : $('#taxon_id').val() ,
			'action' : 'save_taxon' ,
			'name' : $('#taxon-name-input').val() , 
			'content' : tinyMCE.get('taxon-content').getContent() , 
			'language' : taxonActiveLanguage ,
			'page' : taxonActivePage ,
			'save_type' : taxonSaveType ,
			'time' : allGetTimestamp()	
		}),
		type: "POST",
		success: function(data){
			if (data.indexOf('id=')!=-1) {
				$('#taxon_id').val(data.replace('id=',''));
				allSetMessage('saved');
			} else
			if (data.length>0) {
				alert(data.replace('<error>',''));
			}
			taxonUpdatePageBlock();
			if (execafter) eval(execafter);
			taxonSaveType = 'auto';
		}
	});

}

function taxonGetData(language,page) {

	if ($('#taxon_id').val().length==0) return;
/*
	prompt('url',"ajax_interface.php"+
			'?id=' + $('#taxon_id').val() +
			'&action=get_taxon' +
			'&language='+language +
			'&page='+page +
			'&time='+allGetTimestamp()			
	);
*/
	$.post(
		"ajax_interface.php", 
		{
			'id' : $('#taxon_id').val() ,
			'action' : 'get_taxon' ,
			'language' : language ,
			'page' : page ,
			'time' : allGetTimestamp()			
		},
		function(data){
			//alert(data);
			obj = $.parseJSON(data);
			$('#taxon-name-input').val(obj.title ? obj.title : '');
			tinyMCE.get('taxon-content').setContent(obj.content ? obj.content : '');
			taxonActiveLanguage = obj.language_id;
			taxonActivePage = obj.page_id;
			taxonPublishState = obj.publish;
			taxonUpdateLanguageBlock();
			taxonUpdatePageBlock();
			taxonDrawPublishBlock();
		}
	);

}

function taxonDeleteData(language) {

	if ($('#taxon_id').val().length==0) return;

	if (!allDoubleDeleteConfirm('all content in all languages for taxon',$('#taxon-name-input').val())) return;

	$.post(
		"ajax_interface.php", 
		{
			'id' : $('#taxon_id').val() ,
			'action' : 'delete_taxon' ,
			'language' : language ,
			'page' : taxonActivePage ,
			'time': allGetTimestamp()	
		},
		function(data){
			window.open('list.php','_top');
		}
	);

}

function taxonConfirmSaveOnUnload() {
	return;
	
	// issue: jquery synchronous ajax call doesn't seem to work, so this is pointless:
	if (confirm('Do you want to save your changes before you leave this page?')) {

		taxonSaveData(false,true);

	}

}

function taxonSetHeartbeat(userid,app,ctrllr,view) {

	var params = Array();

	params[0] = ['taxon_id',$('#taxon_id').val()];

	heartbeatParams = params;

	allSetHeartbeat(userid,app,ctrllr,view);

}

function taxonClearAllUsageCells() {

	$("td[id*='usage']").html('');

}

function taxonCheckLockOutStates() {

	$.ajax({
		url : "../utilities/ajax_interface.php",
		type: "GET",
		data : ({
			'action' : 'get_taxa_edit_states',
			'time': allGetTimestamp()
		}),
		success : function (data) {
			taxonClearAllUsageCells();
			if (data) {
				obj = $.parseJSON(data);
				for(var i=0;i<obj.length;i++) {
					if (obj[i].first_name.length > 0 || obj[i].last_name.length > 0) {
						$('#usage-'+obj[i].taxon_id).html(obj[i].first_name+' '+obj[i].last_name);
					}
				}
			}
		}
	});

	setTimeout ("taxonCheckLockOutStates()", heartbeatFreq/2);

}

function taxonPublishContent(state) {
	
	if (state==1) taxonSaveData();

	$.ajax({
		url : "ajax_interface.php",
		data : ({
			'id' : $('#taxon_id').val() ,
			'action' : 'publish_content' ,
			'language' : taxonActiveLanguage ,
			'page' : taxonActivePage ,
			'state' : state ,
			'time' : allGetTimestamp()	
		}),
		type: "GET",
		success: function(data){
			if (data=='<ok>') {
				taxonPublishState = state;
				taxonDrawPublishBlock();
				taxonUpdatePageBlock();
			}
		}
	});

}

function taxonDrawPublishBlock() {

	if (taxonPublishState=='1')
		$('#taxon-publish-table-div').html(
			'This page has been published. '+
			'<input type="button" value="unpublish" onclick="taxonPublishContent(0);" />'
		);
	else
		$('#taxon-publish-table-div').html(
			'This page has not been published. '+
			'<input type="button" value="publish" onclick="taxonPublishContent(1);" />'
		);

}

function taxonRunAutoSave() {

	if (!taxonInitAutoSave) taxonSaveData();

	taxonInitAutoSave = false;

	setTimeout("taxonRunAutoSave()", autosaveFreq);

}

function taxonGetUndo() {

	$.post(
		"ajax_interface.php", 
		{
			'id' : $('#taxon_id').val() ,
			'action' : 'get_taxon_undo' ,
			'language' : taxonActiveLanguage ,
			'page' : taxonActivePage ,
			'time' : allGetTimestamp()			
		},
		function(data){
			if (data) {
				obj = $.parseJSON(data);
				$('#taxon-name-input').val(obj.title ? obj.title : '');
				tinyMCE.get('taxon-content').setContent(obj.content ? obj.content : '');
				taxonActiveLanguage = obj.language_id;
				taxonActivePage = obj.page_id;
				taxonPublishState = obj.publish;
				taxonUpdateLanguageBlock();
				taxonUpdatePageBlock();
				taxonDrawPublishBlock();
				allSetMessage('recovered');
			} else {
				allSetMessage('cannot undo');
			}
		}
	);

}

