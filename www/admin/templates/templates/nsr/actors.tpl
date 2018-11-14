{include file="../shared/admin-header.tpl"}

{include file="../shared/left_column_tree.tpl"}
{include file="../shared/left_column_admin_menu.tpl"}

<script>

var new_actors=Array();

function add_actor()
{
	var new_id=$('#taxon_actor_id').val();
	var new_label=$('#taxon_actor').val();

	if (new_id.length==0) return;

	for (var i=0;i<new_actors.length;i++)
	{
		if (new_actors[i].id==new_id) return;
	}

	new_actors.push( { id:new_id, label:new_label } )
}

function remove_actor( id )
{
	for (var i=0;i<new_actors.length;i++)
	{
		if (new_actors[i].id==id)
		{
			new_actors.splice(i,1);
			return;
		}
	}
}

function print_actors()
{
	$('#new_actors').html('');
	for (var i=0;i<new_actors.length;i++)
	{
		$('#new_actors').append(
			'<li>' + new_actors[i].label + '<a href="#" onclick="remove_actor('+new_actors[i].id+');print_actors();return false;" style="padding:0 5px 0 5px"> x </a></li>' );
	}
	$('#new_actor_header').toggle(new_actors.length>0);
}

function save_actors()
{
	var form=$('#theForm');
	for (var i=0;i<new_actors.length;i++)
	{
		form.append('<input type=hidden name=new_actors[] value="'+new_actors[i].id+'" />');
	}
	$(window).unbind('beforeunload');
	form.submit();
}

function del_actor( id )
{
	if (confirm(_('Are you sure?')))
	{
		$('#action').val('delete');
		var form=$('#theForm');
		form.append('<input type="hidden" name="actors_taxa_id" value="'+id+'" />');
		$(window).unbind('beforeunload');
		form.submit();
	};
}

</script>

<div id="page-container-div">

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">{t}experts{/t}:</span> {$concept.taxon}</h2>

<p>
    <h4>{t}referenced experts{/t}</h4>
    <ul id="old_actors">
    {foreach $actors v k}
        <li>
            {$v.label}{if $v.company_of_name} ({$v.company_of_name}){/if}
	        <a href="#" onclick="del_actor({$v.actors_taxa_id});return false;" style="padding:0 5px 0 5px"> x </a>
        </li>
    {/foreach}
    </ul>
</p>

<p>
    <h4 style="display:none" id="new_actor_header">{t}new experts{/t}</h4>
	<a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'{t}Expert{/t}', { dropListSelectedTextStyle:'full', closeDialogAfterSelect: false } );return false;" rel="taxon_actor_id">{t}add expert{/t}</a>
    <ul id="new_actors"></ul>
</p>

<input type="hidden" id="taxon_actor_id" value="" onchange="add_actor();print_actors();" />
<input type="hidden" id="taxon_actor" value="" />
<input type="button" value="{t}save{/t}" onclick="save_actors();" />

<form id=theForm method=post>
<input type=hidden id=action name=action value=save />
<input type=hidden name=rnd value={$rnd} />
</form>

    <p>
        <a href="taxon.php?id={$concept.id}&amp;noautoexpand=1">{t}back{/t}</a>
    </p>

</div>

{include file="../shared/admin-messages.tpl"}

</div>

<script>

function unload_check()
{
	if (new_actors.length>0) return "{t}Not all data has been saved!\nLeave page anyway?{/t}";
}

$(document).ready(function()
{
	$(window).on('beforeunload',function() { return unload_check() } );
	$('#page-block-messages').fadeOut(1500);
});

</script>

{include file="../shared/admin-footer.tpl"}