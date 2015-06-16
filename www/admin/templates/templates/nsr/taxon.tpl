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

<h2><span style="font-size:12px;font-style:normal">conceptkaart:</span> {$concept.taxon}</h2>
{if $concept.is_deleted}<span style="color:red;font-weight:bold">CONCEPT IS GEMARKEERD ALS VERWIJDERD</span><br />
<a href="#" onclick="deletedataform(false);" class="edit" style="margin:0">verwijdering ongedaan maken</a>
{/if}

<form id="data" onsubmit="return false;">
<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />

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
                <span id="parent_taxon"><a href="taxon.php?id={$concept.parent.id}">{$concept.parent.taxon}</a></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Ouder');return false;" rel="parent_taxon_id">edit</a> *
				<input type="hidden" id="parent_taxon_id" value="{$concept.parent.id}" mandatory="mandatory"  label="ouder" droplistminlength="3" />
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
				<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_expert_id">edit</a>
				<span class="editspan" id="expert">
				
                <select id="presence_expert_id">
					<option value="-1" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='0'}
					<option value="{$v.id}" {if $v.id==$presence.expert_id} selected="selected"{/if}>{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
                                
				</span>
			</td>
		</tr>

		<tr><th>organisatie:</th>
			<td>
				{if $presence.organisation_id!=''}
					{$presence.organisation_name}
				{else}n.v.t.{/if}
				<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_organisation_id">edit</a>
				<span class="editspan" id="organisation">

				<select id="presence_organisation_id">
					<option value="-1" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}" {if $v.id==$presence.organisation_id} selected="selected"{/if}>{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
                                
				</span>
				<input type="hidden" id="" value="{$presence.organisation_id}" />
			</td>
		</tr>

		<tr><th>publicatie:</th>
			<td>

                <span id="presence_reference">{if $presence.reference_id!=''}{$presence.reference_label}{/if}</span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Publicatie');return false;" rel="presence_reference_id">edit</a> *
				<input type="hidden" id="presence_reference_id" value="{$presence.reference_id}" />
			</td>
		</tr>
		</table>
</p>
<input type="button" value="opslaan" onclick="saveconcept();" />
</form>



{if $concept}
<p>

	<h4>namen</h4>
		
	<ul>
	{foreach from=$names.list item=v}
		<li>
		{$v.name_no_tags} {*({$v.language_label})*}<i>({$v.nametype_label})</i> <a href="{makeNameLink nametype=$v.nametype}?id={$v.id}" class="edit">edit</a>
		</li>
	{/foreach}
	</ul>
	<a href="name.php?taxon={$concept.id}" class="edit" style="margin:0">Nederlandse naam toevoegen</a><br />
	<a href="synonym.php?taxon={$concept.id}" class="edit" style="margin:0" title="toevoegen van geldige naam, synoniem, etc.">wetenschappelijke naam toevoegen</a>


</p>
{/if}


<p>
	{if $concept.base_rank==$smarty.const.GENUS_RANK_ID}
		<a href="taxon_new.php?parent={$concept.id}&newrank={$rank_id_species}" class="edit" style="margin:0">soort toevoegen aan {$concept.taxon}</a><br />
	{elseif $concept.base_rank >= $smarty.const.GENUS_RANK_ID}
		<a href="taxon_new.php?parent={$concept.id}&newrank={$rank_id_subspecies}" class="edit" style="margin:0">ondersoort toevoegen aan "{$concept.taxon}"</a><br />
	{elseif $concept.base_rank < $smarty.const.GENUS_RANK_ID}
		<a href="taxon_new.php?parent={$concept.id}" class="edit" style="margin:0">onderliggend taxon toevoegen aan "{$concept.taxon}"</a><br />
	{/if}
</p>
<p>
	<a href="paspoort.php?id={$concept.id}" class="edit" style="margin:0">paspoort</a><br />

	<a href="images.php?id={$concept.id}" class="edit" style="margin:0">afbeeldingen</a><br />
	
    {foreach from=$traitgroups item=v}{if $v.taxon_count>0}
	<a href="../traits/taxon.php?id={$concept.id}&group={$v.id}" class="edit" style="margin:0;">{$v.sysname}</a><br />
    {/if}{/foreach}
		
	<a href="/linnaeus_ng/app/views/species/nsr_taxon.php?id={$concept.id}&epi={$session.admin.project.id}" class="edit" style="margin:0" target="nsr">taxon bekijken in het Soortenregister (nieuw venster)</a><br />
    {if !$concept.is_deleted}
    <br />
    <a href="#" onclick="deletedataform(true);" class="edit" style="margin:0">taxon markeren als verwijderd</a>
	{/if}
    <br />
    <a href="taxon_edit_concept_direct.php?id={$concept.id}" class="edit" style="margin:0">naam taxon concept direct aanpassen</a>

    {assign var=k value=0}
    {foreach $traitgroups v}{if $v.taxon_count==0}
   	{if $k==0}<br /><br /><span class="small">Kenmerken toevoegen:</span><br />{/if}
	<a href="../traits/taxon.php?id={$concept.id}&group={$v.id}" class="edit" style="margin:0;">{$v.sysname}</a><br />
    {assign var=k value=$k+1}
    {/if}{/foreach}

</p>


</p>


</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">
    <p>
{if $concept.is_deleted}
    <a href="taxon_deleted.php" style="margin:0">overzicht verwijderde taxa</a><br />
    <a href="index.php">index</a>
{else}
    <a href="index.php">terug</a>
{/if}
    </p>
    
</div>


<script>
$(document).ready(function()
{
	allLookupNavigateOverrideUrl('taxon.php?id=%s');

	speciesBaseRankid={$smarty.const.SPECIES_RANK_ID};
	genusBaseRankid={$smarty.const.GENUS_RANK_ID};

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
	{/if}

	$('th[title]').each(function(key,value)
	{
		$(this).html('<span class="tooltip">'+$(this).html()+'</span>');
	});

	$('#page-block-messages').fadeOut(3000);

});
</script>

{include file="../shared/admin-footer.tpl"}
