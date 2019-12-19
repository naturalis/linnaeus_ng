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

var allLookupForceLookup=true;
var allLookupActiveRow=false;
var allLookupRowCount=0;
var allLookupListName='allLookupList'; 
var allLookupBoxName='allLookupBox'; 
var allLookupTargetUrl=false;
var allLookupData=null;
var allLookupLastString=String;
var allNavigateDefaultUrl='edit.php?id=%s';
var allNavigateTargetUrl=null;
var allNavigateStartWithAll=false;
var allNavigateMatchStartOnly=false;
var allLookupNavigateListFunction=allLookupBuildList;

function allLookupNavigateOverrideUrl(url)
{
	allLookupTargetUrl = allNavigateTargetUrl = url;
}

function allLookupNavigateOverrideListFunction(fnc)
{
	allLookupNavigateListFunction = fnc;
}

function allLookup()
{
	var text = $('#'+allLookupBoxName).val();

	if (text == allLookupLastString) return;
	
	if (allLookupGetData(text))
	{
		allLookupPositionDiv();
		allLookupShowDiv();
	}

	allLookupLastString = text;

}


function allLookupGetData(text)
{
	
	if (text.length<=allLookupLastString.length)
	{
		allLookupForceLookup=true;
	}

	if (text.length==0 && !allNavigateStartWithAll)
	{
		allLookupClearDiv();
		allLookupData = null;
		allLookupHideDiv();
		return false;
	}

	if (text.length<3)
	{
		return false;
	}

	if (allLookupForceLookup || allLookupData==null)
	{
		
		$.ajax({
			url : "ajax_interface.php",
			type: "POST",
			data : ({
				'action' : 'get_lookup_list' ,
				'search' : text,
				'get_all' : (text.length==0 && allNavigateStartWithAll ? '1' : '0') ,
				'match_start' : (allNavigateMatchStartOnly ? '1' : '0'),
				'time' : allGetTimestamp()
			}),
			success : function (data) {
				//console.log(data);
				allLookupData = $.parseJSON(data);
				//console.dir(allLookupData);
				allLookupForceLookup=(allLookupData.fullset==false);
				if (data) 
				{
					//allLookupBuildList(allLookupData,text);
					allLookupNavigateListFunction(allLookupData,text);
				}

			}
		});
		
	} 
	else 
	{

		if (allLookupData && allLookupData.results)
		{
			//var d = eval(allLookupData.toSource());
			var d = jQuery.extend(true, {}, allLookupData);
			r = new Array();

			for(var i=0;i<allLookupData.results.length;i++)
			{
				if (!allLookupData.results[i].label) continue;
				
				if (allLookupData.results[i].label.match(eval('/'+addSlashes(text)+'/ig')))
				{
					r[r.length] = allLookupData.results[i]
				}
			}

			d.results = r;
		}
		//allLookupBuildList(d,text);
		allLookupNavigateListFunction(d,text);
		
	}

	return true;

}



function allLookupBuildList(obj,txt)
{

	allLookupRowCount = 0;
	allLookupClearDiv();
	var buffer=Array();

	if (obj.results)
	{
		
		var url = allLookupTargetUrl ? allLookupTargetUrl : obj.url;

		for(var i=0;i<obj.results.length;i++) {
			
			var d = obj.results[i];
			
			if (d.id && d.label)
			{
				buffer.push(
					'<li><a href="'+(d.url ? d.url : url.replace('%s',d.id))+'">'+
					d.label+
					(d.source ? ' <span class="allLookupListSource">('+d.source+')</span>' : '')+
					'</a></li>'
				);
				
				allLookupRowCount++;

			}

		}

		$('#'+allLookupListName).append('<ul>'+buffer.join('')+'</ul>');

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
		if ((e.keyCode<65 || (e.keyCode >= 112 && e.keyCode <= 123)) && e.keyCode!=8)
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