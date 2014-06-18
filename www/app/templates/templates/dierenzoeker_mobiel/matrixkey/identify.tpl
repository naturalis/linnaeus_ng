{include file="../shared/header.tpl"}
<title>{$projectUrls.systemMedia}</title>
<body class="ui-mobile-viewport">
<div tabindex="0" data-url="main" id="main" class="main-page ui-page ui-body-c ui-page-active" data-role="page" style="background-image: none; min-height: 920px;">

    <div id="speciescontent" class="content keuze-content ui-content" style="display:none">

		<div>
			<div data-role="header" class="header">
				<img src="{$projectUrls.systemMedia}header-show.png" alt="Dierenzoeker" class="logo" onClick="setactivepage(0);forceScrollTop=true;main()">
				<a href="#" onClick="detailback()" data-transition="slide" data-direction="reverse" data-role="none">
					<img src="{$projectUrls.systemMedia}back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="">
				</a>
			</div>

		</div>

		<div id="species-detail-content" class="soortpagina"></div>

	</div>

    <div id="charactercontent" class="content keuze-content ui-content" style="display:none">

		<div id="x-menu-header-back">

			<div data-role="header" class="header">
				<img src="{$projectUrls.systemMedia}header_speech.png" alt="Dierenzoeker" class="logo" onClick="setactivepage(0);forceScrollTop=true;main()">
				<a href="#" onClick="appController.states(selection);" data-transition="slide" data-direction="reverse" data-role="none">
					<img src="{$projectUrls.systemMedia}back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="">
				</a>
			</div>

		</div>

    	<div class="collapsible-set-wrapper">

			<div class="ui-collapsible" data-icon-pos="right" style="border-bottom:3px solid #323232;" id="expanded-characters">
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
				<h4 class="tagline left-tagline ie-rounded" style="position: absolute;top: 26px;left:20px;padding-top:5px;">Wat weet je over het dier?</h4>
			</div>

			<div id="filter" class="filterset">
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

    <div id="resultcontent" style="display:none">

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

{literal}
<script>
$(document).ready(function() {
	//use_emerging_characters={$use_emerging_characters};
	init({{/literal}project:{$projectId},matrix:{$matrix.id},language:{$currentLanguageId}{literal}});
{/literal}
	resultBatchSize={$matrix_items_per_page};
{literal}
});
{/literal}
{literal}
var templates = {
{/literal}
	character : '<h4 class="tagline left-tagline ie-rounded keuze-tagline ui-collapsible-heading"><span aria-hidden="true" class="ui-btn-inner ui-corner-top ui-corner-bottom"><span class="ui-btn-text">%description%</span><span class="ui-icon ui-icon-shadow ui-icon-minus"></span></span></h4><div class="ui-collapsible-content-wrapper"><div aria-hidden="false" class="ui-collapsible-content"><div class="ui-grid-c">%states%</div></div></div>',
	state : '<div class="facet-btn ui-block-%letter%"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="%onclick%"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$projectUrls.projectMedia}%image%" class="grid-icon" alt=""></div><div class="grid-labelbox">%label%</div></span></span></a></div>',
	stateselected : '<div class="facet-btn ui-block-%letter% ui-selected"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="%onclick%"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$projectUrls.projectMedia}selected-background.png" class="selected-icon-overlay-border" alt=""><img src="{$projectUrls.projectMedia}%image%" class="grid-icon" alt=""><img src="{$projectUrls.projectMedia}selected-badge.png" class="selected-icon-overlay-check" alt=""></div><div class="grid-labelbox selected">%label%</div></span></span></a></div>',
	statedisabled : '<div class="facet-btn ui-block-%letter% ui-selected"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled ui-btn ui-btn-up-c" onclick="%onclick%"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$projectUrls.projectMedia}%image%" class="grid-icon" alt=""></div><div class="grid-labelbox ">%label%</div></span></span></a></div>',
	resultcontent : '<div class="ui-btn-text"><a class="resultlink ui-link-inherit" onclick="%onclick%"><img src="%image%" class="result ui-li-thumb ui-corner-tl" alt="">%label%</a></div><span class="pijltje"></span>',
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
	speciesdetail : '<div role="main" data-role="content" class="soortpagina ui-content" id="species-default"><div class="soortpagina-inner"><h2>%title%</h2><h3>%subtitle%</h3>%image% %text% %extra_images% <p id="imageCredits" style="font-style:italic;color:#6d6d6d;"><span id="imageCreditsNames"></span>.</p></div></div>%group% %similar%</div>',
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
<script>
(function(i,s,o,g,r,a,m) { i['GoogleAnalyticsObject']=r;i[r]=i[r]||function() { (i[r].q=i[r].q||[]).push(arguments)}},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
} )(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-27823424-2', 'dierenzoeker.nl');
ga('send', 'pageview');

</script>
<!-- Begin Sitestat ECMA -->
<script type="text/ecmascript">
// <![CDATA[
function sitestat(u) { var d=document,l=location,ns_pixelUrl=u+"&ns__t="+(new Date().getTime());u=ns_pixelUrl+"&ns_ti="+encodeURIComponent(d.title)+"&ns_jspageurl="+encodeURIComponent(l.href)+"&ns_referrer="+encodeURIComponent(d.referrer);(d.images)?new Image().src=u:d.write('<img src="'+u+'" height=1 width=1 alt="*">');};
sitestat("http://nl.sitestat.com/klo/ntr-mobiel/s?ntr.hetklokhuis.dierenzoeker.home&category=hetklokhuis&ns_webdir=hetklokhuis&ns_channel=nieuws_informatie&po_source=fixed&po_sitetype=plus&po_merk=video.zz.zappelin&ntr_genre=jeugd&pom_context=web&pom_appver=1.00&pom_appname=dierenzoeker&ns_t=1401874745");
// ]]>
</script>
<noscript>
<img src="http://nl.sitestat.com/klo/ntr-mobiel/s?ntr.hetklokhuis.dierenzoeker.home&category=hetklokhuis&ns_webdir=hetklokhuis&ns_channel=nieuws_informatie&po_source=fixed&po_sitetype=plus&po_merk=video.zz.zappelin&ntr_genre=jeugd&pom_context=web&pom_appver=1.00&pom_appname=dierenzoeker&ns_t=1401874745" height=1 width=1 alt="*" />
</noscript>
<!-- End Sitestat ECMA -->


</body>
</html>


