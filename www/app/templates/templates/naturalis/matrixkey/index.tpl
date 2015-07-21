{include file="../shared/head.tpl"}

  <body style='cursor: default;'>

	{include file="../shared/navbar.tpl"}
	
    <div id='container'>
      <a name='top'></a>
      <div id='main'>

        <div id='logo'>
          <img src='{$baseUrl}app/style/naturalis/images/logo-medium-zwart.png'>
        </div>

		{snippet language=$currentLanguageId}titles.html{/snippet}

          <div id='left'>
            <div id='quicksearch'>
              <h2>{t}Zoek op naam{/t}</h2>
            <form class='input-group' name='inlineformsearch' onsubmit='setSearch();return false;'>
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

          <div class='control'>
            <span class='icon icon-reload'></span>
              <a id="clearSelectionLink" href="#" onclick="resetMatrix();return false;">{t}Opnieuw beginnen{/t}</a>
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
              <input class='ui-button' id='show-more-button' onclick='printResults();return false;' type='button' value='show more results' class='hidden'>
            </div>
          </div>
        </div>
    </div>

	{include file="../shared/footerbar.tpl"}
	
	{snippet}{"google_analytics-`$smarty.server.SERVER_NAME`.html"}{/snippet}

  </body>

<div id="jDialog" title="" class="ui-helper-hidden"></div>
<div id="tmpcontent" title="" class="ui-helper-hidden"></div>

<script type="text/JavaScript">
$(document).ready(function()
{
	setSetting({
		matrixId: {$matrix.id},
		projectId: {$session.app.project.id},
		imageRootSkin: '{$image_root_skin}',
		imageRootProject: '{$projectUrls.projectMedia}',
		useEmergingCharacters: {$matrix_use_emerging_characters},
		defaultSpeciesImages: { portrait: '{$image_root_skin}noimage.gif', landscape: '{$image_root_skin}noimage-lndscp.gif' } ,
		imageOrientation: '{$matrix_image_orientation}',
		browseStyle: '{$matrix_browse_style}',
		scoreThreshold: {$matrix_score_threshold},
		alwaysShowDetails: {$matrix_always_show_details},
		perPage: {$matrix_items_per_page},
		perLine: {$matrix_items_per_line},
	});

	setScores($.parseJSON('{$session_scores}'));
	setStates($.parseJSON('{$session_states}'));
	setCharacters($.parseJSON('{$session_characters}'));
			
	matrixInit();
	retrieveDataSet();
});
</script>


<script type="text/JavaScript">

var resultsHtmlTpl = '<div class="resultRow">%RESULTS%</div>';
var noResultHtmlTpl='<div style="margin-top:10px">%MESSAGE%</div>';
var resultsLineEndHtmlTpl = '</div><br/><div class="resultRow">';
var brHtmlTpl = '<br />';

var photoLabelHtmlTpl = ' \
<div style="margin-left:130px"> \
	%SCI-NAME% \
	%GENDER% \
	%COMMON-NAME% \
	%PHOTO-DETAILS% \
</div> \
';

//var photoLabelGenderHtmlTpl = '<img class="gender" height="17" width="8" src="%IMG-SRC%" title="%GENDER-LABEL%" />';
var photoLabelGenderHtmlTpl = '';

var photoLabelPhotographerHtmlTpl = '<br />(%PHOTO-LABEL% %PHOTOGRAPHER%)';

var imageHtmlTpl = '\
<a rel="prettyPhoto[gallery]" href="%IMAGE-URL%" pTitle="%PHOTO-LABEL%" title=""> \
	<img class="result-image" src="%THUMB-URL%" title="%PHOTO-CREDIT%" /> \
</a>\
';

//var genderHtmlTpl = '<img class="result-gender-icon" src="%ICON-URL%" title="%GENDER-LABEL%" />';
var genderHtmlTpl = '';

var matrixLinkHtmlTpl = '<br /><a href="?mtrx=%MATRIX-ID%">%MATRIX-LINK-TEXT%</a>';

var remoteLinkClickHtmlTpl = 'onclick="window.open(\'%REMOTE-LINK%\',\'_blank\');" title="%TITLE%"';

var statesClickHtmlTpl = 'onclick="toggleDetails(\'%LOCAL-ID%\');return false;"title="%TITLE%"';

var relatedClickHtmlTpl = 'onclick="setSimilar({ id:%ID%,type:\'%TYPE%\' });return false;" title="%TITLE%"';

var statesHtmlTpl = '\
<div id="det-%LOCAL-ID%" class="result-detail hidden"> \
	<ul> \
		<li>%STATES%</li> \
	</ul> \
</div> \
';

var statesJoinHtmlTpl = '</li><li>';

var speciesStateItemHtmlTpl = '<span class="result-detail-label">%GROUP% %CHARACTER%:</span> <span class="result-detail-value">%STATE%</span>';

var resultHtmlTpl = '\
<div id="res-v-%LOCAL-ID%" class="result"> \
	<div class="result-result"> \
		<div class="result-image-container"> \
			%IMAGE-HTML% \
		</div> \
		<div class="result-labels"> \
			<span class="result-name-scientific" title="%SCI-NAME-TITLE%">%SCI-NAME%</span> \
			<span class="result-name-common" title="%COMMON-NAME-TITLE%">%COMMON-NAME%</span> \
		</div> \
	</div> \
	<div class="result-icons"> \
		<div class="icon icon-book" title="more information" %REMOTE-LINK-CLICK%></div> \
			<div class="icon %SHOW-STATES-CLASS%" id="tog-%LOCAL-ID%" \
				%SHOW-STATES-CLICK% \
			>%SHOW-STATES-ICON%</div> \
		<div class="icon %RELATED-CLASS%" %RELATED-CLICK%></div> \
	</div>%STATES% \
</div> \
';	

//<div class="icon no-content"></div>
//		<div %SHOW-STATES-CLICK% id="tog-%LOCAL-ID%" class="icon icon-details"></div> \
	
var iconInfoHtmlTpl='<img class="result-icon-image icon-info" src="%IMG-URL%">';
var iconUrlHtmlTpl = iconInfoHtmlTpl.replace(' icon-info','');
var iconSimilarTpl = iconInfoHtmlTpl.replace(' icon-info',' icon-similar');	


var resultBatchHtmlTpl= '<span class=result-batch style="%STYLE%">%RESULTS%</span>' ;
var buttonMoreHtmlTpl='<li id="show-more"><input type="button" id="show-more-button" onclick="printResults();return false;" value="%LABEL%" class="ui-button"></li>';
var counterExpandHtmlTpl='%START-NUMBER%%NUMBER-SHOWING%&nbsp;%FROM-LABEL%&nbsp;%NUMBER-TOTAL%';
var pagePrevHtmlTpl='<li><a href="#" onclick="browsePage(\'p\');return false;">&lt;</a></li>';
var pageCurrHtmlTpl='<li><strong>%NR%</strong></li>';
var pageNumberHtmlTpl='<li><a href="#" onclick="browsePage(%INDEX%);return false;">%NR%</a></li>';
var pageNextHtmlTpl='<li><a href="#" onclick="browsePage(\'n\');return false;" class="last">&gt;</a></li>';
var counterPaginateHtmlTpl=' %FIRST-NUMBER%-%LAST-NUMBER% %NUMBER-LABEL% %NUMBER-TOTAL%';

var menuOuterHtmlTpl ='<ul>%MENU%</ul>';

var menuGroupHtmlTpl = '\
<li id="character-item-%ID%" class="closed"><a href="#" onclick="toggleGroup(%ID%);return false;">%LABEL%</a></li> \
<ul id="character-group-%ID%" class="hidden"> \
	%CHARACTERS% \
</ul> \
';

var menuLoneCharHtmlTpl='\
<li class="inner ungrouped last"> \
	<a class="facetLink" href="#" onclick="showStates(%ID%);return false;">%LABEL%%VALUE%</a> \
	%SELECTED% \
</li> \
';
var menuLoneCharDisabledHtmlTpl='<li class="inner ungrouped %CLASS% disabled" title="%TITLE%" ondblclick="showStates(%ID%);">%LABEL%%VALUE%	%SELECTED% </li>';
var menuLoneCharEmergentDisabledHtmlTpl='\
<li class="inner ungrouped %CLASS%" title="%TITLE%"> \
	<a class="facetLink emergent_disabled" href="#" onclick="showStates(%ID%);return false;">(%LABEL%%VALUE%)</a> \
	%SELECTED% \
</li> \
';
var menuCharHtmlTpl=menuLoneCharHtmlTpl.replace('ungrouped ','');
var menuCharDisabledHtmlTpl=menuLoneCharDisabledHtmlTpl.replace('ungrouped ','');
var menuCharEmergentDisabledHtmlTpl=menuLoneCharEmergentDisabledHtmlTpl.replace('ungrouped ','');

var menuSelStateHtmlTpl = '\
<div class="facetValueHolder"> \
%VALUE% %LABEL% %COEFF% \
<a href="#" class="removeBtn" onclick="clearStateValue(\'%STATE-ID%\');return false;"> \
<img src="%IMG-URL%"></a> \
</div> \
';
var menuSelStatesHtmlTpl = '<span>%STATES%</span>';



var similarHeaderHtmlTpl='\
%HEADER-TEXT% <span id="similarSpeciesName">%SPECIES-NAME%</span> <span class="result-count">(%NUMBER-START%-%NUMBER-END%)</span> \
<br /> \
<a class="clearSimilarSelection" href="#" onclick="closeSimilar();return false;">%BACK-TEXT%</a> \
<span id="show-all-divider"> | </span> \
<a class="clearSimilarSelection" href="#" onclick="toggleAllDetails();return false;" id="showAllLabel">%SHOW-STATES-TEXT%</a> \
';

var searchHeaderHtmlTpl='\
%HEADER-TEXT% <span id="similarSpeciesName">%SEARCH-TERM%</span> <span class="result-count">(%NUMBER-START%-%NUMBER-END% %OF-TEXT% %NUMBER-TOTAL%)</span> \
<br /> \
<a class="clearSimilarSelection" href="#" onclick="closeSearch();return false;">%BACK-TEXT%</a> \
';
</script>



</html>