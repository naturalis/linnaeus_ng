{include file="../shared/header.tpl"}
<body class="ui-mobile-viewport">
<div tabindex="0" data-url="main" id="main" class="main-page ui-page ui-body-c ui-page-active" data-role="page" style="background-image: none; min-height: 920px;">

    <div id="speciescontent" class="content keuze-content ui-content" style="display:block">
	
		<div>
			<div data-role="header" class="header">	    
				<img src="img/header-show.png" alt="Dierenzoeker" class="logo">
				<a href="javascript:toggleScreens();" data-transition="slide" data-direction="reverse" data-role="none">
					<img src="img/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="">
				</a>
			</div>
	
		</div>

		<div id="species-detail-content">
		</div>
        
	</div>


    <div id="charactercontent" class="content keuze-content ui-content" style="display:none">
	
		<div>
		
			<div data-role="header" class="header">	    
				<img src="{$session.app.project.urls.systemMedia}header_speech.png" alt="Dierenzoeker" class="logo">
				<a href="javascript:toggleScreens();" data-transition="slide" data-direction="reverse" data-role="none">
					<img src="{$session.app.project.urls.systemMedia}back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="">
				</a>
			</div>
	
		</div>

    	<div class="collapsible-set-wrapper">
						
			<div class="ui-collapsible" data-icon-pos="right" style="border-bottom:3px solid #323232;" id="expanded-characters">
			
			</div>

        </div>
        
	</div>



    <div id="selectioncontent" role="main" class="ui-content" data-role="content" style="display:block">

		<div id="x-menu-header">
		
			<div role="banner" class="ui-header ui-bar-a" data-role="header" id="mainheader">
				<img src="{$session.app.project.urls.systemMedia}header_speech.png" alt="Dierenzoeker" class="logo">    	
				<a data-theme="a" id="infobutton" href="javascript:alert('http://www.dierenzoeker.nl/index.php/identify/page/colofon');" data-role="button" data-inline="true" data-transition="none" class="ui-btn-left ui-btn ui-btn-inline ui-btn-corner-all ui-shadow ui-btn-up-a" style="top:50%;margin-top:-17px;height:24px;">
				<span aria-hidden="true" class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">
					<img src="{$session.app.project.urls.systemMedia}info.png" alt="">
				</span></span></a>
			</div>
			
		</div>

		<div id="x-menu-characters">

			<div class="facetheadercontainer">	    	
				<h4 class="tagline left-tagline ie-rounded" style="position: absolute;top: 25px;left:20px;padding-top:5px;">Wat weet je over het dier?</h4>
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
					   <img src="{$session.app.project.urls.systemMedia}delete-all-choices-btn.png" onClick="appController.reinitialise(main);" alt="">            
					</span>
					<div id="selectiongrid" class="ui-grid-c"> 
					<!-- selected states -->
					</div>        
				</div>
			</div>
		
		</div>

	</div>


    <div id="resultcontent" role="main" class="ui-content" data-role="content" style="display:block">

		<div id="results" class="block-container middle-block-container" style="margin-bottom: 60px;">
		
			<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
			<span class="ie-rounded num-of-results"></span><span class="num-of-results-label"></span>
			</h4>
			
			<ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist ui-listview ui-listview-inset ui-corner-all ui-shadow">
			<!-- results -->
			</ul>
		
			<span id="button-container">
				<a data-theme="c" href="http://www.dierenzoeker.nl/index.php/identify/page/faq" data-role="button" id="askNaturalis" class="simplebutton ie-rounded ui-btn ui-btn-up-c ui-btn-corner-all ui-shadow" style="background-color:#5a5c5f;color:white;border:none;">
					<span aria-hidden="true" class="ui-btn-inner ui-btn-corner-all">
						<span class="ui-btn-text">Vragen?</span>
					</span>
				</a>
			</span>
		
		</div>
			
	</div>

</div>

<a href="#results" id="bottom-bar" class="menu-button-img">
	<span style="color:orange" class="num-of-results"></span><span class="num-of-results-label"></span>
</a>

<div style="top: 460px;" class="ui-loader ui-body-a ui-corner-all">
	<span class="ui-icon ui-icon-loading spin"></span>
	<h1>loading</h1>
</div>


<script>
{literal}
$(document).ready(function() {
	init({{/literal}matrix:{$matrix.id},language:{$currentLanguageId},imgroot:'{$session.app.project.urls.projectMedia}'{literal}});
	main();
});
{/literal}


var templates = {literal}{{/literal}
	character : '<h4 class="tagline left-tagline ie-rounded keuze-tagline ui-collapsible-heading"><span aria-hidden="true" class="ui-btn-inner ui-corner-top ui-corner-bottom"><span class="ui-btn-text">%description%</span><span class="ui-icon ui-icon-shadow ui-icon-minus"></span></span></h4><div class="ui-collapsible-content-wrapper"><div aria-hidden="false" class="ui-collapsible-content"><div class="ui-grid-c">%states%</div></div></div>',
	state : '<div class="facet-btn ui-block-%letter%"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="%onclick%"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}%image%" class="grid-icon" alt=""></div><div class="grid-labelbox">%label%</div></span></span></a></div>',
	stateselected : '<div class="facet-btn ui-block-%letter% ui-selected"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="%onclick%"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.systemMedia}selected-background.png" class="selected-icon-overlay-border" alt=""><img src="{$session.app.project.urls.projectMedia}%image%" class="grid-icon" alt=""><img src="{$session.app.project.urls.systemMedia}selected-badge.png" class="selected-icon-overlay-check" alt=""></div><div class="grid-labelbox selected">%label%</div></span></span></a></div>',
	statedisabled : '<div class="facet-btn ui-block-%letter% ui-selected"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled ui-btn ui-btn-up-c" onclick="%onclick%"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}%image%" class="grid-icon" alt=""></div><div class="grid-labelbox ">%label%</div></span></span></a></div>',
	resultcontent : '<div class="ui-btn-text"><a class="resultlink ui-link-inherit" onclick="alert(%id%)"><img src="%image%" class="result ui-li-thumb ui-corner-tl" alt="">%label%</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span>',
	resultfirst : '<li data-theme="c" class="result%n% ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-corner-top ui-btn-up-c"><div aria-hidden="true" class="ui-btn-inner ui-li ui-corner-top">%content%</div></li>',
	resultrest : '<li data-theme="c" class="result%n% ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb"><div aria-hidden="true" class="ui-btn-inner ui-li">%content%</div></li>',
	resultlast : '<li data-theme="c" class="result%n% ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-corner-bottom"><div aria-hidden="true" class="ui-btn-inner ui-li">%content%</div></li>',
	selectedstate : '<div class="ui-block-%letter%"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="%onclick%" facetlabel="%label%"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><div class="grid-labelbox" style="color:white;padding-top:5px;font-style:italic">%charlabel%</div><img src="{$session.app.project.urls.projectMedia}%image%" class="grid-icon" style="top:25px;" alt=""><img src="{$session.app.project.urls.systemMedia}button-close-shadow-overlay.png" style="position:relative;top:0px;left:-5px;" alt=""></div><div class="grid-labelbox" style="padding-top:23px;">%label%</div></span></span></a></div>',
	speciesdetail : '<div role="main" data-role="content" class="soortpagina ui-content" id="species-default"><div class="soortpagina-inner"><h2>%title%</h2><h3>%subtitle%</h3><div class="illustratie"><img src="img/%image%" alt=""></div>%text%</div><p style="font-style:italic;color:#6d6d6d;">%image_copyright%</p></div>%group% %similar%</div>',
	speciesgroup : '<div class="soortpagina-list" style="margin-bottom:-18px;"><h4 style="padding-top:10px;">Hoort bij de diergroep:</h4><ul data-role="listview" data-inset="true" class="resultlist ui-listview ui-listview-inset ui-corner-all ui-shadow"><li data-theme="c" class="similar-species-list-item ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-corner-top ui-corner-bottom ui-btn-up-c"><div aria-hidden="true" class="ui-btn-inner ui-li ui-corner-top"><div class="ui-btn-text"><a href="#" onclick="%onclick%" class="result-link ui-link-inherit">%label%</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span></div></li></ul></div>',
	speciessimilar : '<div class="soortpagina-list"><h4 style="padding-top:10px;">Lijkt op:</h4><ul data-role="listview" data-inset="true" class="resultlist ui-listview ui-listview-inset ui-corner-all ui-shadow">%specieslist%</ul><a data-theme="c" href="#top" data-role="button" class="simplebutton ie-rounded to-top ui-btn ui-btn-up-c ui-btn-corner-all ui-shadow" style="background-color:#5a5c5f;color:white;border:none;"><span aria-hidden="true" class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">Naar boven</span></span></a></div>',
	speciessimilaritem : '<li data-theme="c" class="similar-species-list-item ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-corner-top"><div aria-hidden="true" class="ui-btn-inner ui-li ui-corner-top"><div class="ui-btn-text"><a href="#" onclick="%onclick%" class="resultlink ui-link-inherit"><img src="img/%image%" class="result ui-li-thumb ui-corner-tl" alt="">%label%</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span></div></li>'
{literal}}{/literal}
</script>

</body>
</html>