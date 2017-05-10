{*

	make a tab with the external URL:
    http://api.biodiversitydata.nl/v0/multimedia/search/?scientificName=%SCI-NAME%
    http://api.biodiversitydata.nl/v0/multimedia/search/?scientificName=%NOMEN%

    add the substitute:
    %NOMEN% --> nomen
    %SCI-NAME% --> scientific name
    
    choose "link or embed"-option
    embed parametrized URL only (no content, better performance)
    
    and as template:
    _tab_ext_nba_collection.tpl
    (this file)

*}

<script>

$(document).ready(function()
{
	{foreach item=v from="\n"|explode:$external_content->full_url}
	urls.push('{$v|@trim}');
    {/foreach} 
	
	baseurl = urls.shift();
	getSpecimens();
});

var baseurl;
//var basedata={};
var results=[];
var maxresults=5;
var totresults=0;
var urls=[];
var searchName="";

function getSpecimens()
{
	$.ajax({
		url : "../../../shared/tools/remote_service.php",
		type: "POST",
		data: ({
			url : encodeURIComponent(baseurl),
			original_headers : 1
		}),
		success : function(data)
		{
			//basedata=data;
			processBaseData( data );
			checkForExtraUrls();
		},
		complete : function( jqXHR, textStatus )
		{
			//"success", "notmodified", "nocontent", "error", "timeout", "abort", "parsererror"
			if( textStatus != "success" && results.length==0 )
			{
				$( '#results' ).html( fetchTemplate( 'errorOccurredTpl' ).replace( '%STATUS%', textStatus ) );
				$( '.nba-source' ).toggle( false );
			}
		}
	});
}

var searches=[];

function processBaseData( data )
{
	//console.log(baseurl);
	//console.dir(data);
	
	searches.push( { url:baseurl, search:data.queryParameters.scientificName[0], size:0 } );
	
	if (!data.searchResults) return;
	
	totresults+=data.totalSize;
	
	searches[searches.length-1].size=data.totalSize;
	
	for(var i=0; i<data.searchResults.length; i++)
	{
		var r=data.searchResults[i].result;

		if (!r.associatedSpecimen || r.associatedSpecimen.unitID.length==0) continue;

		if (!searches[searches.length-1].name)
		{
			searches[searches.length-1].name=r.associatedSpecimen.identifications[0].scientificName.fullScientificName;
		}
	
		results.push({
			specimen: {
				unitID: r.associatedSpecimen.unitID,
				PURL: r.associatedSpecimen.unitGUID,
				sourceSystem: r.associatedSpecimen.sourceSystem, 
				recordBasis: r.associatedSpecimen.recordBasis, 
				kindOfUnit: r.associatedSpecimen.kindOfUnit, 
			},
			image: {
				title: r.title,
				imgUrl: r.serviceAccessPoints.MEDIUM_QUALITY.accessUri
			}
		});
	}
}

function printBaseData()
{
	var rowTpl = fetchTemplate( 'specimenRowTpl' ); 
	
	if (results.length==0)
	{
		$('.nba-data').toggle();
		return;
	}
	
	var buffer=[];
	
	for(var i=0; i<results.length; i++)
	{
		if (i>=maxresults) break;

		var r=results[i];
		buffer.push(
			rowTpl
				.replace('%PURL%',r.specimen.PURL)
				.replace(/%UNIT-ID%/g,r.specimen.unitID)
				.replace('%COLLECTION%',r.specimen.sourceSystem.name)
				.replace('%RECORD-BASIS%',r.specimen.recordBasis)
				.replace('%KIND_UNIT%',r.specimen.kindOfUnit ? ": " + r.specimen.kindOfUnit : "" )
				.replace(/%IMG-SRC%/g,r.image.imgUrl)
				.replace(/%THUMB-SRC%/g,r.image.imgUrl.replace('large','medium'))
				.replace('%REL%','prettyPhoto[gallery]')
		);
	}

	var header="";

	if (results.length>maxresults)
	{
		header=fetchTemplate( 'resultHeaderTpl' )
			.replace('%SHOWING%', maxresults)
			.replace('%TOTAL%', totresults);
	}
	else
	{
		header=fetchTemplate( 'resultHeaderMinTpl' )
			.replace('%SHOWING%', results.length);
	}

	header=header+fetchTemplate( 'remarkTpl' );

	$('#results').html( buffer.join( "\n" ) );
	$('#result-header').html( header );
	$('#result-footer').html( fetchTemplate( 'moreResultsTpl' ) );

}

function checkForExtraUrls()
{
	if (baseurl = urls.shift())
	{
		getSpecimens();
	}
	else
	{
		printBaseData();
		printBioPortalSearchTerm();
		printLogInfo();
	}
}

function printBioPortalSearchTerm()
{
	var s=0;
	for(var i=0;i<searches.length;i++)
	{
		if (searches[i].size>s)
		{
			searchName=searches[i].search;
			s=searches[i].size;
		}
	}
	$('.searchName').val(searchName);
}

function printLogInfo()
{
	var d=[];
	for(var i=0;i<searches.length;i++)
	{
		d.push("  &#149; " + searches[i].search + ": " + searches[i].size + "\n    (" + searches[i].url + ")");
	}

	$('#page-log').html( fetchTemplate( 'logTpl' ).replace('%URLS%',d.join("\n")).replace('%BIOP%',searchName) );
}




</script>

<p>
    <h2 id="name-header">{$requested_category.title}</h2>

    {if $content}
    <p>{$content}</p>
    {/if}
    
    <div class="nba-data" style="display:none">
    {t}Geen collectie-exemplaren gevonden in de{/t} <a href="http://bioportal.naturalis.nl/" target="_new">Naturalis Bioportal</a>.
    </div>
    
    <div id="result-header" style="margin-bottom:10px;">
    </div>
    <div id="results" class="nba-data" style="margin-bottom:10px;">
    </div>
    <br clear="all" />
    <div id="result-footer" style="margin-bottom:10px;">
    </div>

</p>

<!-- templates -->

<div class="inline-templates" id="resultHeaderTpl">
<!--
	<span style="font-weight:bold">{t}%SHOWING% van %TOTAL% resultaten{/t}</span>
-->
</div>

<div class="inline-templates" id="resultHeaderMinTpl">
<!--
	<span style="font-weight:bold">{t}%SHOWING% resultaten{/t}</span>
-->
</div>

<div class="inline-templates" id="moreResultsTpl">
<!--
	<a href="#" onclick="$('#theForm').submit();return false;">{t}Bekijk meer resultaten in de Naturalis Bioportal{/t}</a>
-->
</div>


<div class="inline-templates" id="remarkTpl">
<!--
    <span style="display:inline-block;margin-top:3px;">{t}Dit is een selectie uit de gedigitaliseerde museumobjecten van Naturalis Biodiversity Center. Deze selectie geeft niet per se een compleet beeld.{/t}</div>
-->
</div>

<div class="inline-templates" id="specimenRowTpl">
<!--
<div style="margin-bottom:20px;margin-right:2px;float:left;padding:2px;">
	<div class="thumbContainer" style="margin-bottom:2px;">
		<a class="fancybox" ptitle="<div></div>" href="%IMG-SRC%/%UNIT-ID%.jpg" data-fancybox-group="gallery">
			<img class="speciesimage" src="%THUMB-SRC%" title="%UNIT-ID%" alt="%UNIT-ID%" style="max-width:400px;max-height:200px;">
		</a>
	</div>
    <b><a href="%PURL%" target="_blank">%UNIT-ID%</a></b><br>
    <span style="font-size:0.9em">
    %COLLECTION%<br />
    %RECORD-BASIS%%KIND_UNIT%
    </span>
</div>
-->
</div>

<div class="inline-templates" id="errorOccurredTpl">
<!--
	{t}An error occurred (%STATUS%).{/t} <a href="" onclick="$(this).html('{t}Reloading...{/t}');return true;" target=_top>{t}Reload page{/t}</a>.
-->
</div>

<div class="inline-templates" id="logTpl">
<!--
searches:
%URLS%

linked bioportal search:
  m_scientificName="%BIOP%"
-->
</div>


<form id="theForm" method="post" target="_blank" action="http://bioportal.naturalis.nl/nba/result">
<input type="hidden" name="form_id" value="ndabio_advanced_taxonomysearch" />
<input type="hidden" name="m_scientificName" class="searchName" id="searchName" value="{$names.nomen_no_formatting}" />
</form>

<span id="page-log" style="display:none;">
</span>