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
{* if $taxaJSON}
nbcData = $.parseJSON('{$taxaJSON}');
nbcDoResults({literal}{resetStart:true}{/literal});
nbcDoOverhead();
nbcDoPaging();
{else}
nbcGetResults();
{/if *}

{literal}
try {{/literal}
	nbcData = $.parseJSON('{$taxaJSON}');
	nbcDoResults({literal}{resetStart:false}{/literal});
	nbcDoOverhead();
	nbcDoPaging();
{literal}} catch(err){
	nbcGetResults();
}
{/literal}






{/if}

{literal}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}