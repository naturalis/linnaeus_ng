var selectedLanguages = Array();

function projectSaveLanguage(action,lan) {

	if (action == 'delete') {

		if (!allDoubleDeleteConfirm('the language',lan[1])) return;

	}

	$.ajax({
		url: "ajax_interface.php",
		data: {
			'view' : 'languages' ,
			'action' : encodeURIComponent(action) ,
			'id' : encodeURIComponent(lan[0]) ,
			'time' : allGetTimestamp()
		},
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
