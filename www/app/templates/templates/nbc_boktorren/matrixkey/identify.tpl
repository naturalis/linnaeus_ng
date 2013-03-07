{include file="../shared/header.tpl"}
{include file="_left_column.tpl"}
    
    <div id="content" class="title-type4">
    
        <div id="resultsHeader">
            <span>
	            <h2 style="width:100%">{t}Boktorren determineren{/t}</h2>
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
    
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
nbcImageRoot = '{$nbcImageRoot}';
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
{if $taxa}
nbcData = $.parseJSON('{$taxa}');
nbcDoResults({literal}{resetStart:false}{/literal});
{else}
nbcGetResults();
{/if}
{/if}

{literal}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}