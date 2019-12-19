var reverttexts=Array();
var currenttexts=Array();
var currentpublish=Array();
var messageFadeOutDelay=2000;


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

	var editor=CKEDITOR.replace('editor'+id,
	{
		toolbar:'Basic',
		height: 300,
        removeButtons: 'subscript,superscript'
		/*,
		entities_processNumerical: true
		*/
	});

	editor.on( 'dataReady', function( evt )
	{
		currenttexts[id]=evt.editor.getData();
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

function changeLanguage(caller)
{
    var query = window.location.search;
    var params = query.slice(1).split('&');
    var newquery = "?";
    for (var i=0; i < params.length; i++) {
    	if (params[i].search('activeLanguage=') < 0) {
    		newquery = newquery + params[i] + "&";
		}
	}
	newquery = newquery + "activeLanguage=" + $(caller).val();

    window.location = window.location.origin + window.location.pathname + newquery;

	return false;
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
    var lang=$('#language_id').val();

	$.ajax({
		url : 'paspoort_ajax_interface.php',
		type: 'POST',
		data: {
			action: 'save_passport',
			taxon: taxon,
            lang: lang,
			page : page,
			content : content,
			publish : publish,
		},
		success : function (data)
		{
			//console.log(data);
			if(data==true)
			{
				$('#message'+id).html('Tekst opgeslagen.').toggle(true).fadeOut(messageFadeOutDelay);
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


var collectedreferences=[];

function collectReferences(val)
{
	var exists=false;
	for (var j in collectedreferences)
	{
		if (collectedreferences[j].id==val)
		{
			exists=true;
		}
	}
	if (!exists)
	{
		collectedreferences.push( { id:val,label:$('#reference').text() } );
	}

	displayReferences();
}

function removeReference(i)
{
	var index=-1;
	for (var j in collectedreferences)
	{
		if (collectedreferences[j].id==i)
		{
			var index=j;
		}
	}
	if (index>-1)
	{
		collectedreferences.splice(index, 1);
	}

	displayReferences();
}

function displayReferences()
{
	var buffer=[]

	for (var i in collectedreferences)
	{
		var ele=collectedreferences[i];
		buffer[i]=ele.label+' <a href="#" class="edit" onclick="removeReference('+ele.id+');return false;">delete</a>';
	}

	$('#references').html(buffer.length>0 ? '<ul><li>'+buffer.join('</li><li>')+'</li></ul>' : '');
}

function doPassportMeta()
{
	var havedata=false;
	$('[id^=actor_id] :selected').each(function(){ if($(this).val().length!=0) havedata=true; });
	$('[id^=organisation_id] :selected').each(function(){ if($(this).val().length!=0) havedata=true; });
	if (collectedreferences.length>0) havedata=true;

	if (!havedata) return;

	for (var i in collectedreferences)
	{
		if (collectedreferences[i].id)
			$('#theForm').append('<input type="hidden" name="reference_id[]" value="'+collectedreferences[i].id+'" />');
	}
	$('#theForm').submit();
}

function doDeleteMeta(id)
{
	if (id=='*' && !confirm('Weet u het zeker?')) return;

	$('#action').val('delete');
	$('#theForm').append('<input type="hidden" name="tab" value="'+id+'" />');
	$('#theForm').submit();
}



























