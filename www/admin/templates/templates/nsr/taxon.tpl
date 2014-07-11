{include file="../shared/admin-header.tpl"}

<div id="page-main">

<form>
		naam zoeken: <input type="text" id="allLookupBox" onkeyup="allLookup()" placeholder="typ een naam"/>
</form>

<h2>{$concept.taxon}</h2>
<h3>{$names.preffered_name}</h3>

<form id="data" onsubmit="return false;">

<p>
	<h4>concept</h4>
	nsr id: {$concept.nsr_id}<br />

	ouder: {$concept.parent.taxon}
	<a class="edit" href="#" onclick="toggleedit(this);editparent(this);return false;" rel="parent_taxon_id">edit</a>
	<span class="editspan" id="parent">
	</span>
	<input type="hidden" id="parent_taxon_id" value="{$concept.parent.id}" />

	<br />

	rang: 	{foreach from=$ranks item=v}{if $v.id==$concept.rank_id}{$v.rank}{/if}{/foreach} 
	<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="concept_rank_id">edit</a>
	<span class="editspan">
		<select id="concept_rank_id" onchange="storedata(this);" >
		{foreach from=$ranks item=v}
			<option value="{$v.id}" {if $v.id==$concept.rank_id} selected="selected"{/if}>{$v.rank}</option>
		{/foreach}
		</select>
	</span>

</p>

<p>

	<h4>voorkomen</h4>
	status:
	{if $presence.presence_id}
		<span title="{$presence.presence_information_one_line}">{$presence.presence_index_label}. {$presence.presence_label}</span>
	{else}n.v.t.{/if}
	<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_presence_id">edit</a>
	<span class="editspan">
		<select id="presence_presence_id" onchange="storedata(this);" >
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

<br />

	endemisch: 
	{if $presence.presence_id!=''}
		{if $presence.is_indigenous=='1'}ja{else if $presence.is_indigenous=='0'}nee{else}n.v.t.{/if}
	{else}n.v.t.{/if}
	<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_is_indigenous">edit</a>
	<span class="editspan">
		<select id="presence_is_indigenous" onchange="storedata(this);" >
			<option value="-1" {if $presence.is_indigenous==''} selected="selected"{/if}>n.v.t.</option>
			<option value="1" {if $presence.is_indigenous==1} selected="selected"{/if}>ja</option>
			<option value="0" {if $presence.is_indigenous==0} selected="selected"{/if}>nee</option>
		</select>
	</span>
	

<br />

	habitat: 
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

<br />

	expert:
	{if $presence.expert_id!=''}
		{$presence.expert_name}
	{else}n.v.t.{/if}
	<a class="edit" href="#" onclick="toggleedit(this);editexpert(this);return false;" rel="presence_expert_id">edit</a>
	<span class="editspan" id="expert">
	</span>
	<input type="hidden" id="presence_expert_id" value="{$presence.expert_id}" />

<br />

	organisatie:
	{if $presence.organisation_id!=''}
		{$presence.organisation_name}
	{else}n.v.t.{/if}
	<a class="edit" href="#" onclick="toggleedit(this);editorganisation(this);return false;" rel="presence_organisation_id">edit</a>
	<span class="editspan" id="organisation">
	</span>
	<input type="hidden" id="presence_organisation_id" value="{$presence.organisation_id}" />

<br />

	publicatie:
	{if $presence.reference_id!=''}
		"{$presence.reference_label}"{if $presence.reference_author}, {$presence.reference_author}{/if}{if $presence.reference_date} ({$presence.reference_date}){/if}
	{else}n.v.t.{/if}
	<a class="edit" href="#" onclick="toggleedit(this);editreference(this);return false;" rel="presence_reference_id">edit</a>
	<span class="editspan" id="reference">
	</span><br />
	<input type="hidden" id="presence_reference_id" value="{$presence.reference_id}" />

</p>

<p>

	<h4>namen</h4>
	<ul>
	{foreach from=$names.list item=v}
		<li>
		{$v.name} ({$v.language_label}) <i>{$v.nametype}</i> <a href="name.php?id={$v.id}" class="edit">edit</a>
		</li>
	{/foreach}
		<li><a href="name.php?taxon={$concept.id}" class="edit" style="margin:0">nieuwe naam toevoegen</a></li>
	</ul>


</p>

<input type="button" value="opslaan" onclick="savedataform();" />
</form>

<p>
	<a href="index.php">terug</a>
</p>

<!-- p>
	<a href="taxon.php?id={$concept.id}">soortsbeschrijvingen bewerken</a>
</p -->

</div>

<div id="dropdown-list">
	<div id="dropdown-list-close"><a href="#" onclick="closedropdownlist();return false;" />X</a></div>
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
	dataid={$concept.id};
	taxonrank={$concept.base_rank};
	$('#data :input[type!=button]').each(function(key,value) {
		values.push( { name:$(this).attr('id'),current:$(this).val() } );
	});
	$(window).on('beforeunload',function() { return checkunsavedvalues() } );
	console.dir(values);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
