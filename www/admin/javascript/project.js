var selectedLanguages = Array();

function projectSaveLanguage(action,lan) {

	if (action == 'delete') {

		if (!allDoubleDeleteConfirm('the language',lan[1])) return;

	}

	$.ajax({
		url: "ajax_interface.php",
		data: {
			'view' : 'languages' ,
			'action' : action ,
			'id' : lan[0] ,
			'time' : allGetTimestamp()
		},
		success: function(data){
			//alert(data);
			if (data=='<ok>') {
		
				if (action=='add') {

					lan[4]=0;
					lan[5]=0;
					selectedLanguages[selectedLanguages.length] = lan;

				} else {

					for(var i=0;i<selectedLanguages.length;i++) {

						if (action=='default') selectedLanguages[i][3]=(selectedLanguages[i][0]==lan[0] ? '1' : '0');
						if (action=='deactivate' && selectedLanguages[i][0]==lan[0]) selectedLanguages[i][4]='0';
						if (action=='reactivate' && selectedLanguages[i][0]==lan[0]) selectedLanguages[i][4]='1';
						if (action=='translated' && selectedLanguages[i][0]==lan[0]) selectedLanguages[i][5]=1;
						if (action=='untranslated' && selectedLanguages[i][0]==lan[0]) selectedLanguages[i][5]=0;
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

}
//untranslated translated
function projectUpdateLanguageBlock() {

	b = '<table><tr><th>Language</th><th>Default</th><th>Translation</th><th>Status</th><th>Delete</th></tr>';

	for (var i=0;i<selectedLanguages.length;i++) {

		b = b+
			'<tr>'+
				'<td class="cell-language-name'+(selectedLanguages[i][4]!=1 ? '-unused' : '')+'">'+selectedLanguages[i][1]+'</td>'+
				'<td style="padding-right:15px">['+
					(selectedLanguages[i][3]==1 ? 
						'current' : 
						'<span class="pseudo-a" onclick="projectSaveLanguage(\'default\',[\''+selectedLanguages[i][0]+'\'])">make default</span>' )+
			']</td>'+
			'<td style="padding-right:15px">'+
				'<label><input name="translate-'+selectedLanguages[i][0]+'" type="radio" '+(selectedLanguages[i][5]==0 ? 'checked="checked"' : '')+' onclick="projectSaveLanguage(\'untranslated\',[\''+selectedLanguages[i][0]+'\'])" />needs to be translated</label>'+
				'<label><input name="translate-'+selectedLanguages[i][0]+'" type="radio" '+(selectedLanguages[i][5]!=0 ? 'checked="checked"' : '')+' onclick="projectSaveLanguage(\'translated\',[\''+selectedLanguages[i][0]+'\'])" />translated</label>' +
			'</td>'+
			'<td style="padding-right:15px">'+
				'[<span class="pseudo-a" onclick="projectSaveLanguage('+
				(selectedLanguages[i][4]==1 ?
					'\'deactivate\',[\''+selectedLanguages[i][0]+'\'])">unpublish' :
					'\'reactivate\',[\''+selectedLanguages[i][0]+'\'])">publish'
				)+'</span>]'+
			'</td>'+
			'<td>'+
				(selectedLanguages[i][4]==1 ? 
					'[delete]' : 
					'[<span class="pseudo-a" onclick="projectSaveLanguage(\'delete\',[\''+selectedLanguages[i][0]+'\',\''+selectedLanguages[i][1]+'\'])">delete</span>]'
				)+
			'</td></tr>'


	}

	b = b + '</table>';

	$('#language-list').html(b);

}
