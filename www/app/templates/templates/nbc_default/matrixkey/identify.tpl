{include file="../shared/header.tpl"}

<style>
.homepage #content, .conceptcard #content {
    margin-top:-16px;
}
</style>

<div id="dialogRidge">

	{include file="_left_column.tpl"}
    
    <div id="content" class="title-type4">
    
        <div id="resultsHeader">
            <span>
	            <h1>{$matrix.name}{* if $master_matrix.id}{$matrix.name}{else}{$session.app.project.title}{/if *}</h1>
                <div class="headerPagination">
                    <ul id="paging-header" class="list paging"></ul>
                </div>
            </span>
        </div>

		<div>
			{if $master_matrix.id}
			<a href="?mtrx={$master_matrix.id}">{t}terug naar {$master_matrix.name}{/t}</a><br />
			{/if}

			<div id="similarSpeciesHeader" class="hidden"></div>
	
			<div id="result-count" class="headerSelectionLabel"></div>
			
		</div>
        
        <div id="results">
            <div id="results-container"></div>
        </div>

        <div id="footerPagination" class="footerPagination">
            <ul id="paging-footer" class="list paging"></ul>
        </div>
        
    </div>

</div>
    

<script type="text/JavaScript">
$(document).ready(function(){

nbcImageRoot='{$nbcImageRoot}';
baseUrlProjectImages='{$projectUrls.projectMedia}';
nbcBrowseStyle='{$nbcBrowseStyle}';
matrixId={$matrix.id};
projectId={$projectId};
nbcUseEmergingCharacters={$matrix_use_emerging_characters};


if (typeof nbcInit=='function') {
	nbcInit();
}

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

try {
	nbcData = $.parseJSON('{$taxaJSON}');
	nbcFilterEmergingCharacters();
	nbcDoResults( { resetStart:false } );
	nbcDoOverhead();
	nbcDoPaging();
	nbcRefreshGroupMenu();
} catch(err){
	nbcGetResults();
}

{else}
nbcGetResults();
{/if}
{/if}


});
</script>


{include file="../shared/footer.tpl"}