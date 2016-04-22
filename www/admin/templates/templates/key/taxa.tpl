{include file="../shared/admin-header.tpl"}

<script type="text/JavaScript">

var new_taxa=Array();

function add_taxon()
{
	var new_id=$('#taxon_id').val();
	var new_label=$('#taxon').val();

	for (var i=0;i<new_taxa.length;i++)
	{
		if (new_taxa[i].id==new_id) return;
	}

	new_taxa.push( { id:new_id, label:new_label } )
}

function remove_taxon( id )
{
	for (var i=0;i<new_taxa.length;i++)
	{
		if (new_taxa[i].id==id) 
		{
			new_taxa.splice(i,1);
			return;
		}
	}
}

function print_taxa()
{
	$('#new_taxa').html('');
	for (var i=0;i<new_taxa.length;i++)
	{
		$('#new_taxa').append(
			'<li>' + new_taxa[i].label + '<a href="#" onclick="remove_taxon('+new_taxa[i].id+');print_taxa();return false;" style="padding:0 5px 0 5px"> x </a></li>' );
	}
}

function del_ref( id )
{
	if (confirm(_('Are you sure?')))
	{
		$('#action').val('delete');
		var form=$('#theForm');
		form.append('<input type=hidden name=link_id value="'+id+'" />');
		//$(window).unbind('beforeunload');
		form.submit();
	};
}

function saveForm()
{
	var form=$('#theForm');

	for (var i=0;i<new_taxa.length;i++)
	{
		form.append('<input type=hidden name=new_taxa[] value="'+new_taxa[i].id+'" />');
	}

	form.submit();
}
	
</script>


<div id="page-main">

    <h2>{t}Linked taxa{/t}</h2>
    
    <p>

    <ul id="old_refs">
    {foreach $taxa v k}
    <li>
		{$v.taxon}
        <a href="#" onclick="del_ref({$v.id});return false;" style="padding:0 5px 0 5px"> x </a>
	</li>
    {/foreach}
    </ul>
    
    <form method="post" id="theForm">
        <input type="hidden" name="id" value="{$id}">
        <input type="hidden" name="action" id="action" value="save">
        <input type="hidden" name="rnd" value="{$rnd}">
        <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'{t}Taxon{/t}' );return false;" rel="taxon_id">{t}add{/t}</a>
        <input type="hidden" id="taxon_id" value="" onchange="add_taxon();print_taxa();" />
        <input type="hidden" id="taxon" value="" />
        <ul id="new_taxa">
        </ul>  
        <input type="button" value="save" onclick="saveForm();" />
    </form>
   	</p>
    
    <p>
	    <a href="step_show.php?id={$id}">{t}back{/t}</a>
    </p>

</div>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
