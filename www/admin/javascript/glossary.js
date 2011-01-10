var glossAddedSynonyms = Array();
var glossThisTerm;


function glossAddSynonymToList(synonym) {

	if (!synonym) {
	
		synonym = $('#synonym').val().trim();

	}

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
	if ($('#definition').val().length==0) {

		alert(_('A definition is required.'));
		
		$('#definition').focus();

	} else {

		for(var i=0;i<glossAddedSynonyms.length;i++) {

			$("#theForm").append('<input type="hidden" name="synonyms[]" value="'+ encodeURIComponent(glossAddedSynonyms[i])+'">');

		}

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
				$('#media-row-'+id).remove();
			}			
		}
	});
	

}
