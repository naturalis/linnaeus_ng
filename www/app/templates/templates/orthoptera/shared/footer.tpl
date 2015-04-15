<script>

function fetch_footer()
{
	var url="http://"+(document.domain)+"/about";
	$.get( url, function( data )
	{
		parse_footer( data );
	});
}

function parse_footer( data )
{
	var div="<div id='footer'>";
	var page="<!--/.page -->";
	r=data.split( div );
	r=r[1].split( page );
	$( "body" ).append( div.concat( r[0] ) );
}

$(document).ready(function()
{
	allLookupAlwaysFetch=true;
});

</script>
</body>
</html>