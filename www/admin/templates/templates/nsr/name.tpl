{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2><span style="font-size:12px">naamkaart:</span> {if $newname}nieuwe naam{else}{$name.name}{/if}</h2>
<h3><span style="font-size:12px;font-style:normal">concept:</span> {$concept.taxon}</h3>

<p>
<form id="data" onsubmit="return false;">

<table>
	<tr><th>naam:</th><td><input type="text" id="name_name" value="{$name.name}" label="naam" mandatory="mandatory" /> *</td></tr>
	<tr><th>type:</th><td>
		<select id="name_type_id" mandatory="mandatory" label="type">
			<option value="" id="nametype-none" {if !$name.type_id && $k==0} selected="selected"{/if}>n.v.t.</option>
		{foreach from=$nametypes item=v key=k}
			{if $v.noNameParts}
			<option  value="{$v.id}" id="nametype-{$v.id}" {if $v.id==$name.type_id} selected="selected"{/if}>{$v.nametype_label}</option>
			{/if}
		{/foreach}
		</select> *
	</td></tr>
	<tr><th>taal:</th><td>
		<select id="name_language_id" mandatory="mandatory" label="taal" onchange="checkprefnameavail()">
			{assign var=first value=true}
			<option value="" {if !$name.language_id} selected="selected"{/if}>n.v.t.</option>
		{foreach from=$languages item=v key=k}
			{if $v.sort_criterium==0 && $first==true}
			<option value="" disabled="disabled">&nbsp;</option>
			{assign var=first value=false}
			{/if}
			{if $v.id!=$smarty.const.LANGUAGE_ID_SCIENTIFIC}
			<option value="{$v.id}" {if $v.id==$name.language_id} selected="selected"{/if}>{$v.label}</option>
			{/if}
		{/foreach}
		</select> *
        <span></span>
	</td></tr>
	<tr>
    	<th colspan="2">&nbsp;</td>
	</tr>
	<tr>
    	<th>literatuur:</th>
        <td>
    	<span id="name_reference">
		{if $name.reference_id!=''}
			{$name.reference_name}
		{else}n.v.t.{/if}	
        </span>
        <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Publicatie');return false;" rel="name_reference_id">edit</a><br />
        <input type="hidden" id="name_reference_id" value="{$name.reference_id}" />
		</td>
	</tr>
	
	<tr><th>expert:</th><td>
		<select id="name_expert_id">
			<option value="" {if !$name.expert_id} selected="selected"{/if}>n.v.t.</option>
		{foreach from=$actors item=v key=k}
		{if $v.is_company=='0'}
			<option value="{$v.id}" {if $v.id==$name.expert_id} selected="selected"{/if}>{$v.label}</option>
		{/if}
		{/foreach}
		</select> 
	</td></tr>

	<tr><th>organisatie:</th><td>
		<select id="name_organisation_id">
			<option value="" {if !$name.organisation_id} selected="selected"{/if}>n.v.t.</option>
		{foreach from=$actors item=v key=k}
		{if $v.is_company=='1'}
			<option value="{$v.id}" {if $v.id==$name.organisation_id} selected="selected"{/if}>{$v.label}</option>
		{/if}
		{/foreach}
		</select> 
	</td></tr>
	
	{if !$newname}
		<tr><th colspan="2">&nbsp;</td></tr>
		{if $name.reference}<tr><th>literatuur (alt.):</th><td><input type="text" id="name_reference" value="{$name.reference}" /></td></tr>{/if}
		{if $name.reference}<tr><th>expert (alt.):</th><td><input type="text" id="name_expert" value="{$name.expert}" /></td></tr>{/if}
		{if $name.reference}<tr><th>organisatie (alt.):</th><td><input type="text" id="" value="{$name.organisation}" /></td></tr>{/if}
	{/if}
</table>
</p>

<input type="button" value="opslaan" onclick="savename();" />
{if !$newname}<input type="button" value="verwijderen" onclick="deleteform();" />{/if}
</form>

<p>
	<a href="taxon.php?id={$concept.id}">terug</a>
</p>

</div>

<script>
$(document).ready(function()
{
	{if !$newname}
	dataid={$name.id};
	{else}
	nameownerid={$concept.id};
	{/if}

	$('#data :input[type!=button]').each(function(key,value) {
		values.push( { name:$(this).attr('id'),label:$(this).attr('label'),current:$(this).val(), mandatory:$(this).attr('mandatory')=='mandatory' } );
		$(this).on('change',function() { setnewvalue( { name:$(this).attr('id'),value:$(this).val() } ); } );
	});
	//console.dir(values);
	$(window).on('beforeunload',function() { return checkunsavedvalues() } );

	{if $newname}
	$("#name_language_id").val({$smarty.const.LANGUAGE_ID_DUTCH}).trigger('change');
	{/if}

	$('th[title]').each(function(key,value)
	{
		$(this).html('<span class="tooltip">'+$(this).html()+'</span>');
	});

	{foreach from=$preferrednames item=v key=k}
	storeprefname( { id:{$v.id},language_id:{$v.language_id},name:'{$v.name|@escape}' } );
	{/foreach}
	{if $name.id};
	currentnameid={$name.id};
	{/if}
	preferrednameid={$preferrednameid};
	checkprefnameavail();


});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}