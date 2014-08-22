{function name=makeNameLink nametype=0}
{if 
	$nametype==$smarty.const.PREDICATE_VALID_NAME ||
	$nametype==$smarty.const.PREDICATE_HOMONYM ||
	$nametype==$smarty.const.PREDICATE_BASIONYM ||
	$nametype==$smarty.const.PREDICATE_SYNONYM ||
	$nametype==$smarty.const.PREDICATE_SYNONYM_SL ||
	$nametype==$smarty.const.PREDICATE_MISSPELLED_NAME ||
	$nametype==$smarty.const.PREDICATE_INVALID_NAME
}synonym.php{else}name.php{/if}
{*
	$nametype==$smarty.const.PREDICATE_PREFERRED_NAME
	$nametype==$smarty.const.PREDICATE_ALTERNATIVE_NAME
*}
{/function}


{include file="../shared/admin-header.tpl"}

<div id="page-main">

<!-- form>
		naam zoeken: <input type="text" id="allLookupBox" onkeyup="allLookup()" placeholder="typ een naam"/>
</form -->
<h2>{$concept.taxon}</h2>
<h3>{$names.preffered_name}</h3>

<form id="data" onsubmit="return false;">

<p>
	

	<table>
		<tr>
			<td></td>
			<td><i>concept</i></td>
		</tr>
		<tr>
			<th>naam:</th>
			<td>
				{$concept.taxon}
				<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="concept_taxon">edit</a>
				<span class="editspan" id="taxon">
				<input type="text" id="concept_taxon" value="{$concept.taxon}" mandatory="mandatory" />
				</span> *
				<input type="hidden" id="concept_taxon_id" value="{$concept.taxon.id}" />
			</td>
		</tr>
		<tr><th>rang:</th>
			<td>
				{foreach from=$ranks item=v}{if $v.id==$concept.rank_id}{$v.label}{/if}{/foreach} 
				<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="concept_rank_id">edit</a>
				<span class="editspan">
				<select id="concept_rank_id" onchange="storedata(this);" >
				{foreach from=$ranks item=v}
				<option value="{$v.id}" {if $v.id==$concept.rank_id} selected="selected"{/if}>{$v.label}</option>
				{/foreach}
				</select>
				</span> *
			</td>
		</tr>
		<tr><th>ouder:</th>
			<td>
				<a href="taxon.php?id={$concept.parent.id}">{$concept.parent.taxon}</a>
				<a class="edit" href="#" onclick="toggleedit(this);editparent(this);return false;" rel="parent_taxon_id">edit</a>
				<span class="editspan" id="parent">
				</span> *
				<input type="hidden" id="parent_taxon_id" value="{$concept.parent.id}" mandatory="mandatory" />
			</td>
		</tr>

		<tr><th>nsr id:</th><td>{if $concept}{$concept.nsr_id}{else}(auto){/if}</td></tr>


		<tr><th>&nbsp;</td></tr>

		<tr>
			<td></td>
			<td><i>voorkomen</i></td>
		</tr>
		<tr>
			<th>status:</th>
			<td>
				{if $presence.presence_id}
					<span title="{$presence.presence_information_one_line}">{$presence.presence_index_label}. {$presence.presence_label}</span>
				{else}n.v.t.{/if}
				<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_presence_id">edit</a>
				<span class="editspan">
					<select id="presence_presence_id" onchange="storedata(this);" >
					<option value="-1" {if $presence.presence_id==''} selected="selected"{/if}>n.v.t.</option>
					{assign var=first value=true}
					{foreach from=$statuses item=v}
						{if $v.index_label==99 && $first==true}
						<option value="" disabled="disabled">&nbsp;</option>
						{assign var=first value=false}
						{/if}
						<option value="{$v.id}" {if $v.id==$presence.presence_id} selected="selected"{/if}>{$v.index_label}. {$v.label}</option>
					{/foreach}
					</select>
				</span>
			</td>
		</tr>

		<tr><th>habitat:</th>
			<td>
				{if $presence.habitat_id!=''}
					{$presence.habitat_label}
				{else}n.v.t.{/if}
				<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_habitat_id">edit</a>
				<span class="editspan">
					<select id="presence_habitat_id" onchange="storedata(this);" >
						<option value="-1" {if $presence.habitat_id==''} selected="selected"{/if}>n.v.t.</option>
					{foreach from=$habitats item=v}
						<option value="{$v.id}" {if $v.id==$presence.habitat_id} selected="selected"{/if}>{$v.label}</option>
					{/foreach}
					</select>
				</span>
			</td>
		</tr>

		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr><th>expert:</th>
			<td>
				{if $presence.expert_id!=''}
					{$presence.expert_name}
				{else}n.v.t.{/if}
				<a class="edit" href="#" onclick="toggleedit(this);editexpert(this);return false;" rel="presence_expert_id">edit</a>
				<span class="editspan" id="expert">
				</span>
				<input type="hidden" id="presence_expert_id" value="{$presence.expert_id}" />
			</td>
		</tr>

		<tr><th>organisatie:</th>
			<td>
				{if $presence.organisation_id!=''}
					{$presence.organisation_name}
				{else}n.v.t.{/if}
				<a class="edit" href="#" onclick="toggleedit(this);editorganisation(this);return false;" rel="presence_organisation_id">edit</a>
				<span class="editspan" id="organisation">
				</span>
				<input type="hidden" id="presence_organisation_id" value="{$presence.organisation_id}" />
			</td>
		</tr>

		<tr><th>publicatie:</th>
			<td>
				{if $presence.reference_id!=''}
					"{$presence.reference_label}"{if $presence.reference_author}, {$presence.reference_author}{/if}{if $presence.reference_date} ({$presence.reference_date}){/if}
				{else}n.v.t.{/if}
				<a class="edit" href="#" onclick="toggleedit(this);editreference(this);return false;" rel="presence_reference_id">edit</a>
				<span class="editspan" id="reference">
				</span><br />
				<input type="hidden" id="presence_reference_id" value="{$presence.reference_id}" />
			</td>
		</tr>
		</table>
</p>

{if $concept}
<p>

	<h4>namen</h4>
		
	<ul>
	{foreach from=$names.list item=v}
		<li>
		{$v.name} ({$v.language_label}) <i>{$v.nametype}</i> <a href="{makeNameLink nametype=$v.nametype}?id={$v.id}" class="edit">edit</a>
		</li>
	{/foreach}
	</ul>
	<a href="name.php?taxon={$concept.id}" class="edit" style="margin:0">nieuwe naam toevoegen</a><br />
	<a href="synonym.php?taxon={$concept.id}" class="edit" style="margin:0">nieuw synoniem toevoegen</a>


</p>
{/if}

<input type="button" value="opslaan" onclick="savedataform();" />
</form>

<p>
	
	{if $concept.base_rank==$smarty.const.GENUS_RANK_ID}
		<a href="taxon_new.php?parent={$concept.id}&newrank={$rank_id_species}">soort toevoegen aan {$concept.taxon}</a><br />
	{elseif $concept.base_rank >= $smarty.const.GENUS_RANK_ID}
		<a href="taxon_new.php?parent={$concept.id}&newrank={$rank_id_subspecies}">ondersoort toevoegen aan {$concept.taxon}</a><br />
	{/if}

	<a href="index.php">terug</a>
</p>

<!-- p>
	<a href="taxon.php?id={$concept.id}">soortsbeschrijvingen bewerken</a>
</p -->

</div>

<div id="dropdown-list">
	<div id="dropdown-list-content"></div>
</div>

{*
	// revert:
	var previousValues=Array();
	{foreach from=$data item=v key=k}
	previousValues.push( { name: '{$k}', value: {$v.current} } );
	{/foreach}
*}

<script>
$(document).ready(function()
{
	allLookupNavigateOverrideUrl('taxon.php?id=%s');
	{if $concept}
	dataid={$concept.id};
	taxonrank={$concept.base_rank};
	{/if}
	$('#data :input[type!=button]').each(function(key,value) {
		values.push( { name:$(this).attr('id'),current:$(this).val(), mandatory:$(this).attr('mandatory')=='mandatory' } );
		$(this).on('change',function() { setnewvalue( { name:$(this).attr('id'),value:$(this).val() } ); } );
	});
	$(window).on('beforeunload',function() { return checkunsavedvalues() } );
	//console.dir(values);

	{if !$concept}
	// if new concept, trigger all edit-clicks
	$('a.edit').each(function() {
		$(this).trigger('click'); 
		//$(this).remove(); 
	} );
	$('span.editspan').each(function() {
		//$(this).removeClass('editspan'); 
	} );
	{/if}

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
