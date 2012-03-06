function l2SetMap(url,w,h) {

	$('#mapTable').css('background','url('+url+')');
	$('#mapTable').css('width',w+'px');
	$('#mapTable').css('height',h+'px');

}

function l2ScaleCells(w,h) {
	$('td').each(function (i) {
		if ($(this).hasClass('mapCell')) {
	
			$(this).css('width',w+'px');
			$(this).css('height',h+'px');

		}
	});
	
}

function l2MapMouseOver(x,y) {

  msg = x + ", " + y;
  $("#coordinates").html(msg);
}


function l2ToggleDatatype(ele) {

	if ($(ele).attr('checked'))
		$('td[datatype='+$(ele).val()+']').css('visibility','visible');
	else
		$('td[datatype='+$(ele).val()+']').css('visibility','hidden');

}

function l2DoMapCompare() {

	if($('#idA').val()=='' || $('#idB').val()=='') {
	
		alert(_('You must select two taxa to compare.'));		
	
		if ($('#idA').val()=='')
			$('#idA').focus();
		else
			$('#idB').focus();
		
	} else
	if($('#idA').val()==$('#idB').val()) {

		alert(_('You cannot compare a taxon to itself.'));		
		$('#idA').focus();

	} else {

		$('#theForm').submit();
	
	}

}

function l2DataTypeToggle() {

	if($('#idA').val()!='' && $('#idB').val()!='') l2DoMapCompare();

}

function l2TagMapCell(ele) {
	
	if ($(ele).hasClass('mapCellTagged')) {
		
		$(ele).removeClass('mapCellTagged');
		
	} else {

		$(ele).addClass('mapCellTagged');

	}
	

}

function l2DoClearSearch() {

	$('td[id^="cell-"]').each(function(i) {

		if ($(this).hasClass('mapCellTagged')) $(this).removeClass('mapCellTagged');

	});


}

function l2DoSearchMap() {

	$('td[id^="cell-"]').each(function(i) {

		if ($(this).hasClass('mapCellTagged')) {
			
			$('<input type="hidden" name="selectedCells[]">').val($(this).attr('id').replace('cell-','')).appendTo('#theForm');
			
		} 

	});
	
	$('#theForm').submit();


}

function l2DiversityCellClick(ele) {

	$('<input type="hidden" name="selectedCell">').val($(ele).attr('id').replace('cell-','')).appendTo('#theForm');
	$('#theForm').submit();

}