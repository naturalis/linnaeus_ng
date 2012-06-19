var imagePath;
var selectIndicator = '\u2022 ';

function getData(action,id,postFunction) {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			'action' : action ,
			'id' : id , 
			'inc_unknowns' : $('#unknowns-y').attr('checked') ? 1 : 0 , 
			'time' : getTimestamp()
		}),
		success : function (data) {
			//alert(data);
			obj = $.parseJSON(data);
			eval(postFunction+'(obj,id)');
		}
	})
	
}

var characteristics = Array();
var states = Array();
var selected = Array();

function storeCharacteristic(id,label,char) {

	characteristics[id] = [label,char];

}

function fillStates(obj,char) {

	$('#comparison').empty();

	setInfo(' ');

	if (!obj) return;

	for(var i=0;i<obj.length;i++) {

		if (obj[0].type.name != 'range' && obj[0].type.name != 'distribution') {
	
			$('#comparison').append('<option value="'+obj[i].id+'">'+obj[i].label+'</option>').val(obj[i].id);
	
		}
		
		states[obj[i].id] = obj[i];

	}

	if (obj[0].type.name != 'range' && obj[0].type.name != 'distribution') {

		$("#states :first").attr('selected','selected');

		goState();

	} else {

		setInfo(
			'<b>'+$('#characteristics :selected').text()+'</b><br />'+
			sprintf(
				_('Click %shere%s to specify a value; you can also double-click "%s" to do so.'),
				'<span class="a" onclick="addSelected($(\'#characteristics\'))">',
				'</span>',
				$('#characteristics :selected').text()
			)
		);

	}

	highlightSelected();

}

function setInfo(val) {

	$('#info').html(val);

}

function goState() {

	var state = states[$('#comparison').val()];

//	alert(dumpObj(state));
	
	switch (state.type.name) {
		case 'text':
			var val = state.text;
			break;
		case 'media':
			var file = encodeURIComponent(state.file_name);
			var val = '<img alt="'+file+'" onclick="showMedia(\''+imagePath+file+'\',\''+file+'\');" src="'+imagePath+state.file_name+'" class="info-image" /><br />'+_('(click image to enlarge)');
			break;
		case 'range':
			var val = 	
				_('lower: ')+state.lower+'<br />'+
				_('upper: ')+state.upper+'<br />';
			break;
		case 'distribution':
			var val = 	
				_('mean: ')+state.mean+'<br />'+
				_('sd: ')+state.sd+'<br />';
		break;
	}

	setInfo(val ? '<span class="info-header">'+characteristics[state.characteristic_id][0]+': '+state.label+'</span><br/>'+val : ' ');

}

function goCharacteristic() {

	getData('get_states',$('#characteristics').val(),'fillStates');

}

var strOpen = 
	'<table style="font-size:11px;">'+
		'<tr style="height:30px;vertical-align:top">'+
			'<td colspan=2>%s</td>'+
		'</tr>'+
		'<tr>'+
			'<td>'+_('Value:')+'</td>'+
			'<td><input type=text id=dialogValue style="font-size:12px;width:35px;text-align:right" /></td>'+
		'</tr>';

var strClose = 
	'<tr style="height:50px;vertical-align:bottom">'+
		'<td colspan=2>'+
			'<input type=button value="'+_('ok')+'" onclick="doDialog();" >'+
			'<input type=button value="'+_('cancel')+'" onclick="$(\'#dialog-close\').click();">'+
		'</td>'+
	'</tr>'+
'</table>'+
'<script>'+
'	$("#dialogValue").keypress(function(e) {'+
'		if(e.keyCode == 13) {'+
'			doDialog();'+
'		}'+
'	});'+
'</script>';
		
var strDistro = 
	strOpen +
	'<tr>'+
	'<td style="padding-right:10px">'+_('Number of allowed standard deviations:')+'</td>'+
		'<td><select id=dialogSD style="font-size:11px;"><option selected>1</option><option>2</option><option>3</option></select></td>'+
	'</tr>'+
	strClose;

var strRange = 
	strOpen +
	strClose;


function doDialog() {
	
	var v = $('#dialogValue').val();
	
	if (v.length==0) {
		
		alert(_('Please enter a value'));
		$('#dialogValue').focus();
		return;
		
	}
	if (isNaN(parseInt(v))) {
		
		alert(_('Please enter a valid number'));
		$('#dialogValue').val('');
		$('#dialogValue').focus();
		return;
		
	}

	setFreeValues([v,$('#dialogSD').val()]);

	$('#dialog-close').click();

}

function setFreeValues(vals) {

	var c = characteristics[$('#characteristics').val()];

	if (c[1]=='range')
		$('#selected').append('<option id="f'+(Math.floor(Math.random()*11))+'" value="f:'+$('#characteristics').val()+':'+(vals[0])+'">'+c[0]+': '+vals[0]+'</option>');
	else
		$('#selected').append('<option id="f'+(Math.floor(Math.random()*11))+'" value="f:'+$('#characteristics').val()+':'+(vals[0])+':'+(vals[1])+'">'+c[0]+': '+_('mean')+' '+vals[0]+' &plusmn; '+vals[1]+' '+_('sd')+'</option>');

	getScores();

}

function addSelected(caller) {

	var c = characteristics[$('#characteristics').val()];

	if (caller.id=='characteristics' && (c[1]!='distribution' && c[1]!='range')) return;

	if (c[1]=='distribution') {

		showDialog(_('Enter a value'),sprintf(strDistro,sprintf(_('Enter the required values for "%s":'),c[0])));
		$('#dialogValue').focus();

	} else
	if (c[1]=='range') {

		showDialog(_('Enter a value'),sprintf(strRange,sprintf(_('Enter the required value for "%s":'),c[0])));
		$('#dialogValue').focus();

	} else {
		
		var s = states[$('#comparison').val()];

		if (s && (selected[s.id]==false || selected[s.id]==undefined)) {
	
			$('#selected').
				append('<option id="s'+s.id+'" value="c:'+s.characteristic_id+':'+s.id+'">'+characteristics[s.characteristic_id][0]+': '+s.label+'</option>').
				val(s.id);
			selected[s.id] = true;
	
		}
		
		getScores();

	}
	
}

function highlightSelected() {

	var d = Array();

	$('#selected option').each(function(i){
		var e = $(this).val().split(':');

		$('#characteristics option').each(function(i){

			if ($(this).val()==e[1]) {
				$(this).addClass('character-selected');
				if ($(this).text().substring(0,selectIndicator.length) != selectIndicator) $(this).text(selectIndicator + $(this).text());
			}

		});
		
		if (e[1]==$('#characteristics :selected').val()) {

			$('#states option').each(function(i){

				if (e[2]==$(this).val())  {
					$(this).addClass('state-selected');
					if ($(this).text().substring(0,selectIndicator.length) != selectIndicator) $(this).text(selectIndicator + $(this).text());
				}

			});

		}

	});

}

function removeHighlight() {

	$('#characteristics option').each(function(i){
		$(this).removeClass('character-selected');
		if ($(this).text().substring(0,selectIndicator.length) == selectIndicator) $(this).text($(this).text().substring(selectIndicator.length-1));
	});
	
	$('#states option').each(function(i){
		$(this).removeClass('state-selected');
		if ($(this).text().substring(0,selectIndicator.length) == selectIndicator) $(this).text($(this).text().substring(selectIndicator.length-1));
	});

}

function deleteSelected() {

	var id = $('#selected').val();

	if (id.substr(0,1)!='f') {

		selected[id] = false;

	}

	$('#selected').children(':selected').remove();

	getScores($('#selected').children().length==0 ? 'clear' : null);
	
	removeHighlight();
	highlightSelected();

}

function clearSelected() {

	$('#selected').empty();

	selected = selected.splice(0,0);

	getScores('clear');
	
	removeHighlight();

}

function getScores(action) {

	if (action!='clear') {

		var opt = Array();
	
		$('#selected').children().each(function(){
			opt[opt.length] = ($(this).val());
		});

	} else {

		opt = -1;

	}

	getData('get_taxa',opt,'fillScores');

}

function fillScores(obj,char) {

	$('#scores').empty();

	if (!obj) return;

	for(var i=0;i<obj.length;i++) {

		$('#scores').
			append('<option ondblclick="'+(obj[i].type=='matrix' ? 'goMatrix' : 'goTaxon')+'('+obj[i].id+');" value="'+obj[i].id+'">'+
				(obj[i].score!=undefined ? obj[i].score+': ' : '')+
				(obj[i].type=='matrix' ? _('Matrix: ')+obj[i].name : obj[i].taxon)+'</option>').
			val(obj[i].id);

	}
	
	highlightSelected();
	
	$('#scores option').each(function(i){
		$(this).attr('selected','');
	});	

}

function showMatrixResults() {

	$('#search-pattern').css('display','none');
	$('#search-results').css('display','block');

}

function showMatrixPattern() {

	$('#search-pattern').css('display','block');
	$('#search-results').css('display','none');

}

function setSelectedState(val,id,charId,label) {

	var val = val.split(':');

	if (val[0]=='c') {
		
		$('#selected').append('<option id="s'+id+'" value="c:'+charId+':'+id+'">'+characteristics[charId][0]+': '+label+'</option>').val(id);

		selected[id] = true;

	} else {

		var c = characteristics[val[1]];
	
		if (c[1]=='range')
			$('#selected').append('<option id="f'+(Math.floor(Math.random()*11))+'" value="f:'+val[1]+':'+(val[2])+'">'+c[0]+': '+val[2]+'</option>');
		else
			$('#selected').append('<option id="f'+(Math.floor(Math.random()*11))+'" value="f:'+val[1]+':'+(val[2])+':'+(val[3])+'">'+c[0]+': '+_('mean')+' '+val[2]+' &plusmn; '+val[3]+' '+_('sd')+'</option>');
			
	}

}

function fillTaxonStates(obj,char) {

	$('#states tbody tr').remove();

	if (!obj) return;

	for(var i in obj) {

		$('#states').append('<tr class="highlight"><td>'+obj[i].type.name+'</td><td>'+obj[i].characteristic+'</td><td>'+obj[i].state.label+'</td><td></td></tr>');

	}
	$('#states').removeClass().addClass('visible');

}

function goExamine() {

	getData('get_taxon_states',$('#taxon-list').val(),'fillTaxonStates');

}

function goCompare() {

	var id1 = $('#taxon-list-1').val();
	var id2 = $('#taxon-list-2').val();

	if (id1=='' || id2=='') {

		alert(_('You must select two taxa.'));
		return;

	} else
	if (id1==id2) {

		alert(_('You cannot compare a taxon to itself.'));
		return;

	}
	getData('compare',[id1,id2],'fillCompareResults');

}

function fillCompareResults(obj) {
	
	fillTaxaStatesOverviews(obj);
	fillTaxaStates(obj);
	
}

function fillTaxaStatesOverviews(obj) {
	
	$('#taxon_name_1').html(obj.taxon_1.taxon);
	$('#taxon_name_2').html(obj.taxon_2.taxon);
	
	var s;
	
	if (obj.taxon_states_1) for (i in obj.taxon_states_1)  s = s + obj.taxon_states_1[i].characteristic+': '+obj.taxon_states_1[i].state.label+'<br />';
	$('#states1').html(s ? s : _('(none)'));
	s = '';

	if (obj.taxon_states_2) for (i in obj.taxon_states_2)  s = s + obj.taxon_states_2[i].characteristic+': '+obj.taxon_states_2[i].state.label+'<br />';
	$('#states2').html(s ? s : _('(none)'));
	s = '';

	if (obj.taxon_states_overlap) for (i in obj.taxon_states_overlap)  s = s + obj.taxon_states_overlap[i].characteristic+': '+obj.taxon_states_overlap[i].state.label+'<br />';
	$('#statesBoth').html(s ? s : _('(none)'));

	$('#overview').removeClass('invisible').addClass('visible');

}

function fillTaxaStates(obj) {
			
	$('#count-both').html(obj.both);
	$('#taxon-1').html(obj.taxon_1.taxon);
	$('#count-1').html(obj.count_1);
	$('#taxon-2').html(obj.taxon_2.taxon);
	$('#count-2').html(obj.count_2);
	$('#count-total').html(obj.total);
	$('#count-neither').html(obj.neither);
	$('#coefficient').html(obj.coefficients[0].value);

	var s = '<select onchange="$(\'#coefficient\').html($(this).val());" id="coefficients">';

	for (i in obj.coefficients) {
	
		s = s + '<option value="'+obj.coefficients[i].value+'"'+(i==0 ? ' selected="selected"' : '')+'>'+obj.coefficients[i].name+'</option>'+"\n";

	}
	
	s = s + '</select>';


	$('#formula').html(s);

	$('#comparison').removeClass().addClass('visible');

}
