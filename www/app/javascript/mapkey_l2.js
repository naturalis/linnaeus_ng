var l2MapCoordinates = '';
var l2MapPxHeight = -1;
var l2MapPxWidth = -1;
var l2DataColours = Array();
var eastLabel='E';
var westLabel='W';
var northLabel='N';
var southLabel='S';

$(document).ready(function()
{
	eastLabel=_(eastLabel);
	westLabel=_(westLabel);
	northLabel=_(northLabel);
	southLabel=_(southLabel);
});

function l2SetMap(mapUrl,mapW,mapH,mapCoord,cellW,cellH, resized)
{
	l2MapPxWidth = mapW;
	l2MapPxHeight = mapH;

	l2MapCoordinates = $.parseJSON(mapCoord);

	$('#mapTable').css('width',(mapW)+'px');
	$('#mapTable').css('height',(mapH)+'px');
	//l2ScaleCells(cellW,cellH);
	if (resized==0)
	{
		$('#mapTable').css('background','url('+mapUrl+')');
	} 
	else
	{
		if (Modernizr.backgroundsize) 
		{
			$('#mapTable').css('background','url('+mapUrl+')');
			$('#mapTable').css('background-size','cover');
		} else {
			$('#mapTable').css({
		        "filter": "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+mapUrl+"', sizingMethod='scale')",
		        "-ms-filter": "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+mapUrl+"', sizingMethod='scale')"
			});
		}
	}
	$('#mapTable').css('visibility','visible');
}
function l2ScaleCells(w,h)
{
	$('#mapTable').find('tr').eq(0).children().each(function()
	{
		$(this).css('width',w+'px');
	});
	$('#mapTable tr').each(function()
	{
		$(this).find('td:first').css('height',h+'px');
	});
}

function l2MapMouseOver(x,y)
{
	var o = $('#mapTable').offset();

	var widthInDegrees = 
		(l2MapCoordinates.topLeft.long >= l2MapCoordinates.bottomRight.long ? 
		  	l2MapCoordinates.topLeft.long - l2MapCoordinates.bottomRight.long : 
			360 + l2MapCoordinates.topLeft.long - l2MapCoordinates.bottomRight.long);

	var posY = -1 * ((((y-o.top) / l2MapPxHeight) * (l2MapCoordinates.topLeft.lat - l2MapCoordinates.bottomRight.lat)) - l2MapCoordinates.topLeft.lat);
	var posX = -1 * (l2MapCoordinates.topLeft.long - (((x-o.left) / l2MapPxWidth) * widthInDegrees));

	if (posX>180) posX = posX - 360;
	if (posX<-180) posX = posX + 360;

	posY = Math.round(posY);
	posX = Math.round(posX);

	var labX = (posX>0 ? eastLabel : westLabel);
	var labY = (posY>=0 ? northLabel : southLabel);

	$("#coordinates").html(Math.abs(posY)+'&deg;'+labY+', '+Math.abs(posX)+'&deg;'+labX);
}

function l2ToggleDatatype(ele)
{
	if ($(ele).prop('checked'))
	{
		$('td[datatype='+$(ele).val()+']').css('background-color',l2DataColours[$(ele).val()]);
	} 
	else 
	{
		l2DataColours[$(ele).val()] = $('td[datatype='+$(ele).val()+']').css('background-color');
		$('td[datatype='+$(ele).val()+']').css('background-color','transparent');
	}
}

function l2DoMapCompare()
{
	if($('#idA').val()=='' || $('#idB').val()=='')
	{
		alert(_('You must select two taxa to compare.'));		
	
		if ($('#idA').val()=='')
		{
			$('#idA').focus();
		}
		else
		{
			$('#idB').focus();
		}
	} 
	else
	if($('#idA').val()==$('#idB').val())
	{
		alert(_('You cannot compare a taxon to itself.'));		
		$('#idA').focus();
	} 
	else 
	{
		$('#theForm').submit();
	}
}

function l2DataTypeToggle()
{
	if($('#idA').val()!='' && $('#idB').val()!='') l2DoMapCompare();
}

function l2TagMapCell(ele)
{
	if (!$(ele).length)
	{
		ele=$('#'+ele);
	}
	
	if ($(ele).hasClass('mapCellTagged'))
	{
		$(ele).removeClass('mapCellTagged');
	} 
	else
	{
		$(ele).addClass('mapCellTagged');
	}
}

function l2DoClearSearch()
{
	$('.mapCellTagged').each(function(i)
	{
		$(this).removeClass('mapCellTagged');
	});
}

function l2DoSearchMap()
{
	var squares;
	
	$('td[id^="cell-"]').each(function(i)
	{
		if ($(this).hasClass('mapCellTagged'))
		{
			squares = true;
			$('<input type="hidden" name="selectedCells[]">').val($(this).attr('id').replace('cell-','')).appendTo('#theForm');
		} 
	});
	
	if (!squares)
	{
		alert(_('Please select at least one square'));
		return;
	} 

	var types=false

	$('[name^=dataTypes]').each(function()
	{
		if ($(this).is(':checked')) types = true;
	});
	
	if (!types)
	{
		alert(_('Please select at least one datatype'));
		return;
	} 

	$('#theForm').submit();
}

function l2DiversitySetSelectedCell(ele)
{
	$('[id^=cell-]').each(function()
	{
		$(this).removeClass('mapCellSelected');
	});

	$(ele).addClass('mapCellSelected');
}

function l2DiversityGetSelectedTypes()
{
	var types = [];

	$('[name^=selectedDatatypes]').each(function()
	{
		if ($(this).prop('checked')==true) types[types.length] = $(this).val();
	});
	
	return types;
}

function l2DiversityClearAll()
{
	$('[id^=cell-]').each(function()
	{
		$(this).removeClass();
		$(this).attr('total','0');
	});
}

function l2DiversityClearLegend()
{
	$('#legend').empty();
}

function l2DiversityGiantOilSpill(data)
{
	l2DiversityClearAll();
	for(var i=0;i<data.length;i++)
	{
		$('#cell-'+data[i].id).addClass('mapCellDiversity'+data[i].css);
		$('#cell-'+data[i].id).attr('total',data[i].total);	
	}
}


function l2DiversityUpdateLegend(data)
{
	l2DiversityClearLegend();
	var textToAppend = [];
	for(var i=0;i<data.length;i++)
	{
		textToAppend[i] = 
			'<div class="mapCheckbox"><label>'+
			'<span class="opacity">'+
			'<span class="mapCellLegend mapCellDiversity'+data[i].id+'">&nbsp;&nbsp;&nbsp;&nbsp;</span></span>'+
			data[i].min+'-'+data[i].max+' '+_('records')+
			'</label></div>';
	}

	$('#legend').append(textToAppend.join(''));
}

function l2DiversityTypeClick()
{
	var types = l2DiversityGetSelectedTypes();
	
	if (types.length==0)
	{
		l2DiversityClearAll();
		l2DiversityClearLegend();
		return;
	}

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			'action' : 'get_diversity',
			'm' : $('#mapId').val(),
			'types' : types
		}),
		success : function (data)
		{
			var tmp = $.parseJSON(data);
			if (tmp.index) l2DiversityGiantOilSpill(tmp.index);
			if (tmp.legend) l2DiversityUpdateLegend(tmp.legend);
		}
	});
}

function l2DiversityCellClick(ele)
{
	var types = l2DiversityGetSelectedTypes();

	if ($(ele).attr('total')==0) return;
	if (types.length==0) return;

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			'action' : 'get_cell_diversity',
			'm' : $('#mapId').val(),
			'id' : $(ele).attr('id').replace('cell-',''),
			'types' : types
		}),
		success : function (data) {
			//alert(data);
			allLookupNavigateOverrideDialogTitle('Taxa in that square');
			allLookupShowDialog(data);
			l2DiversitySetSelectedCell(ele);			
		}
	});
}

function l2SetCompareSpecies(i,j)
{
	var label = $('[lookupId="'+j+'"]').html();

	if (i==1)
	{
		$('#idA').val(j);
		$('#speciesNameA').html(label);
	} 
	else
	if (i==2)
	{
		$('#idB').val(j);
		$('#speciesNameB').html(label);
	}

	$('#dialog-close').click();	
}

function l2DiversityCellMouseOver(ele)
{
	var i = $(ele).attr('total');
	$('#species-number').html((i ? i : 0) + _(' species'));
}

function l2MapIEFix()
{
	if (($.browser.msie && $.browser.version<=9))
	{
		$('td[id^=cell-]').removeClass('mapCell').addClass('mapCell-IElt9compat');
	}
}

function l2ToggleGrid(caller)
{	
	$('td[id^=cell-]').toggleClass('nogrid');
	$(caller).children().each(function(){$(this).css('display',($(this).css('display')=='block'?'none':'block'));});
}

function l2CloseTaxonList()
{
	$("#lookup-DialogContent").toggle(false);
}

function l2TaxonSelection(n)
{
	$('#lookup-DialogContent').toggle(true);
	allLookupSetExtraVar( { name: 'l2_must_have_geo', value: 1 } );
	allLookupNavigateOverrideUrl('javascript:l2SetCompareSpecies('+n+',%s);l2CloseTaxonList()');
	allLookupShowDialog()
}
