{include file="../shared/_matrix_head.tpl"}
{include file="../shared/_matrix_body-start.tpl"}

<style>
.matrix-links {
	text-align:left;
    color: #009ee0;
    font-weight: bold;
}
.matrix-links a {
	text-decoration:none;
    color: #009ee0;
	line-height:1.5em;
}
.matrix-links a:hover {
	text-decoration:underline;
}
.matrix-links a.active-item {
    color: black;
	cursor: default;
}
.matrix-links a.active-item:hover {
	text-decoration:none;
}
{if $settings->use_overview_image == 1}
#dialogRidge #results-container .result-image {
	max-width: 241px;
	max-height: 281px;
	margin: 0 auto;
}
{/if}


#result-count, .headerPagination {
	margin:3px 0 3px 0;
}

.list.paging li {
	display:inline;
	font-weight:bold;
}

.list.paging li a {
	text-decoration: none;
	font-weight:normal;	
	color: black;
}
.list.paging li a:hover {
	text-decoration: underline;
}

</style>

	<div id="dialogRidge">

		{include file="_left_column.tpl"}

	    <div id="content" class="title-type4">

			<div>
				<div id="similarSpeciesHeader" class="hidden" style="width:100%"></div>
				<div id="result-count" class="headerSelectionLabel"></div>
			</div>

	        <div id="resultsHeader">
                <div class="headerPagination">
                    <ul id="paging-header" class="list paging"></ul>
                </div>
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
		{ key : 'Informatie', translation : '{t}Informatie{/t}' },
		{ key : 'Filter op kenmerken', translation : '{t}Filter op kenmerken{/t}' },
		{ key : 'determinatiesleutel', translation : '{t}determinatiesleutel{/t}' },
		{ key : 'soortzoeker', translation : '{t}soortzoeker{/t}' },
		{ key : 'Kenmerken', translation : '{t}Kenmerken{/t}' },
		{ key : 'kenmerken', translation : '{t}kenmerken{/t}' },
		{ key : 'Versiegeschiedenis', translation : '{t}Versiegeschiedenis{/t}' },
		{ key : 'geen afbeelding beschikbaar', translation : '{t}geen afbeelding beschikbaar{/t}' },
	];

	setSetting({
		matrixId: {$matrix.id},
		projectId: {$session.app.project.id},
		noTaxonImages: {if $settings->no_taxon_images==1}true{else}false{/if},
		imageRootSkin: '{$image_root_skin}',
		imageRootProject: '{$projectUrls.projectMedia}',
		useEmergingCharacters: {if $settings->use_emerging_characters}{$settings->use_emerging_characters}{else}0{/if},
		suppressImageEnlarge: {if $settings->suppress_image_enlarge}{$settings->suppress_image_enlarge}{else}0{/if},
		defaultSpeciesImages: { portrait: '{$generic_images.portrait}', landscape: '{$generic_images.landscape}' } ,
		imageOrientation: '{if $settings->image_orientation}{$settings->image_orientation}{else}portrait{/if}',
		browseStyle: '{$settings->browse_style}',
		scoreThreshold: {if (int)$settings->score_threshold>-1 && (int)$settings->score_threshold<101}{$settings->score_threshold+0}{else}100{/if},
		alwaysShowDetails: {if $settings->always_show_details}{$settings->always_show_details}{else}0{/if},
		perPage: {if $settings->items_per_page}{$settings->items_per_page}{else}15{/if},
		perLine: {if $settings->items_per_line}{$settings->items_per_line}{else}3{/if},
		generalSpeciesInfoUrl: '{$settings->species_info_url}',
		infoLinkTarget: '{if $settings->info_link_target}{$settings->info_link_target}{else}_blank{/if}',
		hideImagesWhenNoneAvailable: {if $settings->hide_images_when_none_available==='0'}0{else}1{/if},
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

<!-- templates -->

<div class="inline-templates" id="resultsHtmlTpl">
<!-- 
	%RESULTS%
-->
</div>

<div class="inline-templates" id="noResultHtmlTpl">
<!-- 
	<div class="noResults">%MESSAGE%</div>
-->
</div>

<div class="inline-templates" id="resultsLineEndHtmlTpl">
</div>

<div class="inline-templates" id="photoLabelHtmlTpl">
<!-- 
    <div style="margin-left:130px">
        %SCI-NAME%
        %GENDER%
        %COMMON-NAME%
        %PHOTO-DETAILS%
    </div>
-->
</div>

<div class="inline-templates" id="photoLabelGenderHtmlTpl">
</div>

<div class="inline-templates" id="photoLabelPhotographerHtmlTpl">
<!-- 
	<br />(%PHOTO-LABEL% %PHOTOGRAPHER%)
-->
</div>

<div class="inline-templates" id="imageHtmlTpl">
<!-- 
	<img class="result-image" src="%THUMB-URL%" title="%PHOTO-CREDIT%" />
-->
</div>

<div class="inline-templates" id="imageHtmlUrlTpl">
<!-- 
    <a rel="prettyPhoto[gallery]" href="%IMAGE-URL%" pTitle="%PHOTO-LABEL%" title="">
	%IMAGE%
    </a>
-->
</div>

<div class="inline-templates" id="genderHtmlTpl">
</div>

<div class="inline-templates" id="matrixLinkHtmlTpl">
<!-- 
	<a class="matrix-matrix-link" href="?mtrx=%MATRIX-ID%">%MATRIX-LINK-TEXT% ></a>
-->
</div>

<div class="inline-templates" id="noActionIconHtmlTpl">
<!-- 
	<div class="result-icon no-content"></div>
-->
</div>

<div class="inline-templates" id="remoteLinkIconHtmlTpl">
<!-- 
	<div class="result-icon" onClick="doRemoteLink('%LINK%','%SCI-NAME%', '%NAMESCIENTIFIC%', '%NAMECOMMON%');" title="%TITLE%">%REMOTE-LINK-ICON%</div>
-->
</div>

<div class="inline-templates" id="statesIconHtmlTpl">
<!-- 
	<div class="result-icon icon-details" id="tog-%LOCAL-ID%" onClick="toggleDetails('%LOCAL-ID%');return false;" title="%TITLE%">%SHOW-STATES-ICON%</div>
-->
</div>

<div class="inline-templates" id="relatedIconHtmlTpl">
<!-- 
	<div class="result-icon icon-resemblance" onClick="setSimilar({ id:%ID%,type:'%TYPE%' });return false;" title="%TITLE%">%RELATED-ICON%</div>
-->
</div>

<div class="inline-templates" id="statesHtmlTpl">
<!-- 
    <div id="det-%LOCAL-ID%" class="result-detail hidden">
        <ul>
			%STATES%
        </ul>
    </div>
-->
</div>

<div class="inline-templates" id="speciesStateItemHtmlTpl">
<!--
	<li><span class="result-detail-label">%GROUP% %CHARACTER%:</span> <span class="result-detail-value">%STATE%</span></li>
-->
</div>

<div class="inline-templates" id="resultHtmlTpl">
<!--
    <div class="result%CLASS-HIGHLIGHT% %MATRIX-LINK-CLASS%" id="res-%LOCAL-ID%">
        <div class="result-result">
            <div class="result-image-container">
                %IMAGE-HTML%
            </div>
            <span class="photographerName">%PHOTOGRAPHER%</span>
            <div class="result-labels">
                %GENDER%
                <span class="result-name-scientific" title="%SCI-NAME-TITLE%">%SCI-NAME%</span>
                <span class="result-name-common" title="%COMMON-NAME-TITLE%"><br />%COMMON-NAME%</span>
                %SCORE%
	            %MATRIX-LINK%
			</div>
        </div>
        <div class="result-icons">
        	%REMOTE-LINK%
            %SHOW-STATES%
            %RELATED-TAXA%
        </div>%STATES%
    </div>
-->
</div>

<div class="inline-templates" id="resultScoreHtmlTpl">
<!-- 
	<span style="float:right;display:inline-block;margin-top:3px;font-weight:bold">%SCORE%%</span>
-->
</div>

<div class="inline-templates" id="resultBatchHtmlTpl">
<!-- 
	<span class=result-batch style="%STYLE%">%RESULTS%</span>
-->
</div>

<div class="inline-templates" id="buttonMoreHtmlTpl">
<!--
	<li id="show-more"><input type="button" id="show-more-button" onclick="printResults();return false;" value="%LABEL%" class="ui-button"></li>
-->
</div>

<div class="inline-templates" id="counterExpandHtmlTpl">
<!-- 
	<div class="headerText">%START-NUMBER%%NUMBER-SHOWING%&nbsp;%FROM-LABEL%&nbsp;%NUMBER-TOTAL%</div>
-->
</div>

<div class="inline-templates" id="pagePrevHtmlTpl">
<!--
	<li><a href="#" onclick="browsePage('p');return false;">&lt;</a></li>
-->
</div>

<div class="inline-templates" id="pageCurrHtmlTpl">
<!--
	<li><strong>%NR%</strong></li>
-->
</div>

<div class="inline-templates" id="pageNumberHtmlTpl">
<!--
	<li><a href="#" onclick="browsePage(%INDEX%);return false;">%NR%</a></li>
-->
</div>

<div class="inline-templates" id="pageNextHtmlTpl">
<!--
	<li><a href="#" onclick="browsePage('n');return false;" class="last">&gt;</a></li>
-->
</div>

<div class="inline-templates" id="counterPaginateHtmlTpl">
<!-- 
	%FIRST-NUMBER%-%LAST-NUMBER% %NUMBER-LABEL% %NUMBER-TOTAL%
-->
</div>

<div class="inline-templates" id="menuOuterHtmlTpl">
<!--
	<ul>%MENU%</ul>
-->
</div>

<div class="inline-templates" id="menuGroupHtmlTpl">
<!--
    <li id="character-item-%ID%" class="closed">
    <a href="#" onclick="toggleGroup(%ID%);return false;">
        %LABEL%
        <span class="menuToggleIcon">
            <i class="ion-chevron-down down"></i>
            <i class="ion-chevron-up up"></i>
        </span>
    </a>
    <ul id="character-group-%ID%" class="hidden">
        %CHARACTERS%
    </ul>
    </li>
-->
</div>

<div class="inline-templates" id="menuLoneCharHtmlTpl">
<!--
    <li class="inner ungrouped last">
        <a class="facetLink" href="#" onclick="showStates(%ID%);return false;">%LABEL%%VALUE%</a>
        %SELECTED%
    </li>
-->
</div>

<div class="inline-templates" id="menuLoneCharDisabledHtmlTpl">
<!--
	<li class="inner ungrouped %CLASS% disabled secretlyclickable" data-id="%ID%" title="%TITLE%">%LABEL%%VALUE%	%SELECTED% </li>
-->
</div>

<div class="inline-templates" id="menuLoneCharEmergentDisabledHtmlTpl">
<!--
	<li class="inner emergent_disabled %CLASS% secretlyclickable" data-id="%ID%" title="%TITLE%">(%LABEL%%VALUE%) %SELECTED% </li>
-->
</div>

<div class="inline-templates" id="menuCharHtmlTpl">
<!--
    <li class="inner last">
        <a class="facetLink" href="#" onclick="showStates(%ID%);return false;">%LABEL%%VALUE%</a>
        %SELECTED%
    </li>
-->
</div>

<div class="inline-templates" id="menuCharDisabledHtmlTpl">
<!--
	<li class="inner %CLASS% disabled secretlyclickable" data-id="%ID%" title="%TITLE%">%LABEL%%VALUE%	%SELECTED% </li>
-->
</div>

<div class="inline-templates" id="menuCharEmergentDisabledHtmlTpl">
<!--
	<li class="inner emergent_disabled %CLASS% secretlyclickable" data-id="%ID%" title="%TITLE%">(%LABEL%%VALUE%) %SELECTED% </li>
-->
</div>

<div class="inline-templates" id="menuSelStateHtmlTpl">
<!-- 
    <div class="facetValueHolder">
        <a href="#" class="removeBtn" onclick="clearStateValue('%STATE-ID%');return false;">
        %VALUE% %LABEL% %COEFF%
        <i class="ion-close-circled"></i></a>
    </div>
-->
</div>

<div class="inline-templates" id="menuSelStatesHtmlTpl">
<!--
	<span>%STATES%</span>
-->
</div>

<div class="inline-templates" id="iconInfoHtmlTpl">
<!-- 
	<img class="result-icon-image icon-info" src="%IMG-URL%">
-->
</div>

<div class="inline-templates" id="iconUrlHtmlTpl">
<!-- 
	<img class="result-icon-image" src="%IMG-URL%">
-->
</div>

<div class="inline-templates" id="iconSimilarTpl">
<!-- 
	<img class="result-icon-image icon-similar" src="%IMG-URL%">
-->
</div>

<div class="inline-templates" id="similarHeaderHtmlTpl">
<!-- 
    <div class="headerText similarHeader">
	    <a class="clearSimilarSelection" href="#" onclick="closeSimilar();return false;"><i class="ion-chevron-left"></i></a>
    <span class="similarSpeciesText">%HEADER-TEXT% <span id="similarSpeciesName">%SPECIES-NAME%</span><span class="result-count"> (%NUMBER-START%-%NUMBER-END%) </span></span></div>
-->
</div>

<div class="inline-templates" id="searchHeaderHtmlTpl">
<!-- 
	<div class="headerText">%HEADER-TEXT% <span id="similarSpeciesName">%SEARCH-TERM%</span> <span class="result-count"> (%NUMBER-START%-%NUMBER-END% %OF-TEXT% %NUMBER-TOTAL%) </span></div>
-->
</div>

<div class="inline-templates" id="infoDialogHtmlTpl">
<!--
    <div style="text-align:left;width:400px">
    <style>
    p {
        margin-bottom:15px;
    }
    </style>
    %BODY%
    %URL%
    </div>
-->
</div>

<div class="inline-templates" id="infoDialogUrlHtmlTpl">
<!-- 
	<a href="%URL%" class="popup-link" target="_blank">%LINK-LABEL%</a>
-->
</div>

<div class="inline-templates" id="matrixSelectList">
<!--
	<ul class="matrix-links">%LINES%</ul>
-->
</div>

<div class="inline-templates" id="matrixSelectItem">
<!--
	<li><a href="index.php?mtrx=%ID%" class="matrix-link%CLASS%">%LABEL%</a></li>
-->
</div>

<!-- /templates -->

{include file="../shared/_matrix_footer.tpl"}
