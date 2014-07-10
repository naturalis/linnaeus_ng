{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>{$concept.taxon}</h2>
<h3>{$name.name}</h3>

<form id="data" onsubmit="return false;">

<table>
	<tr><th>naam:</th><td><input type="text" id="name_name" value="{$name.name}" /></td></tr>
	<tr><th>uninomial:</th><td><input class="medium" type="text" id="name_uninomial" value="{$name.uninomial}" /></td></tr>
	<tr><th>specific epithet:</th><td><input class="medium" type="text" id="name_specific_epithet" value="{$name.specific_epithet}" /></td></tr>
	<tr><th>infra specific epithet:</th><td><input class="medium" type="text" id="name_infra_specific_epithet" value="{$name.infra_specific_epithet}" /></td></tr>
	<tr><th>authorship:</th><td><input class="medium" type="text" id="name_authorship" value="{$name.authorship}" /></td></tr>
	<tr><th>name author:</th><td><input class="medium" type="text" id="name_name_author" value="{$name.name_author}" /></td></tr>
	<tr><th>authorship year:</th><td><input class="small" type="text" maxlength="4" id="name_authorship_year" value="{$name.authorship_year}" /></td></tr>
	<tr><th colspan="2">&nbsp;</td></tr>
	<tr><th>type:</th><td>
		<select id="name_type_id" >
		{foreach from=$nametypes item=v}
			<option value="{$v.id}" {if $v.id==$name.type_id} selected="selected"{/if}>{$v.nametype}</option>
		{/foreach}
		</select>
	</td></tr>
	<tr><th>taal:</th><td>
		<select id="name_language_id" >
		{assign var=first value=true}
		{foreach from=$languages item=v}
			{if $v.sort_criterium==0 && $first==true}
			<option value="" disabled="disabled">&nbsp;</option>
			{assign var=first value=false}
			{/if}
			<option value="{$v.id}" {if $v.id==$name.language_id} selected="selected"{/if}>{$v.label}</option>
		{/foreach}
		</select>	
	</td></tr>
	<tr><th colspan="2">&nbsp;</td></tr>
	<tr><th>literatuur:</th><td>{$name.reference_name} / {$name.reference_id}</td></tr>
	<tr><th>literatuur (alt.):</th><td><input type="text" id="name_reference" value="{$name.reference}" /></td></tr>
	<tr><th colspan="2">&nbsp;</td></tr>
	<tr><th>expert:</th><td>{$name.expert_name} / {$name.expert_id}</td></tr>
	<tr><th>expert (alt.):</th><td><input type="text" id="name_expert" value="{$name.expert}" /></td></tr>
	<tr><th colspan="2">&nbsp;</td></tr>
	<tr><th>organisatie:</th><td>{$name.organisation_name} / {$name.organisation_id}</td></tr>
	<tr><th>organisatie (alt.):</th><td><input type="text" id="" value="{$name.organisation}" /></td></tr>
</table>
<p>
re "alt" literatuur, expert en organisatie: de oorspronkelijke names-table bevatte zowel verwijzingen naar de literature- en 
actor-tabellen, als, in sommige gevallen, letterlijke strings met de namen van literatuur, expert en organisatie.
</p>

<input type="button" value="save" onclick="savedataform();" />
</form>
<p>
	<a href="taxon.php?id={$name.taxon_id}">teug</a>
</p>

</div>

<script>
$(document).ready(function()
{
	dataid={$name.id};
	$('#data :input[type!=button]').each(function(key,value) {
		values.push( { name:$(this).attr('id'),current:$(this).val() } );
		$(this).on('change',function() { setnewvalue($(this).attr('id'),$(this).val()); } );
	});
	console.dir(values);
	$(window).on('beforeunload',function() { return checkunsavedvalues() } );
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}