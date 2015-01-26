function doSaveOrder()
{
	var form=$('<form method="post"></form>').appendTo('body');
	form.append('<input type="hidden" name="action" value="saveorder" />');
	 
	$( "li[id^=sortable]").each(function( index ) {
		form.append('<input type="hidden" name="sortable[]" value="'+$(this).attr('id').replace('sortable','')+'" />');
	});
	
	form.submit();
}


var name0Focused=false;

function duplicateSysLabel()
{
	if ($('#sysname').val().length>0 && !name0Focused)
	{
		$('#name').val($('#sysname').val());
		$('#names-0').val($('#sysname').val());
	}
}

function setName0Focused()
{
	name0Focused=true;
}



function checkAndSaveForm()
{
	var buffer=Array();

	$(':input').each(function()
	{
		if ($(this).attr('mandatory')=='mandatory' && $(this).val().length<1)
		{
			var id=$(this).attr('id');
			var label=$("label[for='"+id+"']").html();
			label=label.length<1 ? id : label;
			buffer.push(label);
		}
	});

	if (buffer.length>0)
	{
		alert("Values are missing for the following mandatory field(s):\n"+buffer.join("\n"));
		return false;
	}
	else
	{
		if ($('#project_type_id option:selected').attr('sysname').indexOf('date')!==0)
		{
			$('#date_format_id').remove();
		}
		
		$(":input[type!='hidden']").each(function()
		{
			if ($(this).is(':hidden')) $(this).remove();
				
		});
				
		$('#theForm').submit();
	}
		
}

function deleteItem()
{
	if (confirm(_('Are you sure?')))
	{
		$('#action').val('delete');
		$('#theForm').submit();
	}
}



var valuelist=[];
var valuelisthash=null;
var usersave=false;

function hash(a)
{
	var b='';
	for (var i=0;i<valuelist.length;i++)
	{
		b+=valuelist[i]+"\t";
	}
	return b;
}

function setUserSave()
{
	usersave=true;
}

function setInitialValueListHash()
{
	valuelisthash=hash(valuelist);
}

function publishRemark(r)
{
	$('#remarks').toggle(true).html(r).fadeOut(1000);
}

function characterCount()
{
	$('#character-count').html(maxlength-$('#newvalue').val().length);
}

function doAddTraitValue(v)
{
	valuelist.push(v);
}

function addTraitValue(checkresult)
{
	var v=$('#newvalue').val();

	if (v.length==0)
	{
		publishRemark(_("cannot add a empty value"));
		return;
	}

	if (checkresult || typeof checkTraitValue=="function")
	{
		if (checkresult)
		{
			var r=checkresult;
		}
		else
		{
			var r=checkTraitValue(v);
		}
		
		if (r.result!=true)
		{
			if (r.remarks.length>0)
			{
				alert("Error(s) occurred:\n"+r.remarks.join("\n"));
			}
			return;
		}
		else
		{
			if (r.remarks.length>0)
			{
				publishRemark(r.remarks.join("<br />"));
			}
		}
	}
	
	doAddTraitValue(v);
	$('#newvalue').val('');
	characterCount();
}

function updateValueCount()
{
	$('#value-count').html(valuelist.length);
}


function removeTraitValue(i)
{
	valuelist.splice(i,1);
}

function updateValueList()
{
	$('#valuelist').html('');
	if (valuelist.length==0) return;
	
	var b=[];
	for (var i=0;i<valuelist.length;i++)
	{
		b.push(
			'<span class="value">'+valuelist[i]+'</span> \
			<a href="#" class="edit" onclick="removeTraitValue('+i+');updateValueList();updateValueCount();return false;">'+
			_('remove')+
			'</a>');
	}
	$('#valuelist').html('<li>'+b.join('</li><li>')+'</li>');
}

function checkUnsavedValues()
{
	if (!usersave && valuelisthash && valuelisthash!=hash(valuelist))
	{
		return "There are unsaved values!\nLeave page anyway?";
	}
}

function reorderValueList()
{
	valuelist.empty();
	$("#valuelist li").each(function( index )
	{
		$.each( $.parseHTML( $(this).html() ), function( i, el )
		{
			if (el.className=='value') valuelist.push(el.textContent);
		});
	});
}


function saveValues()
{
	/*
	if (valuelist.length<1)
	{
		publishRemark(_("nothing to save"));
	}
	else
	*/
	{
		var form=$('<form method="post"></form>').appendTo('body');
		form.append('<input type="hidden" name="action" value="save" />');
		
		$.each( valuelist, function( index, value ) {
			form.append('<input type="hidden" name="values[]" value="'+value+'" />');
		});

		form.submit();
	}
}

function traitValuesInitialise()
{
	updateValueList();
	updateValueCount();
	setInitialValueListHash();
	$('#newvalue').focus();
}

