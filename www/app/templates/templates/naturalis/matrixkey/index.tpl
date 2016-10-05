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
              <a id="clearSelectionLink" href="#" onClick="resetMatrix();return false;">{t}Opnieuw beginnen{/t}</a>
          </div>

		{if $master_matrix_id}
		<div class='control'>
			<span class='icon icon-arrow-left-up'></span>
			<a href="?mtrx={$master_matrix_id}">{t}Terug naar de hoofdsleutel{/t}</a>
		</div>
		{/if}
						
            <div id='legend'>
              <h2>{t}Legenda{/t}</h2>
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

				{* snippet}colofon.html{/snippet *}


        {if $introduction_links[$settings->introduction_topic_inline_info]}
            <div id='info-container'>
            <script>
			$(document).ready(function()
			{
                $.get( '../introduction/topic.php?id={$introduction_links[$settings->introduction_topic_inline_info].page_id}&format=plain' )
                .success(function(data) { $('#info-container').html( data ); } ) ;
			});
			</script>
            </div>
        {/if}        

        {if $introduction_links[$settings->introduction_topic_colophon_citation]}
            <div>
                <h3><a href="#" onClick="
                $.get( '../introduction/topic.php?id={$introduction_links[$settings->introduction_topic_colophon_citation].page_id}&format=plain' )
                .success(function(data) { printInfo( data ,'{t}Colofon en citatie{/t}'); } ) ;
                ">{t}Colofon en citatie{/t}</a></h3>
            </div>
        {/if}

        {if $introduction_links[$settings->introduction_topic_versions]}
            <div>
                <h3><a href="#" onClick="
                $.get( '../introduction/topic.php?id={$introduction_links[$settings->introduction_topic_versions].page_id}&format=plain' )
                .success(function(data) { printInfo( data ,'{t}Versiegeschiedenis{/t}'); } ) ;
                ">{t}Versiegeschiedenis{/t}</a></h3>
            </div>
        {/if}
        



              <!-- div>
                <h3>{t}Geïmplementeerd door{/t}</h3>
                <p>{t}Naturalis & ETI BioInformatics.{/t}</p>
              </div -->
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
                  <span id='showAllLabelLabel'>{t}toon alle kenmerken{/t}</span>
                </a>
              </div>
            </div>
            <div id='results'>
              <div class='hidden'></div>
              <div class='layout-landscapes' id='results-container'></div>
            </div>
            <div class='footerPagination noline' id='footerPagination'>
            <span id='paging-footer'>
            <span id='show-more'>
              <input class='ui-button' id='show-more-button' onclick='printResults();return false;' type='button' value='show more results' class='hidden'>
              </span></span>
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
	{if $settings->image_orientation=="landscape"}
		$("<link/>", {
		   rel: "stylesheet",
		   type: "text/css",
		   href: "{$projectUrls.projectCSS}/matrix_landscape.css"
		}).appendTo("head");	
	{/if}
	
	popup_species_link="{$settings->popup_species_link_text|@escape}";
	
	__translations = [
		{ key : 'Dit kenmerk is bij de huidige selectie niet langer onderscheidend.', translation : '{t}Dit kenmerk is bij de huidige selectie niet langer onderscheidend.{/t}' },
		{ key : 'Dit kenmerk is bij de huidige selectie nog niet onderscheidend.', translation : '{t}Dit kenmerk is bij de huidige selectie nog niet onderscheidend.{/t}' },
		{ key : 'Ga naar sleutel', translation : '{t}Ga naar sleutel{/t}' },
		{ key : 'Geen resultaten.', translation : '{t}Geen resultaten.{/t}' },
		{ key : 'Gelijkende soorten van', translation : '{t}Gelijkende soorten van{/t}' },
		{ key : 'Informatie over soort/taxon', translation : '{t}Informatie over soort/taxon{/t}' },
		{ key : 'Meer informatie', translation : '{t}Meer informatie{/t}' },
		{ key : 'meer informatie', translation : '{t}meer informatie{/t}' },
		{ key : 'Meer informatie over soort/taxon', translation : '{t}Meer informatie over soort/taxon{/t}' },
		{ key : 'Zoekresultaten voor', translation : '{t}Zoekresultaten voor{/t}' },
		{ key : 'alle onderscheidende kenmerken tonen', translation : '{t}alle onderscheidende kenmerken tonen{/t}' },
		{ key : 'toon alle kenmerken', translation : '{t}toon alle kenmerken{/t}' },
		{ key : 'foto', translation : '{t}foto{/t}' },
		{ key : 'gelijkende soorten', translation : '{t}gelijkende soorten{/t}' },
		{ key : 'kenmerken verbergen', translation : '{t}kenmerken verbergen{/t}' },
		{ key : 'alle kenmerken verbergen', translation : '{t}alle kenmerken verbergen{/t}' },
		{ key : 'meer resultaten laden', translation : '{t}meer resultaten laden{/t}' },
		{ key : 'onderscheidende kenmerken', translation : '{t}onderscheidende kenmerken{/t}' },
		{ key : 'terug', translation : '{t}terug{/t}' },
		{ key : 'van', translation : '{t}van{/t}' },
	];

	setSetting({
		matrixId: {$matrix.id},
		projectId: {$session.app.project.id},
		imageRootSkin: '{$image_root_skin}',
		imageRootProject: '{$projectUrls.projectMedia}',
		useEmergingCharacters: {$settings->use_emerging_characters},
		defaultSpeciesImages: { portrait: '{$image_root_skin}noimage.gif', landscape: '{$image_root_skin}noimage-lndscp.gif' } ,
		imageOrientation: '{$settings->image_orientation}',
		browseStyle: '{$settings->browse_style}',
		scoreThreshold: {$settings->score_threshold},
		alwaysShowDetails: {$settings->always_show_details},
		perPage: {$settings->items_per_page},
		perLine: {$settings->items_per_line},
		generalSpeciesInfoUrl: '{$settings->species_info_url}',
		showScores: {$settings->show_scores},
		initialSortColumn: '{$settings->initial_sort_column}',
		alwaysSortByInitial: {$settings->always_sort_by_initial},
		similarSpeciesShowDistinctDetailsOnly: {if $settings->similar_species_show_distinct_details_only}{$settings->similar_species_show_distinct_details_only}{else}0{/if},
	});

	setScores($.parseJSON('{$session_scores|@addslashes}'));
	setStates($.parseJSON('{$session_states|@addslashes}'));
	setCharacters($.parseJSON('{$session_characters|@addslashes}'));
	setMenu($.parseJSON('{$session_menu|@addslashes}'));
	setDataSet($.parseJSON('{$full_dataset|@addslashes}'));

	matrixInit();

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
<div id="det-%LOCAL-ID%" class="result-detail" style="display:none"> \
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
			<div class="icon %SHOW-STATES-CLASS% result-detail-icon" id="tog-%LOCAL-ID%" \
				%SHOW-STATES-CLICK% \
			>%SHOW-STATES-ICON%</div> \
		<div class="icon %RELATED-CLASS%" %RELATED-CLICK%></div> \
	</div>%STATES% \
</div> \
';	

//<div class="icon no-content"></div>
//		<div %SHOW-STATES-CLICK% id="tog-%LOCAL-ID%" class="icon icon-details"></div> \
	
var iconInfoHtmlTpl='';
var iconUrlHtmlTpl = iconInfoHtmlTpl.replace(' icon-info','');
var iconSimilarTpl = iconInfoHtmlTpl.replace(' icon-info',' icon-similar');	


var resultBatchHtmlTpl= '<span class=result-batch style="%STYLE%">%RESULTS%</span>' ;
var buttonMoreHtmlTpl="<span id='show-more'> \<input class='ui-button' id='show-more-button' onclick='printResults();return false;' type='button' value='show more results' class='hidden'> \
</span>";
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
<a href="#" class="removeBtn" onclick="clearStateValue(\'%STATE-ID%\');return false;"></a></div> \
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

var infoDialogHtmlTpl=' \
<div style="text-align:left;width:400px"> \
<style> \
p { \
	margin-bottom:15px; \
} \
</style> \
%BODY% \
%URL% \
</div>';

var infoDialogUrlHtmlTpl='<a href="%URL%" class="popup-link" target="_blank">%LINK-LABEL%</a>';

</script>

</html>
