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
{include file="../shared/left_column_admin_menu.tpl"}

<div id="page-container-div">

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">{t}concept card{/t}:</span> {$concept.taxon|@strip_tags}</h2>
{if $concept.is_deleted}<span style="color:red;font-weight:bold">{t}CONCEPT IS MARKED AS DELETED{/t}</span><br />
<a href="#" onclick="deletedataform(false);" class="edit" style="margin:0">{t}undo deletion{/t}</a><br />
<a href="#" onclick="irrevocablydelete();" class="edit" style="margin:0">{t}delete this taxon irrevocably{/t}</a>
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
            <th>{t}name{/t}:</th>
            <td>
                {$concept.taxon|@strip_tags}
            </td>
        </tr>
        <tr><th>{t}rank{/t}:</th>
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
        <tr><th>{t}parent{/t}:</th>
            <td>
                <span id="parent_taxon"><a href="taxon.php?id={$concept.parent.id}">{$concept.parent.taxon}</a></span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Parent');return false;" rel="parent_taxon_id">{t}edit{/t}</a> *
                <input type="hidden" id="parent_taxon_id" value="{$concept.parent.id}" mandatory="mandatory"  label="{t}parent{/t}" droplistminlength="3" />
            </td>
        </tr>

        {if $show_nsr_specific_stuff}

        <tr><th>{t}nsr id:{/t}</th><td>{if $concept}{$concept.nsr_id}{else}{t}(auto){/t}{/if}</td></tr>

        <tr><th>&nbsp;</td></tr>

        <tr>
            <td></td>
            <td><i>{t}presence{/t}</i></td>
        </tr>
        <tr>
            <th>{t}status{/t}:</th>
            <td>
                {if $presence.presence_id}
                    <span title="{$presence.presence_information_one_line}">{$presence.presence_index_label}. {$presence.presence_label}</span>
                {else}{t}n.a.{/t}{/if}
                <a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_presence_id">{t}edit{/t}</a>
                <span class="editspan">
                    <select id="presence_presence_id" onchange="storedata(this);" >
                    <option value="-1" {if $presence.presence_id==''} selected="selected"{/if}>{t}n.a.{/t}</option>
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
                {else}{t}n.a.{/t}{/if}
                <a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_habitat_id">{t}edit{/t}</a>
                <span class="editspan">
                    <select id="presence_habitat_id" onchange="storedata(this);" >
                        <option value="-1" {if $presence.habitat_id==''} selected="selected"{/if}>{t}n.a.{/t}</option>
                    {foreach from=$habitats item=v}
                        <option value="{$v.id}" {if $v.id==$presence.habitat_id} selected="selected"{/if}>{$v.label}</option>
                    {/foreach}
                    </select>
                </span>
            </td>
        </tr>

        <tr><td colspan="2" style="height:5px;"></td></tr>

        <tr><th>{t}publication{/t}:</th>
            <td>
                <span id="presence_reference">{if $presence.reference_id!=''}{$presence.reference_label}{/if}</span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Publicatie');return false;" rel="presence_reference_id">{t}edit{/t}</a> *
                <a class="edit" style="margin-left:0;display:none" href="#" onclick="setNsrDropListValue(null,'presence_reference_id');return false;" id="presence_reference_remove" rel="presence_reference_id">x</a>
                <input type="hidden" id="presence_reference_id" value="{$presence.reference_id}" onchange="$('#presence_reference_remove').toggle($(this).val().length>0);" />
            </td>
        </tr>

        {/if}

        </table>
    </p>
    <input type="button" value="{t}save{/t}" onclick="saveconcept();" />

    </form>

    {if $concept}
    <p>

        <h4>{t}names{/t}</h4>

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
        <a href="name.php?taxon={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}add common name{/t}</a><br />
        <a href="synonym.php?taxon={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}add scientific name{/t}</a>

    </p>
    {/if}

    <p>
        {if $concept.base_rank==$smarty.const.GENUS_RANK_ID || $concept.base_rank==$smarty.const.SUBGENUS_RANK_ID}
            <a href="taxon_new.php?parent={$concept.id}&newrank={$rank_id_species}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t _s1=$concept.taxon}add species to "%s"{/t}</a><br />
        {elseif $concept.base_rank >= $smarty.const.SUBGENUS_RANK_ID}
            <a href="taxon_new.php?parent={$concept.id}&newrank={$rank_id_subspecies}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t _s1=$concept.taxon}add infraspecific taxon to "%s"{/t}</a><br />
        {elseif $concept.base_rank < $smarty.const.GENUS_RANK_ID}
            <a href="taxon_new.php?parent={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t _s1=$concept.taxon}add child taxon to "%s"{/t}</a><br />
        {/if}
    </p>
    <p>
        <a href="paspoort.php?id={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}passport{/t}</a><br />
        <a href="media.php?id={$concept.id}&noautoexpand=1" class="edit" style="margin:0">{t}media{/t}</a><br />
        <a href="literature.php?id={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}literature{/t}</a><br />
        <a href="actors.php?id={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}experts{/t}</a><br />
        {if $show_nsr_specific_stuff}
        <a href="images.php?id={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}images{/t}</a><br />
        {/if}
        
        {foreach from=$traitgroups item=v}
        	{if $v.taxon_count>0}
        		<a href="../traits/taxon.php?id={$concept.id}&group={$v.id}" class="edit" style="margin:0;">{$v.sysname}</a><br />
        	{/if}
        {/foreach}

        <a href="/linnaeus_ng/app/views/species/nsr_taxon.php?id={$concept.id}&epi={$session.admin.project.id}" class="edit" style="margin:0" target="nsr">{t}view taxon in front-end (new window){/t}</a><br />
        {if !$concept.is_deleted}
        <br />
        <a href="#" onclick="deletedataform(true);" class="edit" style="margin:0">{t}mark taxon as deleted{/t}</a>
        {/if}
        <br />
        <a href="taxon_edit_concept_direct.php?id={$concept.id}{if $noautoexpand}&noautoexpand=1{/if}" class="edit" style="margin:0">{t}rename taxon concept directly{/t}</a>

        {assign var=k value=0}
        {foreach $traitgroups v}
        {if $k==0}<br /><br /><span class="small">{t}add traits{/t}:</span><br />{/if}
        <a href="../traits/taxon.php?id={$concept.id}&group={$v.id}" class="edit" style="margin:0;">{$v.sysname}</a><br />
        {assign var=k value=$k+1}
        {/foreach}

    </p>

{if $concept.is_deleted}
    <a href="taxon_deleted.php" class="edit" style="margin:0">{t}show taxa marked as deleted{/t}</a><br />
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

</div>

<script>
$(document).ready(function()
{
    allLookupNavigateOverrideUrl('taxon.php?id=%s');

    speciesBaseRankid={$smarty.const.SPECIES_RANK_ID};
    genusBaseRankid={$smarty.const.GENUS_RANK_ID};

    {if $concept}
    dataid={$concept.id};
    {if $concept.base_rank}taxonrank={$concept.base_rank};{/if}
    $('#presence_reference_id').trigger('change');
    {/if}
    $('#data :input[type!=button]').each(function(key,value)
    {
        values.push( { name:$(this).attr('id'),current:$(this).val(), mandatory:$(this).attr('mandatory')=='mandatory' } );
        $(this).on('change',function() { setnewvalue( { name:$(this).attr('id'),value:$(this).val() } ); } );
    });
    $(window).on('beforeunload',function() { return checkunsavedvalues() } );
    //console.dir(values);

    {if !$concept}
    $('a.edit').each(function()
    {
        $(this).trigger('click');
    });
    {/if}

    $('th[title]').each(function(key,value)
    {
        $(this).html('<span class="tooltip">'+$(this).html()+'</span>');
    });

});
</script>

{include file="../shared/admin-footer.tpl"}
