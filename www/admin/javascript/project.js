var selectedLanguages = Array();

function projectSaveLanguage(action,lan) {

	if (action == 'delete') {

		if (!allDoubleDeleteConfirm(_('the language'),lan[1])) return;

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

function projectUpdateLanguageBlock() {

	b = '<table><tr><th>'+_('Language')+
			'</th><th>'+_('Default')+
			'</th><th>'+_('Translation')+
			'</th><th>'+_('Status')+
			'</th><th>'+_('Delete')+
		'</th></tr>';

	for (var i=0;i<selectedLanguages.length;i++) {

		b = b+
			'<tr class="tr-highlight" style="vertical-align:top">'+
				'<td class="cell-language-name'+(selectedLanguages[i][4]!=1 ? '-unused' : '')+'">'+selectedLanguages[i][1]+'</td>'+
				'<td style="padding-right:15px">['+
					(selectedLanguages[i][3]==1 ? 
						_('current') : 
						'<span class="a" onclick="projectSaveLanguage(\'default\',[\''+selectedLanguages[i][0]+'\'])">'+_('make default')+'</span>' )+
			']</td>'+
			'<td style="padding-right:15px">'+
				'<label><input name="translate-'+selectedLanguages[i][0]+'" type="radio" '+(selectedLanguages[i][5]==0 ? 'checked="checked"' : '')+' onclick="projectSaveLanguage(\'untranslated\',[\''+selectedLanguages[i][0]+'\'])" />'+_('to be translated')+'</label><br />'+
				'<label><input name="translate-'+selectedLanguages[i][0]+'" type="radio" '+(selectedLanguages[i][5]!=0 ? 'checked="checked"' : '')+' onclick="projectSaveLanguage(\'translated\',[\''+selectedLanguages[i][0]+'\'])" />'+_('translated')+'</label>' +
			'</td>'+
			'<td style="padding-right:15px">'+
				(selectedLanguages[i][4]==1 ?
					_('published') :
					_('unpublished')
				)+'&nbsp;'+
				'[<span class="a" onclick="projectSaveLanguage('+
				(selectedLanguages[i][4]==1 ?
					'\'deactivate\',[\''+selectedLanguages[i][0]+'\'])">'+_('unpublish') :
					'\'reactivate\',[\''+selectedLanguages[i][0]+'\'])">'+_('publish')
				)+'</span>]'+
			'</td>'+
			'<td>'+
				(selectedLanguages[i][4]==1 ? 
					'['+_('delete')+']' : 
					'[<span class="a" onclick="projectSaveLanguage(\'delete\',[\''+selectedLanguages[i][0]+'\',\''+selectedLanguages[i][1]+'\'])">'+_('delete')+'</span>]'
				)+
			'</td></tr>'


	}

	b = b + '</table>';

	$('#language-list').html(b);

}
