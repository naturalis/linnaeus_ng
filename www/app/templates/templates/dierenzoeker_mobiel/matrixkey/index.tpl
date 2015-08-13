{include file="../shared/header.tpl"}

<title>{$projectUrls.systemMedia}</title>
<body class="ui-mobile-viewport">
<div tabindex="0" data-url="main" id="main" class="main-page ui-page ui-body-c ui-page-active" data-role="page" style="background-image: none; min-height: 920px;">

    <div id="speciescontent" class="content keuze-content ui-content" style="display:none">

		<div>
			<div data-role="header" class="header" style="background-color:black;height:67px;">
				<img src="{$projectUrls.systemMedia}header-show.png" alt="Dierenzoeker" class="logo" onClick="setactivepage(0);forceScrollTop=true;main()">
				<a href="#" onClick="detailback()" data-transition="slide" data-direction="reverse" data-role="none">
					<img src="{$projectUrls.systemMedia}back.png" class="info-button" style="position:absolute;left:-10px;top:2px;" alt="">
				</a>
			</div>

		</div>

		<div id="species-detail-content" class="soortpagina"></div>

	</div>

    <div id="charactercontent" class="content keuze-content ui-content" style="display:none">

		<div id="x-menu-header-back">

			<div data-role="header" class="header" style="background-color:black;height:48px;">
				<img src="{$projectUrls.systemMedia}header_speech.png" alt="Dierenzoeker" class="logo" onClick="setactivepage(0);forceScrollTop=true;main()">
				<a href="#" onClick="appController.states(selection);" data-transition="slide" data-direction="reverse" data-role="none">
					<img src="{$projectUrls.systemMedia}back.png" class="info-button" style="position:absolute;left:-10px;top:2px;" alt="">
				</a>
			</div>

		</div>

    	<div class="collapsible-set-wrapper">

			<div class="ui-collapsible" data-icon-pos="right" style="border-bottom:0px solid #323232;" id="expanded-characters">
			</div>

        </div>

	</div>

    <div id="selectioncontent" role="main" class="ui-content" data-role="content" style="display:none">

		<div id="x-menu-header">

			<div role="banner" class="ui-header ui-bar-a" data-role="header" id="mainheader">
				<img src="{$projectUrls.systemMedia}header_speech.png" alt="Dierenzoeker" class="logo" style="display:block" id="mainlogo">
				<a data-theme="a" id="infobutton" href="#" onClick="loadpage('../../static/dierenzoeker_mobiel/colofon.php')" data-role="button" data-inline="true" data-transition="slide" class="ui-btn-left ui-btn ui-btn-inline ui-btn-corner-all ui-shadow ui-btn-up-a" style="top:50%;margin-top:-17px;height:24px;">
				<span aria-hidden="true" class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">
					<img src="{$projectUrls.systemMedia}info.png" alt="">
				</span></span></a>
			</div>

		</div>

		<div id="x-menu-characters">

			<div class="facetheadercontainer">
<h4 class="tagline left-tagline ie-rounded keuze-tagline ui-collapsible-heading"><span class="ui-btn-inner ui-corner-top ui-corner-bottom" aria-hidden="true"><span class="ui-btn-text"> Wat weet je over het dier?</h4>
			</div>

			<div id="filter" class="filterset" style="padding-top:27px;">
				<div id="filtergrid" class="ui-grid-c">
				<!-- groups,characters,states -->
				</div>
			</div>

		</div>

		<div id="x-menu-selection" style="display:none">

			<div style="background-color:#2383b0;position:relative;">

				<div id="" class="filterset" style="background:none">
					<h4 style="margin: 0px;margin-left:9px;padding-top: 10px;">Dit weet je:</h4>
					<span style="position:absolute;right:22px;top:3px;">
					   <img id="clear-all-button" src="{$projectUrls.systemMedia}delete-all-choices-btn.png" onClick="clearall()" alt="">
					</span>
					<div id="selectiongrid" class="ui-grid-c">
					<!-- selected states -->
					</div>
				</div>
			</div>

		</div>

	</div>

    <div id="resultcontent" style="display:none;margin-top:2px">

		<div class="block-container middle-block-container" style="margin-bottom: 60px;">

			<a id="result-top" name="result-top"></a>
			<!-- h4 style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">
			<span class="ie-rounded num-of-results" id="num-of-results-top"></span><span class="num-of-results-label" id="num-of-results-label-top"></span>
			</h4 -->

			<ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist ui-listview ui-listview-inset ui-corner-all ui-shadow">
			<!-- results -->
			</ul>

			<span id="button-container">

				<a id="moreRowsButton" class="simplebutton ui-btn ui-btn-corner-all ui-shadow ui-btn-up-c" style="background-color:#5a5c5f;color:white;border:none;display:none;margin-top:35px;" onClick="event.preventDefault();resultsexpand();" data-role="button" href="#" data-theme="c">
					<span class="ui-btn-inner ui-btn-corner-all" aria-hidden="true">
					<span class="ui-btn-text" id="moreRowsButton-text">Volgende tonen</span>
					</span>
				</a>

				<a data-theme="c" href="#" onClick="loadpage('../../static/dierenzoeker_mobiel/faq.php')" data-transition="slide" data-role="button" id="askNaturalis" class="simplebutton ie-rounded ui-btn ui-btn-up-c ui-btn-corner-all ui-shadow" style="background-color:#5a5c5f;color:white;border:none;">
					<span aria-hidden="true" class="ui-btn-inner ui-btn-corner-all">
						<span class="ui-btn-text">Vragen?</span>
					</span>
				</a>
			</span>

		</div>

	</div>

</div>

<a href="#" onClick="scrollresults()" id="bottom-bar" class="menu-button-img">
	<span style="color:orange" class="num-of-results" id="num-of-results-bottom"></span><span class="num-of-results-label" id="num-of-results-label-bottom"></span>
</a>

<div tabindex="0" data-url="main" id="secondary" class="main-page ui-page ui-body-c ui-page-active" data-role="page" style="background-image:none;min-height:920px;display:none">

    <div class="content keuze-content ui-content">

		<div>
			<div data-role="header" class="header" style="background-color:black;height:67px;">
				<img src="{$projectUrls.systemMedia}header-show.png" alt="Dierenzoeker" class="logo" onClick="setactivepage(0);forceScrollTop=true;loadpage();">
				<a href="#" onClick="loadpage()" data-transition="slide" data-direction="reverse" data-role="none">
					<img src="{$projectUrls.systemMedia}back.png" class="info-button" style="position:absolute;left:-10px;top:2px;" alt="">
				</a>
			</div>

		</div>

		<div id="secondary-content" class="soortpagina">
		</div>

	</div>

</div>


<script>
$(document).ready(function()
{

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
		generalSpeciesInfoUrl: '{$settings->species_info_url}'
	});

	setScores($.parseJSON('{$session_scores}'));
	setStates($.parseJSON('{$session_states}'));
	setStateCount($.parseJSON('{$session_statecount}'));
	setCharacters($.parseJSON('{$session_characters}'));
	setDataSet($.parseJSON('{$full_dataset|@addslashes}'));
			
	matrixInit();


});



var resultsHtmlTpl = '<ul>%RESULTS%</ul>';
var noResultHtmlTpl='<div style="margin-top:10px">%MESSAGE%</div>';
var resultsLineEndHtmlTpl = '';
var brHtmlTpl = '<br />';

var photoLabelHtmlTpl = '';
var photoLabelGenderHtmlTpl = '';
var photoLabelPhotographerHtmlTpl = '';
var imageHtmlTpl = '<img src="%THUMB-URL%" alt="">';
var genderHtmlTpl = '';
var matrixLinkHtmlTpl = '';
var remoteLinkClickHtmlTpl = '';
var statesClickHtmlTpl = '';
var relatedClickHtmlTpl = '';
var statesHtmlTpl = '';
var statesJoinHtmlTpl = '';
var speciesStateItemHtmlTpl = '';


var resultHtmlTpl = '\
<li class="result0"> \
<a style="" onclick="drnzkr_toon_dier( { id:%ID% } );return false;" href="#"> \
<table><tbody><tr><td>%IMAGE-HTML% \
</td><td style="width:100%">%COMMON-NAME%</td></tr></tbody></table></a> \
</li> \
';


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

var iconInfoHtmlTpl='<img class="result-icon-image icon-info" src="%IMG-URL%">';
var iconUrlHtmlTpl = iconInfoHtmlTpl.replace(' icon-info','');
var iconSimilarTpl = iconInfoHtmlTpl.replace(' icon-info',' icon-similar');

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


var drzkr_mainMenuItemHtmlTemplate = '\
<li class="facetgroup-btn" title="%HINT%"> \
	<div class="facet-btn ui-block-d"> \
		<a data-facetgrouppageid="facetgrouppage%INDEX%" href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick="" characters="{$smarty.capture.chars|@trim}"> \
			<div class="grid-iconbox" > \
				<img src="%ICON%" class="grid-icon" alt="" /> \
			</div> \
			<div class="grid-labelbox ">%LABEL%</div> \
		</a> \
	</div> \
</li> \
';

var drzkr_selectedFacetHtmlTemplate = '<li><div class="ui-block-a"><a class="chosen-facet" onclick="clearStateValue(\'%STATE-VAL%\');return false;" href="#"><div class="grid-iconbox"><div class="grid-labelbox" style="color:white;font-style:italic;margin-top:-15px;padding-bottom:5px;">%CHARACTER-LABEL%</div><img class="grid-icon" src="%ICON%" style="top:25px;" alt=""><img src="%IMG-ROOT-SKIN%button-close-shadow-overlay.png" style="position:relative;top:-5px;left:0px;margin-left:-73px;" alt=""></div><div class="grid-labelbox" style="margin-top:-5px;">%STATE-LABEL%</div></a></div></li> \
';
























{literal}
var templates = {
{/literal}
	character : '<h4 class="tagline left-tagline ie-rounded keuze-tagline ui-collapsible-heading"><span aria-hidden="true" class="ui-btn-inner ui-corner-top ui-corner-bottom"><span class="ui-btn-text">%description%</span><span class="ui-icon ui-icon-shadow ui-icon-minus"></span></span></h4><div class="ui-collapsible-content-wrapper"><div aria-hidden="false" class="ui-collapsible-content"><div class="ui-grid-c">%states%</div></div></div>',
	state : '<div class="facet-btn ui-block-%letter%"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="%onclick%"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$projectUrls.projectMedia}%image%" class="grid-icon" alt=""></div><div class="grid-labelbox">%label%</div></span></span></a></div>',
	stateselected : '<div class="facet-btn ui-block-%letter% ui-selected"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="%onclick%"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$projectUrls.projectMedia}selected-background.png" class="selected-icon-overlay-border" alt=""><img src="{$projectUrls.projectMedia}%image%" class="grid-icon" alt=""><img src="{$projectUrls.projectMedia}selected-badge.png" class="selected-icon-overlay-check" alt=""></div><div class="grid-labelbox selected">%label%</div></span></span></a></div>',
	statedisabled : '<div class="facet-btn ui-block-%letter% ui-selected"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled ui-btn ui-btn-up-c" onclick="%onclick%"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$projectUrls.projectMedia}%image%" class="grid-icon" alt=""></div><div class="grid-labelbox ">%label%</div></span></span></a></div>',
	resultcontent : '<div class="ui-btn-text"><a class="resultlink ui-link-inherit" onclick="%onclick%"><img src="%image%" class="result ui-li-thumb ui-corner-tl" alt="">%label%</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span>',
	resultfirst : '<li data-theme="c" class="result%n% ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-corner-top ui-btn-up-c"><div aria-hidden="true" class="ui-btn-inner ui-li ui-corner-top">%content%</div></li>',
	resultrest : '<li data-theme="c" class="result%n% ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb"><div aria-hidden="true" class="ui-btn-inner ui-li">%content%</div></li>',
	resultlast : '<li data-theme="c" class="result%n% ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-corner-bottom"><div aria-hidden="true" class="ui-btn-inner ui-li">%content%</div></li>',
	result : {literal}{{/literal}
		tpl : '<li data-theme="c" content-type="result" class="result%n% %class%" style="%style%"><div aria-hidden="true" class="ui-btn-inner ui-li ui-corner-top">%content%</div></li>',
		class_0 : 'ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-corner-top',
		class_1 : 'ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb',
		class_n : 'ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-corner-bottom'
	{literal}}{/literal},
	selectedstate : '<div class="ui-block-%letter%"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="%onclick%" facetlabel="%label%"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><div class="grid-labelbox" style="color:white;padding-top:5px;font-style:italic">%charlabel%</div><img src="{$projectUrls.projectMedia}%image%" class="grid-icon" style="top:25px;" alt=""><img src="{$projectUrls.projectMedia}button-close-shadow-overlay.png" style="position:relative;top:0px;left:-5px;" alt=""></div><div class="grid-labelbox" style="padding-top:23px;">%label%</div></span></span></a></div>',
	speciesdetail : '<div role="main" data-role="content" class="soortpagina ui-content" id="species-default"><div class="soortpagina-inner"><h2>%title%</h2><h3>%subtitle%</h3>%image% %text% %extra_images% <p id="imageCredits" style="font-style:italic;color:#6d6d6d;"><span id="imageCreditsNames"></span></p></div></div>%group% %similar%</div>',
	speciesdetailimage : '<div class="illustratie" style="margin-left:auto;margin-right:auto;"><img src="%image%" alt=""></div>',
	imagecreditlabel : 'Beeldmateriaal van:<br />',
	extraimages: '<div class="fotos">%images%</div>',
	extraimage: {literal}{{/literal}
		tpl : '<img style="%style%" src="%image%" alt="">',
		style_0 : 'padding-right:20px;padding-bottom:20px;',
		style_n : 'padding-bottom:20px;',
	{literal}}{/literal},
	//extraimagescredits: '<p style="font-style:italic;color:#6d6d6d;"><span>Beeldmateriaal van:<br />%credits%</p>',
	speciesgroup : '<div class="soortpagina-list" style="margin-bottom:-18px;"><h4 style="padding-top:10px;">Hoort bij de diergroep:</h4><ul data-role="listview" data-inset="true" class="resultlist ui-listview ui-listview-inset ui-corner-all ui-shadow"><li data-theme="c" class="similar-species-list-item ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-corner-top ui-corner-bottom ui-btn-up-c"><div aria-hidden="true" class="ui-btn-inner ui-li ui-corner-top"><div class="ui-btn-text"><a href="#" onclick="%onclick%" class="result-link ui-link-inherit">%label%</a></div><span class="pijltje"></span></div></li></ul></div>',
	speciessimilar : '<div class="soortpagina-list"><h4 style="padding-top:10px;">%title%</h4><ul data-role="listview" data-inset="true" class="resultlist ui-listview ui-listview-inset ui-corner-all ui-shadow">%specieslist%</ul><a id="go-to-top-link" data-theme="c" href="#" onclick="scrolltop();" data-role="button" class="simplebutton ie-rounded to-top ui-btn ui-btn-up-c ui-btn-corner-all ui-shadow" style="background-color:#5a5c5f;color:white;border:none;"><span aria-hidden="true" class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">Naar boven</span></span></a><br /><br /><br /></div>',
	speciessimilaritem : {literal}{{/literal}
		tpl : '<li data-theme="c" class="%class%"><div aria-hidden="true" class="ui-btn-inner ui-li ui-corner-top"><div class="ui-btn-text"><a href="#" onclick="%onclick%" class="resultlink ui-link-inherit"><img src="%image%" class="result ui-li-thumb ui-corner-tl" alt="">%label%</a></div><span class="pijltje"></span></div></li>',
		class_0 : 'similar-species-list-item ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-corner-top',
		class_1 : 'similar-species-list-item ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-btn-up-c',
		class_n : 'similar-species-list-item ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-corner-bottom ui-btn-up-c',
	{literal}}
}
{/literal}
</script>


{* include file="_counters.html" *}

</body>
</html>


