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

{include file="../shared/left_column_tree.tpl"}

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">{t}conceptkaart:{/t}</span> {$concept.taxon}</h2>
{if $concept.is_deleted}<span style="color:red;font-weight:bold">{t}CONCEPT IS GEMARKEERD ALS VERWIJDERD{/t}</span><br />
<a href="#" onclick="deletedataform(false);" class="edit" style="margin:0">{t}verwijdering ongedaan maken{/t}</a>
{/if}

<form id="data" onsubmit="return false;">
<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />

<p>

    <table>
        <tr>
            <td></td>
            <td><i>{t}concept{/t}</i></td>
        </tr>
        <tr>
            <th>{t}naam:{/t}</th>
            <td>
                {$concept.taxon}
            </td>
        </tr>
        <tr><th>{t}rang:{/t}</th>
            <td>
                {foreach from=$ranks item=v}{if $v.id==$concept.rank_id}{$v.label}{/if}{/foreach}
                <a class="edit" href="#" onclick="toggleedit(this);return false;" rel="concept_rank_id">{t}edit{/t}</a>
                <span class="editspan">
                <select id="concept_rank_id" onchange="storedata(this);" >
                {foreach from=$ranks item=v}
                <option value="{$v.id}" {if $v.id==$concept.rank_id} selected="selected"{/if}>{$v.label}</option>
                {/foreach}
                </select>
                </span> *
            </td>
        </tr>
        <tr><th>{t}ouder:{/t}</th>
            <td>
                <span id="parent_taxon"><a href="taxon.php?id={$concept.parent.id}">{$concept.parent.taxon}</a></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Ouder');return false;" rel="parent_taxon_id">{t}edit{/t}</a> *
                <input type="hidden" id="parent_taxon_id" value="{$concept.parent.id}" mandatory="mandatory"  label="{t}ouder{/t}" droplistminlength="3" />
            </td>
        </tr>

        <tr><th>{t}nsr id:{/t}</th><td>{if $concept}{$concept.nsr_id}{else}{t}(auto){/t}{/if}</td></tr>

        <tr><th>&nbsp;</td></tr>

        <tr>
            <td></td>
            <td><i>{t}voorkomen{/t}</i></td>
        </tr>
        <tr>
            <th>{t}status:{/t}</th>
            <td>
                {if $presence.presence_id}
                    <span title="{$presence.presence_information_one_line}">{$presence.presence_index_label}. {$presence.presence_label}</span>
                {else}n.v.t.{/if}
                <a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_presence_id">{t}edit{/t}</a>
                <span class="editspan">
                    <select id="presence_presence_id" onchange="storedata(this);" >
                    <option value="-1" {if $presence.presence_id==''} selected="selected"{/if}>{t}n.v.t.{/t}</option>
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

        <tr><th>{t}habitat:{/t}</th>
            <td>
                {if $presence.habitat_id!=''}
                    {$presence.habitat_label}
                {else}{t}n.v.t.{/t}{/if}
                <a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_habitat_id">{t}edit{/t}</a>
                <span class="editspan">
                    <select id="presence_habitat_id" onchange="storedata(this);" >
                        <option value="-1" {if $presence.habitat_id==''} selected="selected"{/if}>{t}n.v.t.{/t}</option>
                    {foreach from=$habitats item=v}
                        <option value="{$v.id}" {if $v.id==$presence.habitat_id} selected="selected"{/if}>{$v.label}</option>
                    {/foreach}
                    </select>
                </span>
            </td>
        </tr>

        <tr><td colspan="2" style="height:5px;"></td></tr>

        <tr><th>{t}expert:{/t}</th>
            <td>
                {if $presence.expert_id!=''}
                    {$presence.expert_name}
                {else}{t}n.v.t.{/t}{/if}
                <a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_expert_id">{t}edit{/t}</a>
                <span class="editspan" id="expert">

                <select id="presence_expert_id">
                    <option value="-1" selected="selected">{t}n.v.t.{/t}</option>
                {foreach from=$actors item=v key=k}
                {if $v.is_company=='0'}
                    <option value="{$v.id}" {if $v.id==$presence.expert_id} selected="selected"{/if}>{$v.label}</option>
                {/if}
                {/foreach}
                </select>

                </span>
            </td>
        </tr>

        <tr><th>{t}organisatie:{/t}</th>
            <td>
                {if $presence.organisation_id!=''}
                    {$presence.organisation_name}
                {else}{t}n.v.t.{/t}{/if}
                <a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_organisation_id">{t}edit{/t}</a>
                <span class="editspan" id="organisation">

                <select id="presence_organisation_id">
                    <option value="-1" selected="selected">{t}n.v.t.{/t}</option>
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

        <tr><th>{t}publicatie:{/t}</th>
            <td>
                <span id="presence_reference">{if $presence.reference_id!=''}{$presence.reference_label}{/if}</span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Publicatie');return false;" rel="presence_reference_id">{t}edit{/t}</a> *
                <input type="hidden" id="presence_reference_id" value="{$presence.reference_id}" />
            </td>
        </tr>
        </table>
    </p>
    <input type="button" value="opslaan" onclick="saveconcept();" />

    </form>

    {if $concept}
    <p>
    
        <h4>{t}namen{/t}</h4>
    
        <ul>
        {foreach from=$names.list item=v}
            <li>
            {$v.name_no_tags}
            {if $v.addition[$main_language_name_language_id].addition}({$v.addition[$main_language_name_language_id].addition}){/if}
            {if $v.rank_label} [{$v.rank_label}]{/if}
            <i>({$v.nametype_label})</i>
            <a href="{makeNameLink nametype=$v.nametype}?id={$v.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit">{t}edit{/t}</a>
            </li>
        {/foreach}
        </ul>
        <a href="name.php?taxon={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}niet-wetenschappelijke naam toevoegen{/t}</a><br />
        <a href="synonym.php?taxon={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0" title="{t}toevoegen van geldige naam, synoniem, etc.{/t}">{t}wetenschappelijke naam toevoegen{/t}</a>
    
    </p>
    {/if}
    
    <p>
        {if $concept.base_rank==$smarty.const.GENUS_RANK_ID}
            <a href="taxon_new.php?parent={$concept.id}&newrank={$rank_id_species}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t _s1=$concept.taxon}soort toevoegen aan "%s"{/t}</a><br />
        {elseif $concept.base_rank >= $smarty.const.GENUS_RANK_ID}
            <a href="taxon_new.php?parent={$concept.id}&newrank={$rank_id_subspecies}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t _s1=$concept.taxon}ondersoort toevoegen aan "%s"{/t}</a><br />
        {elseif $concept.base_rank < $smarty.const.GENUS_RANK_ID}
            <a href="taxon_new.php?parent={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t _s1=$concept.taxon}onderliggend taxon toevoegen aan "%s"{/t}</a><br />
        {/if}
    </p>
    <p>
        <a href="paspoort.php?id={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}paspoort{/t}</a><br />
        <a href="media.php?id={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}media{/t}</a><br />
        <a href="literature.php?id={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}literatuur{/t}</a><br />
        <a href="images.php?id={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}afbeeldingen (NSR-only){/t}</a><br />
    
        {foreach from=$traitgroups item=v}{if $v.taxon_count>0}
        <a href="../traits/taxon.php?id={$concept.id}&group={$v.id}" class="edit" style="margin:0;">{$v.sysname}</a><br />
        {/if}{/foreach}
    
        <a href="/linnaeus_ng/app/views/species/nsr_taxon.php?id={$concept.id}&epi={$session.admin.project.id}" class="edit" style="margin:0" target="nsr">{t}taxon bekijken in front-end (nieuw venster){/t}</a><br />
        {if !$concept.is_deleted}
        <br />
        <a href="#" onclick="deletedataform(true);" class="edit" style="margin:0">{t}taxon markeren als verwijderd{/t}</a>
        {/if}
        <br />
        <a href="taxon_edit_concept_direct.php?id={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}naam taxon concept direct aanpassen{/t}</a>
    
        {assign var=k value=0}
        {foreach $traitgroups v}
        {if $k==0}<br /><br /><span class="small">{t}kenmerken toevoegen:{/t}</span><br />{/if}
        <a href="../traits/taxon.php?id={$concept.id}&group={$v.id}" class="edit" style="margin:0;">{$v.sysname}</a><br />
        {assign var=k value=$k+1}
		{/foreach}
    
    </p>

{if $concept.is_deleted}
    <a href="taxon_deleted.php" style="margin:0">{t}overzicht verwijderde taxa{/t}</a><br />
{/if}

    <!-- p>
{if $concept.is_deleted}
    <a href="taxon_deleted.php" style="margin:0">{t}overzicht verwijderde taxa{/t}</a><br />
    <a href="index.php">{t}index{/t}</a>
{else}
    <a href="index.php">{t}terug{/t}</a>
{/if}
    </p -->

</div>



{include file="../shared/admin-messages.tpl"}



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
	$('#data :input[type!=button]').each(function(key,value)
	{
		values.push( { name:$(this).attr('id'),current:$(this).val(), mandatory:$(this).attr('mandatory')=='mandatory' } );
		$(this).on('change',function() { setnewvalue( { name:$(this).attr('id'),value:$(this).val() } ); } );
	});
	$(window).on('beforeunload',function() { return checkunsavedvalues() } );
	//console.dir(values);

	{if !$concept}
	// if new concept, trigger all edit-clicks
	$('a.edit').each(function()
	{
		$(this).trigger('click');
		//$(this).remove();
	});
	{/if}

	$('th[title]').each(function(key,value)
	{
		$(this).html('<span class="tooltip">'+$(this).html()+'</span>');
	});

});
</script>

{include file="../shared/admin-footer.tpl"}
