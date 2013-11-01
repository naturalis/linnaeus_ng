{include file="../shared/admin-header.tpl"}

<div id="page-main">

<div id="tree1"></div>


	<p>
        <form method="post" action="sort_taxa.php" id="theForm">
        <input type="hidden" name="rnd" value="{$rnd}" />
        <input type="hidden" name="taxatype" value="{if $isHigherTaxa}Ht{else}Sp{/if}" />
        <input type="button" value="save taxon order" onclick="allSaveDragOrder()"/>
        </form>
    </p>
    <p>
    	<a href="javascript:taxonSortTaxaAlpha();">Sort taxa alphabetically (this list only)</a><br />
    	<a href="javascript:taxonSortTaxaAlpha(true);">Sort taxa alphabetically (all taxa)</a><br />
    	<a href="javascript:taxonSortTaxaTaxonomic();">Sort taxa alphabetically per taxonomic level</a> (affects both species and higher taxa)
    </p>

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	var data = [
    {
        label: 'node1',
        children: [
            { label: 'child1' },
            { label: 'child2' }
        ]
    },
    {
        label: 'node2',
        children: [
            { label: 'child3' }
        ]
    }
];

$(function() {
    $('#tree1').tree({
        data: data
    });
});
/*
$.getJSON(
    '/some_url/',
    function(data) {
        $('#tree1').tree({
            data: data
        });
    }
);
*/	
})
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
