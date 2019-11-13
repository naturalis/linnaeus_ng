{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>{t}new concept{/t}</h2><span id="timer"></span>

<form id="data" onsubmit="return false;">
<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
<p>

	<table>

		<tr>
        	<th></th><td><i>{t}classification{/t}</i></th><td></td>
		</tr>
		<tr>
        	<th>{t}rank{/t}:</th>
			<td>
				{foreach from=$ranks item=v}{if $v.id==$concept.rank_id}{$v.rank}{/if}{/foreach}
				<select id="concept_rank_id" onchange="storedata(this);" mandatory="mandatory" label="{t}rank{/t}">
				<option value=""></option>
				{foreach from=$ranks item=v}
				<option value="{$v.id}" base_rank_id="{$v.rank_id}" {if $newrank && $v.id==$newrank} selected="selected"{/if}>{$v.label}</option>
				{/foreach}
				</select> *
			</td>
		</tr>
		<tr>
        	<th>{t}parent{/t}:</th>
			<td style="vertical-align:bottom">
                <span id="parent_taxon">{$parent.taxon}</span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Parent');return false;" rel="parent_taxon_id">{t}edit{/t}</a> *
                <input type="hidden" id="parent_taxon_id" value="" mandatory="mandatory" onchange="getinheritablename();" label="{t}parent{/t}" droplistminlength="3" />
			</td>
		</tr>

		<tr>
        	<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th></th>
			<td><i>{t}valid scientific name{/t}</i></th>
		</tr>
		<tr>
        	<th>{t}genus or uninomial{/t}:</th>
            <td><input type="text" class="medium" id="name_uninomial" value="" mandatory="mandatory" label="{t}genus{/t}" /> *</td>
		</tr>
		<tr>
        	<th>{t}species{/t}:</th>
            <td><input type="text" class="medium" id="name_specific_epithet" value="" label="{t}species{/t}" /></td>
		</tr>
		<tr>
        	<th>{t}third name element{/t}:<br /><span class="inline_subtext">{t}(subspecies, forma, varietas, etc.){/t}</span></th>
	        <td><input type="text" class="medium" id="name_infra_specific_epithet" value="" /></td>
		</tr>

		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr>
			<th title="{t}enter the complete value for authorship, including comma, year and brackets; the program automatically deduces the values for author and year.{/t}">
				{t}authorship{/t}:
			</th>
            <td>
            	<input type="text" class="medium" id="name_authorship" value="" label="{t}authorship{/t}" />
			</td>
		</tr>
		<tr><th>{t}author(s){/t}:</th><td><input type="text" class="medium" id="name_name_author" value="" disabled="disabled" label="{t}author{/t}" /></td></tr>
		<tr><th>{t}year{/t}:</th><td><input type="text" class="small" id="name_authorship_year" value="" disabled="disabled" label="{t}year{/t}" /></td></tr>

		{if $show_nsr_specific_stuff}
<!--
		<tr><td colspan="2" style="height:5px;"></td></tr>

			<th>{t}expert{/t}:</th>
			<td>
				<select id="name_expert_id">
					<option value="" selected="selected">{t}n.a.{/t}</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='0'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select>
			</td>
		</tr>

		<tr>
			<th>{t}organisation{/t}:</th>
			<td>
				<select id="name_organisation_id">
					<option value="" selected="selected">{t}n.a.{/t}</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select>
			</td>
		</tr>
-->
		<tr><th>{t}publication{/t}:</th>
			<td style="vertical-align:bottom">
                <span id="name_reference"></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'{t}Publicatie{/t}');return false;" rel="name_reference_id">edit</a>
                <input type="hidden" id="name_reference_id" value="" />
			</td>
		</tr>

		{/if}


		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<th></th>
			<td><i>{t}concept{/t}</i></td>
		</tr>
		<tr>
        	<th title="{t}concept name is concatenated automatically based on the valid scientific name.{/t}">{t}name{/t}:</th>
            <td>
				<input type="text" id="concept_taxon" value="" mandatory="mandatory" onchange="$('#name_name').val($(this).val()).trigger('change');" disabled="disabled" label="name concept" />
				<input type="hidden" id="name_name" value="" mandatory="mandatory" />
			</td>
		</tr>

        {if $show_nsr_specific_stuff}

		<tr>
        	<th>{t}nsr id:{/t}</th><td>({t}generated automatically{/t})</td>
		</tr>

        {/if}

		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<th></th>
			<td>
            <i><span id="main_language_display"></span></i></td>
		</tr>
		<tr>
        	<th>{t}name{/t}:</th>
            <td>
				<input type="text" id="main_language_name" value="" onchange="" />
			</td>
		</tr>

        {if $show_nsr_specific_stuff}

			<!--

		<tr><td colspan="2" style="height:5px;"></td></tr>
		<tr>
			<th>{t}expert{/t}:</th>
			<td>
				<select id="main_language_name_expert_id">
					<option value="" selected="selected">{t}n.a.{/t}</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='0'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select>
			</td>
		</tr>

		<tr>
			<th>{t}organisation{/t}:</th>
			<td>
				<select id="main_language_name_organisation_id">
					<option value="" selected="selected">{t}n.a.{/t}</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select>
			</td>
		</tr>
-->
		<tr><th>{t}publication{/t}:</th>
			<td style="vertical-align:bottom">
                <span id="main_language_name_reference"></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'{t}Publication{/t}');return false;" rel="main_language_name_reference_id">{t}edit{/t}</a>
                <input type="hidden" id="main_language_name_reference_id" value="" />
			</td>
		</tr>

		<tr><td colspan="2">&nbsp;</td></tr>

        {/if}

		{if $show_nsr_specific_stuff}

		<tr>
			<th></th>
			<td title="{t}status presence can only be entered for species and lower.{/t}"><i>{t}presence{/t}</i></td>
		</tr>
		<tr><th>{t}status{/t}:</th>
			<td>
				<select id="presence_presence_id" onchange="storedata(this);" >
				<option value="-1">{t}n.a.{/t}</option>
				{assign var=first value=true}
				{foreach from=$statuses item=v}
					{if $v.index_label==99 && $first==true}
					<option value="" disabled="disabled">&nbsp;</option>
					{assign var=first value=false}
					{/if}
					<option value="{$v.id}">{$v.index_label}. {$v.label}</option>
				{/foreach}
				</select>
			</td>
		</tr>

		<tr><th>{t}habitat{/t}:</th>
			<td>
				<select id="presence_habitat_id" onchange="storedata(this);" >
					<option value="-1">{t}n.a.{/t}</option>
				{foreach from=$habitats item=v}
					<option value="{$v.id}">{$v.label}</option>
				{/foreach}
				</select>
			</td>
		</tr>
<!--
		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr>
			<th>{t}expert{/t}:</th>
			<td>
				<select id="presence_expert_id">
					<option value="-1" selected="selected">{t}n.a.{/t}</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='0'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select>
			</td>
		</tr>

		<tr>
			<th>{t}organisation{/t}:</th>
			<td>
				<select id="presence_organisation_id">
					<option value="-1" selected="selected">{t}n.a.{/t}</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select>
			</td>
		</tr>
-->
		<tr>
        	<th>{t}publication{/t}:</th>
			<td style="vertical-align:bottom">
                <span id="presence_reference"></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'{t}Publicatie{/t}');return false;" rel="presence_reference_id">{t}edit{/t}</a>
                <input type="hidden" id="presence_reference_id" value="" />
			</td>
		</tr>

		{/if}

	</table>
</p>


<input type="button" value="{t}save{/t}" onclick="savenewconcept();" />
</form>
<p>
	{if $parent.id}
	<a href="taxon.php?id={$parent.id}&amp;noautoexpand=1">{t}back{/t}</a>
    {else}
	<a href="index.php">{t}back{/t}</a>
    {/if}
</p>

</div>

<script>
$(document).ready(function()
{
	{* REFAC2015 *}
	{if $main_language_name_language_id==$smarty.const.LANGUAGE_ID_ENGLISH}
	main_language_display_label='{t}Name in English{/t}';
	{else}
	main_language_display_label='{t}Common name{/t}';
	{/if}

	$('#main_language_display').html( main_language_display_label.toLowerCase() );

	$('#name_uninomial').on('paste',function() { setTimeout(function() { partstoname(); },100); } ).on('keyup',function() { partstoname(); } );
	$('#name_specific_epithet').on('paste',function() { setTimeout(function() { partstoname(); },100); } ).on('keyup',function() { partstoname(); } );
	$('#name_infra_specific_epithet').on('paste',function() { setTimeout(function() { partstoname(); },100); } ).on('keyup',function() { partstoname(); } );
	$('#name_authorship').on('paste',function() { setTimeout(function() { partstoname(); },100); } ).on('keyup',function() { partstoname(); } );

	//allLookupNavigateOverrideUrl('taxon.php?id=%s');

	speciesBaseRankid={$smarty.const.SPECIES_RANK_ID};
	genusBaseRankid={$smarty.const.GENUS_RANK_ID};

	baseRanks.push(	{ rank: 'species',id: {$smarty.const.SPECIES_RANK_ID} } );
	baseRanks.push(	{ rank: 'genus',id: {$smarty.const.GENUS_RANK_ID} } );
	{if $smarty.const.NOTHOGENUS_RANK_ID}baseRanks.push( { rank: 'nothogenus',id: {$smarty.const.NOTHOGENUS_RANK_ID} } );{/if}
	{if $smarty.const.NOTHOGENUS_RANK_ID}baseRanks.push( { rank: 'nothospecies',id: {$smarty.const.NOTHOSPECIES_RANK_ID} } );{/if}
	{if $smarty.const.NOTHOGENUS_RANK_ID}baseRanks.push( { rank: 'nothosubspecies',id: {$smarty.const.NOTHOSUBSPECIES_RANK_ID} } );{/if}
	{if $smarty.const.NOTHOGENUS_RANK_ID}baseRanks.push( { rank: 'nothovarietas',id: {$smarty.const.NOTHOVARIETAS_RANK_ID} } );{/if}

	$('#data :input[type!=button]').each(function(key,value)
	{
		var set={
			name:$(this).attr('id'),
			label:$(this).attr('label'),
			current:$(this).val(),
			mandatory:$(this).attr('mandatory')=='mandatory',
			hidden:$(this).attr('type')=='hidden'
		};

		{if $parent}
		if ($(this).attr('id')=='parent_taxon_id')
		{
			set.current=-1;
			set.new={$parent.id};
		}
		{/if}
		{if $newrank}
		if ($(this).attr('id')=='concept_rank_id')
		{
			set.current=-1;
			set.new={$newrank};
		}
		{/if}
		values.push( set );
		$(this).on('change',function() { setnewvalue( { name:$(this).attr('id'),value:$(this).val() } ); } );
	});

	values.push( { name:'name_type_id',current:'',new:{$name_type_id},mandatory:true } );
	values.push( { name:'name_language_id',current:'',new:{$name_language_id},mandatory:true } );
	values.push( { name:'main_language_name_language_id',current:'',new:{$main_language_name_language_id},mandatory:true } );

	$('[havedroplist=true]').each(function()
	{
		$(this).attr('autocomplete','off');
		$(this).bind('keyup', function(e) {
			doNsrDropList({ e:e, id: $(this).attr('id') } )
		} );
	});

	{if $parent}
	inheritablename='{$parent.inheritable_name|@escape}';
	partstoname();
	{/if}

	{if $data}
	{foreach from=$data item=v key=k}
		$('#{$k}').val('{$v|@escape}').trigger('change');
	{/foreach}
	{/if}
	{if $texts}
	{foreach from=$texts item=v key=k}
		$('#{$k}').text('{$v|@escape}');
	{/foreach}
	{/if}

	$('th[title]').add('td[title]').each(function(key,value)
	{
		$(this).html('<span class="tooltip">'+$(this).html()+'</span>');
	});


});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}