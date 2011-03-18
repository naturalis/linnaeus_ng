var imagePath;

function getData(action,id,postFunction) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : action ,
			'id' : id , 
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

	$('#states').empty();

	setInfo(' ');

	if (!obj) return;

	for(var i=0;i<obj.length;i++) {

		if (obj[0].type.name != 'range' && obj[0].type.name != 'distribution') {
	
			$('#states').append('<option value="'+obj[i].id+'">'+obj[i].label+'</option>').val(obj[i].id);
	
		}
		
		states[obj[i].id] = obj[i];

	}

	if (obj[0].type.name != 'range' && obj[0].type.name != 'distribution') {

		$("#states :first").attr('selected','selected');

		goState();

	}

}

function setInfo(val) {

	$('#info').html(val);

}

function goState() {

	var state = states[$('#states').val()];

//	alert(dumpObj(state));
	
	switch (state.type.name) {
		case 'text':
			var val = state.text;
			break;
		case 'media':
			var file = encodeURIComponent(state.file_name);
			var val = '<img onclick="showMedia(\''+imagePath+file+'\',\''+file+'\');" src="'+imagePath+state.file_name+'" class="info-image" /><br />'+_('(click image to enlarge)');
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
				'<input type=button value="'+_('ok')+'" onclick="doDialog();">'+
				'<input type=button value="'+_('cancel')+'" onclick="$(\'#dialog-close\').click();">'+
			'</td>'+
		'</tr>'+
	'</table>';

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

	if ($('#dialogSD'))
		setFreeValues([v,$('#dialogSD').val()]);

	$('#dialog-close').click();

}

function setFreeValues(vals) {

	if (isArray(vals)) alert('array'); else alert('blaa');return;
alert(vals);return;

	if (val!= null && !isNaN(parseInt(val))) {
			
			$('#selected').
				append('<option id="f'+(Math.floor(Math.random()*11))+'" value="f:'+$('#characteristics').val()+':'+(val)+':'+c[1]+'">'+c[0]+': '+val+'</option>');

	}

}

function addSelected() {

	var c = characteristics[$('#characteristics').val()];

	if (c[1]=='distribution') {

		showDialog(sprintf(strDistro,sprintf(_('Enter the required values for "%s":'),c[0])));
		$('#dialogValue').focus();

	} else
	if (c[1]=='range') {

		showDialog(sprintf(strRange,sprintf(_('Enter the required values for "%s":'),c[0])));
		$('#dialogValue').focus();

	} else {
		
		var s = states[$('#states').val()];

	}

	if (s && (selected[s.id]==false || selected[s.id]==undefined)) {

		$('#selected').
			append('<option id="s'+s.id+'" value="'+s.id+'">'+characteristics[s.characteristic_id][0]+': '+s.label+'</option>').
			val(s.id);
		selected[s.id] = true;

	}
	
	getScores();

}

function deleteSelected() {

	var id = $('#selected').val();

	if (id.substr(0,1)!='f') {

		selected[id] = false;

	}

	$('#selected').children(':selected').remove();

	getScores($('#selected').children().length==0 ? 'clear' : null);

}

function clearSelected() {

	$('#selected').empty();
	selected = selected.splice(0,0);

	getScores('clear');

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
			append('<option ondblclick="goTaxon('+obj[i].id+');" value="'+obj[i].id+'">'+
				(obj[i].score!=undefined ? obj[i].score+': ' : '')+
				obj[i].taxon+'</option>').
			val(obj[i].id);

	}

}


function fillTaxonStates(obj,char) {

	$('#states tbody tr').remove();

	if (!obj) return;


//	for(var i=0;i<obj.length;i++) {
	for(var i in obj) {
		$('#states').append('<tr class="highlight"><td>'+obj[i].type.name+'</td><td>'+obj[i].characteristic+'</td><td>'+obj[i].state.label+'</td></tr>');

	}

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

	}

	getData('compare',[id1,id2],'fillTaxaStates');

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
	$('#formula').html('('+obj.coefficients[0].name+')');

	$('#states').removeClass().addClass('visible');

}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	