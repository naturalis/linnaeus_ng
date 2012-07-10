var glossAddedSynonyms = Array();
var glossThisTerm;


function glossAddSynonymToList(synonym) {

	if (!synonym) synonym = $('#synonym').val().trim();

	var add = true;

	for(var i=0;i<glossAddedSynonyms.length;i++) {

		if (glossAddedSynonyms[i] == synonym) {

			add = false;

			break;

		}

	}

	if (add) { 

		glossAddedSynonyms[glossAddedSynonyms.length] = synonym;

		glossUpdateSynonyms();
		
		$('#synonym').val('');

	}

}

function glossUpdateSynonyms() {

	var b = '';
	
	for(var i=0;i<glossAddedSynonyms.length;i++) {
	
		b = b + '<span style="cursor:pointer" ondblclick="glossRemoveSynonymFromList('+i+')">'+glossAddedSynonyms[i]+'</span><br />';
	
	}
	
	$('#synonyms').html(b);

}

function glossRemoveSynonymFromList(id) {

	if (!confirm('Are you sure?')) return;

	var t = Array();

	for(var i=0;i<glossAddedSynonyms.length;i++) {

		if (i != id) {

			t[t.length] = glossAddedSynonyms[i];

		}

	}
	
	glossAddedSynonyms = t;

	glossUpdateSynonyms();

}

function glossCheckForm(ele) {

	if ($('#term').val().length==0) {

		alert(_('A term is required.'));
		
		$('#term').focus();

	} else 
	if (tinyMCE.get('definition').getContent().length==0) {

		alert(_('A definition is required.'));
		
		$('#definition').focus();

	} else {

		for(var i=0;i<glossAddedSynonyms.length;i++) {

			$("#theForm").append('<input type="hidden" name="synonyms[]" value="'+ encodeURI(glossAddedSynonyms[i])+'">');

		}

		$("#definition").val(tinyMCE.get('definition').getContent());

		$(ele).closest("form").submit();

	}

}

function glossDelete() {

	if (!allDoubleDeleteConfirm(_('the term'),glossThisTerm)) return;
	
	$('#action').val('delete');

	$('#theForm').submit();

}


function glossMediaDelete(id) {

	if (!confirm(_('Are you sure?')));

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'delete_media' ,
			'id' : id ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			if(data=='<ok>') {
				$('tr[id^=media-row-'+id+']').remove();
			}			
		}
	});
	

}


var taxonMediaSaveButtonClicked = false;
var taxonMediaDescBeforeEdit = false;
var glossMediaFiles = Array();

function glossMediaFileStore(file) {

	glossMediaFiles[glossMediaFiles.length] = file;

}

function glossDrawLanguages(fnc,includeDef) {

	if (allLanguages.length<=1) return;

	var buffer = '';

	for (var i=0;i<allLanguages.length;i++) {

		if (allLanguages[i][2]!=1 || includeDef==true) {
			buffer = buffer+
				'<span class="project-language'+
				(allLanguages[i][0]==allActiveLanguage ? 
					'-active"' : 
					'" class="a" onclick="'+fnc+'('+allLanguages[i][0]+');' 
				)+
				'">'+
				allLanguages[i][1]+
				'</span>&nbsp;';
		}
		
		if (allLanguages[i][0]==allDefaultLanguage) var def = allLanguages[i][1];
	}
	
	

//	$('#taxon-language-other-language').html(buffer);
	$('[id^="taxon-language-other-language"]').html(buffer);

//	$('#taxon-language-default-language').html(def);
	$('[id^="taxon-language-default-language"]').html(def);

}

function glossMediaSaveDesc(ele,id) {

	var val = $('#'+ele).val();

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'save_media_desc' ,
			'id' : id ,
			'description' : val ,
			'language' : allActiveLanguage ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			if(data=='<ok>') allSetMessage(_('saved'));		
		}
	});

}

function glossMediaGetDescriptions() {

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'get_media_descs' ,
			'id' : $('#gloss_id').val() ,
			'language' : allActiveLanguage ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			if (data) {
				obj = $.parseJSON(data);
				if (obj) {
					for(var i=0;i<obj.length;i++) {
						$('#media-'+obj[i].id).val(obj[i].description);
					}
				}
			}			
			allHideLoadingDiv();
		}
	});

}

function glossMediaChangeLanguage(lan) {

	allShowLoadingDiv();
	glossMediaSaveDesc();
	allActiveLanguage = lan;
	glossDrawLanguages('glossMediaChangeLanguage',true);
	glossMediaGetDescriptions();

}