{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>{t}nieuw concept{/t}</h2><span id="timer"></span>

<form id="data" onsubmit="return false;">
<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
<p>

	<table>

		<tr>
        	<th></th><td><i>{t}taxonomie{/t}</i></th><td></td>
		</tr>
		<tr>
        	<th>{t}rang:{/t}</th>
			<td>
				{foreach from=$ranks item=v}{if $v.id==$concept.rank_id}{$v.rank}{/if}{/foreach} 
				<select id="concept_rank_id" onchange="storedata(this);" mandatory="mandatory" label="{t}rang{/t}">
				<option value=""></option>
				{foreach from=$ranks item=v}
				<option value="{$v.id}" base_rank_id="{$v.rank_id}" {if $newrank && $v.id==$newrank} selected="selected"{/if}>{$v.label}</option>
				{/foreach}
				</select> *
			</td>
		</tr>
		<tr>
        	<th>{t}ouder:{/t}</th>
			<td style="vertical-align:bottom">
                <span id="parent_taxon">{$parent.taxon}</span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Ouder');return false;" rel="parent_taxon_id">{t}edit{/t}</a> *
                <input type="hidden" id="parent_taxon_id" value="" mandatory="mandatory" onchange="getinheritablename();" label="{t}ouder{/t}" droplistminlength="3" />
			</td>
		</tr>

		<tr>
        	<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th></th>
			<td><i>{t}geldige wetenschappelijke naam{/t}</i></th>
		</tr>
		<tr>
        	<th>{t}genus of uninomial:{/t}</th>
            <td><input type="text" class="medium" id="name_uninomial" value="" mandatory="mandatory" label="{t}genus{/t}" /> *</td>
		</tr>
		<tr>
        	<th>{t}soort:{/t}</th>
            <td><input type="text" class="medium" id="name_specific_epithet" value="" label="{t}soort{/t}" /></td>
		</tr>
		<tr>
        	<th>{t}derde naamdeel:{/t}<br /><span class="inline_subtext">{t}(ondersoort, forma, varietas, etc.){/t}</span></th>
	        <td><input type="text" class="medium" id="name_infra_specific_epithet" value="" /></td>
		</tr>

		<tr><td colspan="2" style="height:5px;"></td></tr>
		
		<tr>
			<th title="{t}vul de volledige waarde voor 'auteurschap' in, inclusief komma, jaartal en haakjes; het programma leidt de waarden voor auteur en jaar automatisch af.{/t}">	
				{t}auteurschap:{/t}
			</th>
            <td>
            	<input type="text" class="medium" id="name_authorship" value="" label="{t}auteurschap{/t}" />
			</td>
		</tr>
		<tr><th>{t}auteur(s):{/t}</th><td><input type="text" class="medium" id="name_name_author" value="" disabled="disabled" label="{t}auteur{/t}" /></td></tr>	
		<tr><th>{t}jaar:{/t}</th><td><input type="text" class="small" id="name_authorship_year" value="" disabled="disabled" label="{t}jaar{/t}" /></td></tr>	

		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr>
			<th>{t}expert:{/t}</th>
			<td>
				<select id="name_expert_id">
					<option value="" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='0'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
			</td>
		</tr>
	
		<tr>
			<th>{t}organisatie:{/t}</th>
			<td>
				<select id="name_organisation_id">
					<option value="" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
			</td>
		</tr>

		<tr><th>{t}publicatie:{/t}</th>
			<td style="vertical-align:bottom">
                <span id="name_reference"></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'{t}Publicatie{/t}');return false;" rel="name_reference_id">edit</a>
                <input type="hidden" id="name_reference_id" value="" />
			</td>
		</tr>
		

		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<th></th>
			<td><i>{t}concept{/t}</i></td>
		</tr>
		<tr>
        	<th title="{t}conceptnaam wordt automatische samengesteld op basis van de geldige wetenschappelijke naam.{/t}">{t}naam:{/t}</th>
            <td>
				<input type="text" id="concept_taxon" value="" mandatory="mandatory" onchange="$('#name_name').val($(this).val()).trigger('change');" disabled="disabled" label="naam concept" />
				<input type="hidden" id="name_name" value="" mandatory="mandatory" />
			</td>
		</tr>
		<tr>
        	<th>{t}nsr id:{/t}</th><td>{t}(wordt automatisch gegenereerd){/t}</td>
		</tr>

		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<th></th>
			<td>
            <i><span id="main_language_display"></span></i></td>
		</tr>
		<tr>
        	<th>{t}naam:{/t}</th>
            <td>
				<input type="text" id="main_language_name" value="" onchange="" />
			</td>
		</tr>

		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr>
			<th>{t}expert:{/t}</th>
			<td>
				<select id="main_language_name_expert_id">
					<option value="" selected="selected">{t}n.v.t.{/t}</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='0'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
			</td>
		</tr>
	
		<tr>
			<th>{t}organisatie:{/t}</th>
			<td>
				<select id="main_language_name_organisation_id">
					<option value="" selected="selected">{t}n.v.t.{/t}</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
			</td>
		</tr>

		<tr><th>{t}publicatie:{/t}</th>
			<td style="vertical-align:bottom">
                <span id="main_language_name_reference"></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'{t}Publicatie{/t}');return false;" rel="main_language_name_reference_id">{t}edit{/t}</a>
                <input type="hidden" id="main_language_name_reference_id" value="" />
			</td>
		</tr>

		<tr><td colspan="2">&nbsp;</td></tr>

		<tr>
			<th></th>
			<td title="{t}status voorkomen kan alleen worden ingevuld voor soorten en lager.{/t}"><i>{t}voorkomen{/t}</i></td>
		</tr>
		<tr><th>{t}status:{/t}</th>
			<td>
				<select id="presence_presence_id" onchange="storedata(this);" >
				<option value="-1">{t}n.v.t.{/t}</option>
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

		<tr><th>{t}habitat:{/t}</th>
			<td>
				<select id="presence_habitat_id" onchange="storedata(this);" >
					<option value="-1">{t}n.v.t.{/t}</option>
				{foreach from=$habitats item=v}
					<option value="{$v.id}">{$v.label}</option>
				{/foreach}
				</select>
			</td>
		</tr>

		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr>
			<th>{t}expert:{/t}</th>
			<td>
				<select id="presence_expert_id">
					<option value="-1" selected="selected">{t}n.v.t.{/t}</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='0'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select>
			</td>
		</tr>
	
		<tr>
			<th>{t}organisatie:{/t}</th>
			<td>
				<select id="presence_organisation_id">
					<option value="-1" selected="selected">{t}n.v.t.{/t}</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select>
			</td>
		</tr>
	
		<tr>
        	<th>{t}publicatie:{/t}</th>
			<td style="vertical-align:bottom">
                <span id="presence_reference"></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'{t}Publicatie{/t}');return false;" rel="presence_reference_id">{t}edit{/t}</a>
                <input type="hidden" id="presence_reference_id" value="" />
			</td>
		</tr>
	</table>
</p>


<input type="button" value="{t}opslaan{/t}" onclick="savenewconcept();" />
</form>
<p>
	{if $parent.id}
	<a href="taxon.php?id={$parent.id}">{t}terug{/t}</a>
    {else}
	<a href="index.php">{t}terug{/t}</a>
    {/if}
</p>

</div>

<script>
$(document).ready(function()
{
	{* REFAC2015 *}
	{if $main_language_name_language_id==$smarty.const.LANGUAGE_ID_ENGLISH}
	main_language_display_label='{t}Engelse naam{/t}';
	{else}
	main_language_display_label='{t}Nederlandse naam{/t}';
	{/if}
	
	$('#main_language_display').html( main_language_display_label.toLowerCase() );

	$('#name_uninomial').on('paste',function() { setTimeout(function() { partstoname(); },100); } ).on('keyup',function() { partstoname(); } );
	$('#name_specific_epithet').on('paste',function() { setTimeout(function() { partstoname(); },100); } ).on('keyup',function() { partstoname(); } );
	$('#name_infra_specific_epithet').on('paste',function() { setTimeout(function() { partstoname(); },100); } ).on('keyup',function() { partstoname(); } );
	$('#name_authorship').on('paste',function() { setTimeout(function() { partstoname(); },100); } ).on('keyup',function() { partstoname(); } );

	//allLookupNavigateOverrideUrl('taxon.php?id=%s');
	
	speciesBaseRankid={$smarty.const.SPECIES_RANK_ID};
	genusBaseRankid={$smarty.const.GENUS_RANK_ID};

	baseRanks.push(
		{ rank: 'species',id: {$smarty.const.SPECIES_RANK_ID} } ,
		{ rank: 'genus',id: {$smarty.const.GENUS_RANK_ID} } ,
		{ rank: 'nothogenus',id: {$smarty.const.NOTHOGENUS_RANK_ID} } ,
		{ rank: 'nothospecies',id: {$smarty.const.NOTHOSPECIES_RANK_ID} } ,
		{ rank: 'nothosubspecies',id: {$smarty.const.NOTHOSUBSPECIES_RANK_ID}  } ,
		{ rank: 'nothovarietas',id: {$smarty.const.NOTHOVARIETAS_RANK_ID} } 
	);
	
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