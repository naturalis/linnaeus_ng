{include file="../shared/admin-header.tpl"}


<script type="text/JavaScript">

var new_refs=Array();

function add_ref()
{
	var new_id=$('#taxon_reference_id').val();
	var new_label=$('#taxon_reference').val();

	for (var i=0;i<new_refs.length;i++)
	{
		if (new_refs[i].id==new_id) return;
	}

	new_refs.push( { id:new_id, label:new_label } )
}

function del_ref( id )
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
			'<li>' + new_refs[i].label + '<a href="#" onclick="del_ref('+new_refs[i].id+');print_refs();return false;" style="padding:0 5px 0 5px"> x </a></li>' );
	}
}


function save_refs()
{
	var form=$('<form method=post></form>').appendTo('body');
	for (var i=0;i<new_refs.length;i++)
	{
		form.append('<input type=hidden name=new_refs[] value="'+new_refs[i].id+'" />');
	}
	form.submit();
}




	
</script>	
	
<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">{t}literatuur:{/t}</span> {$concept.taxon}</h2>

<p>
    existing refs
    <ul id="old_refs">
    {foreach $literature v k}
    <li>{$v.label}</li>
    {/foreach}
    </ul>
</p>

<p>
    new refs
    <ul id="new_refs">
    </ul>
</p>

<a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Publicatie', { dropListSelectedTextStyle:'full', closeDialogAfterSelect: false } );return false;" rel="taxon_reference_id">{t}add literature{/t}</a>
<input type="hidden" id="taxon_reference_id" value="" onchange="add_ref();print_refs();" />
<input type="hidden" id="taxon_reference" value="" />
<input type="button" value="{t}save{/t}" onclick="save_refs();" />

</div>

{include file="../shared/admin-messages.tpl"}

<script type="text/JavaScript">

$(document).ready(function()
{
	$('#page-block-messages').fadeOut(1500);
});
</script>

{include file="../shared/admin-footer.tpl"}