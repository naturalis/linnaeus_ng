var groups=Array();

function registerGroup(p)
{
	groups.push(p);
}

function closeGroupSelector()
{
	$('#group-list').toggle(false);
}

function showGroupSelector(id)
{
	var buffer=Array();
	buffer.push(
		'Add to group: \
		<a href="#" class="edit" onclick="closeGroupSelector();return false;">(close window)</a>\
		<span style="float:right;padding-right:2px;" id="save-msg"></span>'
	);
	
	for(var i=0;i<groups.length;i++)
	{
		var group=groups[i];
		var b="";
		for(var j=0;j<group.level;j++)
		{
			b+="&nbsp;&nbsp;";
		}
		buffer.push(b+'<a href="#" onclick="saveTaxonToGroup('+id+','+group.id+');return false;">'+group.sys_label+'</a>');
	}
	$('#group-list').html(buffer.join('<br/>')).toggle(true);;
	
	allStickElementUnderElement('add'+id,'group-list');
}

function saveTaxonToGroup(taxon,group)
{
	$.ajax({
		url : "taxongroup_ajax_interface.php" ,
		type: "POST",
		data : ({
			action : 'save_taxon_to_group',
			taxon : taxon,
			group : group,
			time : allGetTimestamp()			
		}),
		success : function (data) {
			if(data==1)
			{
				$("#add"+taxon).remove();
				$("#span"+taxon).addClass('non-zero');
				for(var i=0;i<groups.length;i++)
				{
					if (groups[i].id==group)
						$("#groups"+taxon).html('['+groups[i].sys_label+']');
				}				
				$("#save-msg").html('saved').fadeOut(750, function() { closeGroupSelector(); } );
			}
			else
			{
				$("#save-msg").html('error');
			}
		}
	});

}

function addTaxonToGroup(id,label)
{
	label=label?label:$('#taxon'+id).html();
	
	if ($('#selected'+id).length==0)
	{
		$('#selection').append(
			'<li id="selected'+id+'" value="'+id+'">'+
			label+
			'<a href="#" class="edit" onclick="removeTaxonFromGroup('+id+');return false;">remove</a></li>'
		);
		$('#add'+id).toggle(false);
	}
	dataChanged=true;
}

function removeTaxonFromGroup(id)
{
	$('#selected'+id).remove();
	$('#add'+id).toggle(true);
	dataChanged=true;
}

function doTaxongroupTaxaFormSubmit()
{
	$( "#selection").find("li").each(function( index ) {
		$("#theForm").append('<input type="hidden" name="taxa[]" value="'+ $( this ).attr("value") +'" />');
	});
	$("#theForm").append('<input type="hidden" name="action" value="save" />');
	$("#theForm").submit();
}

function doSaveGroupOrder()
{
	var form=$('<form method="post"></form>').appendTo('body');
	form.append('<input type="hidden" name="action" value="saveorder" />');
	 
	$( "li[id^=group]").each(function( index ) {
		form.append('<input type="hidden" name="groups[]" value="'+$(this).attr('id').replace('group','')+'" />');
	});
	
	form.submit();
}
