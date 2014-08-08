{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>nieuw concept</h2><span id="timer"></span>

<form id="data" onsubmit="return false;">
<p>

	<table>
		<tr><th><h4>concept</h4></td><td></td></tr>

		<tr><th>rang:</th>
			<td>{$newrank}
				{foreach from=$ranks item=v}{if $v.id==$concept.rank_id}{$v.rank}{/if}{/foreach} 
				<select id="concept_rank_id" onchange="storedata(this);" mandatory="mandatory" >
				<option value=""></option>
				{foreach from=$ranks item=v}
				<option value="{$v.id}" {if $newrank && $v.rank_id==$newrank} selected="selected"{/if}>{$v.rank}</option>
				{/foreach}
				</select> *
			</td>
		</tr>
		<tr><th>ouder:</th>
			<td>
				<span id="parent">{$parent.label}</span> <input type="text" class="medium" id="__parent_list_input" value="" /> *
				<input type="hidden" id="parent_taxon_id" value="{$parent.id}" mandatory="mandatory" />
			</td>
		</tr>
		<tr><th>&nbsp;</td></tr>
		<tr><th></th><td><i>geldige naam</i></th><td></td></tr>
		<tr><th>genus:</th><td><input onkeyup="partstoname();" type="text" class="medium" id="name_uninomial" value="" mandatory="mandatory" /> *</td></tr>
		<tr><th>soort:</th><td><input onkeyup="partstoname();" type="text" class="medium" id="name_specific_epithet" value="" /></td></tr>
		<tr><th>ondersoort:</th><td><input onkeyup="partstoname();" type="text" class="medium" id="name_infra_specific_epithet" value="" /></td></tr>
		<tr><th>auteurschap:</th><td><input onkeyup="partstoname();" type="text" class="medium" id="name_authorship" value="" mandatory="mandatory"/> *</td></tr>	
		<tr><th>auteur:</th><td><input onkeyup="partstoname();" type="text" class="medium" id="name_name_author" value="" mandatory="mandatory" disabled="disabled"/></td></tr>	
		<tr><th>jaar:</th><td><input onkeyup="partstoname();" type="text" class="small" id="name_authorship_year" value="" mandatory="mandatory" disabled="disabled"/></td></tr>	

		<tr><th>&nbsp;</td></tr>
		<tr><th></th><td><i>concept</i></th><td></td></tr>
		<tr><th>naam:</th><td>
			<input type="text" id="concept_taxon" value="" mandatory="mandatory" onchange="$('#name_name').val($(this).val()).trigger('change');" disabled="disabled" />
			<input type="hidden" id="name_name" value="" mandatory="mandatory" />
		</td></tr>
		<tr><th>nsr id:</th><td>(wordt automatisch gegenereerd)</td></tr>

		<tr><th>&nbsp;</td></tr>
		<tr><th></th><td><i>nederlandse naam</i></th><td></td></tr>
		<tr><th>naam:</th><td>
			<input type="text" id="dutch_name" value="" onchange="" />
		</td></tr>

		<tr><th>&nbsp;</td></tr>

		<tr><th><h4>voorkomen</h4></td><td></td></tr>
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
				</select>
			</td>
		</tr>

		{*<!-- tr><th>inheems:</th>
			<td>
				<select id="presence_is_indigenous" onchange="storedata(this);" >
					<option value="-1">n.v.t.</option>
					<option value="1">ja</option>
					<option value="0">nee</option>
				</select>
			</td>
		</tr -->*}

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

		<tr><th>expert:</th>
			<td>
				<span id="expert"></span> <input type="text" class="medium" id="__expert_list_input" value="" />
				<input type="hidden" id="presence_expert_id" value="" />
			</td>
		</tr>

		<tr><th>organisatie:</th>
			<td>
				<span id="organisation"></span> <input type="text" class="medium" id="__organisation_list_input" value="" />
				<input type="hidden" id="presence_organisation_id" value="" />
			</td>
		</tr>

		<tr><th>publicatie:</th>
			<td>
				<span id="reference"></span> <input type="text" class="medium" id="__reference_list_input" value="" />
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
	allLookupNavigateOverrideUrl('taxon.php?id=%s');

	$('#data :input[type!=button]').each(function(key,value) {
		values.push( { name:$(this).attr('id'),current:$(this).val(), mandatory:$(this).attr('mandatory')=='mandatory' } );
		$(this).on('change',function() { setnewvalue( { name:$(this).attr('id'),value:$(this).val() } ); } );
	});

	values.push( { name:'name_type_id',current:'',new:{$name_type_id},mandatory:true } );
	values.push( { name:'name_language_id',current:'',new:{$name_language_id},mandatory:true } );

	$( '#__parent_list_input' ).bind('keyup', function(e) { 
		searchdata.action='species_lookup';
		searchdata.search=$(this).val();
		searchdata.taxa_only=1;
		searchdata.formatted=0;
		searchdata.rank_above=taxonrank;
		searchdata.buffer_keys=true;
		searchdata.url='ajax_interface.php';
		dolookuplist({ e:e,data:searchdata,callback:buildparentlist,targetvar:'parent_taxon_id'} )
	} );
	$( '#__expert_list_input' ).bind('keyup', function(e) {
		searchdata.action='expert_lookup';
		searchdata.search=$(this).val();
		searchdata.buffer_keys=false;
		searchdata.url='ajax_interface.php';
		dolookuplist({ e:e,minlength:1,data:searchdata,callback:buildexpertlist,targetvar:'presence_expert_id' } )
	} );
	$( '#__organisation_list_input' ).bind('keyup', function(e) {
		searchdata.action='expert_lookup';
		searchdata.search=$(this).val();
		searchdata.buffer_keys=false;
		searchdata.url='ajax_interface.php';
		dolookuplist({ e:e,minlength:1,data:searchdata,callback:buildorganisationlist,targetvar:'presence_organisation_id' } )
	} );
	$( '#__reference_list_input' ).bind('keyup', function(e) {
		searchdata.action='reference_lookup';
		searchdata.search=$(this).val();
		searchdata.buffer_keys=false;
		searchdata.url='../literature2/ajax_interface.php';
		dolookuplist({ e:e,minlength:1,data:searchdata,callback:buildreferencelist,targetvar:'presence_reference_id' } )
	} );
	
	{if $parent}
	inheritablename='{$parent.inheritable_name|@escape}';
	partstoname();
	{/if}


});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}