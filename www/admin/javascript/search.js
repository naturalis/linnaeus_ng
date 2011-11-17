function searchCheckModules() {
}



function searchDoReplaceAction(id,action) {

	if (!id) return;

	$.ajax({
		url : "ajax_interface_search.php",
		type: "POST",
		data : ({
			'action' : action ,
			'id' : id ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			obj = $.parseJSON(data);
			if (obj) searchParseResult(obj,id);
		}
	});
	

}

function searchParseResult(obj,id) {

	if (id=='*' && (obj[1]=='replaced' || obj[1]=='skipped')) {

		$('div[id^="replace-"]').remove();
		//$('input[id^="button-replace-"]').attr("disabled", "true");
		//$('input[id^="button-skip-"]').attr("disabled", "true");
		$('input[id^="button-replace-"]').remove();
		$('input[id^="button-skip-"]').remove();

	} else
	if (id.substring(0,7)=='module:' && (obj[1]=='replaced' || obj[1]=='skipped')) {
	
		var module = id.substring(7).toLowerCase().replace(/\s/gi,'_');

		$('div[id*="-'+module+'-"]').remove();
		//$('input[id*="-'+module+'"]').attr("disabled", "true");
		//$('input[id*="-'+module+'"]').attr("disabled", "true");
		$('input[id*="-'+module+'"]').remove();
		$('input[id*="-'+module+'"]').remove();

	}
 	else
	if (!isNaN(id) && (obj[1]=='replaced' || obj[1]=='skipped')) {

		$('div[id*="-id-'+id+'"]').remove();
		//$('input[id*="-id-'+id+'"]').attr("disabled", "true");
		//$('input[id*="-id-'+id+'"]').attr("disabled", "true");

	}

}

function searchDoReplaceAll(id) {

	if (confirm(_('Are you sure?'))) searchDoReplaceAction('*','replace');

}

function searchDoSkipAll(id) {

	if (confirm(_('Are you sure?'))) searchDoReplaceAction('*','skip');

}

function searchDoReplaceModule(id) {

	if (confirm(_('Are you sure?'))) searchDoReplaceAction('module:'+id,'replace');

}

function searchDoSkipModule(id) {

	if (confirm(_('Are you sure?'))) searchDoReplaceAction('module:'+id,'skip');

}

function searchDoReplace(id) {

	searchDoReplaceAction(id,'replace');

}

function searchDoSkip(id) {

	searchDoReplaceAction(id,'skip');

}
