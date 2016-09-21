{*

	make a tab with the external URL:
    http://api.biodiversitydata.nl/v0/multimedia/search/?scientificName=%NOMEN%

    add the substitute:
    %NOMEN% --> nomen
    
    choose "link or embed"-option
    embed parametrized URL only (no content, better performance)
    
    and as template:
    _nba_collection.tpl
    (this file)

*}

<script>
var baseurl;
var basedata={};
var results=[];
var maxresults=5;

function getSpecimens()
{
	$.ajax({
		url : "/linnaeus_ng/shared/tools/remote_service.php",
		type: "POST",
		data: ({
			url : encodeURIComponent(baseurl),
			original_headers : 1
		}),
		success : function(data)
		{
			basedata=data;
			processBaseData();
			printBaseData();
		}
	});
}

function processBaseData()
{
	
	if (!basedata.searchResults) return;
	
	for(var i=0; i<basedata.searchResults.length; i++)
	{
		var r=basedata.searchResults[i].result;

		if (!r.associatedSpecimen || r.associatedSpecimen.unitID.length==0) continue;

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
				.replace('%KIND_UNIT%',r.specimen.kindOfUnit)
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
			.replace('%TOTAL%', results.length)
	}

		header=fetchTemplate( 'resultHeaderTpl' )
			.replace('%SHOWING%', maxresults)
			.replace('%TOTAL%', results.length)
	
	urlHeaderTpl
	
	$('#results').html( header + buffer.join( "\n" ) );
	
	prettyPhotoInit();
	
}

function prettyPhotoInit()
{
	if(jQuery().prettyPhoto)
	{
		$("a[rel^='prettyPhoto']").prettyPhoto({
			allow_resize:true,
			animation_speed:50,
			opacity: 0.70, 
			show_title: false,
			overlay_gallery: false,
			social_tools: false
		});
	}
}


</script>

<p>

    <h2 id="name-header">{$requested_category.title}</h2>
    <p style="display:none"><code id=url></code></p>

    {if $content}
    <p>{$content}</p>
    {/if}
    
    <div class="nba-data" style="display:none">
    {t}Geen collectie-exemplaren gevonden in de{/t} <a href="http://bioportal.naturalis.nl/" target="_new">Naturalis Bioportal</a>.
    </div>
    
    <div id="results" class="nba-data">
    </div>

    <div class="nba-data">
    {t}Source:{/t} <a href="http://bioportal.naturalis.nl/" target="_new">Naturalis Bioportal</a>.
    </div>


</p>

<script>
$(document).ready(function()
{
	baseurl = '{$external_content->full_url|@escape}';
	$( '#url' ).html( baseurl );
	getSpecimens();
//	nbcPrettyPhotoInit();
	
	
	
});
</script>
<p style="display:none"><code id=url></code></p>


<div class="inline-templates" id="resultHeaderTpl">
<!--
	<p><b>{t}Showing %SHOWING% of %TOTAL% specimen{/t}</b></p>
-->
</div>

<div class="inline-templates" id="urlHeaderTpl">
<!--
	<p><a href="%URL%" target=_blank>see all</a></p>
-->
</div>

<div class="inline-templates" id="specimenRowTpl--org">
<!--
	<div class="thumbContainer">
	    <a class="fancybox" ptitle="%UNIT-ID%" href="%IMG-SRC%" rel="%REL%">
		<img class="speciesimage" src="%IMG-SRC%" style="max-width:120px;max-height:70px;" />
        </a>
		<strong><a href="%PURL%" target="_blank">%UNIT-ID%</a></strong>
		<br>
		%COLLECTION%
		<br>
		%RECORD-BASIS%: %KIND_UNIT%
	</div>
-->
</div>

<div class="inline-templates" id="specimenRowTpl">
<!--
<div style="margin-bottom:20px">
	<div class="thumbContainer">
		<a class="fancybox" ptitle="<div></div>" href="%IMG-SRC%/%UNIT-ID%.jpg" data-fancybox-group="gallery">
			<img class="speciesimage" src="%THUMB-SRC%" title="%UNIT-ID%" alt="%UNIT-ID%" style="max-width:400px;max-height:200px;">
		</a>
	</div>
    <b><a href="%PURL%" target="_blank">%UNIT-ID%</a></b><br>
    %COLLECTION%<br />
    %RECORD-BASIS%: %KIND_UNIT%
</div>
-->
</div>
