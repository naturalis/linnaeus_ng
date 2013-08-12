{literal}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />	<title>
		Dierenzoeker			</title>
	<meta property="og:description" content="Zie je een dier in je huis of tuin, en weet je niet wat het is? Kijk goed en ontdek het in de Dierenzoeker."/>
            <link rel="image_src" href="/app/webroot/img/dierenzoeker-logo.png" />
    	<link href="/app/webroot/favicon.ico" type="image/x-icon" rel="icon" /><link href="/app/webroot/favicon.ico" type="image/x-icon" rel="shortcut icon" />	<script type="text/javascript">
		// Important global vars:	
		var images_url = "/app/webroot/";
		var requestmore_url = "/index.php/identify/more";
	</script>
	
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.css" />
	<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script>	
	<script type="text/javascript" src="/app/webroot/js/shared.js"></script>	<script type="text/javascript" src="/app/webroot/js/mobile.js"></script>	<link rel="stylesheet" type="text/css" href="/app/webroot/css/shared.css" /> 
	<link rel="stylesheet" type="text/css" href="/app/webroot/css/mobile.css" /> 
	 
	<link rel="stylesheet" type="text/css" href="/app/webroot/css/mobile-small.css" /> 

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    
	
	<!-- Voor het geval de CDN het niet doet hieronder de lokale en google versies:
		
	<link rel="stylesheet" href="/js/jquery/jquery.mobile-1.0rc2.min.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
	<script src="/js/jquery/jquery.mobile-1.0rc2.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	
	-->
	<script type="text/javascript">
		<!--
		function selectFacetGroup(pageId) {
			$.mobile.changePage($('#' + pageId));
		}
		
		$(document).bind("pagecreate", function() {
		        $(".faq-answer").css("display", "none");
                $(".faq-question").bind("click", function(e) {
                    console.log("JA!");
                    var $elem = $(this).next();
                    if ($elem.css("display")=="block") {
                        $(this).next().css("display", "none"); 
                    } else {
                        $(this).next().css("display", "block"); 
                    }            
                });
        });
		
		
		// -->
	</script>
</head>
<body>
			
			<!-- form shared by all determinatie pages -->
<form data-ajax="false" method="post" id="facetForm" action="#main">
<input type="hidden" name="data[facet][searchTerms]" id="searchTerms" value="&set=NatuurOmDeHoek">
</form>
<div id="main" class="main-page" data-role="page" style="background-image:none;">
    <div data-role="header" id="mainheader">
    	<img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />    	
    	<a id="infobutton" href="/index.php/identify/page/colofon" data-role="button" data-inline="true" data-transition="none" class="ui-btn-left" style="top:50%;margin-top:-17px;height:24px;">
    		<img src="/app/webroot/img/verbeterslag/info.png" alt="" />    	</a>
    </div> 
    <div data-role="content" id="maincontent">
    	

    	
    	<div class="facetheadercontainer">	    	
		    <h4 class="tagline left-tagline ie-rounded" style="position: absolute;top: 25px;left:20px;padding-top:5px;">Wat weet je over het dier?</h4>
	    </div>
    	<div id="filter" class="filternotset">
    		
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="selectFacetGroup('facetgrouppage0')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__rnax_hasSubGroup.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">lijkt op</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="selectFacetGroup('facetgrouppage1')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__rnax_hasHabitat.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">leefgebied</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="selectFacetGroup('facetgrouppage2')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__rnax_hasSeason.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">seizoen</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="selectFacetGroup('facetgrouppage3')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__rnax_hasSize.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">grootte</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="selectFacetGroup('facetgrouppage4')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__detgroupColor.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kleur</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="selectFacetGroup('facetgrouppage5')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__detgroupHead.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kop</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="selectFacetGroup('facetgrouppage6')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__detgroupBody.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">lichaam</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="selectFacetGroup('facetgrouppage7')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__detgroupLegs.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">poten</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="selectFacetGroup('facetgrouppage8')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__rnax_hasWingShape.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vleugels</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="selectFacetGroup('facetgrouppage9')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__rnax_hasBehaviour.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">gedrag</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="selectFacetGroup('facetgrouppage10')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__rnax_hasInitialChar.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">beginletter</div></a></div></div>       </div>


    	<div id="results" class="block-container middle-block-container" style="margin-bottom: 60px;"> 
    		
					<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
			    <span id='button-container'>
		 <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' id='moreRowsButton' class="simplebutton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>


         <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
        	     
	     </span>
	    </div>
	    	   
	</div>
</div>

<script language="JavaScript">
  $(function(){
    $(".ui-collapsible-content").wrap("<div class='ui-collapsible-content-wrapper' />");  
  });           
</script>

<!-- insert 'pages' for the determinatie, showing and hiding these is controlled by jquery mobile -->
	
<div id="facetgrouppage0" data-role="page" class="facetgrouppage">
	<div data-role='header' class='header'>	    
        <img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />        <a href="#main" data-transition="slide" data-direction="reverse" data-role="none">
            <img src="/app/webroot/img/verbeterslag/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="" />        </a>
    </div>
    <div data-role="content" class="content keuze-content" >
				
		
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible"  data-collapsed='false'>        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Waar lijkt het dier op? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=zoogdier+%7c+groepZoogdier')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepZoogdier.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zoogdier</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=vogel+%7c+groepVogel')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepVogel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vogel</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=amfibie+%7c+groepAmfibie')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepAmfibie.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">amfibie</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=vis+%7c+groepVis')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepVis.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vis</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=vlieg+%7c+groepVlieg')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepVlieg.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vlieg</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=bij+%7c+groepBij')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepBij.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">bij</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=vlinder+%7c+groepVlinder')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepVlinder.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vlinder</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=kever+%7c+groepKever')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepKever.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kever</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=mier+%7c+groepMier')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepMier.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">mier</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=kriebeldier+%7c+groepKriebeldier')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepKriebeldier.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kriebeldier</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=spin+%7c+groepAchtpoter')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepAchtpoter.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">spin</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=worm+%7c+groepWorm')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepWorm.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">worm</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSubGroup', '&rnax_hasSubGroup=slak%2fschelp+%7c+groepSlak%2fschelp')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groepSlak-schelp.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">slak/schelp</div></a></div></div>        </div>
        </div>
        
        
	</div>
	
	<div id="facetgrouppage0-items" class="block-container middle-block-container results" style="margin-bottom:100px;">
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
				<span id='button-container'>
                    <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' class="simplebutton moreRowsButton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>
                <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
	</div>	
	
</div>    
	
<div id="facetgrouppage1" data-role="page" class="facetgrouppage">
	<div data-role='header' class='header'>	    
        <img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />        <a href="#main" data-transition="slide" data-direction="reverse" data-role="none">
            <img src="/app/webroot/img/verbeterslag/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="" />        </a>
    </div>
    <div data-role="content" class="content keuze-content" >
				
		
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible"  data-collapsed='false'>        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Waar zie je het dier? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=binnen+%7c+gebiedBinnen')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedBinnen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">binnen</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=in+de+grond+%7c+gebiedInGrond')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedInGrond.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">in de grond</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=op+de+grond+%7c+gebiedOpDeGrond')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedOpDeGrond.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">op de grond</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=dood+blad+%7c+gebiedDoodBlad')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedDoodBlad.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">dood blad</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=op+plant+%7c+gebiedOpPlant')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedOpPlant.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">op plant</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=op+bloem+%7c+gebiedOpBloem')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedOpBloem.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">op bloem</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=in+struik+%7c+gebiedInStruik')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedInStruik.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">in struik</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=in+boom+%7c+gebiedInBoom')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedInBoom.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">in boom</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=schuilplek+%7c+gebiedSchuilplek')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedSchuilplek.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">schuilplek</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=ergens+op+%7c+gebiedErgensOp')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedErgensOp.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">ergens op</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=tegen+muur+%7c+gebiedOpMuur')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedOpMuur.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">tegen muur</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=in+de+lucht+%7c+gebiedInDeLucht')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedInDeLucht.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">in de lucht</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHabitat', '&rnax_hasHabitat=water+%7c+gebiedWater')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gebiedWater.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">water</div></a></div></div>        </div>
        </div>
        
        
	</div>
	
	<div id="facetgrouppage1-items" class="block-container middle-block-container results" style="margin-bottom:100px;">
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
				<span id='button-container'>
                    <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' class="simplebutton moreRowsButton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>
                <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
	</div>	
	
</div>    
	
<div id="facetgrouppage2" data-role="page" class="facetgrouppage">
	<div data-role='header' class='header'>	    
        <img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />        <a href="#main" data-transition="slide" data-direction="reverse" data-role="none">
            <img src="/app/webroot/img/verbeterslag/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="" />        </a>
    </div>
    <div data-role="content" class="content keuze-content" >
				
		
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible"  data-collapsed='false'>        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Wanneer zie je het dier? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSeason', '&rnax_hasSeason=lente+%7c+tijdLente')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__tijdLente.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">lente</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSeason', '&rnax_hasSeason=zomer+%7c+tijdZomer')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__tijdZomer.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zomer</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSeason', '&rnax_hasSeason=herfst+%7c+tijdHerfst')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__tijdHerfst.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">herfst</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSeason', '&rnax_hasSeason=winter+%7c+tijdWinter')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__tijdWinter.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">winter</div></a></div></div>        </div>
        </div>
        
        
	</div>
	
	<div id="facetgrouppage2-items" class="block-container middle-block-container results" style="margin-bottom:100px;">
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
				<span id='button-container'>
                    <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' class="simplebutton moreRowsButton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>
                <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
	</div>	
	
</div>    
	
<div id="facetgrouppage3" data-role="page" class="facetgrouppage">
	<div data-role='header' class='header'>	    
        <img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />        <a href="#main" data-transition="slide" data-direction="reverse" data-role="none">
            <img src="/app/webroot/img/verbeterslag/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="" />        </a>
    </div>
    <div data-role="content" class="content keuze-content" >
				
		
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible"  data-collapsed='false'>        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoe groot is het dier? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSize', '&rnax_hasSize=zandkorrel+%7c+grootteZandkorrel')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__grootteZandkorrel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zandkorrel</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSize', '&rnax_hasSize=mier+%7c+grootteMier')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__grootteMier.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">mier</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSize', '&rnax_hasSize=pinda+%7c+groottePinda')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__groottePinda.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">pinda</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSize', '&rnax_hasSize=euro+%7c+grootteEuro')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__grootteEuro.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">euro</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSize', '&rnax_hasSize=huissleutel+%7c+grootteHuissleutel')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__grootteHuissleutel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">huissleutel</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSize', '&rnax_hasSize=muis+%7c+grootteMuis')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__grootteMuis.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">muis</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSize', '&rnax_hasSize=merel+%7c+grootteMerel')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__grootteMerel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">merel</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSize', '&rnax_hasSize=duif+%7c+grootteDuif')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__grootteDuif.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">duif</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSize', '&rnax_hasSize=kat+%7c+grootteKat')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__grootteKat.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kat</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSize', '&rnax_hasSize=groter+%7c+grootteGroter')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__grootteGroter.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">groter</div></a></div></div>        </div>
        </div>
        
        
	</div>
	
	<div id="facetgrouppage3-items" class="block-container middle-block-container results" style="margin-bottom:100px;">
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >

	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
				<span id='button-container'>
                    <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' class="simplebutton moreRowsButton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>
                <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
	</div>	
	
</div>    
	
<div id="facetgrouppage4" data-role="page" class="facetgrouppage">
	<div data-role='header' class='header'>	    
        <img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />        <a href="#main" data-transition="slide" data-direction="reverse" data-role="none">
            <img src="/app/webroot/img/verbeterslag/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="" />        </a>
    </div>
    <div data-role="content" class="content keuze-content" >
				
		
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible" >        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Welke kleuren heeft het dier? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=wit+%7c+kleurWit')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurWit.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">wit</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=zwart+%7c+kleurZwart')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurZwart.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zwart</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=grijs+%7c+kleurGrijs')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurGrijs.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">grijs</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=bruin+%7c+kleurBruin')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurBruin.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">bruin</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=beige+%7c+kleurBeige')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurBeige.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">beige</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=paars+%7c+kleurPaars')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurPaars.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">paars</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=geel+%7c+kleurGeel')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurGeel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geel</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=oranje+%7c+kleurOranje')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurOranje.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">oranje</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=rood+%7c+kleurRood')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurRood.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">rood</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=roze+%7c+kleurRoze')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurRoze.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">roze</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=blauw+%7c+kleurBlauw')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurBlauw.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">blauw</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=groen+%7c+kleurGroen')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurGroen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">groen</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=zilver+%7c+kleurZilver')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurZilver.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zilver</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=goud+%7c+kleurGoud')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurGoud.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">goud</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColor', '&rnax_hasColor=doorzichtig+%7c+kleurDoorzichtig')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kleurDoorzichtig.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">doorzichtig</div></a></div></div>        </div>
			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible" >        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Welk patroon heeft het dier? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColorPattern', '&rnax_hasColorPattern=effen+%7c+patroonEffen')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__patroonEffen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">effen</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColorPattern', '&rnax_hasColorPattern=geblokt+%7c+patroonGeblokt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__patroonGeblokt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geblokt</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColorPattern', '&rnax_hasColorPattern=gestipt+%7c+patroonGestipt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__patroonGestipt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">gestipt</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColorPattern', '&rnax_hasColorPattern=gestreept+%7c+patroonGestreept')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__patroonGestreept.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">gestreept</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasColorPattern', '&rnax_hasColorPattern=gevlekt+%7c+patroonGevlekt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__patroonGevlekt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">gevlekt</div></a></div></div>        </div>
        </div>
        
        
	</div>
	
	<div id="facetgrouppage4-items" class="block-container middle-block-container results" style="margin-bottom:100px;">
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
				<span id='button-container'>
                    <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' class="simplebutton moreRowsButton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>
                <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
	</div>	
	
</div>    
	
<div id="facetgrouppage5" data-role="page" class="facetgrouppage">
	<div data-role='header' class='header'>	    
        <img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />        <a href="#main" data-transition="slide" data-direction="reverse" data-role="none">
            <img src="/app/webroot/img/verbeterslag/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="" />        </a>
    </div>
    <div data-role="content" class="content keuze-content" >
				
		
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible" >        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Wat voor bek heeft het dier? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=geen+bek+%7c+bekGeenBek')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekGeenBek.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geen bek</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=tanden+%7c+bekTanden')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekTanden.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">tanden</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=snavel+dik+%7c+bekSnavelDik')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekSnavelDik.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel dik</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=snavel+dun+%7c+bekSnavelDun')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekSnavelDun.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel dun</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=snavel+kort+%7c+bekSnavelKort')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekSnavelKort.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel kort</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=snavel+lang+%7c+bekSnavelLang')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekSnavelLang.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel lang</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=snavel+krom+%7c+bekSnavelKrom')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekSnavelKrom.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel krom</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=snavel+plat+%7c+bekSnavelPlat')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekSnavelPlat.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snavel plat</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=mond+%7c+bekMond')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekMond.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">mond</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=tong+%7c+bekTong')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekTong.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">tong</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=snuit+%7c+bekSnuit')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekSnuit.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">snuit</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=steeksnuit+%7c+bekSteeksnuit')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekSteeksnuit.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">steeksnuit</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasMouthShape', '&rnax_hasMouthShape=twee+kaken+%7c+bekTweeKaken')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__bekTweeKaken.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">twee kaken</div></a></div></div>        </div>
			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible" >        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Wat zit er op de kop? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHeadProtrusionShape', '&rnax_hasHeadProtrusionShape=niks+%7c+kopNiks')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kopNiks.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">niks</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHeadProtrusionShape', '&rnax_hasHeadProtrusionShape=spriet+lang+%7c+kopSprietLang')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kopSprietLang.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">spriet lang</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHeadProtrusionShape', '&rnax_hasHeadProtrusionShape=spriet+kort+%7c+kopSprietKort')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kopSprietKort.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">spriet kort</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHeadProtrusionShape', '&rnax_hasHeadProtrusionShape=kuif+%7c+kopKuif')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kopKuif.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kuif</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHeadProtrusionShape', '&rnax_hasHeadProtrusionShape=draden+%7c+kopDraden')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kopDraden.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">draden</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasHeadProtrusionShape', '&rnax_hasHeadProtrusionShape=bult+%7c+kopBult')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__kopBult.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">bult</div></a></div></div>        </div>
			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible" >        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Wat voor ogen heeft het dier? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasEyeShape', '&rnax_hasEyeShape=geen+ogen+%7c+ogenGeenOgen')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__ogenGeenOgen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geen ogen</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasEyeShape', '&rnax_hasEyeShape=insectenoog+%7c+ogenInsectenoog')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__ogenInsectenoog.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">insectenoog</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasEyeShape', '&rnax_hasEyeShape=met+pupil+%7c+ogenMetPupil')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__ogenMetPupil.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met pupil</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasEyeShape', '&rnax_hasEyeShape=geen+pupil+%7c+ogenGeenPupil')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__ogenGeenPupil.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geen pupil</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasEyeShape', '&rnax_hasEyeShape=op+steeltje+%7c+ogenOpSteeltje')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__ogenOpSteeltje.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">op steeltje</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasEyeShape', '&rnax_hasEyeShape=puntjes+%7c+ogenPuntjes')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__ogenPuntjes.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">puntjes</div></a></div></div>        </div>
        </div>
        
        
	</div>
	
	<div id="facetgrouppage5-items" class="block-container middle-block-container results" style="margin-bottom:100px;">
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
				<span id='button-container'>
                    <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' class="simplebutton moreRowsButton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>
                <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
	</div>	
	
</div>    
	
<div id="facetgrouppage6" data-role="page" class="facetgrouppage">
	<div data-role='header' class='header'>	    
        <img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />        <a href="#main" data-transition="slide" data-direction="reverse" data-role="none">
            <img src="/app/webroot/img/verbeterslag/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="" />        </a>
    </div>
    <div data-role="content" class="content keuze-content" >
				
		
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible" >        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoe ziet de huid eruit? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSkinType', '&rnax_hasSkinType=naakt+%7c+huidNaakt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__huidNaakt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">naakt</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSkinType', '&rnax_hasSkinType=glibberig+%7c+huidGlibberig')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__huidGlibberig.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">glibberig</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSkinType', '&rnax_hasSkinType=glad+%7c+huidGlad')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__huidGlad.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">glad</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSkinType', '&rnax_hasSkinType=met+haren+%7c+huidMetHaren')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__huidMetHaren.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met haren</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSkinType', '&rnax_hasSkinType=vacht+%7c+huidVacht')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__huidVacht.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vacht</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSkinType', '&rnax_hasSkinType=veren+%7c+huidVeren')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__huidVeren.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">veren</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSkinType', '&rnax_hasSkinType=schubben+%7c+huidSchubben')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__huidSchubben.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">schubben</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSkinType', '&rnax_hasSkinType=ruw+%7c+huidRuw')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__huidRuw.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">ruw</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasSkinType', '&rnax_hasSkinType=glanzend+%7c+huidGlanzend')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__huidGlanzend.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">glanzend</div></a></div></div>        </div>
			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible" >        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoe ziet het achterlijf eruit? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasAbdomenShape', '&rnax_hasAbdomenShape=afgerond+%7c+achterAfgerond')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__achterAfgerond.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">afgerond</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasAbdomenShape', '&rnax_hasAbdomenShape=puntig+%7c+achterPuntig')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__achterPuntig.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">puntig</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasAbdomenShape', '&rnax_hasAbdomenShape=staart+kort+%7c+achterStaartKort')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__achterStaartKort.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">staart kort</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasAbdomenShape', '&rnax_hasAbdomenShape=staart+lang+%7c+achterStaartLang')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__achterStaartLang.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">staart lang</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasAbdomenShape', '&rnax_hasAbdomenShape=twee+draden+%7c+achterTweeDraden')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__achterTweeDraden.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">twee draden</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasAbdomenShape', '&rnax_hasAbdomenShape=drie+draden+%7c+achterDrieDraden')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__achterDrieDraden.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">drie draden</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasAbdomenShape', '&rnax_hasAbdomenShape=soort+tang+%7c+achterSoortTang')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__achterSoortTang.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">soort tang</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasAbdomenShape', '&rnax_hasAbdomenShape=met+stekel+%7c+achterMetStekel')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__achterMetStekel.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met stekel</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasAbdomenShape', '&rnax_hasAbdomenShape=visstaart+%7c+achterVisstaart')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__achterVisstaart.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">visstaart</div></a></div></div>        </div>
			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible" >        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Welke vorm heeft het dier? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=dik+%7c+vormDik')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormDik.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">dik</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=dun+%7c+vormDun')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormDun.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">dun</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=lang+%7c+vormLang')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormLang.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">lang</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=plat+%7c+vormPlat')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormPlat.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">plat</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=rond+%7c+vormRond')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormRond.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">rond</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=driehoekig+%7c+vormDriehoekig')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormDriehoekig.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">driehoekig</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=segmenten+%7c+vormSegmenten')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormSegmenten.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">segmenten</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=bolletje+%7c+vormBolletje')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormBolletje.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">bolletje</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=twee+delen+%7c+vormTweeDelen')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormTweeDelen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">twee delen</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=drie+delen+%7c+vormDrieDelen')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormDrieDelen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">drie delen</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=met+huisje+%7c+vormMetHuisje')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormMetHuisje.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met huisje</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBodyShape', '&rnax_hasBodyShape=met+schelp+%7c+vormMetSchelp')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vormMetSchelp.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met schelp</div></a></div></div>        </div>
        </div>
        
        
	</div>
	
	<div id="facetgrouppage6-items" class="block-container middle-block-container results" style="margin-bottom:100px;">
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
				<span id='button-container'>
                    <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' class="simplebutton moreRowsButton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>
                <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
	</div>	
	
</div>    
	
<div id="facetgrouppage7" data-role="page" class="facetgrouppage">
	<div data-role='header' class='header'>	    
        <img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />        <a href="#main" data-transition="slide" data-direction="reverse" data-role="none">
            <img src="/app/webroot/img/verbeterslag/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="" />        </a>
    </div>
    <div data-role="content" class="content keuze-content" >
				
		
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible" >        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoeveel poten heeft het dier? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegAmount', '&rnax_hasLegAmount=0+%7c+potenNr0')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__potenNr0.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">0</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegAmount', '&rnax_hasLegAmount=2+%7c+potenNr2')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__potenNr2.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">2</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegAmount', '&rnax_hasLegAmount=4+%7c+potenNr4')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__potenNr4.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">4</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegAmount', '&rnax_hasLegAmount=6+%7c+potenNr6')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__potenNr6.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">6</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegAmount', '&rnax_hasLegAmount=8+%7c+potenNr8')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__potenNr8.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">8</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegAmount', '&rnax_hasLegAmount=10+%7c+potenNr10')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__potenNr10.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">10</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegAmount', '&rnax_hasLegAmount=10%2b+%7c+potenNr10Plus')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__potenNr10Plus.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">10+</div></a></div></div>        </div>
			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible" >        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoe zien de poten eruit? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=geen+poten+%7c+pootVormGeenPoten')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormGeenPoten.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geen poten</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=lang+en+dun+%7c+pootVormLangEnDun')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormLangEnDun.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">lang en dun</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=plat+%7c+pootVormPlat')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormPlat.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">plat</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=grijppoten+%7c+pootVormGrijppoten')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormGrijppoten.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">grijppoten</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=met+hoeven+%7c+pootVormMetHoeven')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormMetHoeven.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met hoeven</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=lange+tenen+%7c+pootVormLangeTenen')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormLangeTenen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">lange tenen</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=nagels+%7c+pootVormNagels')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormNagels.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">nagels</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=met+haakjes+%7c+pootVormMetHaakjes')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormMetHaakjes.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met haakjes</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=met+scharen+%7c+pootVormMetScharen')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormMetScharen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met scharen</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=behaard+%7c+pootVormBehaard')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormBehaard.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">behaard</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=zwemvliezen+%7c+pootVormZwemvliezen')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormZwemvliezen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zwemvliezen</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasLegShape', '&rnax_hasLegShape=vinnen+%7c+pootVormVinnen')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__pootVormVinnen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vinnen</div></a></div></div>        </div>
        </div>
        
        
	</div>
	
	<div id="facetgrouppage7-items" class="block-container middle-block-container results" style="margin-bottom:100px;">
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
				<span id='button-container'>
                    <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' class="simplebutton moreRowsButton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>
                <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
	</div>	
	
</div>    
	
<div id="facetgrouppage8" data-role="page" class="facetgrouppage">
	<div data-role='header' class='header'>	    
        <img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />        <a href="#main" data-transition="slide" data-direction="reverse" data-role="none">
            <img src="/app/webroot/img/verbeterslag/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="" />        </a>
    </div>
    <div data-role="content" class="content keuze-content" >
				
		
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible"  data-collapsed='false'>        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Hoe zien de vleugels eruit? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasWingShape', '&rnax_hasWingShape=naakt+%7c+huidNaakt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__huidNaakt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">naakt</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasWingShape', '&rnax_hasWingShape=geen+%7c+vleugelsGeen')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vleugelsGeen.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geen</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasWingShape', '&rnax_hasWingShape=doorzichtig+%7c+vleugelsDoorzichtig')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vleugelsDoorzichtig.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">doorzichtig</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasWingShape', '&rnax_hasWingShape=gevlekt+%7c+vleugelsGevlekt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vleugelsGevlekt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">gevlekt</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasWingShape', '&rnax_hasWingShape=gekleurd+%7c+vleugelsGekleurd')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vleugelsGekleurd.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">gekleurd</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasWingShape', '&rnax_hasWingShape=als+vlinder+%7c+vleugelsAlsVlinder')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vleugelsAlsVlinder.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">als vlinder</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasWingShape', '&rnax_hasWingShape=als+kever+%7c+vleugelsAlsKever')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vleugelsAlsKever.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">als kever</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasWingShape', '&rnax_hasWingShape=met+veren+%7c+vleugelsMetVeren')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vleugelsMetVeren.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met veren</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasWingShape', '&rnax_hasWingShape=met+huid+%7c+vleugelsMetHuid')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__vleugelsMetHuid.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">met huid</div></a></div></div>        </div>
        </div>
        
        
	</div>
	
	<div id="facetgrouppage8-items" class="block-container middle-block-container results" style="margin-bottom:100px;">
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
				<span id='button-container'>
                    <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' class="simplebutton moreRowsButton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>
                <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
	</div>	
	
</div>    
	
<div id="facetgrouppage9" data-role="page" class="facetgrouppage">
	<div data-role='header' class='header'>	    
        <img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />        <a href="#main" data-transition="slide" data-direction="reverse" data-role="none">
            <img src="/app/webroot/img/verbeterslag/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="" />        </a>
    </div>
    <div data-role="content" class="content keuze-content" >
				
		
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible"  data-collapsed='false'>        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Wat doet het dier? </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=zit+stil+%7c+gedragZitStil')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragZitStil.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zit stil</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=kruipt+%7c+gedragKruipt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragKruipt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">kruipt</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=loopt+%7c+gedragLoopt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragLoopt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">loopt</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=rent+%7c+gedragRent')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragRent.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">rent</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=springt+%7c+gedragSpringt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragSpringt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">springt</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=rolt+op+%7c+gedragRoltOp')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragRoltOp.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">rolt op</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=graaft+%7c+gedragGraaft')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragGraaft.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">graaft</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=hangt+%7c+gedragHangt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragHangt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">hangt</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=schuilt+%7c+gedragSchuilt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragSchuilt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">schuilt</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=steekt%2fbijt+%7c+gedragSteektBijt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragSteektBijt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">steekt/bijt</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=geluid+%7c+gedragGeluid')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragGeluid.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">geluid</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=vliegt+%7c+gedragVliegt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragVliegt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">vliegt</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasBehaviour', '&rnax_hasBehaviour=zwemt+%7c+gedragZwemt')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__gedragZwemt.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">zwemt</div></a></div></div>        </div>
        </div>
        
        
	</div>
	
	<div id="facetgrouppage9-items" class="block-container middle-block-container results" style="margin-bottom:100px;">
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
				<span id='button-container'>
                    <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' class="simplebutton moreRowsButton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>
                <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
	</div>	
	
</div>    
	
<div id="facetgrouppage10" data-role="page" class="facetgrouppage">
	<div data-role='header' class='header'>	    
        <img src="/app/webroot/img/verbeterslag/header_speach.png" alt="Dierenzoeker" class="logo" />        <a href="#main" data-transition="slide" data-direction="reverse" data-role="none">
            <img src="/app/webroot/img/verbeterslag/back.png" class="info-button" style="position:absolute;left:-10px;top:-4px;" alt="" />        </a>
    </div>
    <div data-role="content" class="content keuze-content" >
				
		
    	<div class="collapsible-set-wrapper">
        			
					
					
		<div data-icon-pos="right" style="border-bottom:3px solid #323232;" data-role="collapsible"  data-collapsed='false'>        
    	<h4  class="tagline left-tagline ie-rounded keuze-tagline"> Beginletter </h4>    	
<div class="ui-grid-c"><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=A+%7c+letterA')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterA.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">A</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=B+%7c+letterB')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterB.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">B</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=C+%7c+letterC')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterC.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">C</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=D+%7c+letterD')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterD.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">D</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=E+%7c+letterE')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterE.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">E</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=F+%7c+letterF')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterF.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">F</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=G+%7c+letterG')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterG.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">G</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=H+%7c+letterH')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterH.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">H</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=I+%7c+letterI')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterI.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">I</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=J+%7c+letterJ')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterJ.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">J</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=K+%7c+letterK')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterK.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">K</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=L+%7c+letterL')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterL.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">L</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=M+%7c+letterM')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterM.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">M</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=N+%7c+letterN')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterN.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">N</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=O+%7c+letterO')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterO.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">O</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=P+%7c+letterP')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterP.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">P</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=R+%7c+letterR')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterR.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">R</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=S+%7c+letterS')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterS.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">S</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=T+%7c+letterT')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterT.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">T</div></a></div><div class="facet-btn ui-block-d"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=U+%7c+letterU')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterU.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">U</div></a></div><div class="facet-btn ui-block-a"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=V+%7c+letterV')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterV.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">V</div></a></div><div class="facet-btn ui-block-b"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=W+%7c+letterW')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterW.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">W</div></a></div><div class="facet-btn ui-block-c"><a   href="#" data-role="button" data-corners="false" data-shadow="false" class="" onclick="submitFacet('rnax_hasInitialChar', '&rnax_hasInitialChar=Z+%7c+letterZ')"><div class="grid-iconbox" ><img src="/app/webroot/img/icons/__letterZ.png" class="grid-icon" alt="" /></div><div class="grid-labelbox ">Z</div></a></div></div>        </div>
        </div>
        
        
	</div>
	
	<div id="facetgrouppage10-items" class="block-container middle-block-container results" style="margin-bottom:100px;">
				<h4 id="results-tab" style="margin-left:9px;margin: 0px;margin-left:9px;padding-top: 10px;">	    	
    		<span class="ie-rounded">290 dieren gevonden</span>
    	</h4>
        <ul data-role="listview" data-inset="true" id="resultsListView" class="resultlist">
			            <li class="result0" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2I3OTA1NmY2LTIyMTEtNDliYi1hOTMzLTZhNmQ0Mjc4ZGRjMg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194832.jpg" class="result" alt="" />	                    Aalscholver	                </a>
	            </li>
	        	            <li class="result1" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E2NWY5MTU4LTI1ZTgtNGE3MC05MTIwLWFhMzQ4MTA5ODZlOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200688.jpg" class="result" alt="" />	                    Aardhommel	                </a>
	            </li>
	        	            <li class="result2" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2JjNGU0NTRmLTY3NmItNDA4My04ZWRkLTAzMjMyMDZiYzg0MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194833.jpg" class="result" alt="" />	                    Aardkruiper	                </a>
	            </li>
	        	            <li class="result3" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzI4ZGY0MTQ2LWQ2ZmEtNGNkMi05NDk2LWQ5ZmE5ODdiNDM2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/199986.jpg" class="result" alt="" />	                    Achtogige bloedegel	                </a>
	            </li>
	        	            <li class="result4" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzg4ODRiZDM2LTkxODUtNDNkMy04NWVhLTdlMjMyMjc5ODU1NQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/215299.jpg" class="result" alt="" />	                    Agaathoren	                </a>
	            </li>
	        	            <li class="result5" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E1NGU3NTdiLTIwYjItNDU2NC1hMDg2LWQ3N2I5ZjBjYTI2Yw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194834.jpg" class="result" alt="" />	                    Agaatvlinder	                </a>
	            </li>
	        	            <li class="result6" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2E3MDdhMWU3LWViZWYtNDJhZi04OTdmLTdmMTg0OTMyZGVhNA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200689.jpg" class="result" alt="" />	                    Akkerhommel	                </a>
	            </li>
	        	            <li class="result7" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzY4ODUxMzNiLThjNzMtNDg1NC1hMWYzLTk4ZmYwYWVkMDA4MQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/206870.jpg" class="result" alt="" />	                    Argusvlinder	                </a>
	            </li>
	        	            <li class="result8" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzE3Yjg2N2FiLTUxMGEtNDY5NC04NzlkLTA1OGQ1NDM2YTRkOA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194835.jpg" class="result" alt="" />	                    Atalanta	                </a>
	            </li>
	        	            <li class="result9" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2RmN2I3ZDQwLWIzZTYtNDcxYy1hOTA1LTVhMWYwY2Y0OTRjYw==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230742.jpg" class="result" alt="" />	                    Baars	                </a>
	            </li>
	        	            <li class="result10" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y5ODcxYjBjLTNhYWUtNDJhMi1hMWZmLTgwN2VkYmQwYjk5Mg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/203268.jpg" class="result" alt="" />	                    Barnsteenslak	                </a>
	            </li>
	        	            <li class="result11" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2U5M2NkY2I2LTRiZjMtNGVlYS05N2Y3LTIwMDc2N2JmZDU0OA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194836.jpg" class="result" alt="" />	                    Bergeend	                </a>
	            </li>
	        	            <li class="result12" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzhmNzA2MjE0LWJlODktNGVjOS05ZWQ2LTg1ZDE2Y2Q1NmU5Zg==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/194837.jpg" class="result" alt="" />	                    Blaaskopvlieg	                </a>
	            </li>
	        	            <li class="result13" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2Y3NGVkZjY1LWZiMDMtNDk4YS05MjI4LWY2N2IyZmRmNjU5YQ==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/230744.jpg" class="result" alt="" />	                    Blankvoorn	                </a>
	            </li>
	        	            <li class="result14" >
	            	<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2MzYWRmNDM0LTI1OWItNDI3ZS1iNjJmLTdmMTdhMTJhMjczZA==" class="resultlink" onclick="return handleResultLinkClick(this)">
	                    <img src="http://images.ncbnaturalis.nl/80x80/200690.jpg" class="result" alt="" />	                    Blauwe metselbij	                </a>
	            </li>
	        		</ul>
		
				<span id='button-container'>
                    <a href='#' data-role='button' onclick='return requestMoreRows(290, 15)' class="simplebutton moreRowsButton" style="background-color:#5a5c5f;color:white;border:none;">Volgende 15 tonen</a>
                <a href='/index.php/identify/page/faq' data-role='button' id='askNaturalis' class="simplebutton ie-rounded"  style="background-color:#5a5c5f;color:white;border:none;">Vragen?</a>
	</div>	
	
</div>    




<script language="javascript">
$(function() {
    
    var isPositionFixedSupported = true;
    if(/(iPhone|iPod|iPad)/i.test(navigator.userAgent)) { 
        if(/OS [2-4]_\d(_\d)? like Mac OS X/i.test(navigator.userAgent)) {  
            isPositionFixedSupported = false;
        } else if(/CPU like Mac OS X/i.test(navigator.userAgent)) {
            isPositionFixedSupported = false; 
        } else {
            // iOS 5 or Newer so Do Nothing
    }
    }   
    
    if (!isPositionFixedSupported) {
        // TODO bottom-bar moet nog ergens komen
        $("#bottom-bar").css("display", "none");                
    }
    
    $("#bottom-bar").touchstart(function(){
        $(this).addClass("down");        
    });
    $("#bottom-bar").touchend(function(){
        $(this).removeClass("down");
                      
    });	
    
    $("#bottom-bar").bind("click", function(ev) {
      ev.preventDefault();
      
      var targetID = "";
      if (location.hash.indexOf("facetgrouppage")!=-1) {
          targetID = location.hash+"-items";
          
      } else {
          targetID = "#results";
      }           
      var target = $(targetID).get(0).offsetTop;
                    
      $.mobile.silentScroll(target);
      
      var isiDevice = navigator.userAgent.match(/iPad|iPod|iPhone/i) != null;
      

      if (isiDevice && isPositionFixedSupported) {
          $("#bottom-bar").css("position", "relative").hide(0, function(){
              $(this).show(0).css("position", "fixed");          
          });
      }
      
      return false;
    });
    
    
    
    
});
</script>

<a href='#results' id="bottom-bar" class="menu-button-img">
	<span style='color:orange'>290</span> dieren gevonden
</a>


	<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-27823424-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script><!-- Begin Sitestat ECMA -->
<script type="text/ecmascript">
// <![CDATA[
function sitestat(u){var d=document,l=location,ns_pixelUrl=u+"&ns__t="+(new Date().getTime());u=ns_pixelUrl+"&ns_ti="+encodeURIComponent(d.title)+"&ns_jspageurl="+encodeURIComponent(l.href)+"&ns_referrer="+encodeURIComponent(d.referrer);(d.images)?new Image().src=u:d.write('<img src="'+u+'" height=1 width=1 alt="*">');};
sitestat("http://nl.sitestat.com/klo/ntr-mobiel/s?ntr.hetklokhuis.dierenzoeker.home&category=hetklokhuis&ns_webdir=hetklokhuis&ns_channel=nieuws_informatie&po_source=fixed&po_sitetype=plus&po_merk=video.zz.zappelin&ntr_genre=jeugd&pom_context=web&pom_appver=1.00&pom_appname=dierenzoeker&ns_t=1376310368");
// ]]>
</script>
<noscript>
<img src="http://nl.sitestat.com/klo/ntr-mobiel/s?ntr.hetklokhuis.dierenzoeker.home&category=hetklokhuis&ns_webdir=hetklokhuis&ns_channel=nieuws_informatie&po_source=fixed&po_sitetype=plus&po_merk=video.zz.zappelin&ntr_genre=jeugd&pom_context=web&pom_appver=1.00&pom_appname=dierenzoeker&ns_t=1376310368" height=1 width=1 alt="*" />
</noscript>
<!-- End Sitestat ECMA -->	</body>
</html>
{/literal}