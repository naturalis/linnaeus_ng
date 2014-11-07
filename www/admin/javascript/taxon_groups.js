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
