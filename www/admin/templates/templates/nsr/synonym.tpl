{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2><span style="font-size:12px">naamkaart:</span> {if $newname}nieuw synoniem{else}{$name.name}{/if}</h2>
<h3><span style="font-size:12px;font-style:normal">concept:</span> {$concept.taxon}</h3>

<p>
<form id="data" onsubmit="return false;">
<input type="hidden" id="name_language_id" name="name_language_id" value="{$name.name_language_id}" new="{$smarty.const.LANGUAGE_ID_SCIENTIFIC}" />

	<table>
{if $concept.base_rank>=$smarty.const.SPECIES_RANK_ID}
		<tr>
        	<th>genus:</th>
            <td><input onkeyup="namepartscomplete();partstoname();" type="text" class="medium" id="name_uninomial" value="{$name.uninomial}" mandatory="mandatory" label="genus" /> *
            </td>
		</tr>
		<tr>
        	<th>soort:</th>
            <td>
            	<input onkeyup="namepartscomplete();partstoname();" type="text" class="medium" id="name_specific_epithet" value="{$name.specific_epithet}" label="soort" />
            	<span class="inline_message" id="name_specific_epithet_message"></span>
			</td>
		</tr>
		<tr>
        	<th>derde naamdeel:<br /><span class="inline_subtext">(ondersoort, forma, varietas, etc.)</span></th>
            <td>
            	<input onkeyup="namepartscomplete();partstoname();" type="text" class="medium" id="name_infra_specific_epithet" value="{$name.infra_specific_epithet}" />
            	<span class="inline_message" id="name_infra_specific_epithet_message"></span>
            </td>
	</tr>
{else}
		<tr>
        	<th>uninomial:</th>
            <td><input onkeyup="partstoname();" type="text" class="medium" id="name_uninomial" value="{$name.uninomial}" mandatory="mandatory" label="naam" /> *
            
            </td>
		</tr>
{/if}

	<tr><td colspan="2" style="height:5px;"></td></tr>

	<tr>
		<th title="vul de volledige waarde voor 'auteurschap' in, inclusief komma, jaartal en haakjes; het programma leidt de waarden voor auteur en jaar automatisch af.">	
			auteurschap:
		</th><td><input onkeyup="partstoname();" type="text" class="medium" id="name_authorship"  value="{$name.authorship}" label="auteurschap" /></td>
	</tr>
	<tr><th>auteur(s):</th><td><input type="text" class="medium" id="name_name_author" value="{$name.name_author}" disabled="disabled" label="auteur" /></td></tr>	
	<tr>
		<th>jaar:</th>
		<td>
			<input type="text" class="small" id="name_authorship_year" value="{$name.authorship_year}" disabled="disabled" label="jaar" />
		</td>
	</tr>	

	<tr><td colspan="2" style="height:5px;"></td></tr>

    <tr><th title="synoniem wordt automatische samengesteld.">synoniem:</th><td>
        <input type="text" id="concept_taxon" value="{$name.name}" onchange="$('#name_name').val($(this).val()).trigger('change');" disabled="disabled" label="synoniem" />
        <input type="hidden" id="name_name" value="" />
    </td></tr>


	<tr><th colspan="2">&nbsp;</td></tr>
	<tr><th>type:</th><td>
		<select id="name_type_id" mandatory="mandatory" label="type" >
			<option value="" {if !$name.type_id && $k==0} selected="selected"{/if}>n.v.t.</option>
		{foreach from=$nametypes item=v key=k}
		{if !$v.noNameParts}
			<option value="{$v.id}" {if $v.id==$name.type_id} selected="selected"{/if}>{$v.nametype}</option>
		{/if}
		{/foreach}
		</select> *
	</td></tr>
    {*
	<tr><th>taal:</th><td>
		<select id="name_language_id" mandatory="mandatory">
			<option value="" {if !$name.language_id} selected="selected"{/if}>n.v.t.</option>
			{foreach from=$languages item=v key=k}
			{if $v.id==$smarty.const.LANGUAGE_ID_SCIENTIFIC}
				<option value="{$v.id}" {if $v.id==$name.language_id} selected="selected"{/if}>{$v.label}</option>
			{/if}
			{/foreach}
		</select> *
	</td></tr>
    *}
	<tr><th colspan="2">&nbsp;</td></tr>
	<tr><th>literatuur:</th><td>
		{if $name.reference_id!=''}
			{$name.reference_name}
		{else}n.v.t.{/if}	
		<a class="edit" href="#" onclick="toggleedit(this);editreference(this);return false;" rel="name_reference_id">edit</a>
		<span class="editspan" id="reference"></span>
		<input type="hidden" id="name_reference_id" value="{$name.reference_id}" />
	</td></tr>
	
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

<p>
<input type="button" value="opslaan" onclick="savedataform();" />
{if !$newname}<input type="button" value="verwijderen" onclick="deleteform();" />{/if}
</form>
</p>
</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">

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

	$('#data :input[type!=button]').each(function(key,value)
	{
		var p={ name:$(this).attr('id'),label:$(this).attr('label'),current:$(this).val(), mandatory:$(this).attr('mandatory')=='mandatory' };
		if ($(this).attr('new') && $(this).val().length==0)
		{
			p.new=$(this).attr('new');
			p.nocheck=true;
		}
		values.push( p );
		$(this).on('change',function() { setnewvalue( { name:$(this).attr('id'),value:$(this).val() } ); } );
	});
	//console.dir(values);
	$(window).on('beforeunload',function() { return checkunsavedvalues() } );

	$('th[title]').each(function(key,value)
	{
		$(this).html('<span class="tooltip">'+$(this).html()+'</span>');
	});

});
</script>

{include file="../shared/admin-footer.tpl"}