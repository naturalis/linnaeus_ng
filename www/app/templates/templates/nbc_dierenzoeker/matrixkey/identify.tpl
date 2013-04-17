{include file="../shared/header.tpl"}

{literal}
<script>

(function($) {
   
   IJS.namespace("ntr.dz.hooks");
   IJS.ntr.dz.hooks.applyHooks = function(config) {
       
       // On next button click              
       $(config.nextBtn).click(function(e) {
           e.preventDefault();             
           config.listNavigator.loadNextPage(); 
           return false; 
       });       
       
       // On prev button click
       $(config.prevBtn).click(function(e) {
           e.preventDefault(); 
           config.listNavigator.loadPrevPage(); 
           return false; 
       });
       
       // On facet button click
       $(config.facetGroupBtn).click(function(e){
           e.preventDefault();           
           // Hide all facet group pages:
           $(".facetgrouppage").css("display", "none");                      
           // Get facetgrouppageid from node.
           var pageID = $(this).attr("data-facetgrouppageid");
           // Set display to block:
           $("#"+pageID).css("display", "block");                    
           return false;
       });       
       
       // On facet close button click
       // On submit facet button click
       $(config.submitFacetBtn).click(function(e) {
           e.preventDefault();
           
           var name = $(this).attr("data-facetname");
           var link = $(this).attr("data-facetlink");
           
           var submitter = config.facetSubmitter;
           submitter.submitFacet(name, link);
           
           return false;           
       });                 
       
       // On dier click                  
       $(config.listItem).live("click", function(e) {                             
           e.preventDefault();           
           
           var className = $(this).attr("class") || "";                     
           var isGroup = className.indexOf("group")!=-1;
           
           var href = $(this).attr("href");
           config.resultShower.show(href, isGroup);
           
           return false;
       });
       // TODO Maak hier CSS van.
       $(config.listItem).live("mouseover", function(e) {
           $(this).closest("li").addClass("list-item-over");
           $(this).css("color", "white");
       });
       $(config.listItem).live("mouseout", function(e) {
           $(this).closest("li").removeClass("list-item-over");
           $(this).css("color", "");
       });
       
       // On "Terug naar het dier" click
       $(config.dierBackBtn).live("click", function(e){           
           e.preventDefault();
           config.resultShower.showPrevious();
           return false;
       });
       
   } 
    
}(jQuery));
</script>

{/literal}

<body> 
       
        <div class="main-wrapper">
            
            <div class="header">
                <div class="header-inner">
                    <a class="no-text" style="width:465px;height:90px;display:block;position:absolute;" href="/">Home</a>
                    
                    <ul class="menu">
                        <li><a href="/" class="no-text" id="home-btn">Home</a></li>
                        <li><a href="" class="no-text" id="tv-btn">Dierenzoeker op TV</a></li>
                        <li><a href="" class="no-text" id="onderwijs-btn">Onderwijs</a></li>                                                
                    </ul>
                    <a href='http://www.naturalis.nl' class="no-text" target="_blank"       style='border:0px solid black;display:block;position:absolute;left:890px;top:2px;width:70px;height:90px;'>Naturalis</a>
                    <a href='http://www.hetklokhuis.nl' class="no-text"  target="_blank"    style='border:0px solid black;display:block;position:absolute;left:818px;top:2px;width:67px;height:62px;'>Klokhuis</a>
                    <div class="clearer"></div>
                    
                    <ul class="wat-weet-je-list">      
                        <li class="wat-weet-je-arrow no-text">Wat weet je van het dier?</li>
                        {foreach from=$guiMenu item=v key=k}
                        <li class='facetgroup-btn'>
                            <div class="facet-btn ui-block-d">
                                <a data-facetgrouppageid="facetgrouppage{$k}" href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick="">
                                    <div class="grid-iconbox" >
                                        <img src="{$session.app.project.urls.projectMedia}{$v.icon}" class="grid-icon" alt="" />
                                    </div>
                                    <div class="grid-labelbox ">
                                        {$v.label}
                                    </div>
                                </a>
                            </div>
                        </li>
                        {/foreach}
                    </ul>                
                </div><!-- /header-inder -->            
            </div><!-- /header -->            

            <div class="content">
                
                <div class="result-list-wrapper">
                    <div class="result-list-header">
                        <ul>

                            <li id="prev-button-container-top" style="visibility:hidden"><a href="#" class="no-text prev-btn" onClick="navigeren('vorige');">Vorige 16</a></li>
                            <li class="num-found" style="margin-top:-2px;"><span class="num" id="result-count-container">0</span> dieren gevonden</li>
                            <li id="next-button-container-top" style="position:relative;left:84px;visibility:visible;"><a href="#" class="no-text next-btn last-child" onClick="navigeren('volgende');">Volgende 16</a></li>

                        </ul>
                        <div class="clearer"></div>
                    </div>
                    <div class="result-list-body" id="result-list-container">
                         <div class="result-list-body-loading-container">
                             <div class="loader-img"></div>                             
                         </div>
                    </div>
                    <div class="result-list-footer">
                         <ul>

                            <li id="prev-button-container-bottom" style="visibility:hidden"><a href="#" class="no-text prev-btn" onClick="navigeren('vorige');">Vorige 16</a></li>
                            <li>&nbsp;</li>
                            <li  id="next-button-container-bottom" style="position:relative;left:261px;visibility:visible;"><a href="#" class="no-text next-btn last-child" onClick="navigeren('volgende');">Volgende 16</a></li>

                        </ul>
                    </div>
                </div>
                
                <div class="dier-wrapper" id="dier-content-wrapper" style="visibility:hidden">
                    <div class="dier-header">
                        Dier
                    </div><div class="dier-content" id="dier-content">
                        <!-- Placeholder for pretty first-time loading. -->
                       <div style='height:650px;'>
                       </div>                        
                    </div>
                </div>                
                
                <div class="clearer"></div>
                                          
            </div>

            <div class="footer">
                <div class="footer-inner">
                    <ul>
                        <li><a href="/index.php/identify/page/mobiel" class="no-text" id="mobiel-btn">Op je mobiel</a></li>
                        <li><a href="/index.php/identify/page/faq" class="no-text" id="faq-btn" >Veel gestelde vragen</a></li>
                        <li><a href="/index.php/identify/page/colofon" class="no-text" id="colofon-btn">Colofon</a></li>
                    </ul>
                    <div class="clearer"></div>               
                    <a href='http://www.naturalis.nl' class="no-text" target="_blank" style='display:block;position:absolute;left:373px;top:30px;width:79px;height:83px;border: 0px solid black;'>Naturalis</a>
                    <a href='http://www.hetklokhuis.nl' class="no-text"  target="_blank"      style='display:block;position:absolute;left:458px;top:30px;width:67px;height:62px;border: 0px solid red;'>Klokhuis</a>
                    <a href='http://www.eis-nederland.nl' class="no-text" target="_blank"   style='display:block;position:absolute;left:535px;top:30px;width:101px;height:68px;border: 0px solid orange;'>EIS</a>
                    <a href='http://www.cultuurfonds.nl' class="no-text"  target="_blank"   style='display:block;position:absolute;left:646px;top:30px;width:53px;height:68px;border:0px solid purple;'>Prins Bernhard fonds</a>
                    <a href='http://www.rijksoverheid.nl/ministeries/eleni' target="_blank" class="no-text" style='display:block;position:absolute;left:722px;top:30px;width:153px;height:68px;border: 0px solid blue;'>Ministerie voor landbouw en innovatie.</a>

                    <div class="social-media">
                    <a href="http://www.facebook.com/dierenzoeker" target="_blank"><img src="/app/webroot/img/facebook.png" alt="" /></a><a href="http://dierenzoeker.hyves.nl" target="_blank"><img src="/app/webroot/img/hyves.png" alt="" /></a></div>                </div>
                </div>

        </div>
       
                   
		<div class="facetgrouppage-wrapper">  

        {foreach from=$guiMenu item=v key=k}

            <div id="facetgrouppage{$k}" class="facetgrouppage">
				<img class="facetpage-puntje" alt="" src="{$session.app.system.urls.systemMedia}facet-puntje.png">
				<a class="no-text facetgrouppage-close-btn" href="#">Sluiten</a>
                
                {if $v.chars}
                {foreach from=$v.chars item=vC key=kC}

				<div class="facetgrouppage-inner">
					<h4 class="tagline left-tagline ie-rounded keuze-tagline">{$vC.info}</h4>
					<div class="facetgrouppage-icons">

						<div class="ui-grid-c">
                
                            {* foreach from=$v.states item=sV key=sK}
                            {if $sV.file_name && $sV.file_exists}                            
                       
                            <div class="facet-btn ui-block-{if $sK+1%4==0}d{elseif $sK+1%3==0}c{elseif $sK+1%2==0}b{else}a{/if}">
                                <a onclick="" class="" data-shadow="false" data-corners="false" data-role="button" href="#" data-facetlink="&amp;rnax_hasSubGroup=zoogdier+%7c+groepZoogdier" data-facetname="rnax_hasSubGroup">
                                <div class="grid-iconbox">
                                    <img alt="" class="grid-icon" src="{$session.app.project.urls.projectMedia}{$sV.file_name}">
                                </div>
                                <div class="grid-labelbox ">
                                    {$sV.label}
                                </div>
                                </a>
                            </div>
                            {/if}
                            {/foreach *}

						</div>
						<div class="clearer"></div>
						<div class="facetgrouppage-bottom-shade"></div>

					</div>  
				</div>
                
                {/foreach}

				{else}

				<div class="facetgrouppage-inner">
					<h4 class="tagline left-tagline ie-rounded keuze-tagline">{$v.info}</h4>
					<div class="facetgrouppage-icons">

						<div class="ui-grid-c">
                
                            {foreach from=$v.states item=sV key=sK}
                            {if $sV.file_name && $sV.file_exists}                            
                       
                            <div class="facet-btn ui-block-{if $sK+1%4==0}d{elseif $sK+1%3==0}c{elseif $sK+1%2==0}b{else}a{/if}">
                                <a onclick="" class="" data-shadow="false" data-corners="false" data-role="button" href="#" data-facetlink="&amp;rnax_hasSubGroup=zoogdier+%7c+groepZoogdier" data-facetname="rnax_hasSubGroup">
                                <div class="grid-iconbox">
                                    <img alt="" class="grid-icon" src="{$session.app.project.urls.projectMedia}{$sV.file_name}">
                                </div>
                                <div class="grid-labelbox ">
                                    {$sV.label}
                                </div>
                                </a>
                            </div>
                            {/if}
                            {/foreach}

						</div>
						<div class="clearer"></div>
						<div class="facetgrouppage-bottom-shade"></div>

					</div>  
				</div>
                
             	{/if}
                
			</div>    
		{/foreach}

		</div>


       <!-- div class="facetgrouppage-wrapper">     




            <div id="facetgrouppage4" class="facetgrouppage">
                <img src="/app/webroot/img/desktop/facet-puntje.png" class="facetpage-puntje" alt="" />                <a href="#" class="no-text facetgrouppage-close-btn">Sluiten</a>             
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Welke kleuren heeft het dier? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=wit+%7c+kleurWit"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurWit.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">wit</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=zwart+%7c+kleurZwart"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurZwart.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zwart</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=grijs+%7c+kleurGrijs"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurGrijs.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">grijs</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=bruin+%7c+kleurBruin"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurBruin.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">bruin</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=beige+%7c+kleurBeige"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurBeige.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">beige</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=paars+%7c+kleurPaars"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurPaars.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">paars</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=geel+%7c+kleurGeel"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurGeel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geel</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=oranje+%7c+kleurOranje"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurOranje.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">oranje</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=rood+%7c+kleurRood"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurRood.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">rood</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=roze+%7c+kleurRoze"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurRoze.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">roze</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=blauw+%7c+kleurBlauw"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurBlauw.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">blauw</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=groen+%7c+kleurGroen"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurGroen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">groen</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=zilver+%7c+kleurZilver"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurZilver.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zilver</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=goud+%7c+kleurGoud"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurGoud.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">goud</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasColor" data-facetlink="&rnax_hasColor=doorzichtig+%7c+kleurDoorzichtig"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kleurDoorzichtig.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">doorzichtig</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                            
                                                
            </div> 
















            
            <div id="facetgrouppage0" class="facetgrouppage" style="display:block">
                <img src="/app/webroot/img/desktop/facet-puntje.png" class="facetpage-puntje" alt="" />                <a href="#" class="no-text facetgrouppage-close-btn">Sluiten</a>             
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Waar lijkt het dier op? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasSubGroup" data-facetlink="&rnax_hasSubGroup=zoogdier+%7c+groepZoogdier"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepZoogdier.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zoogdier</div></a></div><div class="facet-btn ui-block-b ui-selected"><a data-facetname="rnax_hasSubGroup" data-facetlink="&removeFacetValue=rnax_hasSubGroup|vogel+%7c+groepVogel"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepVogel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox selected">vogel</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasSubGroup" data-facetlink="&rnax_hasSubGroup=amfibie+%7c+groepAmfibie"   href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepAmfibie.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">amfibie</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasSubGroup" data-facetlink="&rnax_hasSubGroup=vlieg+%7c+groepVlieg"   href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepVlieg.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vlieg</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasSubGroup" data-facetlink="&rnax_hasSubGroup=bij+%7c+groepBij"   href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepBij.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">bij</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasSubGroup" data-facetlink="&rnax_hasSubGroup=vlinder+%7c+groepVlinder"   href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepVlinder.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vlinder</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasSubGroup" data-facetlink="&rnax_hasSubGroup=kever+%7c+groepKever"   href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepKever.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kever</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasSubGroup" data-facetlink="&rnax_hasSubGroup=mier+%7c+groepMier"   href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepMier.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">mier</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasSubGroup" data-facetlink="&rnax_hasSubGroup=kriebeldier+%7c+groepKriebeldier"   href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepKriebeldier.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kriebeldier</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasSubGroup" data-facetlink="&rnax_hasSubGroup=spin+%7c+groepAchtpoter"   href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepAchtpoter.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">spin</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasSubGroup" data-facetlink="&rnax_hasSubGroup=worm+%7c+groepWorm"   href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepWorm.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">worm</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasSubGroup" data-facetlink="&rnax_hasSubGroup=slak%2fschelp+%7c+groepSlak%2fschelp"   href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled" onclick=""><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepSlak-schelp.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">slak/schelp</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                
            </div>
            

            <div id="facetgrouppage1" class="facetgrouppage" style="display:block">
                <img src="/app/webroot/img/desktop/facet-puntje.png" class="facetpage-puntje" alt="" />                <a href="#" class="no-text facetgrouppage-close-btn">Sluiten</a>             
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Waar zie je het dier? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=binnen+%7c+gebiedBinnen"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedBinnen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">binnen</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=in+de+grond+%7c+gebiedInGrond"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedInGrond.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">in de grond</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=op+de+grond+%7c+gebiedOpDeGrond"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedOpDeGrond.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">op de grond</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=dood+blad+%7c+gebiedDoodBlad"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedDoodBlad.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">dood blad</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=op+plant+%7c+gebiedOpPlant"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedOpPlant.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">op plant</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=op+bloem+%7c+gebiedOpBloem"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedOpBloem.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">op bloem</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=in+struik+%7c+gebiedInStruik"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedInStruik.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">in struik</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=in+boom+%7c+gebiedInBoom"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedInBoom.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">in boom</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=schuilplek+%7c+gebiedSchuilplek"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedSchuilplek.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">schuilplek</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=ergens+op+%7c+gebiedErgensOp"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedErgensOp.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">ergens op</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=tegen+muur+%7c+gebiedOpMuur"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedOpMuur.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">tegen muur</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=in+de+lucht+%7c+gebiedInDeLucht"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedInDeLucht.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">in de lucht</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasHabitat" data-facetlink="&rnax_hasHabitat=water+%7c+gebiedWater"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gebiedWater.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">water</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                
            </div>         
            

            <div id="facetgrouppage2" class="facetgrouppage">
                <img src="/app/webroot/img/desktop/facet-puntje.png" class="facetpage-puntje" alt="" />                <a href="#" class="no-text facetgrouppage-close-btn">Sluiten</a>             
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Wanneer zie je het dier? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasSeason" data-facetlink="&rnax_hasSeason=lente+%7c+tijdLente"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__tijdLente.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">lente</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasSeason" data-facetlink="&rnax_hasSeason=zomer+%7c+tijdZomer"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__tijdZomer.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zomer</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasSeason" data-facetlink="&rnax_hasSeason=herfst+%7c+tijdHerfst"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__tijdHerfst.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">herfst</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasSeason" data-facetlink="&rnax_hasSeason=winter+%7c+tijdWinter"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__tijdWinter.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">winter</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                
            </div>         
            
            <div id="facetgrouppage3" class="facetgrouppage">
                <img src="/app/webroot/img/desktop/facet-puntje.png" class="facetpage-puntje" alt="" />                <a href="#" class="no-text facetgrouppage-close-btn">Sluiten</a>             
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoe groot is het dier? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasSize" data-facetlink="&rnax_hasSize=zandkorrel+%7c+grootteZandkorrel"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__grootteZandkorrel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zandkorrel</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasSize" data-facetlink="&rnax_hasSize=mier+%7c+grootteMier"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__grootteMier.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">mier</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasSize" data-facetlink="&rnax_hasSize=pinda+%7c+groottePinda"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__groottePinda.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">pinda</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasSize" data-facetlink="&rnax_hasSize=euro+%7c+grootteEuro"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__grootteEuro.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">euro</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasSize" data-facetlink="&rnax_hasSize=huissleutel+%7c+grootteHuissleutel"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__grootteHuissleutel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">huissleutel</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasSize" data-facetlink="&rnax_hasSize=muis+%7c+grootteMuis"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__grootteMuis.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">muis</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasSize" data-facetlink="&rnax_hasSize=merel+%7c+grootteMerel"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__grootteMerel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">merel</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasSize" data-facetlink="&rnax_hasSize=duif+%7c+grootteDuif"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__grootteDuif.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">duif</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasSize" data-facetlink="&rnax_hasSize=kat+%7c+grootteKat"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__grootteKat.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kat</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasSize" data-facetlink="&rnax_hasSize=groter+%7c+grootteGroter"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__grootteGroter.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">groter</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                
            </div>         
            
        
            
            <div id="facetgrouppage5" class="facetgrouppage">
                <img src="/app/webroot/img/desktop/facet-puntje.png" class="facetpage-puntje" alt="" />                <a href="#" class="no-text facetgrouppage-close-btn">Sluiten</a>             
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Wat voor bek heeft het dier? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=geen+bek+%7c+bekGeenBek"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekGeenBek.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geen bek</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=tanden+%7c+bekTanden"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekTanden.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">tanden</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=snavel+dik+%7c+bekSnavelDik"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekSnavelDik.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel dik</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=snavel+dun+%7c+bekSnavelDun"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekSnavelDun.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel dun</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=snavel+kort+%7c+bekSnavelKort"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekSnavelKort.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel kort</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=snavel+lang+%7c+bekSnavelLang"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekSnavelLang.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel lang</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=snavel+krom+%7c+bekSnavelKrom"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekSnavelKrom.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel krom</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=snavel+plat+%7c+bekSnavelPlat"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekSnavelPlat.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel plat</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=mond+%7c+bekMond"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekMond.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">mond</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=tong+%7c+bekTong"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekTong.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">tong</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=snuit+%7c+bekSnuit"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekSnuit.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snuit</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=steeksnuit+%7c+bekSteeksnuit"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekSteeksnuit.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">steeksnuit</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasMouthShape" data-facetlink="&rnax_hasMouthShape=twee+kaken+%7c+bekTweeKaken"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__bekTweeKaken.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">twee kaken</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Wat zit er op de kop? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasHeadProtrusionShape" data-facetlink="&rnax_hasHeadProtrusionShape=niks+%7c+kopNiks"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kopNiks.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">niks</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasHeadProtrusionShape" data-facetlink="&rnax_hasHeadProtrusionShape=spriet+lang+%7c+kopSprietLang"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kopSprietLang.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">spriet lang</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasHeadProtrusionShape" data-facetlink="&rnax_hasHeadProtrusionShape=spriet+kort+%7c+kopSprietKort"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kopSprietKort.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">spriet kort</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasHeadProtrusionShape" data-facetlink="&rnax_hasHeadProtrusionShape=kuif+%7c+kopKuif"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kopKuif.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kuif</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasHeadProtrusionShape" data-facetlink="&rnax_hasHeadProtrusionShape=draden+%7c+kopDraden"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kopDraden.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">draden</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasHeadProtrusionShape" data-facetlink="&rnax_hasHeadProtrusionShape=bult+%7c+kopBult"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__kopBult.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">bult</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Wat voor ogen heeft het dier? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasEyeShape" data-facetlink="&rnax_hasEyeShape=geen+ogen+%7c+ogenGeenOgen"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__ogenGeenOgen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geen ogen</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasEyeShape" data-facetlink="&rnax_hasEyeShape=insectenoog+%7c+ogenInsectenoog"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__ogenInsectenoog.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">insectenoog</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasEyeShape" data-facetlink="&rnax_hasEyeShape=met+pupil+%7c+ogenMetPupil"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__ogenMetPupil.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met pupil</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasEyeShape" data-facetlink="&rnax_hasEyeShape=geen+pupil+%7c+ogenGeenPupil"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__ogenGeenPupil.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geen pupil</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasEyeShape" data-facetlink="&rnax_hasEyeShape=op+steeltje+%7c+ogenOpSteeltje"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__ogenOpSteeltje.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">op steeltje</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasEyeShape" data-facetlink="&rnax_hasEyeShape=puntjes+%7c+ogenPuntjes"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__ogenPuntjes.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">puntjes</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                
            </div>         
            
            <div id="facetgrouppage6" class="facetgrouppage">
                <img src="/app/webroot/img/desktop/facet-puntje.png" class="facetpage-puntje" alt="" />                <a href="#" class="no-text facetgrouppage-close-btn">Sluiten</a>             
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoe ziet de huid eruit? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasSkinType" data-facetlink="&rnax_hasSkinType=naakt+%7c+huidNaakt"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__huidNaakt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">naakt</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasSkinType" data-facetlink="&rnax_hasSkinType=glibberig+%7c+huidGlibberig"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__huidGlibberig.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">glibberig</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasSkinType" data-facetlink="&rnax_hasSkinType=glad+%7c+huidGlad"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__huidGlad.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">glad</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasSkinType" data-facetlink="&rnax_hasSkinType=met+haren+%7c+huidMetHaren"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__huidMetHaren.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met haren</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasSkinType" data-facetlink="&rnax_hasSkinType=vacht+%7c+huidVacht"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__huidVacht.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vacht</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasSkinType" data-facetlink="&rnax_hasSkinType=veren+%7c+huidVeren"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__huidVeren.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">veren</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasSkinType" data-facetlink="&rnax_hasSkinType=schubben+%7c+huidSchubben"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__huidSchubben.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">schubben</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasSkinType" data-facetlink="&rnax_hasSkinType=ruw+%7c+huidRuw"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__huidRuw.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">ruw</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasSkinType" data-facetlink="&rnax_hasSkinType=glanzend+%7c+huidGlanzend"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__huidGlanzend.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">glanzend</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoe ziet het achterlijf eruit? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasAbdomenShape" data-facetlink="&rnax_hasAbdomenShape=afgerond+%7c+achterAfgerond"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__achterAfgerond.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">afgerond</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasAbdomenShape" data-facetlink="&rnax_hasAbdomenShape=puntig+%7c+achterPuntig"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__achterPuntig.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">puntig</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasAbdomenShape" data-facetlink="&rnax_hasAbdomenShape=staart+kort+%7c+achterStaartKort"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__achterStaartKort.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">staart kort</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasAbdomenShape" data-facetlink="&rnax_hasAbdomenShape=staart+lang+%7c+achterStaartLang"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__achterStaartLang.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">staart lang</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasAbdomenShape" data-facetlink="&rnax_hasAbdomenShape=twee+draden+%7c+achterTweeDraden"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__achterTweeDraden.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">twee draden</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasAbdomenShape" data-facetlink="&rnax_hasAbdomenShape=drie+draden+%7c+achterDrieDraden"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__achterDrieDraden.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">drie draden</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasAbdomenShape" data-facetlink="&rnax_hasAbdomenShape=soort+tang+%7c+achterSoortTang"   href="#" data-role="button" data-corners="false" data-shadow="false" class="ui-disabled" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__achterSoortTang.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">soort tang</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasAbdomenShape" data-facetlink="&rnax_hasAbdomenShape=met+stekel+%7c+achterMetStekel"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__achterMetStekel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met stekel</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Welke vorm heeft het dier? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=dik+%7c+vormDik"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormDik.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">dik</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=dun+%7c+vormDun"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormDun.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">dun</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=lang+%7c+vormLang"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormLang.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">lang</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=plat+%7c+vormPlat"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormPlat.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">plat</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=rond+%7c+vormRond"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormRond.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">rond</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=driehoekig+%7c+vormDriehoekig"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormDriehoekig.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">driehoekig</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=segmenten+%7c+vormSegmenten"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormSegmenten.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">segmenten</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=bolletje+%7c+vormBolletje"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormBolletje.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">bolletje</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=twee+delen+%7c+vormTweeDelen"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormTweeDelen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">twee delen</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=drie+delen+%7c+vormDrieDelen"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormDrieDelen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">drie delen</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=met+huisje+%7c+vormMetHuisje"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormMetHuisje.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met huisje</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasBodyShape" data-facetlink="&rnax_hasBodyShape=met+schelp+%7c+vormMetSchelp"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vormMetSchelp.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met schelp</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                
            </div>         
            
            <div id="facetgrouppage7" class="facetgrouppage">
                <img src="/app/webroot/img/desktop/facet-puntje.png" class="facetpage-puntje" alt="" />                <a href="#" class="no-text facetgrouppage-close-btn">Sluiten</a>             
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoeveel poten heeft het dier? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasLegAmount" data-facetlink="&rnax_hasLegAmount=0+%7c+potenNr0"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__potenNr0.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">0</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasLegAmount" data-facetlink="&rnax_hasLegAmount=2+%7c+potenNr2"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__potenNr2.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">2</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasLegAmount" data-facetlink="&rnax_hasLegAmount=4+%7c+potenNr4"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__potenNr4.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">4</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasLegAmount" data-facetlink="&rnax_hasLegAmount=6+%7c+potenNr6"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__potenNr6.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">6</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasLegAmount" data-facetlink="&rnax_hasLegAmount=8+%7c+potenNr8"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__potenNr8.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">8</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasLegAmount" data-facetlink="&rnax_hasLegAmount=10+%7c+potenNr10"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__potenNr10.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">10</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasLegAmount" data-facetlink="&rnax_hasLegAmount=10%2b+%7c+potenNr10Plus"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__potenNr10Plus.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">10+</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoe zien de poten eruit? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasLegShape" data-facetlink="&rnax_hasLegShape=geen+poten+%7c+pootVormGeenPoten"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__pootVormGeenPoten.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geen poten</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasLegShape" data-facetlink="&rnax_hasLegShape=lang+en+dun+%7c+pootVormLangEnDun"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__pootVormLangEnDun.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">lang en dun</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasLegShape" data-facetlink="&rnax_hasLegShape=plat+%7c+pootVormPlat"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__pootVormPlat.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">plat</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasLegShape" data-facetlink="&rnax_hasLegShape=grijppoten+%7c+pootVormGrijppoten"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__pootVormGrijppoten.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">grijppoten</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasLegShape" data-facetlink="&rnax_hasLegShape=met+hoeven+%7c+pootVormMetHoeven"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__pootVormMetHoeven.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met hoeven</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasLegShape" data-facetlink="&rnax_hasLegShape=lange+tenen+%7c+pootVormLangeTenen"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__pootVormLangeTenen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">lange tenen</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasLegShape" data-facetlink="&rnax_hasLegShape=nagels+%7c+pootVormNagels"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__pootVormNagels.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">nagels</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasLegShape" data-facetlink="&rnax_hasLegShape=met+haakjes+%7c+pootVormMetHaakjes"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__pootVormMetHaakjes.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met haakjes</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasLegShape" data-facetlink="&rnax_hasLegShape=met+scharen+%7c+pootVormMetScharen"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__pootVormMetScharen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met scharen</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasLegShape" data-facetlink="&rnax_hasLegShape=behaard+%7c+pootVormBehaard"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__pootVormBehaard.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">behaard</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasLegShape" data-facetlink="&rnax_hasLegShape=zwemvliezen+%7c+pootVormZwemvliezen"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__pootVormZwemvliezen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zwemvliezen</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                
            </div>         
            
            <div id="facetgrouppage8" class="facetgrouppage">
                <img src="/app/webroot/img/desktop/facet-puntje.png" class="facetpage-puntje" alt="" />                <a href="#" class="no-text facetgrouppage-close-btn">Sluiten</a>             
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoe zien de vleugels eruit? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasWingShape" data-facetlink="&rnax_hasWingShape=naakt+%7c+huidNaakt"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__huidNaakt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">naakt</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasWingShape" data-facetlink="&rnax_hasWingShape=geen+%7c+vleugelsGeen"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vleugelsGeen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geen</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasWingShape" data-facetlink="&rnax_hasWingShape=doorzichtig+%7c+vleugelsDoorzichtig"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vleugelsDoorzichtig.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">doorzichtig</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasWingShape" data-facetlink="&rnax_hasWingShape=gevlekt+%7c+vleugelsGevlekt"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vleugelsGevlekt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">gevlekt</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasWingShape" data-facetlink="&rnax_hasWingShape=gekleurd+%7c+vleugelsGekleurd"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vleugelsGekleurd.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">gekleurd</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasWingShape" data-facetlink="&rnax_hasWingShape=als+vlinder+%7c+vleugelsAlsVlinder"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vleugelsAlsVlinder.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">als vlinder</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasWingShape" data-facetlink="&rnax_hasWingShape=als+kever+%7c+vleugelsAlsKever"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vleugelsAlsKever.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">als kever</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasWingShape" data-facetlink="&rnax_hasWingShape=met+veren+%7c+vleugelsMetVeren"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vleugelsMetVeren.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met veren</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasWingShape" data-facetlink="&rnax_hasWingShape=met+huid+%7c+vleugelsMetHuid"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__vleugelsMetHuid.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met huid</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                
            </div>         
            
            <div id="facetgrouppage9" class="facetgrouppage">
                <img src="/app/webroot/img/desktop/facet-puntje.png" class="facetpage-puntje" alt="" />                <a href="#" class="no-text facetgrouppage-close-btn">Sluiten</a>             
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Wat doet het dier? </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=zit+stil+%7c+gedragZitStil"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragZitStil.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zit stil</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=kruipt+%7c+gedragKruipt"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragKruipt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kruipt</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=loopt+%7c+gedragLoopt"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragLoopt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">loopt</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=rent+%7c+gedragRent"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragRent.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">rent</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=springt+%7c+gedragSpringt"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragSpringt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">springt</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=rolt+op+%7c+gedragRoltOp"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragRoltOp.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">rolt op</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=graaft+%7c+gedragGraaft"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragGraaft.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">graaft</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=hangt+%7c+gedragHangt"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragHangt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">hangt</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=schuilt+%7c+gedragSchuilt"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragSchuilt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">schuilt</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=steekt%2fbijt+%7c+gedragSteektBijt"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragSteektBijt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">steekt/bijt</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=geluid+%7c+gedragGeluid"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragGeluid.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geluid</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=vliegt+%7c+gedragVliegt"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragVliegt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vliegt</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasBehaviour" data-facetlink="&rnax_hasBehaviour=zwemt+%7c+gedragZwemt"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__gedragZwemt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zwemt</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                
            </div>         
            
            <div id="facetgrouppage10" class="facetgrouppage">
                <img src="/app/webroot/img/desktop/facet-puntje.png" class="facetpage-puntje" alt="" />                <a href="#" class="no-text facetgrouppage-close-btn">Sluiten</a>             
                                                        <div class="facetgrouppage-inner">                                                                           
                                    <h4  class="tagline left-tagline ie-rounded keuze-tagline"> Beginletter </h4>
                                        <div class="facetgrouppage-icons">        
                                            <div class="ui-grid-c"><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=A+%7c+letterA"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterA.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">A</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=B+%7c+letterB"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterB.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">B</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=C+%7c+letterC"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterC.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">C</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=D+%7c+letterD"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterD.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">D</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=E+%7c+letterE"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterE.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">E</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=F+%7c+letterF"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterF.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">F</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=G+%7c+letterG"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterG.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">G</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=H+%7c+letterH"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterH.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">H</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=I+%7c+letterI"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterI.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">I</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=J+%7c+letterJ"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterJ.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">J</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=K+%7c+letterK"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterK.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">K</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=L+%7c+letterL"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterL.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">L</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=M+%7c+letterM"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterM.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">M</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=N+%7c+letterN"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterN.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">N</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=O+%7c+letterO"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterO.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">O</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=P+%7c+letterP"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterP.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">P</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=R+%7c+letterR"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterR.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">R</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=S+%7c+letterS"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterS.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">S</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=T+%7c+letterT"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterT.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">T</div></a></div><div class="facet-btn ui-block-d"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=U+%7c+letterU"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterU.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">U</div></a></div><div class="facet-btn ui-block-a"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=V+%7c+letterV"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterV.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">V</div></a></div><div class="facet-btn ui-block-b"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=W+%7c+letterW"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterW.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">W</div></a></div><div class="facet-btn ui-block-c"><a data-facetname="rnax_hasInitialChar" data-facetlink="&rnax_hasInitialChar=Z+%7c+letterZ"   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick=""><div class="grid-iconbox" ><img src="{$session.app.project.urls.projectMedia}__letterZ.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">Z</div></a></div></div>                                        </div>
                                 </div>                        
                            <div class="clearer"></div>
                            <div class="facetgrouppage-bottom-shade"></div>
                                                
            </div>         
                </div -->
       

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

{if $taxaJSON}
	nbcData = $.parseJSON('{$taxaJSON}');
	nbcDoResults({literal}{resetStart:true}{/literal});
{else}
	nbcGetResults();
{/if}
{literal}

	if(jQuery().prettyPhoto) {
		nbcPrettyPhotoInit();
	}

   $('.facetgrouppage-close-btn').click(function(e){
	   e.preventDefault();
	   // Hide all facet group pages:
	   $(".facetgrouppage").css("display", "none");
	   return false;           
   });
              
   $('[data-facetgrouppageid^="facetgrouppage"]').click(function(e){
	   e.preventDefault();
	   // Show facet group page:
	   $(".facetgrouppage").css("display", "none");
	   $("#"+$(this).attr('data-facetgrouppageid')).css("display", "block");
	   return false;           
   });

});
</script>
{/literal}

</body>
    
    