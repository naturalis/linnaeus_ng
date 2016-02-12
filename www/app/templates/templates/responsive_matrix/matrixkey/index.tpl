	{include file="../shared/header.tpl"}
	<div id="dialogRidge">

		{include file="_left_column.tpl"}

	    <div id="content" class="title-type4">
	    
	        <div id="resultsHeader">
	            <div>
                <div class="headerPagination">
                    <ul id="paging-header" class="list paging"></ul>
                </div>
	            </div>
	        </div>

			<div>
				

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

	
	labels.popup_species_link="{$settings->popup_species_link_text|@escape}";
	
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
		useEmergingCharacters: {if $settings->use_emerging_characters}{$settings->use_emerging_characters}{else}0{/if},
		defaultSpeciesImages: { portrait: '{$image_root_skin}noImage.jpg', landscape: '{$image_root_skin}noImage.png' } ,
		// defaultSpeciesImages: { portrait: '{$image_root_skin}noImagePortrait.jpg', landscape: '{$image_root_skin}noImage.png' } ,
		imageOrientation: '{$settings->image_orientation}',
		browseStyle: '{$settings->browse_style}',
		scoreThreshold: {if $settings->score_threshold}{$settings->score_threshold}{else}100{/if},
		alwaysShowDetails: {if $settings->always_show_details}{$settings->always_show_details}{else}0{/if},
		perPage: {if $settings->items_per_page}{$settings->items_per_page}{else}15{/if},
		perLine: {if $settings->items_per_line}{$settings->items_per_line}{else}3{/if},
		generalSpeciesInfoUrl: '{$settings->species_info_url}',
		showScores: {if $settings->show_scores}{$settings->show_scores}{else}0{/if},
		initialSortColumn: '{$settings->initial_sort_column}',
		alwaysSortByInitial: {if $settings->always_sort_by_initial}{$settings->always_sort_by_initial}{else}0{/if},
		similarSpeciesShowDistinctDetailsOnly: {if $settings->similar_species_show_distinct_details_only}{$settings->similar_species_show_distinct_details_only}{else}0{/if},
	});

	setScores($.parseJSON('{$session_scores}'));
	setStates($.parseJSON('{$session_states}'));
	setCharacters($.parseJSON('{$session_characters}'));
	setMenu($.parseJSON('{$session_menu|@addslashes}'));
	setDataSet($.parseJSON('{$full_dataset|@addslashes}'));
			
	matrixInit();

});
</script>

<script type="text/JavaScript">

var resultsHtmlTpl = '%RESULTS%';
var noResultHtmlTpl='<div class="noResults">%MESSAGE%</div>';
var resultsLineEndHtmlTpl = '';
var brHtmlTpl = '';

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

var matrixLinkHtmlTpl = '<a class="goToMatrixLink" href="?mtrx=%MATRIX-ID%"><i class="ion-chevron-right"></i></a>';

//var remoteLinkClickHtmlTpl = 'onclick="window.open(\'%REMOTE-LINK%\',\'_blank\');" title="%TITLE%"';
var remoteLinkClickHtmlTpl = 'onclick="doRemoteLink(\'%REMOTE-LINK%\',\'%SCI-NAME%\', \'%NAMESCIENTIFIC%\', \'%NAMECOMMON%\');" title="%TITLE%"';


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
<div class="result%CLASS-HIGHLIGHT% %MATRIX-LINK-CLASS%" id="res-%LOCAL-ID%"> \
	<div class="result-result"> \
		<div class="result-image-container"> \
			%IMAGE-HTML% \
		</div> \
		<span class="photographerName">%PHOTOGRAPHER%</span> \
		<div class="result-labels"> \
			%GENDER% \
			<span class="result-name-scientific" title="%SCI-NAME-TITLE%">%SCI-NAME%</span> \
			<span class="result-name-common" title="%COMMON-NAME-TITLE%"><br />%COMMON-NAME%</span> \
			%SCORE% \
			</div> \
    </div> \
    <div class="result-icons"> \
			<div class="result-icon%REMOTE-LINK-CLASS%" \
				%REMOTE-LINK-CLICK% \
			>%REMOTE-LINK-ICON%</div> \
			<div class="result-icon%SHOW-STATES-CLASS%" id="tog-%LOCAL-ID%" \
				%SHOW-STATES-CLICK% \
			>%SHOW-STATES-ICON%</div> \
			<div class="result-icon%RELATED-CLASS% related" \
				%RELATED-CLICK% \
			>%RELATED-ICON%</div> \
			%MATRIX-LINK% \
        </div>%STATES% \
    </div> \
';

var resultScoreHtmlTpl= '<p>Score: %SCORE%%</p>' ;
var resultBatchHtmlTpl= '<span class=result-batch style="%STYLE%">%RESULTS%</span>' ;
var buttonMoreHtmlTpl='<li id="show-more"><input type="button" id="show-more-button" onclick="printResults();return false;" value="%LABEL%" class="ui-button"></li>';
var counterExpandHtmlTpl='<div class="headerText">%START-NUMBER%%NUMBER-SHOWING%&nbsp;%FROM-LABEL%&nbsp;%NUMBER-TOTAL%</div>';
var pagePrevHtmlTpl='<li><a href="#" onclick="browsePage(\'p\');return false;">&lt;</a></li>';
var pageCurrHtmlTpl='<li><strong>%NR%</strong></li>';
var pageNumberHtmlTpl='<li><a href="#" onclick="browsePage(%INDEX%);return false;">%NR%</a></li>';
var pageNextHtmlTpl='<li><a href="#" onclick="browsePage(\'n\');return false;" class="last">&gt;</a></li>';
var counterPaginateHtmlTpl=' %FIRST-NUMBER%-%LAST-NUMBER% %NUMBER-LABEL% %NUMBER-TOTAL%';

var menuOuterHtmlTpl ='<ul>%MENU%</ul>';

var menuGroupHtmlTpl = '\
<li id="character-item-%ID%" class="closed"> \
<a href="#" onclick="toggleGroup(%ID%);return false;"> \
	%LABEL% \
	<span class="menuToggleIcon"> \
		<i class="ion-chevron-down down"></i> \
		<i class="ion-chevron-up up"></i> \
	</span> \
</a> \
<ul id="character-group-%ID%" class="hidden"> \
	%CHARACTERS% \
</ul> \
</li> \
';

var menuLoneCharHtmlTpl='\
<li class="inner ungrouped last"> \
	<a class="facetLink" href="#" onclick="showStates(%ID%);return false;">%LABEL%%VALUE%</a> \
	%SELECTED% \
</li> \
';
var menuLoneCharDisabledHtmlTpl=
	'<li class="inner ungrouped %CLASS% disabled" title="%TITLE%">%LABEL%%VALUE%	%SELECTED% </li>';

var menuLoneCharEmergentDisabledHtmlTpl=
	'<li class="inner emergent_disabled %CLASS%" title="%TITLE%">(%LABEL%%VALUE%) %SELECTED% </li>';
var menuCharHtmlTpl=menuLoneCharHtmlTpl.replace('ungrouped ','');
var menuCharDisabledHtmlTpl=menuLoneCharDisabledHtmlTpl.replace('ungrouped ','');
var menuCharEmergentDisabledHtmlTpl=menuLoneCharEmergentDisabledHtmlTpl.replace('ungrouped ','');

var menuSelStateHtmlTpl = '\
<div class="facetValueHolder"> \
<a href="#" class="removeBtn" onclick="clearStateValue(\'%STATE-ID%\');return false;"> \
%VALUE% %LABEL% %COEFF% \
<i class="ion-close-circled"></i></a> \
</div> \
';
var menuSelStatesHtmlTpl = '<span>%STATES%</span>';

var iconInfoHtmlTpl='<img class="result-icon-image icon-info" src="%IMG-URL%">';
var iconUrlHtmlTpl = iconInfoHtmlTpl.replace(' icon-info','');
var iconSimilarTpl = iconInfoHtmlTpl.replace(' icon-info',' icon-similar');

var similarHeaderHtmlTpl='\
<div class="headerText similarHeader"> \
<a class="clearSimilarSelection" href="#" onclick="closeSimilar();return false;"><i class="ion-chevron-left"></i></a> \
<span class="similarSpeciesText">%HEADER-TEXT% <span id="similarSpeciesName">%SPECIES-NAME%</span><span class="result-count"> (%NUMBER-START%-%NUMBER-END%) </span></span></div> \
';

// var similarHeaderHtmlTpl='\
// %HEADER-TEXT% <span id="similarSpeciesName">%SPECIES-NAME%</span> <span class="result-count">(%NUMBER-START%-%NUMBER-END%)</span> \
// <br /> \
// <a class="clearSimilarSelection" href="#" onclick="closeSimilar();return false;">%BACK-TEXT%</a> \
// <span id="show-all-divider"> | </span> \
// <a class="clearSimilarSelection" href="#" onclick="toggleAllDetails();return false;" id="showAllLabel">%SHOW-STATES-TEXT%</a> \
// ';

var searchHeaderHtmlTpl='\
<div class="headerText">%HEADER-TEXT% <span id="similarSpeciesName">%SEARCH-TERM%</span> <span class="result-count"> (%NUMBER-START%-%NUMBER-END% %OF-TEXT% %NUMBER-TOTAL%) </span></div> \
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

{include file="../shared/footer.tpl"}
