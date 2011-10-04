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
	
	(the droplist div is in the admin-footer file: <div id="allLookupList" class="allLookupListInvisible"></div>)

*/

var allLookupActiveRow = false;
var allLookupRowCount = 0;
var allLookupListName = 'allLookupList'; 
var allLookupBoxName = 'allLookupBox'; 
var allLookupTargetUrl = false;
var allLookupData = null;
var allLookupLastString = null;
var allNavigateDefaultUrl = 'edit.php?id=%s';
var allNavigateTargetUrl = null;


function allLookupNavigateOverrideUrl(url) {

	allLookupTargetUrl = allNavigateTargetUrl = url;

}

function allLookup() {

	var text = $('#'+allLookupBoxName).val();

	if (text == allLookupLastString) return;

	allLookupGetData(text);
	allLookupPositionDiv();
	allLookupShowDiv();
	
	allLookupLastString = text;
	
}


function allLookupGetData(text) {

	if (text.length==0) {

		allLookupClearDiv();
		allLookupData = null;
		return;

	}

	if (allLookupData==null) {

		$.ajax({
			url : "ajax_interface.php",
			type: "POST",
			data : ({
				'action' : 'get_lookup_list' ,
				'search' : text ,
				'time' : allGetTimestamp()
			}),
			async: allAjaxAsynchMode,
			success : function (data) {
				
				allLookupData = $.parseJSON(data);
				if (data) allLookupBuildList(allLookupData,text);

			}
		});
		
	} else {

		if (allLookupData && allLookupData.results) {

			var d = eval (allLookupData.toSource());
			r = new Array();

			for(var i=0;i<allLookupData.results.length;i++) {
				
				if (allLookupData.results[i].label.match(eval('/'+addSlashes(text)+'/ig'))) {
					
					r[r.length] = allLookupData.results[i]
					
				}

			}

			d.results = r;

		}
	
		allLookupBuildList(d,text);

	}

}

function allLookupBuildList(obj,txt) {

	allLookupRowCount = 0;
	
	allLookupClearDiv();

	if (obj.results) {
		
		$('#'+allLookupListName).append('<table id="allLookupListTable">');
		
		var url = allLookupTargetUrl ? allLookupTargetUrl : obj.url;

		for(var i=0;i<obj.results.length;i++) {
			
			var d = obj.results[i];
			
			if (d.id && d.label) {

				//d.label.replace(eval('/'+txt+'/ig'),'<span class="allLookupListHighlight">'+txt+'</span>') +
				
				$('#'+allLookupListName).append(
					'<tr id="allLookupListRow-'+i+'" class="allLookupListRow">'+
						'<td id="allLookupListCell-'+i+'" class="allLookupListCell" onclick="window.open(\''+url.replace('%s',d.id)+'\',\'_self\')">'+
							d.label +
							(d.source ? ' ('+d.source+')' : '')+
						'</td>'+
					'</tr>');
				
				allLookupRowCount++;

			}

		}

		$('#'+allLookupListName).append('</table>');

	}

}

function allLookupClearDiv() {

	$('#'+allLookupListName).empty();

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

function allLookupBindKeyUp() {

	$('#'+allLookupBoxName).keyup(function(e) {

		if (e.keyCode==13) {
			allLookupGoActiveRow();
			return;
		}

		if (e.keyCode==38)
			allLookupMoveUp();
		else
		if (e.keyCode==40)
			allLookupMoveDown();
		else
		if (e.keyCode==37 || e.keyCode==39)
			return;
		else
			allLookup();
	
	});

}

function allNavigate(id) {

	var url = allNavigateTargetUrl ? allNavigateTargetUrl : allNavigateDefaultUrl;

	window.open(url.replace('%s',id),'_self');

}

$(document).ready(function(){

	$('body').click(function() {
		allLookupHideDiv();
	});

	$('#'+allLookupListName).mouseover(function() {
		allLookupHighlightRow(true);
	});

	allLookupBindKeyUp();
	$('#'+allLookupBoxName).focus();

});