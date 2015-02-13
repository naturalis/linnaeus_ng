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
		$('#theForm').unbind('submit');
		$('#action').val('delete');
		$('#theForm').submit();
	}
}

var valuelist=[];
var languages=[]
var valuelisthash=null;
var usersave=false;
var havelabels=false;
var newcounter=-1;

function hash(a)
{
	var b='';
	for (var i=0;i<valuelist.length;i++)
	{
		b+=valuelist[i].id+valuelist[i].value+valuelist[i].labels.join("\t")+"\t";
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
	if (havelabels && !v.labels)
	{
		var labels=[];
		for (var j=0;j<languages.length;j++)
		{
			labels.push( { language:languages[j].id, label:'' } );
		}
		v.labels=labels;
	}

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
	
	doAddTraitValue( { id: newcounter--, value: v } );
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
		var val=valuelist[i];
		var l=[];

		for (var j=0;j<languages.length;j++)
		{
			var lng=languages[j];
			var thisval='';
			
			for(var k=0;k<val.labels.length;k++)
			{
				if (val.labels[k].language==lng.id)
				{
					thisval=val.labels[k].label;
				}
			}
			
			l.push(
				'<span class="language-labels">'+
				lng.language+': \
				<input \
					onkeyup="addTraitValueLabel(this)" \
					class="language-labels" \
					type="text" \
					maxlength="4000" \
					value="'+thisval+'" \
					value-id="'+i+'" \
					language-id="'+lng.id+'"> \
				</span>');
		}

		b.push(
			'<li data-id="'+i+'">'+
			val.value+' \
			<a href="#" class="edit" onclick="removeTraitValue('+i+');updateValueList();updateValueCount();return false;">'+
			_('remove')+
			'</a>'+
			'<br />'+l.join('')+
			'</li>'
			);
	}
	$('#valuelist').html(b.join(''));
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
	var b=[];
	$("#valuelist li").each(function( index )
	{
		b.push(valuelist[$(this).attr('data-id')]);
	});
	valuelist.empty();
	valuelist=b.splice(0);
}

function traitValuesInitialise()
{
	updateValueList();
	updateValueCount();
	setInitialValueListHash();
	$('#newvalue').focus();
}

function doAddTraitLanguage(p)
{
	languages.push(p);
}

function addTraitValueLabel(ele)
{
	
	var id=$(ele).attr('value-id');
	var language=$(ele).attr('language-id');
	var value=$(ele).val();

	//console.log(id,language,value);
	var add=true;
	for(var i=0;i<valuelist.length;i++)
	{
		if (i==id)
		{
			for(var j=0;j<valuelist[i].labels.length;j++)
			{
				if (valuelist[i].labels[j].language==language)
				{
					valuelist[i].labels[j].label=value;
					add=false;
				}
			}
			if (add)
			{
				valuelist[i].labels.push( { language:language,label:value });
			}
		}
	}
	
}

function saveValues()
{
	var form=$('<form method="post"></form>').appendTo('body');
	form.append('<input type="hidden" name="action" value="save" />');
	
	for(var i=0;i<valuelist.length;i++)
	{
		var value=valuelist[i];
		
		form.append('<input type="hidden" name="values['+value.id+']" value="'+value.value+'" />');

		if (value.labels)
		{
			for(var j=0;j<value.labels.length;j++)
			{
				var label=value.labels[j];
				form.append('<input type="hidden" name="valuelabels['+value.id+']['+label.language+']" value="'+label.label+'" />');
			}
		}
	}
	
	form.submit();
}

