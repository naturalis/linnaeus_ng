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
	$( '#this_map' ).attr('src',result.distributionmap);
}

$(document).ready(function()
{
	parsexml($('#raw_output').html());
});
</script>
<div id=raw_output style="display:none">{$external_content->content_raw}</div>
<img id=this_map src="" />
