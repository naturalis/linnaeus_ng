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

function initRelationLists()
{
    $('.add-relation').off('click').on('click',function()
    {
        addTaxonRelation( $(this).attr('data-id') );
    });

    $('.remove-relation').off('click').on('click',function()
    {
        removeTaxonRelation( $(this).attr('data-id') );
    });
}

function addTaxonRelation( addme )
{
    var current=$('#taxon').val();

    if (addme.length==0||current.length==0) return;

    $.ajax({
        url : 'ajax_interface.php',
        type: 'POST',
        data : ({
            action : 'add_taxon_relation',
            time : allGetTimestamp(),
            taxon : current,
            relation : addme
        }),
        success : function(data)
        {
            //console.log(data);
            if (data!=1) return;
            addToRelated(addme);
            removeFromUnrelated(addme);
            sortRelated();
            sortUnrelated();
            initRelationLists();
            updateTaxonListing();
        }
    });
}

function removeTaxonRelation( removeme )
{
    var current=$('#taxon').val();

    if (removeme.length==0||current.length==0) return;

    $.ajax({
        url : 'ajax_interface.php',
        type: 'POST',
        data : ({
            action : 'remove_taxon_relation',
            time : allGetTimestamp(),
            taxon : current,
            relation : removeme
        }),
        success : function(data)
        {
            //console.log(data);
            if (data!=1) return;
            addToUnrelated(removeme);
            removeFromRelated(removeme);
            sortRelated();
            sortUnrelated();
            initRelationLists();
            updateTaxonListing();
        }
    });
}

function addToUnrelated( item )
{
     $('#related li').each(function()
     {
        if ($(this).attr("data-id")==item)
        {
            $('#unrelated').append(
                fetchTemplate('unrelatedItemTpl')
                    .replace(/%ID%/g,$(this).attr("data-id"))
                    .replace(/%LABEL%/g,$(this).attr("data-label"))
            );
        } 
     })
}

function removeFromUnrelated( item )
{
     $('#unrelated li').each(function()
     {
        if ($(this).attr("data-id")==item)
        {
            $(this).remove();
        } 
     })
}

function addToRelated( item )
{
    $('#unrelated li').each(function()
    {
        if ($(this).attr("data-id")==item)
        {
            $('#related').append(
                fetchTemplate('relatedItemTpl')
                    .replace(/%ID%/g,$(this).attr("data-id"))
                    .replace(/%LABEL%/g,$(this).attr("data-label"))
            );
        } 
    })
}

function removeFromRelated( item )
{
    $('#related li').each(function()
    {
        if ($(this).attr("data-id")==item)
        {
            $(this).remove();
        } 
    })
}

function sortList( id )
{
    var mylist = $( id );
    var listitems = mylist.children('li').get();
    listitems.sort(function(a, b)
    {
       return $(a).text().toUpperCase().localeCompare($(b).text().toUpperCase());
    })
    $.each(listitems, function(idx, itm) { mylist.append(itm); });
}

function sortRelated()
{
    sortList( '#related' );
}

function sortUnrelated()
{
    sortList( '#unrelated' );
}

function updateTaxonListing()
{
    $('#taxon :selected').text($('#taxon :selected').text().trim().replace(/\([\d]+\)/,'('+$('#related li').length+')'));
}

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
