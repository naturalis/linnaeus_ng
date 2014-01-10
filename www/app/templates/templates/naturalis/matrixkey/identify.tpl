{include file="../shared/head.tpl"}

  <body class='theme-bruin' style='cursor: default;'>
    
{include file="../shared/navbar.tpl"}
	
    <div id='container'>
      <a name='top'></a>
      <div id='main'>

		{snippet language=$currentLanguageId}titles.html{/snippet}

          <div id='left'>
            <div id='quicksearch'>
              <h2>{t}Zoek op naam{/t}</h2>
            <form action='' class='input-group' method='post' name='inlineformsearch' onsubmit='nbcDoSearch();return false;'>
              <input class='form-control' name='searchString' id='inlineformsearchInput' placeholder='' title='{t}Zoek op naam{/t}' type='text' value=''>
              <span class='input-group-btn'>
                <button class='btn btn-default' id='button-search' type='submit' value='search'>
                  <span class='icon icon-search'></span>
                </button>
              </span>
              </form>
            </div>
            <div id='facets'>
              <h2>{t}Zoek op kenmerken{/t}</h2>
              <span id='facet-categories-menu'></span>
            </div>

          <div class='control' onClick="$('#similarSpeciesNav').addClass('hidden');nbcClearSearchTerm();nbcClearStateValue();">
            <span class='icon icon-reload'></span>
              <a href="">{t}Opnieuw beginnen{/t}</a>
          </div>

		{if $master_matrix_id}
		<div class='control'>
			<span class='icon icon-arrow-left-up'></span>
			<a href="?mtrx={$master_matrix_id}">{t}Terug naar de hoofdsleutel{/t}</a>
		</div>
		{/if}
						
            <div id='legend'>
              <h2>{t}Legenda:{/t}</h2>
              <div class='legend-icon-cell'>
                <span class='icon icon-book'></span>
                {t}meer informatie{/t}
              </div>
              <div class='legend-icon-cell'>
                <span class='icon icon-details'></span>
                {t}onderscheidende kenmerken{/t}
              </div>
              <div class='legend-icon-cell'>
                <span class='icon icon-resemblance'></span>
                {t}gelijkende soorten{/t}
              </div>
            </div>
            <div class='left-divider'></div>
            <div id='dataSourceContainer'>

				{snippet}colofon.html{/snippet}

              <div>
                <h3>{t}Geïmplementeerd door{/t}</h3>
                <p>{t}Naturalis & ETI BioInformatics.{/t}</p>
              </div>
            </div>
            <div class='left-divider'></div>
          </div>
          <div class='title-type4' id='content'>
            <div id='resultsHeader'>
              <h2 id='similarSpeciesHeader' class='hidden'>
                <span id='similarSpeciesLabel'>{t}Soorten lijkend op{/t}</span>
                <span id='similarSpeciesName'></span>
              </h2>
              <div class='headerSelectionLabel' id='result-count'></div>
              <div id='similarSpeciesNav' class='hidden'>
                <a href='#' id='clearSimilarSelection' onclick='nbcCloseSimilar();'>
                  <span class='icon icon-arrow-left'></span>
                  {t}terug{/t}
                </a>
                <a href='#' id='showAllLabel' onclick='nbcToggleAllSpeciesDetail();return false;'>
                  <span class='icon icon-details'></span>
                  <span id='showAllLabelLabel'>{t}alle onderscheidende kenmerken tonen{/t}</span>
                </a>
              </div>
            </div>
            <div id='results'>
              <div class='hidden'></div>
              <div class='layout-landscapes' id='results-container'></div>
            </div>
            <div class='footerPagination noline' id='footerPagination'>
              <input class='ui-button' id='show-more-button' onclick='nbcPrintResults();return false;' type='button' value='show more results' class='hidden'>
            </div>
          </div>
        </div>

      <div id='row-sender'>
        <a href='http://www.naturalis.nl' tagreg='_blank'>
          <img src='{$baseUrl}app/style/naturalis/images/logo-naturalis.png'>
        </a>
      </div>
    </div>

	{include file="../shared/footerbar.tpl"}

  </body>

<div id="jDialog" title="" class="ui-helper-hidden"></div>


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

nbcImageRoot='{$nbcImageRoot}';
baseUrlProjectImages='{$projectUrls.projectMedia}';
nbcBrowseStyle='{$nbcBrowseStyle}';
matrixId={$matrix.id};
projectId={$projectId};
nbcUseEmergingCharacters={$matrix_use_emerging_characters};

{literal}
if (typeof nbcInit=='function') {
	nbcInit();
}
{/literal}
{if $nbcFullDatasetCount}nbcFullDatasetCount={$nbcFullDatasetCount};
{/if}
{if $nbcStart}nbcStart={$nbcStart};
{/if}
{if $nbcPerPage}nbcPerPage={$nbcPerPage};
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
	nbcRefreshGroupMenu();
{literal}} catch(err){
	nbcGetResults();
}
{/literal}
{else}{literal}
nbcGetResults({refreshGroups:true});
{/literal}{/if}
{/if}

{literal}
});
</script>
{/literal}

</html>