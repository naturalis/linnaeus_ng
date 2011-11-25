var keyFullKeyPathVisibility = false;
var keyStepId = false;
var keyChoiceId = false;
var keyCurrentTargetStep = null;
var keyCurrentTargetTaxon = null;

function keyToggleFullKeyPath() {

	if (keyFullKeyPathVisibility) {
		$('#keypath-full').removeClass('keypath-full-visible').addClass('keypath-full-invisible');
		keyFullKeyPathVisibility=false;
	} else {
		$('#keypath-full').removeClass('keypath-full-invisible').addClass('keypath-full-visible');
		keyFullKeyPathVisibility=true;
	}

}

function keyChoiceSave() {
	
	if ($('#contentDefault').val().trim()=='') {
		alert(_('You have to enter text for this choice'));
		$('#contentDefault').focus();
	} else {
		keySaveChoiceContent('default');
		keySaveChoiceContent('other');
		$('#theForm').submit();
	}
}


function keyChoiceDelete(id) {
	
	if (confirm(_('Are you sure you want to delete this choice?'))) {

		if (id) {

			$('#id3').val(id);
			$('#delChoiceForm').submit();

		} else {

			$('#action').val('delete');
			$('#theForm').submit();

		}

	}

}

function keyDeleteImage() {

	if (confirm(_('Are you sure you want to delete this image?'))) {

		$('#action').val('deleteImage');
		$('#theForm').submit();

	}

}

function keyCheckTargetIntegrity(ele) {
	
	if (keyCurrentTargetStep!=null || keyCurrentTargetTaxon!=null) {

		if (!confirm(_('Beware: you are changing the target of this choice.\nThis can radically alter the workings of your key.\nDo you wish to continue?'))) {

			$(ele).val($(ele).attr('prev')==undefined ? 0 : $(ele).attr('prev'));

			return;
		}

	}

	if (ele.id == 'res_taxon_id' && $('#res_taxon_id option:selected').val()!='0') {

		var sel = $('#res_keystep_id');
		sel.attr('prev',sel.val());
		sel.val(0);

	} else
	if (ele.id == 'res_keystep_id' && $('#res_keystep_id option:selected').val()!='0') {

		var sel = $('#res_taxon_id');
		sel.attr('prev',sel.val());
		sel.val(0);

	}

}

var keyRanks = Array();
var keyRankBorder = false;

function keyAddRank(id,rank) {

	keyRanks[keyRanks.length] = [id,rank];

}

function keyMoveBorder(id) {

	keyRankBorder = id;
	keyShowRanks();

}

function keyShowRanks() {

	var first = true;
	var b= '';

	if (keyRankBorder==false) keyRankBorder = keyRanks[0][0];

	for (var i=0;i<keyRanks.length;i++) {

		if (keyRanks[i][0]==keyRankBorder) {

			b = b + 
					'<tr>'+
						'<td id="sub1" colspan="2" class="rankRedLine"></td>'+
						'<td class="rankRedLineEnd"></td>'+
					'</tr>'+
					"\n";

		}

		b = b + 
			'<tr class="tr-highlight">'+
				'<td colspan="2" class="rankSelectedRank" rankId="'+keyRanks[i][0]+'" '+
					'ondblclick="taxonRemoveRank(this.attributes.rankId.value)">'+
					keyRanks[i][1]+
				'</td>'+
				'<td>'+
					(keyRanks[i+1]!=undefined && keyRanks[i+1][0]==keyRankBorder ? 
						'<span class="rankArrow" onclick="keyMoveBorder('+keyRanks[i][0]+');">&uarr;</span>' : 
						'' )+
					(i<keyRanks.length-1 && keyRanks[i+1]!=undefined && keyRanks[i][0]==keyRankBorder ? 
						'<span class="rankArrow" onclick="keyMoveBorder('+keyRanks[i+1][0]+');">&darr;</span>' : 
						'')+
				'</td>'+
			'</tr>'+
			"\n";


	}

	$('#selected-ranks').html('<table id="selectedRanksTable">'+b+
		'</table><input type="hidden" name="keyRankBorder" value="'+keyRankBorder+'">');

}

var keyOpenChoices = Array();
var keyAllOpened = false;

function keyShowChoiceDetails(ele,id) {

	if (id=='all') {

		for (var i=0;i<=99;i++) {
			if (keyAllOpened) {
				$('#choice-'+i).removeClass('key-choice-details').addClass('key-choice-details-invisible');
				$(ele).html(_('(show all)'));
			} else {
				$('#choice-'+i).removeClass('key-choice-details-invisible').addClass('key-choice-details');
				$(ele).html(_('(hide all)'));
			}
		}

		keyAllOpened = !keyAllOpened;

	} else {

		if (keyOpenChoices[id]==true) {
			$('#choice-'+id).removeClass('key-choice-details').addClass('key-choice-details-invisible');
			keyOpenChoices[id]=false;
			$(ele).html('show details');
		} else {
			$('#choice-'+id).removeClass('key-choice-details-invisible').addClass('key-choice-details');
			keyOpenChoices[id]=true;
			$(ele).html('hide details');
		}

	}

}

function keySaveData(id,language,content,action) {

	$.ajax({
		url : "ajax_interface.php",
		data : ({
			'action' : action ,
			'id' : id ,
			'content' : content , 
			'language' : language ,
			'time' : allGetTimestamp()	
		}),
		type: "POST",
		async: allAjaxAsynchMode ,
		success: function (data) {
			allSetMessage(data);
		}

	});

}

function keyGetKeystepContent(language) {
	
	allGeneralGetLabels(language,'get_keystep_content','keySetKeystepContent',keyStepId);
	
}

function keySetKeystepContent(obj,language) {

	if (language==allDefaultLanguage) {
		$('#titleDefault').val(obj ? obj.title : '');
		$('#contentDefault').val(obj ? obj.content : '');
		$('#default-key-title').html(obj ? obj.title : '...');
	} else {
		$('#titleOther').val(obj ? obj.title : '');
		$('#contentOther').val(obj ? obj.content : '');
	}

}

function keySaveStepContent(type) {

	if (type=='default')
		content = [$('#titleDefault').val(),$('#contentDefault').val()];
	else
		content = [$('#titleOther').val(),$('#contentOther').val()];
	
	keySaveData(
		keyStepId,
		type=='default' ? allDefaultLanguage : allActiveLanguage,
		content,
		'save_keystep_content'
	);

}

function keySaveStepSubmit() {

	if ($('#number').val().length==0) {
		alert(_('A number is required.'));
		$('#number').focus();
	} else
	if ($('#titleDefault').val().length==0) {
		alert(_('A title is required.'));
		$('#titleDefault').focus();
	} else
	if ($('#titleOther') && $('#titleOther').val().length==0) {
		alert(_('A title is required.'));
		$('#titleOther').focus();
	} else {
		$('#theForm').submit();
	}

}


function keyDeleteKeyStep() {

	if (!allDoubleDeleteConfirm(_('keystep'),$('#key-title').html())) return;

	$('#delForm').submit();

}

function keySaveChoiceContent(type) {

	if (type=='default')
		content = ['',$('#contentDefault').val()];
	else
		content = ['',$('#contentOther').val()];

	keySaveData(
		keyChoiceId,
		type=='default' ? allDefaultLanguage : allActiveLanguage,
		content,
		'save_key_choice_content'
	);

}

function keyGetChoiceContent(language) {
	
	allGeneralGetLabels(language,'get_key_choice_content','keySetChoiceContent',keyChoiceId);
	
}

function keySetChoiceContent(obj,language) {

	if (language==allDefaultLanguage) {
		$('#contentDefault').val(obj ? obj.choice_txt : '');
	} else {
		$('#contentOther').val(obj ? obj.choice_txt : '');
	}

}

function keySetMapInfoLabel(node) {

	id = node.id;

	if(id.substr(0,1)=='t') { 

		var d = sprintf(_('Click to edit taxon "%s"'),'<a href="../species/taxon.php?id='+node.data.id+'">'+node.data.taxon+'</a>');

	} else {

		var d = sprintf(
			_('Click to see step "%s"'),
			'<a href="step_show.php?node='+node.data.node+'">'+
			node.data.number+'. '+
			(node.data.title ? node.data.title : '...')+'</a>'
		);

	}

	$('#info').html(d);

}

function keyStepUndo() {

	allGeneralGetLabels(allDefaultLanguage,'get_keystep_undo','keyRestoreStep',keyStepId);

}

function keyRestoreStep(obj,language) {

	if (obj==null) {
		allSetMessage(_('nothing to restore'));
		return;
	}

	if (obj.language_id==allDefaultLanguage) {
		$('#titleDefault').val(obj ? obj.title : '');
		$('#contentDefault').val(obj ? obj.content : '');
	} else {
		allActiveLanguage = obj.language_id;
		allDrawLanguages();
		$('#titleOther').val(obj ? obj.title : '');
		$('#contentOther').val(obj ? obj.content : '');
	}
	
	allSetMessage(_('restored'));

}

function keyChoiceUndo() {

	allGeneralGetLabels(allDefaultLanguage,'get_key_choice_undo','keyRestoreChoice',keyChoiceId);


}

function keyRestoreChoice(obj,language) {

	if (obj==null) {
		allSetMessage(_('nothing to restore'));
		return;
	}

	if (obj.language_id==allDefaultLanguage) {
		$('#contentDefault').val(obj ? obj.choice_txt : '');
	} else {
		allActiveLanguage = obj.language_id;
		allDrawLanguages();
		$('#contentOther').val(obj ? obj.choice_txt : '');
	}
	
	allSetMessage(_('restored'));

}
