{include file="../shared/admin-header.tpl"}

<script>

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

function saveActorForm()
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
<p>
<h2>{$actor.name}</h2>
<h3>{$actor.employer_name}</h3>
</p>
<p>

<form method="post" id="theForm">
<input type="hidden" name="id" value="{$actor.id}">
<input type="hidden" name="action" id="action" value="save">
<input type="hidden" name="rnd" value="{$rnd}">
<table>

	<tr><th>name:</th><td><input class="large" type="text" name="name" value="{$actor.name}" /></td></tr>
	<tr><th>alternative name:</th><td><input class="large" type="text" name="name_alt" value="{$actor.name_alt}" /></td></tr>
	<tr id="gender">
		<th>gender:</th>
		<td>
			<label><input type="radio" name="gender" value="f" {if $actor.gender=='f'}checked="checked"{/if} />f</label>
			<label><input type="radio" name="gender" value="m" {if $actor.gender=='m'}checked="checked"{/if} />m</label>
			<label><input type="radio" name="gender" id="no_gender" value="" {if $actor.gender!='f' && $actor.gender!='m'}checked="checked"{/if} />not specified</label>
		</td>
	</tr>
	<tr>
		<th>person or organisation:</th>
		<td>
			<label><input type="radio" name="is_company" value="0" {if $actor.is_company!='1'}checked="checked"{/if} />person</label>
			<label><input type="radio" name="is_company" value="1" {if $actor.is_company=='1'}checked="checked"{/if} />organisation</label>
		</td>
	</tr>
	<tr><th>homepage:</th><td><input class="large" type="text" name="homepage" value="{$actor.homepage}" /></td></tr>
	<tr><th>logo url:</th><td><input class="large" type="text" name="logo_url" value="{$actor.logo_url}" /></td></tr>
	<tr id="employee_of">
		<th>employee of:</th>
		<td>
			<select id="employee_of_id" name="employee_of_id">
				<option value="" {if !$actor.employee_of_id} selected="selected"{/if}>-</option>
				{foreach from=$companies item=v key=k}
				<option value="{$v.id}" {if $v.id==$actor.employee_of_id} selected="selected"{/if}>{$v.name}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	
	<!--
	<tr id="employee_of">
		<th>NSR ID:</th>
		<td>{if $actor.id}{$actor.nsr_id}{else}(will be generated automatically){/if}</td>
	</tr>
	-->
	
	<tr>
    	<th>
        	{t}link to taxa{/t}:
        </th>
    	<td>
            <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'{t}Taxon{/t}', { closeDialogAfterSelect: false } );return false;" rel="taxon_id">{t}add{/t}</a>
            <input type="hidden" id="taxon_id" value="" onchange="add_taxon();print_taxa();" />
            <input type="hidden" id="taxon" value="" />
            <ul id="new_taxa">
            </ul>
		</td>
	</tr>
	
{if ($actor.id && $CRUDstates.can_update) || (!$actor.id && $CRUDstates.can_create)}
	<tr><th><input type="button" value="save" onclick="saveActorForm();" /></th><td></td></tr>
{/if}
{if $actor.id && $CRUDstates.can_delete}
	<tr><td colspan="2" style="height:5px;"></td></tr>
	<tr><th><a href="#" onclick="doDelete('Are you sure you want to delete &quot;{$actor.name|@escape}&quot;?\n{$links.presences|@count} statuses, {$links.names|@count} names, {$links.passports|@count} tabs, {$links.literature|@count} references and  {$links.taxa|@count} taxa are linked to this person.');return false;">delete actor</a></th><td></td></tr>
{/if}
</table>

</form>
</p>
<p>
<div>
	<b>Links</b><br />
	{if $links.presences.total==0 && $links.names.total==0 && $links.passports.total==0 && $links.literature.total==0}
	(no links)
	{/if}
	
	{if $links.taxa.total > 0}
    <div>
	<a href="#" onclick="$('#links-taxa').toggle();return false;">{t}Linked taxa{/t}: {$links.taxa.total} {if $links.taxa.total > $links.taxa.data|@count}({$links.taxa.data|@count} displayed){/if}</a>
	<div id="links-taxa" style="display:none">
		<ul class="small">
			{foreach from=$links.taxa.data item=v}
			<li><a href="../nsr/taxon.php?id={$v.id}">{$v.taxon} [{$v.rank}]</a></li>
			{/foreach}
		</ul>
	</div>
    </div>
	{/if}
	
	
	{if $links.names.total > 0}
	<a href="#" onclick="$('#links-names').toggle();return false;">Linked names: {$links.names.total} {if $links.names.total > $links.names.data|@count}({$links.names.data|@count} displayed){/if}</a>
	<div id="links-names" style="display:none">
		<ul class="small">
			{foreach from=$links.names.data item=v}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.name}{if $v.nametype=='isValidNameOf'} ({$v.nametype_label}){else} ({$v.nametype_label} van {$v.taxon}){/if}</a></li>
			{/foreach}
		</ul>
	</div>
	<br />
	{/if}

	{if $links.presences.total > 0}
	<a href="#" onclick="$('#links-presences').toggle();return false;">Linked presence statuses: {$links.presences.total} {if $links.presences.total > $links.presences.data|@count}({$links.presences.data|@count} displayed){/if}</a>
	<div id="links-presences" style="display:none">
		<ul class="small">
			{foreach from=$links.presences.data item=v}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a>, {$v.presence_label}</li>
			{/foreach}
		</ul>
	</div>
	<br />
	{/if}

	{if $links.passports.total > 0}
	<a href="#" onclick="$('#links-passports').toggle();return false;">Linked passports: {$links.passports.total} {if $links.passports.total > $links.passports.data|@count}({$links.passports.data|@count} displayed){/if}</a>
	<div id="links-passports" style="display:none">
		<ul class="small">
        	{assign var=prev value=null}
            {foreach from=$links.passports.data item=v key=k}{if $v.taxon_id!=$prev}
            	</li><li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a><br />&nbsp;&nbsp;
			{else}, {/if}
            {$v.title}{assign var=prev value=$v.taxon_id}{/foreach}
			</li>
		</ul>
	</div>
	<br />
	{/if}

	{if $links.literature.total > 0}
	<a href="#" onclick="$('#links-literature').toggle();return false;">Linked references: {$links.literature.total} {if $links.literature.total > $links.literature.data|@count}({$links.literature.data|@count} displayed){/if}</a>
	<div id="links-literature" style="display:none">
		<ul class="small">
            {foreach from=$links.literature.data item=v key=k}
            <li><a href="../literature2/edit.php?id={$v.id}">{$v.label}</a></li>
            {/foreach}
		</ul>
	</div>
	<br />
	{/if}

</div>
</p>

<p>
	<a href="index.php">back</a>
</p>


</div>

<script>
$(document).ready(function()
{

	$( 'input[name=is_company]' ).on( "click", function() {

		if ($("input[name=is_company]:checked").val() == 1) {
			$('#employee_of').toggle(false);
			$('#employee_of_id').val("");

			$('#gender').toggle(false);
			$('#no_gender').attr("checked","checked");
		}
		else
		{
			$('#employee_of').toggle(true);
			$('#gender').toggle(true);
		}
	} );

	{if $actor.is_company=='1'}
	$('#employee_of').toggle(false);
	{/if}

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
