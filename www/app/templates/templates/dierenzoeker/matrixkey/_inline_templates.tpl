<div class="inline-templates" id="resultsHtmlTpl">
<!--
<ul>%RESULTS%</ul>
-->
</div>

<div class="inline-templates" id="resultHtmlTpl">
<!--
<li class="result0">
<a style="" onclick="drnzkr_toon_dier( { id:%ID%, name:'%COMMON-NAME%' } );return false;" href="#">
<table><tbody><tr><td>%IMAGE-HTML%
</td><td style="width:100%">%COMMON-NAME%</td></tr></tbody></table></a>
</li>
-->
</div>

<div class="inline-templates" id="noResultHtmlTpl">
<!--
<div style="margin-top:10px">%MESSAGE%</div>
-->
</div>

<div class="inline-templates" id="brHtmlTpl">
<!--
<br />
-->
</div>

<div class="inline-templates" id="imageHtmlTpl">
<!--
<img src="%THUMB-URL%" alt="">
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
%START-NUMBER%%NUMBER-SHOWING%&nbsp;%FROM-LABEL%&nbsp;%NUMBER-TOTAL%
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

<div class="inline-templates" id="menuOuterHtmlTpl ">
<!--
<ul>%MENU%</ul>
-->
</div>

<div class="inline-templates" id="menuGroupHtmlTpl">
<!--
<li id="character-item-%ID%" class="closed"><a href="#" onclick="toggleGroup(%ID%);return false;">%LABEL%</a></li>
<ul id="character-group-%ID%" class="hidden">
%CHARACTERS%
</ul>
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

<div class="inline-templates" id="menuCharHtmlTpl">
<!--
<li class="inner last">
<a class="facetLink" href="#" onclick="showStates(%ID%);return false;">%LABEL%%VALUE%</a>
%SELECTED%
</li>
-->
</div>

<div class="inline-templates" id="menuLoneCharDisabledHtmlTpl">
<!--
<li class="inner ungrouped %CLASS% disabled" title="%TITLE%" ondblclick="showStates(%ID%);">%LABEL%%VALUE%	%SELECTED% </li>
-->
</div>

<div class="inline-templates" id="menuCharDisabledHtmlTpl">
<!--
<li class="inner %CLASS% disabled" title="%TITLE%" ondblclick="showStates(%ID%);">%LABEL%%VALUE%	%SELECTED% </li>
-->
</div>

<div class="inline-templates" id="menuLoneCharEmergentDisabledHtmlTpl">
<!--
<li class="inner ungrouped %CLASS%" title="%TITLE%">
<a class="facetLink emergent_disabled" href="#" onclick="showStates(%ID%);return false;">(%LABEL%%VALUE%)</a>
%SELECTED%
</li>
-->
</div>

<div class="inline-templates" id="menuCharEmergentDisabledHtmlTpl">
<!--
<li class="inner %CLASS%" title="%TITLE%">
<a class="facetLink emergent_disabled" href="#" onclick="showStates(%ID%);return false;">(%LABEL%%VALUE%)</a>
%SELECTED%
</li>
-->
</div>

<div class="inline-templates" id="menuSelStateHtmlTpl">
<!--
<div class="facetValueHolder">
%VALUE% %LABEL% %COEFF%
<a href="#" class="removeBtn" onclick="clearStateValue('%STATE-ID%');return false;">
<img src="%IMG-URL%"></a>
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
%HEADER-TEXT%
<span id="similarSpeciesName">%SPECIES-NAME%</span> <span class="result-count">(%NUMBER-START%-%NUMBER-END%)</span><br />
<a class="clearSimilarSelection" href="#" onclick="closeSimilar();return false;">%BACK-TEXT%</a> <span id="show-all-divider"> | </span>
<a class="clearSimilarSelection" href="#" onclick="toggleAllDetails();return false;" id="showAllLabel">%SHOW-STATES-TEXT%</a>
-->
</div>

<div class="inline-templates" id="searchHeaderHtmlTpl">
<!--
%HEADER-TEXT% <span id="similarSpeciesName">%SEARCH-TERM%</span> <span class="result-count">(%NUMBER-START%-%NUMBER-END% %OF-TEXT% %NUMBER-TOTAL%)</span><br />
<a class="clearSimilarSelection" href="#" onclick="closeSearch();return false;">%BACK-TEXT%</a>
-->
</div>

<div class="inline-templates" id="drzkr_mainMenuItemHtmlTemplate">
<!--
<li class="facetgroup-btn" title="%HINT%">
<div class="facet-btn ui-block-d">
	<a data-facetgrouppageid="facetgrouppage%INDEX%" href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick="" characters="{$smarty.capture.chars|@trim}">
		<div class="grid-iconbox" >
			<img src="%ICON%" class="grid-icon" alt="" />
		</div>
		<div class="grid-labelbox ">%LABEL%</div>
	</a>
</div>
</li>
-->
</div>

<div class="inline-templates" id="drzkr_selectedFacetHtmlTemplate">
    <!--
    <li>
        <div class="ui-block-a">
            <a class="chosen-facet" onclick="clearStateValue('%STATE-VAL%');return false;" href="#">
                <div class="grid-iconbox">
                    <div class="grid-labelbox" style="color:white;font-style:italic;margin-top:-15px;padding-bottom:5px;">%CHARACTER-LABEL%</div>
                    <img class="grid-icon" src="%ICON%" style="top:25px;" alt="">
                </div>
                <div class="grid-labelbox" style="margin-top:-5px;">%STATE-LABEL%</div>
                <img src="%IMG-ROOT-SKIN%button-close-shadow-overlay.png" style="position:absolute;top:0;" alt="">
            </a>
        </div>
    </li>
    -->
</div>
