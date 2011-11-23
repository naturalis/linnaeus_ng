var searchReplaceValue = null;

function searchToggleReplace() {

	if ($('#replaceToggle').is(':checked')) {
	
		$('#replaceParameters').removeClass('replaceBlankedOut').addClass('replaceNotBlankedOut');
		$('#replacement').val(searchReplaceValue);
		$('#searchButton').attr('value',_('search and replace'));
		$('#replacement').removeAttr('disabled');
		$('#optionsAll').removeAttr('disabled');
		$('#optionsShow').removeAttr('disabled');
		
	
	} else {

		$('#replaceParameters').removeClass('replaceNotBlankedOut').addClass('replaceBlankedOut');
		searchReplaceValue = $('#replacement').val();
		$('#replacement').val('');
		$('#searchButton').attr('value',_('search'));
		$('#replacement').attr('disabled','disabled');
		$('#optionsAll').attr('disabled','disabled');
		$('#optionsShow').attr('disabled','disabled');

	}

}

function searchDoSearchForm() {

	if ($('#search').val().trim()=='') {

		alert(_('You need to enter a search term.'));
		$('#search').focus();
		return false;

	} else
	if ($('#search').val().length<3) {

		alert(_('The search term needs to be at least three characters.'));
		$('#search').focus();
		return false;

	} else
	if ($('input[name*=modules]:checked').length==0 && $('input[name*=freeModules]:checked').length==0) {

		alert(_('You need to select at least one module'));
		return false;

	} else
	if ($('#replaceToggle').is(':checked') && $('#replacement').val().trim()=='') {

		alert(_('You need to enter a replacement term.'));
		$('#replacement').focus();
		return false;

	} else 
	if ($('#replaceToggle').is(':checked') && $('#optionsAll').is(':checked')) {


		if (!confirm(_('Are you sure? This action cannot be undone.')))
			return false;
		else
			$('#theForm').submit();

	} else {
	
		$('#theForm').submit();
	
	}

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
