{include file="../shared/header.tpl"}

<body> 
        <div class="main-wrapper">
            
            <div class="header">
                <div class="header-inner">
                
		            {include file="../shared/top-banners.tpl"}

                    <div class="clearer"></div>
                    
                    <ul class="wat-weet-je-list">      
                        <li class="wat-weet-je-arrow no-text">Wat weet je van het dier?</li>
                        
                        {foreach from=$facetmenu item=group key=groupkey}
                        
                            {assign var=foo value="|"|explode:$group.label} 
                                               
                            <li class="facetgroup-btn" title="{$foo[1]}">
                                <div class="facet-btn ui-block-d">
                                    <a data-facetgrouppageid="facetgrouppage{$groupkey}" href="#" data-role="button" data-corners="false" data-shadow="false">
                                        <div class="grid-iconbox" >
                                            <img src="{$projectUrls.projectMedia}__menu{$foo[0]|ucwords|regex_replace:"/\W/":""}.png" class="grid-icon" alt="" />
                                        </div>
                                        <div class="grid-labelbox ">{$foo[0]}</div>
                                    </a>
                                </div>
                            </li>
                                    
                        {/foreach}

                    </ul>                
                </div>
            </div>

			<div class="sub-header-wrapper" style="display:none" >
                <div class="sub-header">
                    <div class="sub-header-inner">
                        <a href="#" class="no-text alles-wissen" onClick="resetMatrix();return false;">Alles wissen</a>
                        <div class="dit-weet-je-arrow no-text">
                                Dit weet je van het dier:
                        </div>
                        <ul class="dit-weet-je-list" id="gemaakte-keuzes" >
                        </ul>
                        <div class="clearer"></div>
                    </div>            
                </div>            
            </div>
            

            <div class="content">
                
                <div class="result-list-wrapper">
                    <div class="result-list-header">
                        <ul>
                            <li id="prev-button-container-top" style="visibility:hidden">
								<a href="#" class="first-btn" onClick="drnzkr_navigeren('eerste');"></a>
								<a href="#" class="prev-btn" onClick="drnzkr_navigeren('vorige');"></a>
							</li>
                            <li class="num-found" style="margin-top:-2px;"><span class="num" id="result-count-container">0</span> dieren gevonden</li>
                            <li id="next-button-container-top" style="position:relative;left:84px;visibility:visible;">
								<a href="#" class="next-btn last-child" onClick="drnzkr_navigeren('volgende');"></a>
								<a href="#" class="last-btn last-child" onClick="drnzkr_navigeren('laatste');"></a>
							</li>
                        </ul>
                        <div class="clearer"></div>
                    </div>
                    <div class="result-list-body" id='results-container' prev-id="result-list-container">
                         <div class="result-list-body-loading-container">
                             <div class="loader-img"></div>                             
                         </div>
                    </div>
                    <div class="result-list-footer">
                         <ul>
                            <li id="prev-button-container-bottom" style="visibility:hidden">
								<a href="#" class="first-btn" onClick="drnzkr_navigeren('eerste');" style="margin-left:5px"></a>
								<a href="#" class="prev-btn" onClick="drnzkr_navigeren('vorige');" style="margin-left:1px"></a>
							</li>
                            <li style="width:184px;">&nbsp;</li>
                            <li id="next-button-container-bottom" style="position:relative;left:84px;visibility:visible;">
								<a href="#" class="next-btn last-child" onClick="drnzkr_navigeren('volgende');"></a>
								<a href="#" class="last-btn last-child" onClick="drnzkr_navigeren('laatste');"></a>
							</li>
                        </ul>
                    </div>
                </div>
                
                <div class="dier-wrapper" id="dier-content-wrapper" style="visibility:hidden">
                    <div id="dier-header" class="dier-header">
                        Dier
                    </div><div class="dier-content" id="dier-content">
                       <div style='height:650px;'>
                       </div>                        
                    </div>
                </div>                
                
                <div class="clearer"></div>
                                          
            </div>
            
            {include file="../shared/bottom-banners.tpl"}

        </div>

		<div class="facetgrouppage-wrapper"> 
               
        {foreach from=$facetmenu item=group key=groupkey}

            <div class="facetgrouppage" id="facetgrouppage{$groupkey}">
				<img class="facetpage-puntje" alt="" src="{$session.app.system.urls.systemMedia}facet-puntje.png">
				<a class="no-text facetgrouppage-close-btn" href="#">Sluiten</a>

                {if $group.chars}
                {foreach from=$group.chars item=character key=characterkey}

				<div class="facetgrouppage-inner">
					<h4 class="tagline left-tagline ie-rounded keuze-tagline">{$character.info}</h4>
					<div class="facetgrouppage-icons">
						<div class="helper-div">
							<div class="ui-grid-c">

								{foreach from=$states item=state key=statekey}
                                {if $state.characteristic_id==$character.id && $state.file_name}

								<div class="facet-btn ui-block-{if $statekey+1%4==0}d{elseif $statekey+1%3==0}c{elseif $statekey+1%2==0}b{else}a{/if}">
									<a 
                                    	href="#" 
                                    	onClick="
                                        	setStateValue('{$character.prefix}:{$character.id}:{$state.id}');
	                                        $('.facetgrouppage-close-btn').trigger('click');
                                            return false;
                                            " 
                                        class="" 
                                        id="state-{$state.id}">
                                        <div class="grid-iconbox">
                                            <img alt="" class="grid-icon" src="{$projectUrls.projectMedia}{$state.file_name}">
                                        </div>
                                        <div class="grid-labelbox ">
                                            {$state.label}
                                        </div>
									</a>
								</div>

								{/if}
								{/foreach}
	
							</div>
						</div>
						<div class="clearer"></div>
						<div class="facetgrouppage-bottom-shade"></div>

					</div>

				</div>
                
                {/foreach}

				{else}

				{assign var=foo value="|"|explode:$group.label} 

				<div class="facetgrouppage-inner">
					<h4 class="tagline left-tagline ie-rounded keuze-tagline">{$foo[1]}</h4>
					<div class="facetgrouppage-icons">
						<div class="helper-div">
							<div class="ui-grid-c">
                            {foreach from=$states item=state key=statekey}
                            {if $state.characteristic_id==$group.id && $state.file_name}
                            <div class="facet-btn ui-block-{if $statekey+1%4==0}d{elseif $statekey+1%3==0}c{elseif $statekey+1%2==0}b{else}a{/if}">
                                <a
                                	href="#" 
                                    onClick="
                                    	setStateValue('{$group.prefix}:{$group.id}:{$state.id}');
                                        $('.facetgrouppage-close-btn').trigger('click');
                                        return false;
                                        " 
                                    class="" 
                                    id="state-{$state.id}">
                                <div class="grid-iconbox">
                                    <img alt="" class="grid-icon" src="{$projectUrls.projectMedia}{$state.file_name}">
                                </div>
                                <div class="grid-labelbox ">
                                    {$state.label}
                                </div>
                                </a>
                            </div>
                            {/if}
                            {/foreach}

						</div>
						</div>
						<div class="clearer"></div>
						<div class="facetgrouppage-bottom-shade"></div>

					</div>  
				</div>
                
             	{/if}
                
			</div>    
		{/foreach}

		</div>
       




<script type="text/JavaScript">
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
		generalSpeciesInfoUrl: '{$settings->species_info_url}',
		initialSortColumn: '{$settings->initial_sort_column}',
		alwaysSortByInitial: {$settings->always_sort_by_initial},
	});

	setScores($.parseJSON('{$session_scores}'));
	setStates($.parseJSON('{$session_states}'));
	setStateCount($.parseJSON('{$session_statecount}'));
	setCharacters($.parseJSON('{$session_characters}'));
	setDataSet($.parseJSON('{$full_dataset|@addslashes}'));
			
	matrixInit();

	{if $requestData.dier}
	drnzkr_startDier='{$requestData.dier|@escape}';
	{/if}

	$('[data-facetgrouppageid^="facetgrouppage"]').click(function(e)
	{
		e.preventDefault();
		drnzkr_update_states();
		var currentstate=$("#"+$(this).attr('data-facetgrouppageid')).css("display");
		// Close all facet group pages (cleanup):
		$(".facetgrouppage").css("display", "none");
		// Show facet group page:
		$("#"+$(this).attr('data-facetgrouppageid')).css("display", currentstate=="none"?"block":"none");
		return false;           
	});
	
	$('.facetgrouppage-close-btn').click(function(e){
		e.preventDefault();
		// Hide all facet group pages:
		$(".facetgrouppage").css("display", "none");
		return false;           
	});

	$.backstretch("../../media/system/skins/dierenzoeker/background.jpg");

});
</script>

<script>
(function(i,s,o,g,r,a,m) { i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-27823424-1', 'dierenzoeker.nl');
ga('send', 'pageview');

</script>

{literal}

<!-- Begin CMC v.1.0.1 -->
<script type="text/javascript">
// <![CDATA[
function sitestat(u) { var d=document,l=d.location;ns_pixelUrl=u+"&ns__t="+(new Date().getTime());u=ns_pixelUrl+"&ns_c="+((d.characterSet)?d.characterSet:d.defaultCharset)+"&ns_ti="+escape(d.title)+"&ns_jspageurl="+escape(l&&l.href?l.href:d.URL)+"&ns_referrer="+escape(d.referrer);(d.images)?new Image().src=u:d.write('<'+'p><img src="'+u+'" height="1" width="1" alt="*"/><'+'/p>');};
sitestat("//nl.sitestat.com/klo/ntr/s?ntr.hetklokhuis.dierenzoeker&category=hetklokhuis&ns_webdir=hetklokhuis&ns_channel=nieuws_informatie&po_source=fixed&po_sitetype=plus&po_merk=video.zz.zappelin&ntr_genre=jeugd");
// ]]>
</script>
<noscript><p><img src="//nl.sitestat.com/klo/ntr/s?ntr.hetklokhuis.dierenzoeker&category=hetklokhuis&ns_webdir=hetklokhuis&ns_channel=nieuws_informatie&po_source=fixed&po_sitetype=plus&po_merk=video.zz.zappelin&ntr_genre=jeugd" height="1" width="1" alt="*"/></p></noscript>
<!-- End CMC -->



<!-- Begin comScore Inline Tag 1.1302.13 --> 
<script type="text/javascript"> 
// <![CDATA[
function udm_(e){var t="comScore=",n=document,r=n.cookie,i="",s="indexOf",o="substring",u="length",a=2048,f,l="&ns_",c="&",h,p,d,v,m=window,g=m.encodeURIComponent||escape;if(r[s](t)+1)for(d=0,p=r.split(";"),v=p[u];d<v;d++)h=p[d][s](t),h+1&&(i=c+unescape(p[d][o](h+t[u])));e+=l+"_t="+ +(new Date)+l+"c="+(n.characterSet||n.defaultCharset||"")+"&c8="+g(n.title)+i+"&c7="+g(n.URL)+"&c9="+g(n.referrer),e[u]>a&&e[s](c)>0&&(f=e[o](0,a-8).lastIndexOf(c),e=(e[o](0,f)+l+"cut="+g(e[o](f+1)))[o](0,a)),n.images?(h=new Image,m.ns_p||(ns_p=h),h.src=e):n.write("<","p","><",'img src="',e,'" height="1" width="1" alt="*"',"><","/p",">")};udm_('http'+(document.location.href.charAt(4)=='s'?'s://sb':'://b')+'.scorecardresearch.com/b?c1=2&c2=17827132&ns_site=po-totaal&name=hetklokhuis.dierenzoeker.home&potag1=hetklokhuis&potag2=dierenzoeker&potag3=ntr&potag4=ntr&potag5=programma&potag6=video&potag7=npozapp&potag8=site&potag9=site&ntr_genre=jeugd');
// ]]>
</script>
<noscript><p><img src="http://b.scorecardresearch.com/p?c1=2&amp;c2=17827132&amp;ns_site=po-totaal&amp;name=hetklokhuis.dierenzoeker.home&amp;potag1=hetklokhuis&amp;potag2=dierenzoeker&amp;potag3=ntr&amp;potag4=ntr&amp;potag5=programma&amp;potag6=video&amp;potag7=npozapp&amp;potag8=site&amp;potag9=site&amp;ntr_genre=jeugd" height="1" width="1" alt="*"></p></noscript> 
<script type="text/javascript" language="JavaScript1.3" src="http://b.scorecardresearch.com/c2/17827132/cs.js"></script>
<!-- End comScore Inline Tag -->

{/literal}




<script type="text/JavaScript">

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


</script>


</body>