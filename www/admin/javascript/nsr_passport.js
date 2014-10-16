var reverttexts=Array();
var currenttexts=Array();
var currentpublish=Array();

function getcallerid(caller)
{
	return caller.id.replace(/[a-zA-Z]*/,'');
}

function openeditor(caller)
{
	var id=getcallerid(caller);

	if (!reverttexts[id]) reverttexts[id]=$('#content'+id).html();

	$('#edit'+id).toggle(false);
	$('#button-container'+id).toggle(true);
	$('#body'+id).addClass('passport-body-no-line');
	$('#content'+id).html('<textarea id="editor'+id+'">'+$('#content'+id).html()+'</textarea>');

	var editor=CKEDITOR.replace('editor'+id,{toolbar:'Basic',height: 300});

	editor.on( 'dataReady', function( evt ) {
		currenttexts[id]=evt.editor.getData();
		console.dir(currenttexts);
	});

}

function reverttext(caller)
{
	var id=getcallerid(caller);
	var editor='editor'+id;
	for(var i in CKEDITOR.instances)
	{
		var e=CKEDITOR.instances[i];
		if (e.name==editor)
		{
			CKEDITOR.instances[i].setData(reverttexts[id]);
		}
	}
}

function geteditordata(editor)
{
	var data='';

	for(var i in CKEDITOR.instances)
	{
		var e=CKEDITOR.instances[i];
		if (e.name==editor)
		{
			data=e.getData();
			
		}
	}
	return data;
}

function closeeditor(caller)
{
	var id=getcallerid(caller);
	var editor='editor'+id;
	var data=geteditordata(editor);

	if (currenttexts[id]!=data)
	{
		if (confirm("De tekst is gewijzigd.\nNieuwe tekst opslaan voor afsluiten van de editor?"))
		{
			saveeditordata(caller);
		}
		currenttexts[id]=data;
	}

	$('#edit'+id).toggle(true);
	$('#button-container'+id).toggle(false);
	$('#body'+id).removeClass('passport-body-no-line');
	$('#content'+id).html(currenttexts[id]);
}

function saveeditordata(caller)
{
	var id=getcallerid(caller);
	var editor='editor'+id;
	var content=geteditordata(editor);
	var publish=$('#publish'+id).is(':checked');

	if (currenttexts[id]==content && currentpublish[id]==publish) return;

	if (publish && content.length==0)
	{
		alert('Een leeg paspoort wordt niet getoond, ook niet wanneer het gepubliceerd is!');
	}

	var page=$('#page'+id).val();
	var taxon=$('#taxon_id').val();

	$.ajax({
		url : 'paspoort_ajax_interface.php',
		type: 'POST',
		data: {
			action: 'save_passport',
			taxon: taxon,
			page : page,
			content : content,
			publish : publish,
		},
		success : function (data)
		{
			if(data==true)
			{
				$('#message'+id).html('Tekst opgeslagen.').toggle(true).fadeOut(2000);
				$('#indicator'+id).html(content.length==0? 'leeg' : content.length+' tekens');

				if (content.length==0)
				{
					$('#indicator'+id).removeClass('passport-unpublished').removeClass('passport-published').addClass('passport-leeg');
				}
				else
				if (currentpublish!==publish && publish==true)
				{
					$('#indicator'+id).removeClass('passport-unpublished').addClass('passport-published');
				}
				else
				if (currentpublish!==publish && publish==true)
				{
					$('#indicator'+id).removeClass('passport-unpublished').addClass('passport-published');
				}
				else
				if (currentpublish!==publish && publish==false)
				{
					$('#indicator'+id).removeClass('passport-published').addClass('passport-unpublished');
				}
				currenttexts[id]=content;
				currentpublish[id]=publish;
			}
			else
			{
				$('#message'+id).html('Tekst <b>niet</b> opgeslagen.').toggle(true).fadeOut(2000);
			}
			
		}
	});	
	
}

