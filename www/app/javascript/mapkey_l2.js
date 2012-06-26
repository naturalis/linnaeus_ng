var l2MapCoordinates = '';
var l2MapPxHeight = -1;
var l2MapPxWidth = -1;
var l2DataColours = Array();

function l2SetMap(mapUrl,mapW,mapH,mapCoord,cellW,cellH) {

	l2MapPxWidth = mapW;
	l2MapPxHeight = mapH;

	l2MapCoordinates = $.parseJSON(mapCoord);

	$('#mapTable').css('background','url('+mapUrl+')');
	$('#mapTable').css('width',(l2MapPxWidth)+'px');
	$('#mapTable').css('height',(l2MapPxHeight)+'px');
	
	l2ScaleCells(cellW,cellH);

}

function l2ScaleCells(w,h) {

	$('td[id^=cell-]').each(function (i) {
		$(this).css('width',w+'px');
		$(this).css('height',h+'px');
	});
}

function l2MapMouseOver(x,y) {

	/*
	l2MapCoordinates.topLeft.lat
	l2MapCoordinates.topLeft.long
	l2MapCoordinates.bottomRight.lat
	l2MapCoordinates.bottomRight.long
	*/
	
	var o = $('#mapTable').offset();

	var widthInDegrees = 
		(l2MapCoordinates.topLeft.long >= l2MapCoordinates.bottomRight.long ? 
		  	l2MapCoordinates.topLeft.long - l2MapCoordinates.bottomRight.long : 
			360 + l2MapCoordinates.topLeft.long - l2MapCoordinates.bottomRight.long);

	var posY = -1 * ((((y-o.top) / l2MapPxHeight) * (l2MapCoordinates.topLeft.lat - l2MapCoordinates.bottomRight.lat)) - l2MapCoordinates.topLeft.lat);
	var posX = (l2MapCoordinates.topLeft.long - (((x-o.left) / l2MapPxWidth) * widthInDegrees));
	posX = (posX<=-180 ? posX+360: posX);

	$("#coordinates").html(Math.round(posY)+','+Math.round(posX));

}

function l2ToggleDatatype(ele) {

	if ($(ele).attr('checked')) {
		
		$('td[datatype='+$(ele).val()+']').css('background-color',l2DataColours[$(ele).val()]);

	} else {

		l2DataColours[$(ele).val()] = $('td[datatype='+$(ele).val()+']').css('background-color');

		$('td[datatype='+$(ele).val()+']').css('background-color','transparent');

	}
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

function l2SetCompareSpecies(i,j) {

	var label = $('[lookupId="'+j+'"]').html();

	if (i==1) {
		$('#idA').val(j);
		$('#speciesNameA').html(label);
	} else
	if (i==2) {
		$('#idB').val(j);
		$('#speciesNameB').html(label);
	}

	$('#dialog-close').click();
	

}

function l2DiversityCellMouseOver(i) {

	$('#species-number').html((i ? i : 0) + _(' species'));

}



