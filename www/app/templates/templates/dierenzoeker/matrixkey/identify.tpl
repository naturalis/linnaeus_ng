{include file="../shared/header.tpl"}

<body> 
        <div class="main-wrapper">
            
            <div class="header">
                <div class="header-inner">
                    <a class="no-text" style="width:465px;height:90px;display:block;position:absolute;" href="">Home</a>
                    
                    <ul class="menu">
                        <li><a id="home-btn" class="no-text" href="">Home</a></li>
                        <li><a id="tv-btn" class="no-text" href="../../static/dierenzoeker/tv.php">Dierenzoeker op TV</a></li>
                        <li><a id="onderwijs-btn" class="no-text" href="../../static/dierenzoeker/onderwijs.php">Onderwijs</a></li>
                    </ul>
                    <a href='http://www.naturalis.nl' class="no-text" target="_blank" style='border:0px solid black;display:block;position:absolute;left:890px;top:2px;width:70px;height:90px;'>Naturalis</a>
                    <a href='http://www.hetklokhuis.nl' class="no-text"  target="_blank" style='border:0px solid black;display:block;position:absolute;left:818px;top:2px;width:67px;height:62px;'>Klokhuis</a>
                    <div class="clearer"></div>
                    
                    <ul class="wat-weet-je-list">      
                        <li class="wat-weet-je-arrow no-text">Wat weet je van het dier?</li>
                        {foreach from=$guiMenu item=v key=k}
                        {capture name=chars}{if $v.chars}{foreach from=$v.chars item=vC}{$vC.id} {/foreach}{else}{$v.id} {/if}{/capture}
                        <li class='facetgroup-btn'>
                            <div class="facet-btn ui-block-d">
                                <a data-facetgrouppageid="facetgrouppage{$k}" href="#" data-role="button" data-corners="false" data-shadow="false" class="" onClick="" characters="{$smarty.capture.chars|@trim}">
                                    <div class="grid-iconbox" >
                                        <img src="{$projectUrls.projectMedia}{$v.icon}" class="grid-icon" alt="" />
                                    </div>
                                    <div class="grid-labelbox ">
                                        {if $v.label_short}{$v.label_short}{else}{$v.label}{/if}
                                    </div>
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
                        <a href="#" class="no-text alles-wissen" onClick="nbcClearStateValue();return false;">Alles wissen</a>
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
								<a href="#" class="first-btn" onClick="navigeren('eerste');"></a>
								<a href="#" class="prev-btn" onClick="navigeren('vorige');"></a>
							</li>
                            <li class="num-found" style="margin-top:-2px;"><span class="num" id="result-count-container">0</span> dieren gevonden</li>
                            <li id="next-button-container-top" style="position:relative;left:84px;visibility:visible;">
								<a href="#" class="next-btn last-child" onClick="navigeren('volgende');"></a>
								<a href="#" class="last-btn last-child" onClick="navigeren('laatste');"></a>
							</li>
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
                            <li id="prev-button-container-bottom" style="visibility:hidden">
								<a href="#" class="first-btn" onClick="navigeren('eerste');" style="margin-left:5px"></a>
								<a href="#" class="prev-btn" onClick="navigeren('vorige');" style="margin-left:1px"></a>
							</li>
                            <li style="width:184px;">&nbsp;</li>
                            <li id="next-button-container-bottom" style="position:relative;left:84px;visibility:visible;">
								<a href="#" class="next-btn last-child" onClick="navigeren('volgende');"></a>
								<a href="#" class="last-btn last-child" onClick="navigeren('laatste');"></a>
							</li>
                        </ul>
                    </div>
                </div>
                
                <div class="dier-wrapper" id="dier-content-wrapper" style="visibility:hidden">
                    <div id="dier-header" class="dier-header">
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
                        <li><a href="../../static/dierenzoeker/mobiel.php" class="no-text" id="mobiel-btn">Op je mobiel</a></li>
                        <li><a href="../../static/dierenzoeker/faq.php" class="no-text" id="faq-btn" >Veel gestelde vragen</a></li>
                        <li><a href="../../static/dierenzoeker/colofon.php" class="no-text" id="colofon-btn">Colofon</a></li>
                    </ul>
                    <div class="clearer"></div>               
                    <a href='http://www.naturalis.nl' class="no-text" target="_blank" 
						style='display:block;position:absolute;left:373px;top:30px;width:79px;height:83px;border:0px solid black;'>Naturalis</a>
                    <a href='http://www.hetklokhuis.nl' class="no-text"  target="_blank"      
						style='display:block;position:absolute;left:458px;top:30px;width:67px;height:62px;border:0px solid red;'>Klokhuis</a>
                    <a href='http://www.eis-nederland.nl' class="no-text" target="_blank"   
						style='display:block;position:absolute;left:535px;top:30px;width:91px;height:82px;border:0px solid orange;'>EIS</a>
                    <a href='http://www.cultuurfonds.nl' class="no-text"  target="_blank"   
						style='display:block;position:absolute;left:640px;top:30px;width:126px;height:42px;border:0px solid purple;'>Prins Bernhard fonds</a>
                    <a href='http://www.nationaalgroenfonds.nl' class="no-text"  target="_blank"   
						style='display:block;position:absolute;left:631px;top:74px;width:140px;height:33px;border:0px solid green;'>Nationaal Groen Fonds</a>
                    <a href='http://www.rijksoverheid.nl/ministeries/ez' target="_blank" class="no-text" 
						style='display:block;position:absolute;left:779px;top:41px;width:150px;height:55px;border:0px solid blue;'>Ministerie voor landbouw en innovatie</a>

				<div class="social-media">
					<a href="http://www.facebook.com/dierenzoeker" target="_blank"><img src="../../media/system/skins/dierenzoeker/facebook.png" alt="" /></a>
					<a href="http://twitter.com/dierenzoeker" target="_blank"><img src="../../media/system/skins/dierenzoeker/twitter.png" alt="" style="width:32px;height:32px;" /></a>
				</div>
					</div>
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
						<div class="helper-div">
							<div class="ui-grid-c">
							
								{foreach from=$vC.states item=sV key=sK}
								{if $sV.file_name && $sV.file_exists}                            
						   
								<div class="facet-btn ui-block-{if $sK+1%4==0}d{elseif $sK+1%3==0}c{elseif $sK+1%2==0}b{else}a{/if}">
									<a href="#" onClick="nbcSetStateValue('c:{$vC.id}:{$sV.id}');return false;" class="" id="state-{$sV.id}">
									<div class="grid-iconbox">
										<img alt="" class="grid-icon" src="{$projectUrls.projectMedia}{$sV.file_name}">
									</div>
									<div class="grid-labelbox ">
										{$sV.label}
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
				
				<div class="facetgrouppage-inner">
					<h4 class="tagline left-tagline ie-rounded keuze-tagline">{$v.description}</h4>
					<div class="facetgrouppage-icons">
						<div class="helper-div">
							<div class="ui-grid-c">
                
                            {foreach from=$v.states item=sV key=sK}
                            {if $sV.file_name && $sV.file_exists}                            
                       
                            <div class="facet-btn ui-block-{if $sK+1%4==0}d{elseif $sK+1%3==0}c{elseif $sK+1%2==0}b{else}a{/if}">
                                <a href="#" onClick="nbcSetStateValue('c:{$v.id}:{$sV.id}');return false;" class="" id="state-{$sV.id}">
                                <div class="grid-iconbox">
                                    <img alt="" class="grid-icon" src="{$projectUrls.projectMedia}{$sV.file_name}">
                                </div>
                                <div class="grid-labelbox ">
                                    {$sV.label}
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
$(document).ready(function() {

matrixId={$matrix.id};
projectId={$projectId};
nbcUseEmergingCharacters={$matrix_use_emerging_characters};

	{if $requestData.dier}
	startDier='{$requestData.dier|@escape}';
	{/if}

	if(jQuery().prettyPhoto) {
		nbcPrettyPhotoInit();
	}
	getInitialValues();
	nbcGetResults();
	
	$('.facetgrouppage-close-btn').click(function(e){
		e.preventDefault();
		// Hide all facet group pages:
		$(".facetgrouppage").css("display", "none");
		return false;           
	});
	  
	$('[data-facetgrouppageid^="facetgrouppage"]').click(function(e){
		e.preventDefault();
		updateStates($(this).attr('characters'));
		var currentstate=$("#"+$(this).attr('data-facetgrouppageid')).css("display");
		// Close all facet group pages (cleanup):
		$(".facetgrouppage").css("display", "none");
		// Show facet group page:
		$("#"+$(this).attr('data-facetgrouppageid')).css("display", currentstate=="none"?"block":"none");
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

</body>
