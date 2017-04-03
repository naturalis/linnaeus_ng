<style>
.my_container ul
{
    list-style: initial;
    margin: initial;
    padding: 0 0 0 40px;
}

.my_container li
{
    display: list-item;
}

.my_container b
{
    font-weight:bold;
}

</style>
<script>

var ezUrl=null;
var ezData=null;
var ezSpeciesId=null
var ezLaws=[];
var ezSpeciesBaseUrl='http://minez.nederlandsesoorten.nl/node/'

$(document).ready(function()
{
	{if $external_content->template_params_decoded->EZ_speciesBaseUrl}
	ezSpeciesBaseUrl='{$external_content->template_params_decoded->EZ_speciesBaseUrl}';
	{/if}
	ezUrl = '{$external_content->full_url|@escape}';
	getData();
});

function getData()
{
	$.ajax({
		url : "../../../shared/tools/remote_service.php",
		type: "GET",
		data: ({
			url : encodeURIComponent(ezUrl),
			original_headers : 1
		}),
		success : function(data)
		{
			ezData=data;
			processData();
			printData();
		},
		complete : function( jqXHR, textStatus )
		{
			//"success", "notmodified", "nocontent", "error", "timeout", "abort", "parsererror"
			if( textStatus != "success")
			{
				$( '#ezContent' ).html( '{t}Kon webservice niet bereiken. Probeer het later opnieuw.{/t}' );
			}
		}
	});
}

function removeTags( string ) 
{
	return string.replace(/(<([^>]+)>)/ig,"");
}

function processData()
{
	for(var i in ezData)
	{
		var c=ezData[i];
		var n=true;
		for(var j in ezLaws)
		{
			var l=ezLaws[j];
			if (l && l.wet==c.wet)
			{
				l.items.push( { categorie: removeTags(c.categorie), publicatie:removeTags(c.publicatie) } );
				n=false;
			}
		}
		if (n)
		{
			var d=[];
			d.push( { categorie: removeTags(c.categorie), publicatie:removeTags(c.publicatie) } );
			ezLaws.push( { wet:c.wet,items:d } );
		}
		ezSpeciesId=c.soort_id;
	}
}

function printData()
{
	
	if (ezLaws.length==0)
	{
		$( '#ezContent' ).html( '{t}Geen gegevens gevonden.{/t}' );
		return;
	}
	
	var buffer=[];
	for(var i=0;i<ezLaws.length;i++)
	{
		var c=ezLaws[i];
		var buffer2=[];
		for(var j=0;j<c.items.length;j++)
		{
			var c2=c.items[j];
			buffer2.push(
				fetchTemplate( 'itemTpl' )
					.replace('%CATEGORY%',c2.categorie)
					.replace('%PUBLICATION%',c2.publicatie)
			);
		}
		
		buffer.push(
			fetchTemplate( 'lawTpl' )
				.replace('%ITEMS%',buffer2.join("\n"))
				.replace('%LAW%',c.wet)
		);
	}
	
	$('#ezContent').html(
		fetchTemplate( 'conventionTpl' )
			.replace('%LAWS%',buffer.join("\n"))
			.replace('%BASE_URL%',ezSpeciesBaseUrl)
			.replace('%SPECIES_ID%',ezSpeciesId)
	);
	
}

</script>


<div class="my_container">

<h2 class="remote-content">{t}Beschermingsstatus{/t}</h2>

<div class="remote-content" id="ezContent">
</div>

{if $content}
<h2>{t}Bedreiging en bescherming{/t}</h2>
<p>
    {$content}
</p>
{/if}

</div>

<div class="inline-templates" id="conventionTpl">
<!--
<p>
    <ul>
	    %LAWS%
    </ul>
    <br />
    {t}Bron:{/t} <a href="%BASE_URL%%SPECIES_ID%">{t}soortgegevens{/t}</a> uit Beschermde natuur van Nederland: soorten in wetgeving en beleid (Ministerie van Economische Zaken)
</p>
-->
</div>

<div class="inline-templates" id="lawTpl">
<!--
    <li>
        <b>%LAW%</b>
        <ul>
            %ITEMS%
        </ul>
    </li>
-->
</div>

<div class="inline-templates" id="itemTpl">
<!--
    <li>
        %CATEGORY%<br />
        %PUBLICATION%
    </li>
-->
</div>
