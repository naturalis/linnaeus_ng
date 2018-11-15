<script>

var rawInputs=[];
var baseurl;
var results=[];
var maxresults={if $external_content->template_params_decoded->max_results}{$external_content->template_params_decoded->max_results}{else}5{/if};
var totresults=0;
var sciName="";
var nomen="";
var synonyms=Array()
var nameParts=Array()
var query="";

$(document).ready(function()
{
	{foreach item=v from="\n"|explode:$external_content->full_url}
	rawInputs.push('{$v|@trim}');
    {/foreach} 

    processRawInputs();
    buildQuery();
	getSpecimens();
	printBioPortalSearchTerm();
});

function localPrettyPhotoInit() {

	$('[data-fancybox]').fancybox({
		arrows : false,
		infobar : true,
		animationEffect : false
	});

}

function processRawInputs()
{
	for(var i=0;i<rawInputs.length;i++)
	{
		if (rawInputs[i].indexOf('sciName=')!=-1)
		{
			sciName=decodeURIComponent(rawInputs[i].replace('sciName=','')).replace(/\+/g,' ');
		}
		else
		if (rawInputs[i].indexOf('nomen=')!=-1)
		{
			nomen=decodeURIComponent(rawInputs[i].replace('nomen=','')).replace(/\+/g,' ');
		}
		else
		if (rawInputs[i].indexOf('synonyms=')!=-1)
		{
			synonyms=$.parseJSON(decodeURIComponent(rawInputs[i].replace('synonyms=','').replace(/\+/g,' ')));
			for(var j=0;j<synonyms.length;j++)
			{
				synonyms[j]['nomen']=synonyms[j]['name'].replace(synonyms[j]['authorship'],'').trim();
			}
			//console.log( synonyms );
		}
		else
		if (rawInputs[i].indexOf('nameParts=')!=-1)
		{
			nameParts=$.parseJSON(decodeURIComponent(rawInputs[i].replace('nameParts=','').replace(/\+/g,' ')));
		}
		else
		if (rawInputs[i].length>0)
		{
		    baseurl=rawInputs[i];
		}
	}
}

function buildQuery()
{

	var s="";
	if (nameParts.infra_specific_epithet)
	{
		s=fetchTemplate( 'nbaSubQueryTpl' ).replace(/%INFRA_SPECIFIC_EPITHET%/g,nameParts.infra_specific_epithet)
	}

	query=fetchTemplate( 'nbaQueryTpl')
		.replace(/%UNINOMIAL%/g,nameParts.uninomial)
		.replace(/%SPECIFIC_EPITHET%/g,nameParts.specific_epithet)
		.replace('%INFRA_SPECIFIC_EPITHET_CLAUSE%',s);
}

function getSpecimens()
{
	//console.log(baseurl + encodeURIComponent( query ) );
	$.ajax({
		url : "../../../shared/tools/remote_service.php",
		type: "POST",
		data: ({
			url : encodeURIComponent( baseurl + encodeURIComponent( query ) ),
			original_headers : 1
		}),
		success : function(data)
		{
			//console.log(data);
			basedata=data;
			processBaseData( );
			printBaseData();
			localPrettyPhotoInit();
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

function processBaseData()
{
	var data=basedata;
	if (!data.resultSet) return;

	totresults+=data.totalSize;
	
	for(var i=0; i<data.resultSet.length; i++)
	{
		var r=data.resultSet[i].item;

		if (!r.associatedMultiMediaUris || r.associatedMultiMediaUris.length==0) continue;

		results.push({
			specimen: {
				unitID: r.unitID,
				PURL: r.unitGUID,
				sourceSystem: r.sourceSystem, 
				recordBasis: r.recordBasis, 
				kindOfUnit: r.kindOfUnit, 
			},
			image: {
				title: r.associatedMultiMediaUris[0].variant,
				imgUrl: r.associatedMultiMediaUris[0].accessUri
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
	
		var imageMetaData = fetchTemplate( 'imageMetaDataTpl' )
				.replace(/%UNIT-ID%/g,r.specimen.unitID)
				.replace('%COLLECTION%',r.specimen.sourceSystem.name)
				.replace('%RECORD-BASIS%',r.specimen.recordBasis)
				.replace('%KIND_UNIT%',r.specimen.kindOfUnit ? ": " + r.specimen.kindOfUnit : "" );
		
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
				.replace('%meta_data%',imageMetaData.replace('"','\"'))
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

	{if !isset($external_content->template_params_decoded->show_bioportal_link) || $external_content->template_params_decoded->show_bioportal_link==1}
	$('#result-footer').html( fetchTemplate( 'moreResultsTpl' ) );
	{/if}

}

function printBioPortalSearchTerm()
{
	$('#theForm2').attr('action',
		$('#theForm2').attr('action')
			.replace(/%UNINOMIAL%/g,encodeURIComponent(nameParts.uninomial))
			.replace(/%SPECIFIC_EPITHET%/g,encodeURIComponent(nameParts.specific_epithet))
			.replace(/%INFRA_SPECIFIC_EPITHET%/g,encodeURIComponent(nameParts.infra_specific_epithet ? nameParts.infra_specific_epithet : "" ))
	);
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
	<a href="#" onclick="$('#theForm2').submit();return false;">{t}Bekijk meer resultaten in de Naturalis Bioportal{/t}</a>
-->
</div>


<div class="inline-templates" id="remarkTpl">
<!--
    <span style="display:inline-block;margin-top:3px;">{t}Dit is een selectie uit de gedigitaliseerde museumobjecten van Naturalis Biodiversity Center. Deze selectie geeft niet per se een compleet beeld.{/t}</span>
-->
</div>

<div class="inline-templates" id="specimenRowTpl">
<!--
<div style="margin-bottom:20px;margin-right:2px;float:left;padding:2px;">
	<div class="thumbContainer" style="margin-bottom:2px;">
		<a class="fancybox" data-caption="%meta_data%" href="%IMG-SRC%/%UNIT-ID%.jpg" data-fancybox="gallery">
			<img class="speciesimage" src="%THUMB-SRC%" title="%UNIT-ID%" alt="%UNIT-ID%" style="max-width:400px;max-height:200px;">
		</a>
	</div>
	<b><a href="%PURL%" target="_blank">%UNIT-ID%</a></b><br>
	<span style="font-size:0.9em">
	%COLLECTION%<br />
	%RECORD-BASIS%%KIND_UNIT%
	</div>
</div>
-->
</div>

<div class="inline-templates" id="imageMetaDataTpl">
<!--
	<b>%UNIT-ID%</b><br>
	%COLLECTION%<br />
	%RECORD-BASIS%%KIND_UNIT%
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

<div class="inline-templates" id="nbaQueryTpl">
<!--
{
    "conditions": [
        {
            "field": "identifications.defaultClassification.genus",
            "operator": "STARTS_WITH_IC",
            "value": "%UNINOMIAL%",
            "boost": 2,
            "or": [
                {
                    "field": "identifications.scientificName.genusOrMonomial",
                    "operator": "STARTS_WITH_IC",
                    "value": "%UNINOMIAL%",
                    "boost": 2
                }
            ]
        },
        {
            "field": "identifications.defaultClassification.specificEpithet",
            "operator": "STARTS_WITH_IC",
            "value": "%SPECIFIC_EPITHET%",
            "boost": 2,
            "or": [
                {
                    "field": "identifications.scientificName.specificEpithet",
                    "operator": "STARTS_WITH_IC",
                    "value": "%SPECIFIC_EPITHET%",
                    "boost": 2
                }
            ]
        },
        {
            "field": "sourceSystem.code",
            "operator": "EQUALS",
            "value": "BRAHMS",
            "or": [
                {
                    "field": "sourceSystem.code",
                    "operator": "EQUALS",
                    "value": "CRS"
                },
                {
                    "field": "sourceSystem.code",
                    "operator": "EQUALS",
                    "value": "NSR"
                },
                {
                    "field": "sourceSystem.code",
                    "operator": "EQUALS",
                    "value": "COL"
                }
            ]
        },
    	{ "field" : "associatedMultiMediaUris.variant", "operator" : "NOT_EQUALS" }
		%INFRA_SPECIFIC_EPITHET_CLAUSE%
    ],
    "logicalOperator": "AND",
    "size": 10
}
-->
</div>


<div class="inline-templates" id="nbaSubQueryTpl">
<!--
    ,{
        "field": "identifications.defaultClassification.infraspecificEpithet",
        "operator": "STARTS_WITH_IC",
        "value": "%INFRA_SPECIFIC_EPITHET%",
        "boost": 2,
        "or": [
            {
                "field": "identifications.scientificName.infraspecificEpithet",
                "operator": "STARTS_WITH_IC",
                "value": "%INFRA_SPECIFIC_EPITHET%",
                "boost": 2
            }
        ]
    }
-->
</div>


<!-- form id="theForm" method="post" target="_blank" action="http://bioportal.naturalis.nl/nba/result" -->
<form id="theForm2" method="post" target="_blank" action="{if $external_content->template_params_decoded->bioportal_server}{$external_content->template_params_decoded->bioportal_server}{else}http://bioportal.naturalis.nl{/if}/result?s_andOr=0&s_scientificName=&s_vernacularName=&s_family=&s_genusOrMonomial=&s_specificEpithet=&s_unitID=&s_sourceSystem=&s_collectionType=&s_typeStatus=&s_localityText=&s_phaseOrStage=&s_sex=&s_gatheringAgent=&s_collectorsFieldNumber=&s_kingdom=&s_phylum=&s_className=&s_order=&s_infraspecificEpithet=&t_andOr=0&t_scientificName=&t_vernacularName=&t_family=&t_genusOrMonomial=&t_specificEpithet=&t_sourceSystem=&t_kingdom=&t_phylum=&t_className=&t_order=&t_subgenus=&t_infraspecificEpithet=&m_andOr=0&m_scientificName=&m_vernacularName=&m_family=&m_genusOrMonomial=%UNINOMIAL%&m_specificEpithet=%SPECIFIC_EPITHET%&m_sourceSystem=&m_collectionType=&m_kingdom=&m_phylum=&m_className=&m_order=&m_infraspecificEpithet=%INFRA_SPECIFIC_EPITHET%&form_build_id=form-teqqTEEmv1dgojD7ILQ-g3Qf_JcN8BN6OSZy9w-y0rs&form_id=ndabio_advanced_taxonomysearch">
</form>

<span id="page-log" style="display:none;">
</span>