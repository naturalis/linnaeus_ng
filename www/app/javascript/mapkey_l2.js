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

var l2TaggedCells = Array();

function l2TagMapCell(ele) {

	if (l2TaggedCells[ele.id]==1) {
		
		l2TaggedCells[ele.id] = 0;
		$(ele).removeClass('mapCellTagged');
		
	} else {

		l2TaggedCells[ele.id] = 1;
		$(ele).addClass('mapCellTagged');

	}


}