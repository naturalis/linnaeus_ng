{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>{$concept.taxon}</h2>
<h3>{if $newname}nieuwe naam{else}{$name.name}{/if}</h3>


<form id="data" onsubmit="return false;">

<table>
	<tr><th>naam:</th><td><input type="text" id="name_name" value="{$name.name}" mandatory="mandatory" /> *</td></tr>
	<tr><th>uninomial:</th><td><input class="medium" type="text" id="name_uninomial" value="{$name.uninomial}" /></td></tr>
	<tr><th>specific epithet:</th><td><input class="medium" type="text" id="name_specific_epithet" value="{$name.specific_epithet}" /></td></tr>
	<tr><th>infra specific epithet:</th><td><input class="medium" type="text" id="name_infra_specific_epithet" value="{$name.infra_specific_epithet}" /></td></tr>
	<tr><th>authorship:</th><td><input class="medium" type="text" id="name_authorship" value="{$name.authorship}" /></td></tr>
	<tr><th>name author:</th><td><input class="medium" type="text" id="name_name_author" value="{$name.name_author}" /></td></tr>
	<tr><th>authorship year:</th><td><input class="small" type="text" maxlength="4" id="name_authorship_year" value="{$name.authorship_year}" /></td></tr>
	<tr><th colspan="2">&nbsp;</td></tr>
	<tr><th>type:</th><td>
		<select id="name_type_id" mandatory="mandatory">
			<option value="" {if !$name.type_id && $k==0} selected="selected"{/if}>n.v.t.</option>
		{foreach from=$nametypes item=v key=k}
			<option value="{$v.id}" {if $v.id==$name.type_id} selected="selected"{/if}>{$v.nametype}</option>
		{/foreach}
		</select> *
	</td></tr>
	<tr><th>taal:</th><td>
		<select id="name_language_id" mandatory="mandatory">
			{assign var=first value=true}
			<option value="" {if !$name.language_id} selected="selected"{/if}>n.v.t.</option>
		{foreach from=$languages item=v key=k}
			{if $v.sort_criterium==0 && $first==true}
			<option value="" disabled="disabled">&nbsp;</option>
			{assign var=first value=false}
			{/if}
			<option value="{$v.id}" {if $v.id==$name.language_id} selected="selected"{/if}>{$v.label}</option>
		{/foreach}
		</select> *
	</td></tr>
	<tr><th colspan="2">&nbsp;</td></tr>
	<tr><th>literatuur:</th><td>
		{if $name.reference_id!=''}
			{$name.reference_name}
		{else}n.v.t.{/if}	
		<a class="edit" href="#" onclick="toggleedit(this);editreference(this);return false;" rel="name_reference_id">edit</a>
		<span class="editspan" id="reference"></span>
		<input type="hidden" id="name_reference_id" value="{$name.reference_id}" />
	</td></tr>
	
	{*<!--tr><th>expert:</th><td>
		{if $name.expert_id!=''}
			{$name.expert_name}
		{else}n.v.t.{/if}	
		<a class="edit" href="#" onclick="toggleedit(this);editexpert(this);return false;" rel="name_expert_id">edit</a>
		<span class="editspan" id="expert">
		</span>
		<input type="hidden" id="name_expert_id" value="{$name.expert_id}" />
	</td></tr-->*}

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

	{*<!-- tr><th>organisatie:</th><td>
		{if $name.organisation_id!=''}
			{$name.organisation_name}
		{else}n.v.t.{/if}	
		<a class="edit" href="#" onclick="toggleedit(this);editorganisation(this);return false;" rel="name_organisation_id">edit</a>
		<span class="editspan" id="organisation">
		</span>
		<input type="hidden" id="name_organisation_id" value="{$name.organisation_id}" />
	</td></tr -->*}

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
<p>

</p>

<input type="button" value="opslaan" onclick="savedataform();" />
{if !$newname}<input type="button" value="verwijderen" onclick="deleteform();" />{/if}
</form>

<p>
	<a href="taxon.php?id={$concept.id}">terug</a>
</p>

</div>

<div id="dropdown-list">
	<div id="dropdown-list-content"></div>
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
		values.push( { name:$(this).attr('id'),current:$(this).val(), mandatory:$(this).attr('mandatory')=='mandatory' } );
		$(this).on('change',function() { setnewvalue( { name:$(this).attr('id'),value:$(this).val() } ); } );
	});
	//console.dir(values);
	$(window).on('beforeunload',function() { return checkunsavedvalues() } );
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}