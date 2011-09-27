/*
	private function getLookupList($search)
	{

		if (empty($search)) return;

		$l1 = $this->models->Glossary->_get(
			array(
				'id' =>
					array(
						'project_id' => $this->getCurrentProjectId(),
						'term like' => '%'.$search.'%'
					),
				'columns' => 'id,term as text,"glossary" as source'
			)
		);

		$l2 = $this->models->GlossarySynonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'synonym like' => '%'.$search.'%'
					),
				'columns' => 'glossary_id as id,synonym as text,"glossary synonym" as source'
			)
		);

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				array_merge((array)$l1,(array)$l2),
				'glossary',
				'../glossary/edit.php?id=%s'
			)
		);
		
	}
	
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
	
	(the droplist div is in the admin-footer file. <div id="allLookupList" class="allLookupListInvisible"></div>)

*/

var allLookupActiveRow = false;
var allLookupRowCount = 0;
var allLookupListName = 'allLookupList'; 
var allLookupBoxName = 'allLookupBox'; 

function allLookup() {

	allLookupGetData($('#'+allLookupBoxName).val());	
	allLookupPositionDiv();
	allLookupShowDiv();
	
}

function allLookupGetData(text) {

	if (text == null) return;

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
			
			allLookupClearDiv();			
			if (data) allLookupBuildList(data,text);

		}
	});

}

function allLookupBuildList(data,text) {

	obj = $.parseJSON(data);
	
	allLookupRowCount = 0;

	if (obj.results) {
		
		$('#'+allLookupListName).append('<table>');
		
		for(var i=0;i<obj.results.length;i++) {
			
			var d = obj.results[i];
			
			if (d.id && d.text) {

				$('#'+allLookupListName).append(
					'<tr id="allLookupListRow-'+i+'" class="allLookupListRow">'+
						'<td id="allLookupListCell-'+i+'" onclick="window.open(\''+obj.url.replace('%s',d.id)+'\',\'_self\')">'+
							d.text.replace(eval('/'+text+'/ig'),'<span class="allLookupListHighlight">'+text+'</span>') +
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

$(document).ready(function(){

	$('body').click(function() {
		allLookupHideDiv();
	});

	$('#'+allLookupListName).mouseover(function() {
		allLookupHighlightRow(true);
	});

	allLookupBindKeyUp();

});