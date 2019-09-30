{include file="../shared/admin-header.tpl"}

{include file="../shared/left_column_tree.tpl"}
{include file="../shared/left_column_admin_menu.tpl"}

<div id="page-container-div">

<div id="page-main">

<h2><span style="font-size:12px">{t}name card{/t}:</span> {if $newname}new scientific name{else}{$name.name}{/if}</h2>
<h3><span style="font-size:12px;font-style:normal">{t}concept{/t}:</span> {$concept.taxon}</h3>

<p>
<form id="data" onsubmit="return false;">
<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
<input type="hidden" id="name_language_id" name="name_language_id" value="{$name.name_language_id}" new="{$smarty.const.LANGUAGE_ID_SCIENTIFIC}" />

	<table>
{if $concept.base_rank>=$smarty.const.SPECIES_RANK_ID}
		<tr>
        	<th>genus:</th>
            <td><input onkeyup="namepartscomplete();partstoname();" type="text" class="medium" id="name_uninomial" value="{$name.uninomial}" mandatory="mandatory" label="genus" {if $name.uninomial|count_characters>0}disabled="disabled"{/if}/> *
            </td>
		</tr>
		<tr>
        	<th>{t}species{/t}:</th>
            <td>
            	<input onkeyup="namepartscomplete();partstoname();" type="text" class="medium" id="name_specific_epithet" value="{$name.specific_epithet}" label="soort" />
            	<span class="inline_message" id="name_specific_epithet_message"></span>
			</td>
		</tr>
		<tr>
        	<th>{t}third name element{/t}:<br /><span class="inline_subtext">({t}subspecies, forma, varietas, etc.{/t})</span></th>
            <td>
            	<input onkeyup="namepartscomplete();partstoname();" type="text" class="medium" id="name_infra_specific_epithet" value="{$name.infra_specific_epithet}" />
            	<span class="inline_message" id="name_infra_specific_epithet_message"></span>
            </td>
	</tr>
{else}
		<tr>
        	<th>{t}uninomial{/t}:</th>
            <td><input onkeyup="partstoname();" type="text" class="medium" id="name_uninomial" value="{$name.uninomial}" mandatory="mandatory" label="naam" /> *

            </td>
		</tr>
{/if}

	<tr><td colspan="2" style="height:5px;"></td></tr>

	<tr>
		<th title="{t}enter the complete value for authorship, including comma, year and brackets; the program automatically deduces the values for author and year.{/t}">
			{t}authorship{/t}:
		</th><td><input onkeyup="partstoname();" type="text" class="medium" id="name_authorship"  value="{$name.authorship}" label="{t}authorship{/t}" /></td>
	</tr>
	<tr><th>{t}author(s){/t}:</th><td><input type="text" class="medium" id="name_name_author" value="{$name.name_author}" disabled="disabled" label="{t}author{/t}" /></td></tr>
	<tr>
		<th>{t}year{/t}:</th>
		<td>
			<input type="text" class="small" id="name_authorship_year" value="{$name.authorship_year}" disabled="disabled" label="{t}year{/t}" />
		</td>
	</tr>

	<tr><td colspan="2" style="height:5px;"></td></tr>

    <tr><th title="{t}scientific name is concatenated automatically.{/t}">{t}scientific name{/t}:</th><td>
        <input type="text" id="concept_taxon" value="{$name.name}" onchange="$('#name_name').val($(this).val()).trigger('change');" disabled="disabled" label="{t}synonym{/t}" />
        <input type="hidden" id="name_name" value="" />
    </td></tr>

	<tr><th colspan="2">&nbsp;</td></tr>

	<tr>
    	<th>{t}type{/t}:</th>
        <td>
            <select id="name_type_id" mandatory="mandatory" label="type" >
                <option value="" {if !$name.type_id && $k==0} selected="selected"{/if}>{t}n.a.{/t}</option>
            {foreach from=$nametypes item=v key=k}
            {if !$v.noNameParts}
                <option
                    value="{$v.id}"
                    {if $v.id==$name.type_id}selected="selected"{/if}
                    {if $hasvalidname && $v.id==$validnameid && $v.id!=$name.type_id}disabled="disabled"{/if}
                    >{$v.nametype_label}</option>
            {/if}
            {/foreach}
            </select> *
		</td>
	</tr>
    <tr>
    	<th>{t}rank{/t}:</th>
        <td>
 			{if $name.type_id!=$validnameid}
                <select id="name_rank_id">
                <option value="" {if $name.rank_id==''} selected="selected"{/if}>{t}n.a.{/t}</option>
                {foreach from=$ranks item=v}
                <option value="{$v.id}" {if $v.id==$name.rank_id} selected="selected"{/if}>{$v.label}</option>
                {/foreach}
                </select>
            {else}
                {foreach from=$ranks item=v}{if $v.id==$concept.rank_id}{$v.label}{/if}{/foreach}
            {/if}
        </td>
    </tr>

	{if $show_nsr_specific_stuff}

	<tr><th colspan="2">&nbsp;</td></tr>
	<tr><th>{t}literature{/t}:</th><td>
    	<span id="name_reference">
		{if $name.reference_id!=''}
			{$name.reference_name}
		{else}{t}n.a.{/t}{/if}
        </span>
        <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Publicatie');return false;" rel="name_reference_id">edit</a><br />
        <input type="hidden" id="name_reference_id" value="{$name.reference_id}" />
	</td></tr>
    {/if}

	{if !$newname}
		<tr><th colspan="2">&nbsp;</td></tr>
		{if $name.reference}<tr><th>{t}literature{/t} (alt.):</th><td><input type="text" id="name_reference" value="{$name.reference}" /></td></tr>{/if}
		{if $name.reference}<tr><th>{t}expert{/t} (alt.):</th><td><input type="text" id="name_expert" value="{$name.expert}" /></td></tr>{/if}
		{if $name.reference}<tr><th>{t}organisation{/t} (alt.):</th><td><input type="text" id="" value="{$name.organisation}" /></td></tr>{/if}
	{/if}

</table>
</p>

<p>
<input type="button" value="{t}save{/t}" onclick="savesynonym();" />
{if !$newname}<input type="button" value="{t}delete{/t}" onclick="deleteform();" />{/if}
</form>
</p>
</div>

{include file="../shared/admin-messages.tpl"}

{if $concept.base_rank>=$smarty.const.SPECIES_RANK_ID}

<div class="page-generic-div">
{t}Note: you need to modify the taxonomic parent of the concept to change the genus.{/t} <a href="taxon.php?id={$concept.id}">{t}Edit the concept.{/t}</a>
</div>

{/if}


<div class="page-generic-div">

	{if $name.nametype=='isValidNameOf'}
    <a href="taxon_edit_synonym_direct.php?id={$name.id}" class="edit" style="margin:0">{t}rename valid name directly{/t}</a>
    {/if}

    <p>
        <a href="taxon.php?id={$concept.id}&amp;noautoexpand=1">{t}back{/t}</a>
    </p>

</div>

</div>

<script>
$(document).ready(function()
{
	speciesBaseRankid={$smarty.const.SPECIES_RANK_ID};
	genusBaseRankid={$smarty.const.GENUS_RANK_ID};

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