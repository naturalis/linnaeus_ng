var keyFullKeyPathVisibility = false;
var keyStepId = false;
var keyChoiceId = false;
var keyCurrentTargetStep = null;
var keyCurrentTargetTaxon = null;

function keyToggleFullKeyPath()
{
	if (keyFullKeyPathVisibility)
	{
		$('#keypath-full').removeClass('keypath-full-visible').addClass('keypath-full-invisible');
		keyFullKeyPathVisibility=false;
	} 
	else
	{
		$('#keypath-full').removeClass('keypath-full-invisible').addClass('keypath-full-visible');
		keyFullKeyPathVisibility=true;
	}
}

function keyChoiceContentCheck()
{
	if (tinyMCE.get('contentDefault').getContent().trim()=='')
	{
		alert(_('You have to enter text for this choice'));
		$('#contentDefault').focus();
		return false;
	} 
	else
	{
		return true;
	}
}


function keyChoiceDelete(id)
{
	if (confirm(_('Are you sure you want to delete this choice?')))
	{
		window.open('choice_edit.php?id='+id+'&action=delete','_top');
	}
}

function keyDeleteImage()
{
	if (confirm(_('Are you sure you want to delete this image?')))
	{
		$('#action').val('deleteImage');
		$('#theForm').submit();
	}
}

function keyDeleteAllImages()
{
	if (confirm(_('Are you sure you want to delete all legacy images?')))
	{
		$('#action').val('deleteAllImages');
		$('#theForm').submit();

	}
}

function keyCheckTargetIntegrity(ele)
{
	if (keyCurrentTargetStep!=null || keyCurrentTargetTaxon!=null)
	{
		if (!confirm(_('Beware: you are changing the target of this choice.\nThis can radically alter the workings of your key.\nDo you wish to continue?')))
		{
			$(ele).val($(ele).attr('prev')==undefined ? 0 : $(ele).attr('prev'));
			return;
		}
	}

	if (ele.id == 'res_taxon_id' && $('#res_taxon_id option:selected').val()!='0')
	{
		var sel = $('#res_keystep_id');
		sel.attr('prev',sel.val());
		sel.val(0);

	} 
	else
	if (ele.id == 'res_keystep_id' && $('#res_keystep_id option:selected').val()!='0')
	{
		var sel = $('#res_taxon_id');
		sel.attr('prev',sel.val());
		sel.val(0);
	}
}


function keySaveData(id,language,content,action,postFunction)
{
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
			if (postFunction) postFunction(data);
		}
	});
}

function keyGetKeystepContent(language)
{
	allGeneralGetLabels(language,'get_keystep_content','keySetKeystepContent',keyStepId);
}

function keySetKeystepContent(obj,language)
{
	if (language==allDefaultLanguage) {
		$('#titleDefault').val(obj ? obj.title : '');
		$('#contentDefault').val(obj ? obj.content : '');
		$('#default-key-title').html(obj ? obj.title : '...');
	} else {
		$('#titleOther').val(obj ? obj.title : '');
		$('#contentOther').val(obj ? obj.content : '');
	}
}

function keySaveStepContent(type,postFunction)
{
	if (type=='default')
		content = [$('#titleDefault').val(),$('#contentDefault').val()];
	else
		content = [$('#titleOther').val(),$('#contentOther').val()];
	
	keySaveData(
		keyStepId,
		type=='default' ? allDefaultLanguage : allActiveLanguage,
		content,
		'save_keystep_content',
		postFunction
	);
}

function keyDeleteKeyStep()
{
	if (!allDoubleDeleteConfirm(_('keystep'),$('#key-step-title').html())) return;

	$('#action').val('delete');
	$('#theForm').submit();
}

function keySetMapInfoLabel(node)
{
	id = node.id;

	if(id.substr(0,1)=='t')
	{ 
		var d = sprintf(_('Click to edit taxon "%s"'),'<a href="../species/taxon.php?id='+node.data.id+'">'+node.data.taxon+'</a>');
	} 
	else
	{
		var d = sprintf(
			_('Click to see step "%s"'),
			'<a href="step_show.php?node='+node.data.node+'">'+
			node.data.number+'. '+
			(node.data.title ? node.data.title : '...')+'</a>'
		);
	}

	$('#info').html(d);
}

