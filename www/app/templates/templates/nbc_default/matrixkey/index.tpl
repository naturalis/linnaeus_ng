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

			<div id="similarSpeciesHeader" class="hidden" style="width:100%"></div>
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
$(document).ready(function()
{

	baseUrlProjectImages='{$projectUrls.projectMedia}';

	setSetting({
		matrixId: {$matrix.id},
		projectId: {$session.admin.project.id},
		imageRoot: '{$nbcImageRoot}',
		useEmergingCharacters: {$matrix_use_emerging_characters},
		defaultImage: '{$nbcImageRoot}noimage.gif',
		browseStyle: '{$matrix_browse_style}',
		scoreThreshold: {$matrix_score_threshold}
	});

	setScores($.parseJSON('{$session_scores}'));
	setStates($.parseJSON('{$session_states}'));
			
	matrixInit();

	setCursor('wait');

	retrieveMenu();
	retrieveDataSet();
	setCursor();

});
</script>

{include file="../shared/footer.tpl"}