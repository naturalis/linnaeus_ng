{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>nieuw concept</h2><span id="timer"></span>

<form id="data" onsubmit="return false;">
<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
<p>

	<table>

		<tr>
        	<th></th><td><i>taxonomie</i></th><td></td>
		</tr>
		<tr>
        	<th>rang:</th>
			<td>
				{foreach from=$ranks item=v}{if $v.id==$concept.rank_id}{$v.rank}{/if}{/foreach} 
				<select id="concept_rank_id" onchange="storedata(this);" mandatory="mandatory" label="rang">
				<option value=""></option>
				{foreach from=$ranks item=v}
				<option value="{$v.id}" base_rank_id="{$v.rank_id}" {if $newrank && $v.id==$newrank} selected="selected"{/if}>{$v.label}</option>
				{/foreach}
				</select> *
			</td>
		</tr>
		<tr>
        	<th>ouder:</th>
			<td style="vertical-align:bottom">
                <span id="parent_taxon">{$parent.taxon}</span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Ouder');return false;" rel="parent_taxon_id">edit</a> *
                <input type="hidden" id="parent_taxon_id" value="" mandatory="mandatory" onchange="getinheritablename();" label="ouder" droplistminlength="3" />
			</td>
		</tr>

		<tr>
        	<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th></th>
			<td><i>geldige wetenschappelijke naam</i></th>
		</tr>
		<tr>
        	<th>genus of uninomial:</th>
            <td><input onkeyup="partstoname();" type="text" class="medium" id="name_uninomial" value="" mandatory="mandatory" label="genus" /> *</td>
		</tr>
		<tr>
        	<th>soort:</th>
            <td><input onkeyup="partstoname();" type="text" class="medium" id="name_specific_epithet" value="" label="soort" /></td>
		</tr>
		<tr>
        	<th>derde naamdeel:<br /><span class="inline_subtext">(ondersoort, forma, varietas, etc.)</span></th>
	        <td><input onkeyup="partstoname();" type="text" class="medium" id="name_infra_specific_epithet" value="" /></td>
		</tr>

		<tr><td colspan="2" style="height:5px;"></td></tr>
		
		<tr>
			<th title="vul de volledige waarde voor 'auteurschap' in, inclusief komma, jaartal en haakjes; het programma leidt de waarden voor auteur en jaar automatisch af.">	
				auteurschap:
			</th>
            <td>
            	<input onkeyup="partstoname();" type="text" class="medium" id="name_authorship" value="" mandatory="mandatory" label="auteurschap" /> *
			</td>
		</tr>
		<tr><th>auteur(s):</th><td><input type="text" class="medium" id="name_name_author" value="" mandatory="mandatory" disabled="disabled" label="auteur" /></td></tr>	
		<tr><th>jaar:</th><td><input type="text" class="small" id="name_authorship_year" value="" mandatory="mandatory" disabled="disabled" label="jaar" /></td></tr>	

		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr>
			<th>expert:</th>
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
			<th>organisatie:</th>
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

		<tr><th>publicatie:</th>
			<td style="vertical-align:bottom">
                <span id="name_reference"></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Publicatie');return false;" rel="name_reference_id">edit</a>
                <input type="hidden" id="name_reference_id" value="" />
			</td>
		</tr>
		

		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<th></th>
			<td><i>concept</i></td>
		</tr>
		<tr>
        	<th title="conceptnaam wordt automatische samengesteld op basis van de geldige wetenschappelijke naam.">naam:</th>
            <td>
				<input type="text" id="concept_taxon" value="" mandatory="mandatory" onchange="$('#name_name').val($(this).val()).trigger('change');" disabled="disabled" label="naam concept" />
				<input type="hidden" id="name_name" value="" mandatory="mandatory" />
			</td>
		</tr>
		<tr>
        	<th>nsr id:</th><td>(wordt automatisch gegenereerd)</td>
		</tr>

		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<th></th>
			<td><i>nederlandse naam</i></td>
		</tr>
		<tr>
        	<th>naam:</th>
            <td>
				<input type="text" id="dutch_name" value="" onchange="" />
			</td>
		</tr>

		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr>
			<th>expert:</th>
			<td>
				<select id="dutch_name_expert_id">
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
			<th>organisatie:</th>
			<td>
				<select id="dutch_name_organisation_id">
					<option value="" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
			</td>
		</tr>

		<tr><th>publicatie:</th>
			<td style="vertical-align:bottom">
                <span id="dutch_name_reference"></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Publicatie');return false;" rel="dutch_name_reference_id">edit</a>
                <input type="hidden" id="dutch_name_reference_id" value="" />
			</td>
		</tr>

		<tr><td colspan="2">&nbsp;</td></tr>

		<tr>
			<th></th>
			<td><i>voorkomen</i></td>
		</tr>
		<tr><th>status:</th>
			<td>
				<select id="presence_presence_id" onchange="storedata(this);" >
				<option value="-1">n.v.t.</option>
				{assign var=first value=true}
				{foreach from=$statuses item=v}
					{if $v.index_label==99 && $first==true}
					<option value="" disabled="disabled">&nbsp;</option>
					{assign var=first value=false}
					{/if}
					<option value="{$v.id}">{$v.index_label}. {$v.label}</option>
				{/foreach}
				</select> *
			</td>
		</tr>

		<tr><th>habitat:</th>
			<td>
				<select id="presence_habitat_id" onchange="storedata(this);" >
					<option value="-1">n.v.t.</option>
				{foreach from=$habitats item=v}
					<option value="{$v.id}">{$v.label}</option>
				{/foreach}
				</select>
			</td>
		</tr>

		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr>
			<th>expert:</th>
			<td>
				<select id="presence_expert_id">
					<option value="-1" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='0'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> *
			</td>
		</tr>
	
		<tr>
			<th>organisatie:</th>
			<td>
				<select id="presence_organisation_id">
					<option value="-1" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> *
			</td>
		</tr>
	
		<tr>
        	<th>publicatie:</th>
			<td style="vertical-align:bottom">
                <span id="presence_reference"></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Publicatie');return false;" rel="presence_reference_id">edit</a> *
                <input type="hidden" id="presence_reference_id" value="" />
			</td>
		</tr>
	</table>
</p>


<input type="button" value="opslaan" onclick="savedataform();" />
</form>

<p>
	<a href="index.php">terug</a>
</p>

</div>

<div id="dropdown-list">
	<div id="dropdown-list-content"></div>
</div>

<script>
$(document).ready(function()
{
	//allLookupNavigateOverrideUrl('taxon.php?id=%s');
	
	speciesBaseRankid={$smarty.const.SPECIES_RANK_ID};
	genusBaseRankid={$smarty.const.GENUS_RANK_ID};

	$('#data :input[type!=button]').each(function(key,value) {
		var set={ name:$(this).attr('id'),label:$(this).attr('label'),current:$(this).val(), mandatory:$(this).attr('mandatory')=='mandatory' };
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

	$('[havedroplist=true]').each(function() {
		$(this).attr('autocomplete','off');
		$(this).bind('keyup', function(e) { 
			doNsrDropList({ e:e, id: $(this).attr('id') } )
		} );
	});

	{if $parent}
	inheritablename='{$parent.inheritable_name|@escape}';
	partstoname();
	{/if}

	$('th[title]').each(function(key,value)
	{
		$(this).html('<span class="tooltip">'+$(this).html()+'</span>');
	});
	
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

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}