var keyFullKeyPathVisibility = false;
var keyStepId = false;
var keyChoiceId = false;

function keyToggleFullKeyPath() {

	if (keyFullKeyPathVisibility) {
		$('#keypath-full').removeClass('keypath-full-visible').addClass('keypath-full-invisible');
		keyFullKeyPathVisibility=false;
	} else {
		$('#keypath-full').removeClass('keypath-full-invisible').addClass('keypath-full-visible');
		keyFullKeyPathVisibility=true;
	}

}

function keyChoiceDelete() {
	
	if (confirm(_('Are you sure you want to delete this choice?'))) {

		$('#action').val('delete');
		$('#theForm').submit();

	}

}

function keyDeleteImage() {

	if (confirm(_('Are you sure you want to delete this image?'))) {

		$('#action').val('deleteImage');
		$('#theForm').submit();

	}

}

function keyCheckTargetIntegrity(ele) {

	if (ele.id == 'res_taxon_id' && $('#res_taxon_id option:selected').val()!='0') {

		$('#res_keystep_id').val(0);

	} else
	if (ele.id == 'res_keystep_id' && $('#res_keystep_id option:selected').val()!='0') {

		$('#res_taxon_id').val(0);

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

function keyShowChoiceDetails(id) {

	if (id=='all') {

		for (var i=0;i<=99;i++) {
			if (keyAllOpened)
				$('#choice-'+i).removeClass('key-choice-details').addClass('key-choice-details-invisible');
			else
				$('#choice-'+i).removeClass('key-choice-details-invisible').addClass('key-choice-details');
		}

		keyAllOpened = !keyAllOpened;

	} else {

		if (keyOpenChoices[id]==true) {
			$('#choice-'+id).removeClass('key-choice-details').addClass('key-choice-details-invisible');
			keyOpenChoices[id]=false;
		} else {
			$('#choice-'+id).removeClass('key-choice-details-invisible').addClass('key-choice-details');
			keyOpenChoices[id]=true;
		}

	}

}

function keyGetKeystepContent(language) {
	
	allGeneralGetLabels(language,'get_key_step_content','keySetKeystepContent',keyStepId);
	
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

function keySaveStepTitle(title,type) {

	keySaveData(
		keyStepId,
		type=='default' ? allDefaultLanguage : allActiveLanguage,
		title,
		'save_step_title'
	);

}

function keySaveStepText(title,type) {

	keySaveData(
		keyStepId,
		type=='default' ? allDefaultLanguage : allActiveLanguage,
		title,
		'save_step_text'
	);

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
			if(data=='<ok>') {
				allSetMessage(_('saved'));
			}
		}

	});

}

function keyDeleteKeyStep() {

	if (!allDoubleDeleteConfirm(_('keystep'),$('#key-title').html())) return;

	$('#delForm').submit();

}

function keyGetChoiceContent(language) {
	
	allGeneralGetLabels(language,'get_key_choice_content','keySetChoiceContent',keyChoiceId);
	
}

function keySetChoiceContent(obj,language) {

	if (language==allDefaultLanguage) {
		$('#titleDefault').val(obj ? obj.title : '');
		$('#contentDefault').val(obj ? obj.choice_txt : '');
		$('#default-choice-title').html(obj ? obj.title : '...');
	} else {
		$('#titleOther').val(obj ? obj.title : '');
		$('#contentOther').val(obj ? obj.choice_txt : '');
	}

}

function keySaveChoiceTitle(title,type) {

	keySaveData(
		keyChoiceId,
		type=='default' ? allDefaultLanguage : allActiveLanguage,
		title,
		'save_choice_title'
	);

}

function keySaveChoiceText(title,type) {

	keySaveData(
		keyChoiceId,
		type=='default' ? allDefaultLanguage : allActiveLanguage,
		title,
		'save_choice_text'
	);

}

function keySetMapInfoLabel(node) {

	id = node.id;

	if(id.substr(0,1)=='t') { 

		var d = sprintf(_('Click to edit taxon "%s"'),'<a href="../species/taxon.php?id='+node.data.id+'">'+node.data.taxon+'</a>');

	} else {

		var d = sprintf(_('Click to see step "%s"'),'<a href="step_show.php?node='+node.data.node+'">'+node.data.title+'</a>');

	}

	$('#info').html(d);

}



















