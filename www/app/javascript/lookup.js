/*

	add:
	
	<input type="text" id="allLookupBox" onkeyup="allLookup()" />
	
	to the script, and create a data retrieval function in the appropriate class file, accessible through ajax.
	expected format of returned data:

		json_encode(
			array(
				'module' => 'module name (optional)',
				'url' => '../relative/url/to/item?id=%s',
				'results' => array(
					'id' => id of item,
					'text' => 'text to display',
					'source' => 'data source (optional)'
				)				   
			)
		);
	
	(the droplist div is in the footer file: <div id="allLookupList" class="allLookupListInvisible"></div>)

*/

var allLookupActiveRow = false;
var allLookupRowCount = 0;
var allLookupListName = 'allLookupList'; 
var allLookupBoxName = 'allLookupBox'; 
var allLookupTargetUrl = false;
var allLookupData = null;
var allLookupLastString = null;
var allLookupMatchStartOnly = true;
var allLookupExtraVars = Array();
var allLookupDialogInputName = 'lookupDialogInput';
var allLookupDialogContentName = 'lookup-DialogContent';
var allNavigateDefaultUrl = 'item.php?id=%s';
var allNavigateTargetUrl = null;

function allLookupNavigateOverrideUrl(url) {

	allLookupTargetUrl = allNavigateTargetUrl = url;

}

function allLookup() {

	var text = $('#'+allLookupBoxName).val();

	if (text == allLookupLastString) return;
	
	if (allLookupGetData(text)) {
		allLookupPositionDiv();
		allLookupShowDiv();
	}

	allLookupLastString = text;
	
}


function allLookupPostProcessing(text,data,getAll) {

	/*
		the 'match start of word only' option was added later, and required restructuring 
		of the corresponding lookup functions in the controller classes. in LinnaeusController
		and GlossaryController, the required alterations were too extensive. its output was 
		therefore left unchanged and is filtered here to meet the requirements of the 
		allLookupMatchStartOnly setting.
	*/
	
	if (allLookupMatchStartOnly  && !getAll) {

		var regExp = '/^'+addSlashes(text)+'/ig';

		//var d = eval (allLookupData.toSource());
		var d = jQuery.extend(true, {}, data);
		r = new Array();

		for(var i=0;i<data.results.length;i++) {
			
			if (data.results[i].label.match(eval(regExp))) {
				
				r[r.length] = data.results[i]
				
			}

		}

		d.results = r;
		
		return d;

	}

	return data;

}

function allLookupDataShowLoading() {

	$('#lookup-DialogContent').append('<div id="allLookupLoadingDiv"></div>');

}

function allLookupDataHideLoading() {

	$('#allLookupLoadingDiv').remove();

}

function allLookupGetData(text,getAll) {

	if (text.length==0 && getAll!=true) {

		allLookupClearDiv();
		allLookupData = null;
		allLookupHideDiv();
		return false;

	}
	
	allLookupDataShowLoading();

	if (allLookupData==null) {

		$.ajax({
			url : "ajax_interface.php",
			type: "POST",
			data : ({
				'action' : 'get_lookup_list' ,
				'search' : (getAll==true ? '*' : text) ,
				'match_start' : (allLookupMatchStartOnly ? '1' : '0') ,
				'get_all' : (getAll==true ? '1' : '0') ,
				'vars' : allLookupExtraVars,
				'time' : allGetTimestamp()
			}),
			success : function (data) {
				//alert(data);
				var tmp = $.parseJSON(data);
				allLookupData = allLookupPostProcessing(text,tmp,getAll);
				if (data) allLookupBuildList(allLookupData,text);
				allLookupDataHideLoading();

			}
		});
		
	} else {

		if (allLookupData && allLookupData.results) {
			
			if (allLookupMatchStartOnly)
				var regExp = '/^'+addSlashes(text)+'/ig';
			else
				var regExp = '/'+addSlashes(text)+'/ig';

			//var d = eval (allLookupData.toSource());
			var d = jQuery.extend(true, {}, allLookupData);
			r = new Array();

			for(var i=0;i<allLookupData.results.length;i++) {
				
				if (getAll || allLookupData.results[i].label.match(eval(regExp))) {
					
					r[r.length] = allLookupData.results[i]
					
				}

			}

			d.results = r;

		}
	
		allLookupBuildList(d,text);

	}

	return true;

}

function allLookupSetExtraVars(name,value) {

	allLookupExtraVars[allLookupExtraVars.length] = [name,value];

}


function allLookupBuildList(obj,txt) {

	allLookupRowCount = 0;
	
	allLookupClearDiv();
	allLookupClearDialogDiv();

	if (obj.results) {
		
		var str = '<table id="allLookupListTable">';

		$('#'+allLookupListName).append(str);
		$('#'+allLookupDialogContentName).append(str);
		
		var url = allLookupTargetUrl ? allLookupTargetUrl : obj.url;

		for(var i=0;i<obj.results.length;i++) {
			
			var d = obj.results[i];
			
			if (d.id && d.label) {

				//d.label.replace(eval('/'+txt+'/ig'),'<span class="allLookupListHighlight">'+txt+'</span>') +

				var str =
					'<tr id="allLookupListRow-'+i+'" class="allLookupListRow">'+
						'<td id="allLookupListCell-'+i+'" class="allLookupListCell" lookupId="'+d.id+'" onclick="window.open(\''+
							(d.url ? d.url : url.replace('%s',d.id)) +
							'\',\'_self\')">'+
							d.label +
							(d.source ? ' <span class="allLookupListSource">('+d.source+')</span>' : '')+
						'</td>'+
					'</tr>';

				$('#'+allLookupListName).append(str);
				$('#'+allLookupDialogContentName).append(str);

				allLookupRowCount++;

			}

		}

		str = '</table>';
		$('#'+allLookupListName).append(str);
		$('#'+allLookupDialogContentName).append(str);

	}

	var dialogTop = Math.abs($(window).height() - $('#dialog').height()) / 2;
	$('#dialog').css('top', (dialogTop >= 25) ? dialogTop : 25);

}

function allLookupClearDiv() {

	$('#'+allLookupListName).empty();

}

function allLookupClearDialogDiv() {

	$('#'+allLookupDialogContentName).empty();

}
	

function allLookupPositionDiv() {

	var pos = $('#'+allLookupBoxName).position();

//	$('#'+allLookupListName).offset({ left: pos.left, top: pos.top + $('#'+allLookupBoxName).height() + 5}); // keeps shifting down!?
	$('#'+allLookupListName).css('left',pos.left);
	$('#'+allLookupListName).css('top',pos.top + $('#'+allLookupBoxName).height() + 5);

}

function allLookupShowDiv() {

	$('#'+allLookupListName).css('display','block');

}

function allLookupHideDiv() {

	$('#'+allLookupListName).css('display','none');

}

function allLookupGoActiveRow() {
	
	$('#allLookupListCell-'+allLookupActiveRow).trigger('click');

}

function allLookupMoveUp() {

	if (allLookupActiveRow===false)
		return;
	else
	if (allLookupActiveRow > 0)
		allLookupActiveRow=allLookupActiveRow-1;

	allLookupHighlightRow();

}

function allLookupMoveDown() {

	if (allLookupActiveRow===false)
		allLookupActiveRow=0;
	else
	if (allLookupActiveRow < allLookupRowCount-1)
		allLookupActiveRow=allLookupActiveRow+1;

	allLookupHighlightRow();

}

function allLookupHighlightRow(clearall) {
	
	for(var i=0;i<allLookupRowCount;i++) {

		if (i==allLookupActiveRow && clearall!=true) {

			$('#allLookupListRow-'+i).addClass('allLookupListActiveRow');

		} else {

			$('#allLookupListRow-'+i).removeClass('allLookupListActiveRow');

		}

	}

}

function allLookupBindKeyUp(ele) {

	$('#'+ele).keyup(function(e) {

		if (e.keyCode==13) {
			// enter
			allLookupGoActiveRow();
			return;
		}

		if (e.keyCode==38)
			// key up
			allLookupMoveUp();
		else
		if (e.keyCode==40)
			// key down
			allLookupMoveDown();
		else
		if ((e.keyCode<65 || (e.keyCode >= 112 && e.keyCode <= 123)) && e.keyCode!=8 && !(e.keyCode>=48 && e.keyCode<=57))
			// smaller than 'a' or a function-key, but not backspace
			return;
		else
			allLookup();
	});

}

function allNavigate(id) {

	var url = allNavigateTargetUrl ? allNavigateTargetUrl : allNavigateDefaultUrl;

	window.open(url.replace('%s',id),'_self');

}


function allLookupDialog() {

	var text = $('#'+allLookupDialogInputName).val();
	if (text == allLookupLastString) return;
	if (text.length==0)
		allLookupGetData('*',true);
	else
		allLookupGetData(text);
	allLookupLastString = text;

}

function allLookupBindDialogKeyUp() {

	$('#'+allLookupDialogInputName).keyup(function(e) {

		if (e.keyCode==27) {
			// esc
			$('#dialog-close').click();
			return;
		}

		if ((e.keyCode<65 || (e.keyCode >= 112 && e.keyCode <= 123)) && e.keyCode!=8 && !(e.keyCode>=48 && e.keyCode<=57))
			// smaller than 'a' or a function-key, but not backspace
			return;
		else
			allLookupDialog();
	});

}

function allLookupShowDialog() {

	showDialog(
		_('Index'),
		'<div id="lookupDialog"><input type="text" id="'+allLookupDialogInputName+'"></div><div id="'+allLookupDialogContentName+'"></div>'
	);

	$('#'+allLookupDialogContentName).css('overflow-y','scroll');
	$('#'+allLookupDialogContentName).css('height','250px');

	allLookupBindDialogKeyUp();
	allLookupGetData('*',true);

	$('#'+allLookupDialogInputName).focus();

}


$(document).ready(function(){

	$('body').click(function() {
		allLookupHideDiv();
	});

	$('#'+allLookupListName).mouseover(function() {
		allLookupHighlightRow(true);
	});

	allLookupBindKeyUp(allLookupBoxName);

	$('#'+allLookupBoxName).focus();

});