{include file="../shared/header.tpl"}
<body class="ui-mobile-viewport">
<div tabindex="0" data-url="main" id="main" class="main-page ui-page ui-body-c ui-page-active" data-role="page" style="background-image: none; min-height: 920px;">

	<!--header-->
    <div role="banner" class="ui-header ui-bar-a" data-role="header" id="mainheader">
    	<img src="{$session.app.project.urls.systemMedia}header_speech.png" alt="Dierenzoeker" class="logo">    	
    	<a data-theme="a" id="infobutton" href="javascript:alert('http://www.dierenzoeker.nl/index.php/identify/page/colofon');" data-role="button" data-inline="true" data-transition="none" class="ui-btn-left ui-btn ui-btn-inline ui-btn-corner-all ui-shadow ui-btn-up-a" style="top:50%;margin-top:-17px;height:24px;">
        <span aria-hidden="true" class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">
        	<img src="{$session.app.project.urls.systemMedia}info.png" alt="">
		</span></span></a>
    </div>
	


    <div class="content keuze-content ui-content">
	
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div class="ui-collapsible" data-icon-pos="right" style="border-bottom:3px solid #323232;"><h4 class="tagline left-tagline ie-rounded keuze-tagline ui-collapsible-heading"><span aria-hidden="true" class="ui-btn-inner ui-corner-top ui-corner-bottom"><span class="ui-btn-text"> Welke kleuren heeft het dier? <span class="ui-collapsible-heading-status"> click to collapse contents</span></span><span class="ui-icon ui-icon-shadow ui-icon-minus"></span></span></h4><div class="ui-collapsible-content-wrapper"><div aria-hidden="false" class="ui-collapsible-content">        

<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColor', '&amp;rnax_hasColor=wit+%7c+kleurWit')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurWit.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">wit</div></span></span></a></div><div class="facet-btn ui-block-b"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColor', '&amp;rnax_hasColor=zwart+%7c+kleurZwart')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurZwart.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">zwart</div></span></span></a></div><div class="facet-btn ui-block-c"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColor', '&amp;rnax_hasColor=grijs+%7c+kleurGrijs')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurGrijs.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">grijs</div></span></span></a></div><div class="facet-btn ui-block-d"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColor', '&amp;rnax_hasColor=bruin+%7c+kleurBruin')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurBruin.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">bruin</div></span></span></a></div><div class="facet-btn ui-block-a"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColor', '&amp;rnax_hasColor=beige+%7c+kleurBeige')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurBeige.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">beige</div></span></span></a></div><div class="facet-btn ui-block-b"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled ui-btn ui-btn-up-c" onclick=""><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurPaars.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">paars</div></span></span></a></div><div class="facet-btn ui-block-c"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColor', '&amp;rnax_hasColor=geel+%7c+kleurGeel')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurGeel.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">geel</div></span></span></a></div><div class="facet-btn ui-block-d"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColor', '&amp;rnax_hasColor=oranje+%7c+kleurOranje')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurOranje.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">oranje</div></span></span></a></div><div class="facet-btn ui-block-a"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColor', '&amp;rnax_hasColor=rood+%7c+kleurRood')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurRood.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">rood</div></span></span></a></div><div class="facet-btn ui-block-b"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled ui-btn ui-btn-up-c" onclick=""><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurRoze.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">roze</div></span></span></a></div><div class="facet-btn ui-block-c"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColor', '&amp;rnax_hasColor=blauw+%7c+kleurBlauw')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurBlauw.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">blauw</div></span></span></a></div><div class="facet-btn ui-block-d"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColor', '&amp;rnax_hasColor=groen+%7c+kleurGroen')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurGroen.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">groen</div></span></span></a></div><div class="facet-btn ui-block-a"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled ui-btn ui-btn-up-c" onclick=""><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurZilver.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">zilver</div></span></span></a></div><div class="facet-btn ui-block-b"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-hover-c" onclick="submitFacet('rnax_hasColor', '&amp;rnax_hasColor=goud+%7c+kleurGoud')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurGoud.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">goud</div></span></span></a></div><div class="facet-btn ui-block-c"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled ui-btn ui-btn-up-c" onclick=""><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__kleurDoorzichtig.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">doorzichtig</div></span></span></a></div></div>        </div></div></div>

		<div class="ui-collapsible" data-icon-pos="right" style="border-bottom:3px solid #323232;"><h4 class="tagline left-tagline ie-rounded keuze-tagline ui-collapsible-heading"><span aria-hidden="true" class="ui-btn-inner ui-corner-top ui-corner-bottom"><span class="ui-btn-text"> Welk patroon heeft het dier? <span class="ui-collapsible-heading-status"> click to collapse contents</span></span><span class="ui-icon ui-icon-shadow ui-icon-minus"></span></span></h4><div class="ui-collapsible-content-wrapper"><div aria-hidden="false" class="ui-collapsible-content">        
    	    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColorPattern', '&amp;rnax_hasColorPattern=effen+%7c+patroonEffen')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__patroonEffen.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">effen</div></span></span></a></div><div class="facet-btn ui-block-b"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled ui-btn ui-btn-up-c" onclick=""><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__patroonGeblokt.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">geblokt</div></span></span></a></div><div class="facet-btn ui-block-c"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled ui-btn ui-btn-up-c" onclick=""><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__patroonGestipt.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">gestipt</div></span></span></a></div><div class="facet-btn ui-block-d"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColorPattern', '&amp;rnax_hasColorPattern=gestreept+%7c+patroonGestreept')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__patroonGestreept.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">gestreept</div></span></span></a></div><div class="facet-btn ui-block-a"><a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onclick="submitFacet('rnax_hasColorPattern', '&amp;rnax_hasColorPattern=gevlekt+%7c+patroonGevlekt')"><span aria-hidden="true" class="ui-btn-inner"><span class="ui-btn-text"><div class="grid-iconbox"><img src="{$session.app.project.urls.projectMedia}__patroonGevlekt.png" class="grid-icon" alt=""></div><div class="grid-labelbox ">gevlekt</div></span></span></a></div></div>        </div></div></div>
        </div>
        
	</div>





    <div role="main" class="ui-content" data-role="content" id="maincontent" style="display:none">

		<div id="x-menu-characters" style="display:none">

			<div class="facetheadercontainer">	    	
				<h4 class="tagline left-tagline ie-rounded" style="position: absolute;top: 25px;left:20px;padding-top:5px;">Wat weet je over het dier?</h4>
			</div>
	
			<!-- choices -->
			<div id="filter" class="filterset">
				<div class="ui-grid-c">
					<div class="facet-btn ui-block-a">
						<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="selectFacetGroup('facetgrouppage0')">
							<span aria-hidden="true" class="ui-btn-inner">
								<span class="ui-btn-text">
									<div class="grid-iconbox">
										<img src="{$session.app.project.urls.projectMedia}/__groepVogel.png" class="grid-icon" alt="">
									</div>
									<div class="grid-labelbox ">lijkt op</div>
								</span>
							</span>
						</a>
					</div>
					<div class="facet-btn ui-block-b">
						<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="selectFacetGroup('facetgrouppage1')">
							<span aria-hidden="true" class="ui-btn-inner">
								<span class="ui-btn-text">
									<div class="grid-iconbox">
										<img src="{$session.app.project.urls.projectMedia}/__rnax_hasHabitat.png" class="grid-icon" alt="">
									</div>
									<div class="grid-labelbox ">leefgebied</div>
								</span>
							</span>
						</a>
					</div>
					<div class="facet-btn ui-block-c">
						<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="selectFacetGroup('facetgrouppage2')">
							<span aria-hidden="true" class="ui-btn-inner">
								<span class="ui-btn-text">
									<div class="grid-iconbox">
										<img src="{$session.app.project.urls.projectMedia}/__rnax_hasSeason.png" class="grid-icon" alt="">
									</div>
									<div class="grid-labelbox ">seizoen</div>
								</span>
							</span>
						</a>
					</div>
					<div class="facet-btn ui-block-d">
						<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="selectFacetGroup('facetgrouppage3')">
							<span aria-hidden="true" class="ui-btn-inner">
								<span class="ui-btn-text">
									<div class="grid-iconbox">
										<img src="{$session.app.project.urls.projectMedia}/__rnax_hasSize.png" class="grid-icon" alt="">
									</div>
									<div class="grid-labelbox ">grootte</div>
								</span>
							</span>
						</a>
					</div>
					<div class="facet-btn ui-block-a ui-selected">
						<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="selectFacetGroup('facetgrouppage4')">
							<span aria-hidden="true" class="ui-btn-inner">
								<span class="ui-btn-text">
									<div class="grid-iconbox">
										<img src="{$session.app.project.urls.projectMedia}/selected-background.png" class="selected-icon-overlay-border" alt="">
										<img src="{$session.app.project.urls.projectMedia}/__detgroupColor.png" class="grid-icon" alt="">
										<img src="{$session.app.project.urls.projectMedia}/selected-badge.png" class="selected-icon-overlay-check" alt="">
									</div>
									<div class="grid-labelbox selected">kleur</div>
								</span>
							</span>
						</a>
					</div>
					<div class="facet-btn ui-block-b">
						<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="selectFacetGroup('facetgrouppage5')">
							<span aria-hidden="true" class="ui-btn-inner">
								<span class="ui-btn-text">
									<div class="grid-iconbox">
										<img src="{$session.app.project.urls.projectMedia}/__detgroupHead.png" class="grid-icon" alt="">
									</div>
								<div class="grid-labelbox ">kop</div>
								</span>
							</span>
						</a>
					</div>
					<div class="facet-btn ui-block-c">
						<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="selectFacetGroup('facetgrouppage6')">
							<span aria-hidden="true" class="ui-btn-inner">
								<span class="ui-btn-text">
									<div class="grid-iconbox">
										<img src="{$session.app.project.urls.projectMedia}/__detgroupBody.png" class="grid-icon" alt="">
									</div>
								   <div class="grid-labelbox ">lichaam</div>
								</span>
							</span>
						</a>
					</div>
					<div class="facet-btn ui-block-d">
						<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="selectFacetGroup('facetgrouppage7')">
							<span aria-hidden="true" class="ui-btn-inner">
								<span class="ui-btn-text">
									<div class="grid-iconbox">
										<img src="{$session.app.project.urls.projectMedia}/__detgroupLegs.png" class="grid-icon" alt="">
									</div>
									<div class="grid-labelbox ">poten</div>
								</span>
							</span>
						</a>
					</div>
					<div class="facet-btn ui-block-a">
						<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="selectFacetGroup('facetgrouppage8')">
							<span aria-hidden="true" class="ui-btn-inner">
								<span class="ui-btn-text">
									<div class="grid-iconbox">
										<img src="{$session.app.project.urls.projectMedia}/__rnax_hasWingShape.png" class="grid-icon" alt="">
									</div>
									<div class="grid-labelbox ">vleugels</div>
								</span>
							</span>
						</a>
					</div>
					<div class="facet-btn ui-block-b ui-selected">
						<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="selectFacetGroup('facetgrouppage9')">
							<span aria-hidden="true" class="ui-btn-inner">
								<span class="ui-btn-text">
									<div class="grid-iconbox">
										<img src="{$session.app.project.urls.projectMedia}/selected-background.png" class="selected-icon-overlay-border" alt="">
										<img src="{$session.app.project.urls.projectMedia}/__rnax_hasBehaviour.png" class="grid-icon" alt="">
										<img src="{$session.app.project.urls.projectMedia}/selected-badge.png" class="selected-icon-overlay-check" alt="">
									</div>
								<div class="grid-labelbox selected">gedrag</div>
								</span>
							</span>
						</a>
					</div>
					<div class="facet-btn ui-block-c">
						<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="selectFacetGroup('facetgrouppage10')">
							<span aria-hidden="true" class="ui-btn-inner">
								<span class="ui-btn-text">
									<div class="grid-iconbox">
										<img src="{$session.app.project.urls.projectMedia}/__rnax_hasInitialChar.png" class="grid-icon" alt="">
									</div>
								<div class="grid-labelbox ">beginletter</div>
								</span>
							</span>
						</a>
					</div>
				</div>       
			</div>
		
		</div>
    
		<div id="x-menu-selection" style="display:none">

			<div style="background-color:#2383b0;position:relative;">
				<div id="filter" class="filterset" style="background:none">        
					<h4 style="margin: 0px;margin-left:9px;padding-top: 10px;">Dit weet je:</h4>        
					<span style="position:absolute;right:22px;top:3px;">           
					   <img src="{$session.app.project.urls.projectMedia}/delete-all-choices-btn.png" onClick="resetFacetChoices('&amp;set=NatuurOmDeHoek');" alt="">            
					</span>
					<div class="ui-grid-c">       
						<div class="ui-block-a">
							<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-up-c" onClick="removeFacet(this);" facetname="rnax_hasColor" facetlabel="rood" facetlink="&amp;removeFacetValue=rnax_hasColor|rood+%7c+kleurRood">
								<span aria-hidden="true" class="ui-btn-inner">
									<span class="ui-btn-text">
										<div class="grid-iconbox">
											<div class="grid-labelbox" style="color:white;padding-top:5px;font-style:italic">kleur</div>
											<img src="{$session.app.project.urls.projectMedia}/__kleurRood.png" class="grid-icon" style="top:25px;" alt="">
											<img src="{$session.app.project.urls.projectMedia}/button-close-shadow-overlay.png" style="position:relative;top:0px;left:-5px;" alt="">
										</div>
										<div class="grid-labelbox" style="padding-top:23px;">rood</div>
									</span>
								</span>
							</a>
						</div>                                                                                             
						<div class="ui-block-b">
							<a data-theme="c" href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-btn ui-btn-hover-c" onClick="removeFacet(this);" facetname="rnax_hasBehaviour" facetlabel="steekt/bijt" facetlink="&amp;removeFacetValue=rnax_hasBehaviour|steekt%2fbijt+%7c+gedragSteektBijt">
								<span aria-hidden="true" class="ui-btn-inner">
									<span class="ui-btn-text">
										<div class="grid-iconbox">
											<div class="grid-labelbox" style="color:white;padding-top:5px;font-style:italic">gedrag</div>
											<img src="{$session.app.project.urls.projectMedia}/__gedragSteektBijt.png" class="grid-icon" style="top:25px;" alt="">
											<img src="{$session.app.project.urls.projectMedia}/button-close-shadow-overlay.png" style="position:relative;top:0px;left:-5px;" alt="">
										</div>
										<div class="grid-labelbox" style="padding-top:23px;">steekt/bijt</div>
									</span>
								</span>
							</a>
						</div>                                                                           
					</div>        
				</div>
			</div>
		
		</div>

		<div id="x-menu-results" style="display:none">

			<div id="results" class="block-container middle-block-container" style="margin-bottom: 60px;">
			
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
				<span class="ie-rounded">5 dieren gevonden</span>
				</h4>
				<ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist ui-listview ui-listview-inset ui-corner-all ui-shadow">
					<li data-theme="c" class="result0 ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-corner-top ui-btn-up-c">
						<div aria-hidden="true" class="ui-btn-inner ui-li ui-corner-top">
							<div class="ui-btn-text">
								<a href="http://www.dierenzoeker.nl/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzZkYzE2ZDUwLWQzNzItNGIxMi1hNGVjLTZjYTRlY2MxNTU0Ng==" class="resultlink ui-link-inherit" onClick="return handleResultLinkClick(this)">
									<img src="{$session.app.project.urls.projectMedia}/200692.jpg" class="result ui-li-thumb ui-corner-tl" alt="">
									Dikkopbloedbij
								</a>
							</div>
							<span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span>
						</div>
					</li>
					<li data-theme="c" class="result1 ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb">
						<div aria-hidden="true" class="ui-btn-inner ui-li">
							<div class="ui-btn-text">
								<a href="http://www.dierenzoeker.nl/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhlYjcxMTZkLWQzZmMtNDRiMC04OTQ1LWUxNGEzZjQ5NWYzNA==" class="resultlink ui-link-inherit" onClick="return handleResultLinkClick(this)">
									<img src="{$session.app.project.urls.projectMedia}/200702.jpg" class="result ui-li-thumb" alt="">
									Roodgatje
								</a>
							</div>
							<span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span>
						</div>
					</li>
					<li data-theme="c" class="result2 ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb">
						<div aria-hidden="true" class="ui-btn-inner ui-li">
							<div class="ui-btn-text">
								<a href="http://www.dierenzoeker.nl/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U4NTM1YjcyLTQ3MjEtNDVhMC04ZTMyLTMwZTE5NGJjYzdiMA==" class="resultlink ui-link-inherit" onClick="return handleResultLinkClick(this)">
								<img src="{$session.app.project.urls.projectMedia}/200705.jpg" class="result ui-li-thumb" alt="">	                    
								Signaalwespbij	                
								</a>
							</div>
							<span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span>
						</div>
					</li>
					<li data-theme="c" class="result3 ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb">
						<div aria-hidden="true" class="ui-btn-inner ui-li">
							<div class="ui-btn-text">
								<a href="http://www.dierenzoeker.nl/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4YmYzM2JmLTRlZmUtNDM3ZC1hZjhhLWZkNWI5NTM2OWRiNw==" class="resultlink ui-link-inherit" onClick="return handleResultLinkClick(this)">
									<img src="{$session.app.project.urls.projectMedia}/200706.jpg" class="result ui-li-thumb" alt="">	                    
									Steenhommel	           
								</a>
							</div>
							<span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span>
						</div>
					</li>
					<li data-theme="c" class="result4 ui-btn ui-btn-up-c ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-corner-bottom">
						<div aria-hidden="true" class="ui-btn-inner ui-li">
							<div class="ui-btn-text">
								<a href="http://www.dierenzoeker.nl/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5NDliZTlkLTM2ZmItNDc4ZS1hYzVjLTllMGY1ZmY1Yzk5Mw==" class="resultlink ui-link-inherit" onClick="return handleResultLinkClick(this)">
									<img src="{$session.app.project.urls.projectMedia}/200708.jpg" class="result ui-li-thumb ui-corner-bl" alt="">	                    
									Vosje	                
									</a>
							</div>
							<span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span>
						</div>
					</li>
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

</div>

<a href="#results" id="bottom-bar" class="menu-button-img">
	<span style="color:orange">5</span> dieren gevonden
</a>

<div style="top: 460px;" class="ui-loader ui-body-a ui-corner-all">
	<span class="ui-icon ui-icon-loading spin"></span>
	<h1>loading</h1>
</div>

{literal}
<script>
$(document).ready(function() {
	init({{/literal}matrix:{$matrix.id},language:{$currentLanguageId}{literal}});
	main();
});
</script>
{/literal}
</body>
</html>