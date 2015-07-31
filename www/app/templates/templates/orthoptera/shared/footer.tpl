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
	fetch_footer();
	allLookupAlwaysFetch=true;
});


</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-21555206-5', 'auto');
  ga('send', 'pageview');
</script>
</body>
</html>