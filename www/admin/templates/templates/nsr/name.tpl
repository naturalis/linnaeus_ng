{include file="../shared/admin-header.tpl"}

{include file="../shared/left_column_tree.tpl"}
{include file="../shared/left_column_admin_menu.tpl"}

<style>
.language-labels {
    -ont-size: 0.9em;
}
.language-labels input {
    -ont-size: 0.9em;
    margin: 0 10px 0 0;
    width: 125px;
}
</style>

<div id="page-container-div">

<div id="page-main">

<h2><span style="font-size:12px">{t}name card{/t}:</span> {if $newname}new name{else}{$name.name}{/if}</h2>
<h3><span style="font-size:12px;font-style:normal">{t}concept{/t}:</span> {$concept.taxon}</h3>

<p>
<form id="data" onsubmit="return false;">

<table>
	<tr><th>{t}name:{/t}</th><td><input type="text" id="name_name" value="{$name.name}" label="naam" mandatory="mandatory" /> *</td></tr>
	<tr><th>{t}language:{/t}</th><td>
		<select id="name_language_id" mandatory="mandatory" label="language" onchange="checkprefnameavail()">
			{assign var=first value=true}
			<option value="" {if !$name.language_id} selected="selected"{/if}>n.a.</option>
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
	<tr><th>{t}type{/t}:</th><td>
		<select id="name_type_id" mandatory="mandatory" label="type">
			<option value="" id="nametype-none" {if !$name.type_id && $k==0} selected="selected"{/if}>{t}n.a.{/t}</option>
		{foreach from=$nametypes item=v key=k}
			{if $v.noNameParts}
			<option  value="{$v.id}" id="nametype-{$v.id}" {if $v.id==$name.type_id} selected="selected"{/if}>{$v.nametype_label}</option>
			{/if}
		{/foreach}
		</select> *
	</td></tr>

	{* if $name.type_id==$preferrednameid || $name.type_id==$alternativenameid *}

	<tr><th>{t}remark{/t}:</th><td>
	{foreach from=$projectlanguages item=v}
	<span class="language-labels">{if $projectlanguages|@count>1}{$v.language}: {/if}
    <input type="text" value="{$name.addition[{$v.language_id}].addition}" id="aanvulling[{$v.language_id}]" name="aanvulling[{$v.language_id}]" /></span>
	{/foreach}
	</td></tr>

	{* /if *}

	{*<tr>
		<th>nsr id:</th>
		<td>
			{$name.nsr_id}
		</td>
	</tr>*}

	<tr>
    	<th colspan="2">&nbsp;</td>
	</tr>

    {if $show_nsr_specific_stuff}

	<tr>
    	<th>{t}literature{/t}:</th>
        <td>
    	<span id="name_reference">
		{if $name.reference_id!=''}
			{$name.reference_name}
		{else}{t}n.a.{/t}{/if}
        </span>
        <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,_('Publication'));return false;" rel="name_reference_id">{t}edit{/t}</a><br />
        <input type="hidden" id="name_reference_id" value="{$name.reference_id}" />
		</td>
	</tr>
    {/if}

	{if !$newname}
		<tr><th colspan="2">&nbsp;</td></tr>
		{if $name.reference}<tr><th>{t}literature{/t} (alt.):</th><td><input type="text" id="name_reference" value="{$name.reference}" /></td></tr>{/if}
		{if $name.reference}<tr><th>{t}expert{/t} (alt.):</th><td><input type="text" id="name_expert" value="{$name.expert}" /></td></tr>{/if}
		{if $name.reference}<tr><th>{t}organisation{/t} (alt.):</th><td><input type="text" id="" value="{$name.organisation}" /></td></tr>{/if}
	{/if}
</table>
</p>

<input type="button" value="{t}save{/t}" onclick="savename();" />
{if !$newname}<input type="button" value="{t}delete{/t}" onclick="deleteform();" />{/if}
</form>

<p>
	<a href="taxon.php?id={$concept.id}&amp;noautoexpand=1">{t}back{/t}</a>
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

	$('#data :input[type!=button]').each(function(key,value)
	{
		values.push( { name:$(this).attr('id'),label:$(this).attr('label'),current:$(this).val(), mandatory:$(this).attr('mandatory')=='mandatory' } );
		$(this).on('change',function() { setnewvalue( { name:$(this).attr('id'),value:$(this).val() } ); } );
	});
	//console.dir(values);
	$(window).on('beforeunload',function() { return checkunsavedvalues() } );

	{if $newname}




	$("#name_language_id").val({$defaultprojectlanguage}).trigger('change');
	{/if}

	$('th[title]').each(function(key,value)
	{
		$(this).html('<span class="tooltip">'+$(this).html()+'</span>');
	});

	{foreach from=$preferrednames item=v key=k}
	storeprefname( { id:{$v.id},language_id:{$v.language_id},name:'{$v.name|@escape}' } );
	{/foreach}
	{if $name.id}
	currentnameid={$name.id};
	{/if}
	preferrednameid={$preferrednameid};
	checkprefnameavail();

});
</script>

{include file="../shared/admin-messages.tpl"}

</div>

{include file="../shared/admin-footer.tpl"}