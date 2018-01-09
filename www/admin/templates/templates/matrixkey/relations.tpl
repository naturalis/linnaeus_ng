{include file="../shared/admin-header.tpl"}
<style>
select, .select-div {
    width:500px;
}
.icon {
    cursor: pointer;
}
</style>

<div id="page-main">


    <h2>{t}Taxon relations{/t}</h2>

    <form id="theForm" method="post"> 

    {t}choose a taxon:{/t}<br />
    <select name="taxon" id="taxon" onchange="$('#theForm').submit();">
        <option></option>
        {foreach $taxa v}
        {if $v.id==$taxon}{assign selectedTaxon $v}}{/if}>
            <option value="{$v.id}" {if $v.id==$taxon}selected="selected"{/if}>
                {$v.taxon}{if $v.name} ({$v.name}){/if} ({$v.relations|@count})
            </option>
        {/foreach}
    </select>

    {if $taxon}
        {$existIds = []}
        <h3>{t}existing relations:{/t}</h3>
        <ul id="related">
        {foreach $selectedTaxon.relations v}
            <li data-id="{$v.id}" data-label="{$v.taxon}{if $v.name} ({$v.name}){/if}">{$v.taxon}{if $v.name} ({$v.name}){/if} <span class="icon remove-relation" data-id="{$v.id}">&#10134;</span></li>
            {$existIds[]=$v.id}
        {/foreach}
        </ul>

        <h3>{t}other taxa:{/t}</h3>
        <div class="select-div" style="height:400px;overflow-y:scroll;">
            <ul id="unrelated">
            {foreach $taxa v}
            {if $v.id!=$taxon && !in_array($v.id,$existIds)}
                <li data-id="{$v.id}" data-label="{$v.taxon}{if $v.name} ({$v.name}){/if}">{$v.taxon}{if $v.name} ({$v.name}){/if} <span class="icon add-relation" data-id="{$v.id}">&#10133;</span></li>
            {/if}
            {/foreach}
            </ul>
        </div>
    {/if}

</form> 

</div>

<script>
$(document).ready(function()
{
    acquireInlineTemplates();
    initRelationLists();
});
</script>

<div class="inline-templates" id="unrelatedItemTpl">
<!--
    <li data-id="%ID%" data-label="%LABEL%">%LABEL% <span class="icon add-relation" data-id="%ID%">&#10133;</span></li>
-->
</div>

<div class="inline-templates" id="relatedItemTpl">
<!--
    <li data-id="%ID%" data-label="%LABEL%">%LABEL% <span class="icon remove-relation" data-id="%ID%">&#10134;</span></li>
-->
</div>



{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
