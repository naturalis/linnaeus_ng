{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}
    
    <div id="content" class="title-type4">
    
        <div id="resultsHeader">
            <span>
	            <h1>{$session.app.project.title}</h1>
                <div class="headerPagination">
                    <ul id="paging-header" class="list paging"></ul>
                </div>
            </span>
        </div>
                <div id="result-count" class="headerSelectionLabel"></div>
        
        <div id="results">
            <div id="similarSpeciesHeader" class="hidden"></div>
			{if $master_matrix_id}
			<div id="mastermatrixHeader"><a href="?mtrx={$master_matrix_id}">Terug naar de hoofdsleutel</a></div>
			{/if}
            <div id="results-container"></div>
        </div>

        <div id="footerPagination" class="footerPagination">
            <ul id="paging-footer" class="list paging"></ul>
        </div>
        
    </div>

</div>
    
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
nbcImageRoot = '{$nbcImageRoot}';
baseUrlProjectImages = '{$session.app.project.urls.projectMedia}';
nbcBrowseStyle = '{$nbcBrowseStyle}';
{literal}
if (typeof nbcInit=='function') {
	nbcInit();
}
{/literal}
{if $nbcFullDatasetCount}nbcFullDatasetCount = {$nbcFullDatasetCount};
{/if}
{if $nbcStart}nbcStart={$nbcStart};
{/if}
{if $nbcPerPage}nbcPerPage={$nbcPerPage};
{/if}
{if $nbcPerLine}nbcPerLine={$nbcPerLine};
{/if}
{if $nbcSimilar}
nbcShowSimilar({$nbcSimilar[0]},'{$nbcSimilar[1]}');
{else}
{if $taxaJSON}
{literal}
try {{/literal}
	nbcData = $.parseJSON('{$taxaJSON}');
	nbcFilterEmergingCharacters();
	nbcDoResults({literal}{resetStart:false}{/literal});
	nbcDoOverhead();
	nbcDoPaging();
	nbcRefreshGroupMenu();
{literal}} catch(err){
	nbcGetResults();
}
{/literal}
{else}
nbcGetResults();
{/if}
{/if}

{literal}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}