var intLinks = Array();
var intLinkLanguage = '';
var intLinkUseJSLinks = false;

function intLinkSetLanguage(language) {

	intLinkLanguage = language;

}

function intLinkShowSelector(language) {

	showDialog(_('Insert internal link'));

	$('#dialog-content-inner').load('../utilities/int_links.php'+(language ? '?language='+language : ''));
	
}

function intLinkStoreLinks(label,controller,url,params) {

	intLinks[intLinks.length] = [label,controller,url,params];

}

function intLinkMakeValueList(param) {

	if (param.language_independent)
		var v = param.values;
	else	
		var v = param.values[intLinkLanguage];
	
	if (v==undefined || v.length==0) return _('no items have been defined in this language');

	var s = '<select id="int-link-selector-'+param.param+'">';

	for(var i=0;i<v.length;i++) {

		s = s + '<option value="'+v[i].id+'">'+(v[i].untranslated==1 ? _(v[i].label) : v[i].label )+'</option>';

	}

	s = s + '</select>';

	return s;

}


function intLinkModuleSelectorChange(index) {

	if (!index)
		i = intLinks[$('#module-selector').val()];
	else
		i = intLinks[index];

	if (!i) return;
	
	$('tr[id*="flex"]').remove();

	if (i[3]) {

		obj = $.parseJSON(i[3]);

		for(var j=0;j<obj.length;j++) {

			$('#int-link-selectors > tbody:last').append(
				'<tr id="flex'+j+'">'+
					'<td>'+obj[j].label+'</td>'+
					'<td>'+ intLinkMakeValueList(obj[j])+'</td>'+
				'</tr>'
			);

		}

	}

}

function intLinkInsertLink() {

	var d = intLinks[$('#module-selector').val()];

	if (!d) return;

	var controller = d[1];
	var url = d[2];
	var params = Array();
	var label = '';
	var i=0;

	$('select[id*="int-link-selector-"]').each(
		function(){
			var name = $(this).attr('id').replace('int-link-selector-','');
			params[params.length] = "'"+addSlashes(name)+':'+addSlashes($(this).val())+"'";
		}
	);

	$('select option:selected').each(
		function(){
			label = label + (i++==0 ? '(' : '') + $(this).text() + ', ';
		}
	);

	label = label.replace(/,\s$/,'')+')';

	var selection = tinyMCE.activeEditor.selection.getContent();


	if (intLinkUseJSLinks) {

		var newContent = 
			'<span class="internal-link" onclick="goIntLink(\''+controller+'\',\''+url+'\',['+params+'])">'+
			(selection!='' ? selection : label) +
			'</span>';
		
	} else {

		var query = '';

		for(var i=0;i<params.length;i++) {

			var d = params[i].replace(/'$/,'').replace(/^'/,'').split(':');
			query = query + d[0]+'='+d[1]+'&';
			
		}

		query = query.replace(/\&$/,'');

		var newContent = 
			'<a class="internal-link" href="../'+controller+'/'+url+(query ? (url.indexOf('?')==-1 ? '?' : '&') + query : '') +'">'+
			(selection!='' ? selection : label) +
			'</a>';
		//alert(newContent);
	}
	

	tinyMCE.activeEditor.selection.setContent(newContent);

	$('#dialog-close').click();

}