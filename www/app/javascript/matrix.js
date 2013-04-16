var imagePath;
var selectIndicator = '\u2022 ';
var characterOrders =
	Array(
		['alphabet',_('Alphabet')],
		['separationCoefficient',_('Separation coefficient')],
		['characterType',_('Character type')],
		['numberOfStates',_('Number of states')],
		['entryOrder',_('Entry order')]
	);
var sortField = null;

function getData(action,id,postFunction) {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			'action' : action ,
			'id' : id , 
			'inc_unknowns' : $('#inc_unknowns').attr('checked') ? 1 : 0 , 
			'time' : getTimestamp()
		}),
		success : function (data) {
			//alert(data);return;
			obj = $.parseJSON(data);
			if (postFunction) eval(postFunction+'(obj,id)');
		}
	});
	
}

var characters = Array();
var states = Array();
var selected = Array();
var freeValues = Array();
var sdValues = Array();
var storedShowState;

function storeCharacter(id,label,type,sorts) {

	characters[characters.length] = {id:id,label:label,type:type,sorts:sorts};


}

function sortfunction(a,b){

	var x = (eval('a.sorts.'+sortField));
	var y = (eval('b.sorts.'+sortField));

	return ((x < y) ? -1 : ((x > y) ? 1 : 0));
	
}

function sortCharacters(field) {

	sortField = field;
	characters.sort(sortfunction);

	$('#characteristics	').empty();
	
	for (var i in characters)
		$('#characteristics').append('<option value="'+characters[i].id+'" class="'+characters[i].type+'">'+characters[i].label+' ['+characters[i].type+']</option>');

}

function goCharacter() {

	getData('get_states',$('#characteristics').val(),'fillStates');

}

function fillStates(obj,char) {

	$('#states').empty();

	setInfo('',' ',' ');

	if (!obj) return;

	for(var i=0;i<obj.length;i++) {

		if (obj[i].type != 'range' && obj[i].type != 'distribution') {
	
			$('#states').append('<option value="'+obj[i].id+'">'+obj[i].label+'</option>').val(obj[i].id);
	
		}
		
		states[obj[i].id] = obj[i];

	}

	if (obj[0].type != 'range' && obj[0].type != 'distribution') {

		$("#states :first").attr('selected','selected');

		goState();

	} else {
		
		var t = $('#characteristics :selected').text().replace(selectIndicator,'');

		setInfo(
			'<b>'+t+'</b><br />',
			sprintf(
				_('Click %shere%s to specify a value; you can also double-click "%s" to do so.'),
				'<span class="internal-link" onclick="addSelected($(\'#characteristics\'))">',
				'</span>',
				t
			)
		);
	
	}

	highlightSelected();

}

function setInfo(h,b,v) {

	if (h) $('#info-header').html(h);
	if (b) $('#info-body').html(b);
	if (v) $('#info-value').html(v);

}

function goState() {

	var state = states[$('#states').val()];

	$('#info-footer').html(null);
	
	var title = ' ';

	switch (state.type) {
		case 'text':
			var val = state.text;
			var c = getCharacter(state.characteristic_id);
			if (val) title = c.label;
			break;
		case 'media':
			if (state.img_dimensions==null) break;
			
			var c = getCharacter(state.characteristic_id);
			
			if (c) var label = c.label;

			title  = label+': '+$('#states :selected').text();

			var file = encodeURIComponent(state.file_name);

			var headerHeight = parseInt($('#info-header').css('height'));
			if (isNaN(headerHeight)) headerHeight = parseInt(document.getElementById('info-header').offsetHeight); // IE7 / IE8
			headerHeight = headerHeight + parseInt($('#info-header').css('marginBottom'));

			var maxW = parseInt($('#info').css('width'));
			var maxH = parseInt($('#info').css('height')) - headerHeight;

			var imgW = state.img_dimensions[0];
			var imgH = state.img_dimensions[1];

			var canEnlarge = ((imgW > maxW) || (imgH > maxH));

			if (canEnlarge) {

				$('#info-footer').html(_('(click image to enlarge)'));

				var footerHeight = parseInt($('#info-footer').css('height'));
				if (isNaN(footerHeight)) footerHeight = parseInt(document.getElementById('info-footer').offsetHeight); // IE7 / IE8
				footerHeight = footerHeight + parseInt($('#info-footer').css('marginTop'));

				if ((maxH/maxW) < (imgH/imgW)) {
					var newH = (maxH - footerHeight);
					var newW = ((newH / imgH) * imgW);
					newW = Math.round(newW);
				} else {
					var newW = maxW;
					var newH = ((maxH / imgH) * imgH);
					newH = Math.round(newH);
				}
/*				
				var val = 
					'<img id="state-'+state.id+'" alt="'+file+'" '+
					'onclick="showMedia(\''+imagePath+file+'\',\''+file+'\');" '+
					'src="'+imagePath+state.file_name+'" class="info-image" '+
					'style="height:'+newH+'px;width:'+newW+'px;" />';
*/				
				var val = 
					'<div id="state-'+state.id+'" alt="'+state.label+'" class="info-image" '+
					'onclick="showMedia(\''+imagePath+file+'\',\''+file+'\');" '+
					'style="background: url(\''+imagePath+state.file_name+'\') no-repeat;'+
					'background-size:cover;height:'+newH+'px;width:'+newW+'px;'+
					'filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''+
					imagePath+state.file_name+'\', sizingMethod=\'scale\');'+
					'-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''+
					imagePath+state.file_name+'\', sizingMethod=\'scale\');"/>';

			} else {
/*
				var val = '<img id="state-'+state.id+'" alt="'+file+
					'" src="'+imagePath+state.file_name+'" class="info-image" '+
					'style="height:'+imgH+'px;width:'+imgW+'px;" />';
*/
				var val = 
					'<div id="state-'+state.id+'" alt="'+state.label+'" class="info-image" '+
					'style="background: url(\''+imagePath+state.file_name+'\') no-repeat;'+
					'height:'+imgH+'px;width:'+imgW+'px;"/>';
				
			}

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

	setInfo(title,val);

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
	highlightSelected();

}



function setFreeValues(vals) {

	var c = getCharacter($('#characteristics').val());

	freeValues[$('#characteristics').val()]=vals[0];

	if (c.type=='range') {
		$('#selected').append('<option id="f'+(Math.floor(Math.random()*11))+'" value="f:'+$('#characteristics').val()+':'+(vals[0])+'">'+c.label+': '+vals[0]+'</option>');
	} else { 
		$('#selected').append('<option id="f'+(Math.floor(Math.random()*11))+'" value="f:'+$('#characteristics').val()+':'+(vals[0])+':'+(vals[1])+'">'+c.label+': '+_('mean')+' '+vals[0]+' &plusmn; '+vals[1]+' '+_('sd')+'</option>');
		sdValues[$('#characteristics').val()]=vals[1];
	}

	
	
	//getScores();

}

function getCharacter(id) {

	for(var i=0;i<characters.length;i++) {

		if (characters[i] && characters[i].id==id) return characters[i];
	}
	
	return null;

}

function addSelected(caller) {

	var c = getCharacter($('#characteristics').val());

	if (caller.id=='characteristics' && (c.type!='distribution' && c.type!='range')) return;

	if (c.type=='distribution') {

		showDialog(_('Enter a value'),sprintf(strDistro,sprintf(_('Enter the required values for "%s":'),c.label)));
		$('#dialogSD').val(sdValues[$('#characteristics').val()]);
		$('#dialogValue').val(freeValues[$('#characteristics').val()]);
		$('#dialogValue').focus();
		$('#dialogValue').select();

	} else
	if (c.type=='range') {

		showDialog(_('Enter a value'),sprintf(strRange,sprintf(_('Enter the required value for "%s":'),c.label)));
		$('#dialogValue').val(freeValues[$('#characteristics').val()]);
		$('#dialogValue').focus();
		$('#dialogValue').select();

	} else {
		
		var s = states[$('#states').val()];
		
		if (s) {
			
			if (s && (selected[s.id]==false || selected[s.id]==undefined)) {
				
				var c = getCharacter(s.characteristic_id);
		
				$('#selected').
					append('<option id="s'+s.id+'" value="c:'+s.characteristic_id+':'+s.id+'">'+c.label+': '+s.label+'</option>').
					val(s.id);
				selected[s.id] = true;
		
			} else {

				deleteSelected(s.id);

			}
			
		}
		
		//getScores();
		highlightSelected();

	}
	
}

function deleteSelected(id) {

	selected[id] = false;
	$('#s'+id).remove();
	removeHighlight();
	highlightSelected();

}

function highlightSelected() {

	$('#selected option').each(function(i){
		var e = $(this).val().split(':');

		$('#characteristics option').each(function(i){

			if ($(this).val()==e[1]) {
				$(this).addClass('character-selected');
				if ($(this).text().substring(0,selectIndicator.length) != selectIndicator) $(this).text(selectIndicator + $(this).text());
			}

		});
		
		if (e[1]==$('#characteristics :selected').val()) {
			
			if (e[0]=='f') {
				
				var preVal = '';

				$('#selected').children().each(function(){
					var t = $(this).val().split(':');
					if (t[1]==e[1]) {
						preVal = e[2];
						if (e[3]) preVal = 'mean '+preVal+' &plusmn; '+e[3]+' sd';
					}
				});	
				
				if (preVal) setInfo(null,null,'Current value: '+preVal);
				
			} else {

				$('#states option').each(function(i){
	
					if (e[2]==$(this).val())  {
						$(this).addClass('state-selected');
						if ($(this).text().substring(0,selectIndicator.length) != selectIndicator) $(this).text(selectIndicator + $(this).text());
					}
	
				});

			}

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
	
	setInfo('','',' ');

}

function deleteSelectedState(id) {

	// anatomy of a selected element: type[f,c]:character id:state id
	var id = $('#selected').val().split(':');

	if (id[0]!='f') selected[id[2]] = false;

	$('#selected').children(':selected').remove();

	getScores($('#selected').children().length==0 ? 'clear' : null);
	
	removeHighlight();

	highlightSelected();

}

function clearSelectedStates() {

	$('#selected').empty();

	selected = selected.splice(0,0);

	//getScores('clear');
	
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
	
	//alert(opt);
	
	getData('get_taxa',opt,'fillScores');

}

function fillScores(obj,char) {

	$('#scores').empty();

	if (!obj) return;

    var textToInsert = [];

	for (var i=0;i<obj.length;i++) {
		
        textToInsert[i] =
			'<option ondblclick="'+(obj[i].type=='mtx' ? 'goMatrix' : 'goTaxon')+'('+obj[i].id+');" value="'+obj[i].id+'">'+
				(obj[i].s!=undefined ? obj[i].s+'%: ' : '')+
				(obj[i].type=='mtx' ? _('Matrix: ') : '')+obj[i].l+'</option>';
     
    }

	$('#scores').append(textToInsert.join(''));

	highlightSelected();
	
	$('#scores option').each(function(i){
		$(this).attr('selected','');
	});	

	if (storedShowState && storedShowState=='pattern') {
		showMatrixPattern();
		storedShowState='';
	} else {
		showMatrixResults();
	}

}

function showMatrixResults() {
	
	$('#search-pattern').css('display','none');
	$('#search-results').css('display','block');
	getData('store_showstate_results',-1); // storing state

}

function showMatrixPattern() {

	$('#search-pattern').css('display','block');
	$('#search-results').css('display','none');
	getData('store_showstate_pattern',-1); // storing state

}

function setSelectedState(id,stateId,charId,label,value) {

	var val = id.split(':');
	var c = getCharacter(charId);
	
	if (val[0]=='c') {
		
		$('#selected').append('<option id="s'+stateId+'" value="c:'+charId+':'+stateId+'">'+c.label+': '+label+'</option>').val(stateId);

		selected[stateId] = true;

	} else {

		if (c.type=='range')
			$('#selected').append('<option id="f'+(Math.floor(Math.random()*11))+'" value="'+id+'">'+c.label+': '+val[2]+'</option>');
		else
			$('#selected').append('<option id="f'+(Math.floor(Math.random()*11))+'" value="'+id+'">'+c.label+': '+_('mean')+' '+val[2]+' &plusmn; '+val[3]+' '+_('sd')+'</option>');
		
		freeValues[charId]=val[2];
		sdValues[charId]=val[3];
		
	}

}

function fillTaxonStates(obj,char) {

	$('#states tbody tr').remove();

	if (!obj) return;

    var textToInsert = [];

	for(var i in obj) {
		
        textToInsert[i] = '<tr class="highlight"><td>'+obj[i].type+'</td><td>'+obj[i].characteristic+'</td><td>'+obj[i].state.label+'</td><td></td></tr>';
     
    }

	$('#states').append(textToInsert.join(''));	
	
	$('#states').removeClass().addClass('visible');
	$('#help-text').removeClass().addClass('invisible');
}

function goExamine(id) {

	if (id) $("#taxon-list option:[value="+id+"]").attr("selected", true);

	getData('get_taxon_states',$('#taxon-list').val(),'fillTaxonStates');
	getData('store_examine_val',$('#taxon-list').val());

}

function goCompare(ids) {

	if (ids) {
		if (ids[0]) $("#taxon-list-1 option:[value="+ids[0]+"]").attr("selected", true);
		if (ids[1]) $("#taxon-list-2 option:[value="+ids[1]+"]").attr("selected", true);
	}
	
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
	getData('store_compare_vals',[id1,id2]);

}

function fillCompareResults(obj) {
	
	fillTaxaStatesOverviews(obj);
	fillTaxaStates(obj);
	
}

function fillTaxaStatesOverviews(obj) {
	
	$('#taxon_name_1').html(obj.taxon_1.taxon);
	$('#taxon_name_2').html(obj.taxon_2.taxon);
	
	var s = '';
	
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
	$('#help-text').removeClass().addClass('invisible');
}

function showMatrixSelect() {
	
	showDialog(_('Choose a matrix to use'));
	$('#dialog-content-inner').load('matrices.php');

}

function showCharacterSort() {

	var html = '<div id="lookup-DialogContent">';

	for(var i=0;i<characterOrders.length;i++) {
		if (characterOrders[i][0]==sortField)
			html = html + '<p class="row row-selected">'+characterOrders[i][1]+'</p>';
		else
			html = html + '<p class="row" onclick="sortCharacters(\''+characterOrders[i][0]+'\');closeDialog();">'+characterOrders[i][1]+'</p>';
	}

	html += '</div>';
	showDialog(_('Sort characters by:'),html);

}