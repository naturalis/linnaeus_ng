{include file="../shared/admin-header.tpl"}

{include file="../shared/left_column_tree.tpl"}

<script type="text/JavaScript">

var new_refs=Array();

function add_ref()
{
	var new_id=$('#taxon_reference_id').val();
	var new_label=$('#taxon_reference').val();
	
	if (new_id.length==0) return;

	for (var i=0;i<new_refs.length;i++)
	{
		if (new_refs[i].id==new_id) return;
	}

	new_refs.push( { id:new_id, label:new_label } )
}

function remove_ref( id )
{
	for (var i=0;i<new_refs.length;i++)
	{
		if (new_refs[i].id==id) 
		{
			new_refs.splice(i,1);
			return;
		}
	}
}

function print_refs()
{
	$('#new_refs').html('');
	for (var i=0;i<new_refs.length;i++)
	{
		$('#new_refs').append(
			'<li>' + new_refs[i].label + '<a href="#" onclick="remove_ref('+new_refs[i].id+');print_refs();return false;" style="padding:0 5px 0 5px"> x </a></li>' );
	}
	$('#new_ref_header').toggle(new_refs.length>0);
}

function save_refs()
{
	var form=$('#theForm');
	for (var i=0;i<new_refs.length;i++)
	{
		form.append('<input type=hidden name=new_refs[] value="'+new_refs[i].id+'" />');
	}
	$(window).unbind('beforeunload');
	form.submit();
}

function del_ref( id )
{
	if (confirm(_('Are you sure?')))
	{
		$('#action').val('delete');
		var form=$('#theForm');
		form.append('<input type=hidden name=literature_taxa_id value="'+id+'" />');
		$(window).unbind('beforeunload');
		form.submit();
	};
}
	
</script>	
	
<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">{t}literatuur:{/t}</span> {$concept.taxon}</h2>

<p>
    <h4>{t}referenced literature{/t}</h4>
    <ul id="old_refs">
    {foreach $literature v k}
    <li>
    	{if $v.author_name}{$v.author_name}, {/if}{$v.label}{if $v.date} ({$v.date}){/if}
        <a href="#" onclick="del_ref({$v.literature_taxa_id});return false;" style="padding:0 5px 0 5px"> x </a>
	</li>
    {/foreach}
    </ul>
</p>

<p>
    <h4 style="display:none" id="new_ref_header">{t}new references{/t}</h4>
	<a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'{t}Publicatie{/t}', { dropListSelectedTextStyle:'full', closeDialogAfterSelect: false } );return false;" rel="taxon_reference_id">{t}add reference{/t}</a>
    <ul id="new_refs">
    </ul>
</p>

<input type="hidden" id="taxon_reference_id" value="" onchange="add_ref();print_refs();" />
<input type="hidden" id="taxon_reference" value="" />
<input type="button" value="{t}save{/t}" onclick="save_refs();" />

<form id=theForm method=post>
<input type=hidden id=action name=action value=save />
<input type=hidden name=rnd value={$rnd} />
</form>

    <p>
        <a href="taxon.php?id={$concept.id}">terug</a>
    </p>

</div>

{include file="../shared/admin-messages.tpl"}

<script type="text/JavaScript">

function unload_check()
{
	if (new_refs.length>0) return "{t}Niet alle data is opgelagen!\nPagina toch verlaten?{/t}";
}


$(document).ready(function()
{
	$(window).on('beforeunload',function() { return unload_check() } );
	$('#page-block-messages').fadeOut(1500);
});
</script>

{include file="../shared/admin-footer.tpl"}