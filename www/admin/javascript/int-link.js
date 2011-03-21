var intLinks = Array();
var intLinkLanguage = '';

function intLinkSetLanguage(language) {

	intLinkLanguage = language;

}

function intLinkShowSelector(language) {

	showDialog('',_('Insert internal link'));

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


function intLinkModuleSelectorChange() {

	i = intLinks[$('#module-selector').val()];

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

	tinyMCE.activeEditor.selection.setContent(
		'<span class="internal-link" onclick="goIntLink(\''+controller+'\',\''+url+'\',['+params+'])">'+
		(selection!='' ? selection : label) +
		'</span>'
	);

	$('#dialog-close').click();

}