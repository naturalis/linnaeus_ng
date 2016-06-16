<script type="text/javascript" src="../../../app/javascript/jquery-1.9.1.min.js"></script>
<script>
function parsexml( data )
{
	xml = $( $.parseXML( data ) );
	result={
		content: xml.find( "tab>content" ).text(),
		author: xml.find( "tab>author" ).text(),
		pubdate: xml.find( "tab>pubdate" ).text(),
		copyright: xml.find( "tab>copyright" ).text(),
		sourcedocument: xml.find( "tab>sourcedocument" ).text(),
		distributionmap: xml.find( "tab>distributionmap" ).text()
	}

	console.dir(result);

	$( '#this_content' ).html( result.content.replace("<![CDATA[", "").replace("]]>", "") );

	if (result.distributionmap.length>0)
		$( '#this_map' ).attr( 'src', result.distributionmap );
	else
		$( '.this_map' ).toggle( false );
	
	if (result.author.length>0)
		$( '#this_author' ).html( result.author );
	else
		$( '.this_author' ).toggle( false );
	
	if (result.sourcedocument.length>0)
		$( '#this_sourcedocument' ).attr( 'href', result.sourcedocument );
	else
		$( '.this_sourcedocument' ).toggle( false );

}

$(document).ready(function()
{
	parsexml($('#raw_output').html());
});
</script>

<div id="atlasdata">

    <p id="this_content"></p>

	<span class="this_map">
        <h2>{t}Verspreidingskaart{/t}</h2>
        <p>
            <img id="this_map" class="verspreidingskaart" src="" />
        </p>
	</span>

	<span class="this_author">
        <h2>{t}Bron{/t}</h2>
        <p>
            <h4 class="source">{t}Auteur(s){/t}</h4>
            <span id="this_author"></span>
        </p>
	</span>
    
    <p class="sourcedocument">
    <a id="this_sourcedocument" href="" target="_blank">{t}Meer over deze soort in de BLWG Verspreidingsatlas{/t}</a>
    </p>

</div>


<div id=raw_output style="display:none">{$external_content->content_raw}</div>