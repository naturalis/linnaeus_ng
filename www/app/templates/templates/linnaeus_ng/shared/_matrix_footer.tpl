</div>
<div class="footerContainer">
	<div id="footer">
        <span class="copyright"><a href="https://science.naturalis.nl/en/ict/products/linnaeus-ng/">Naturalis Biodiversity Center</a>
        </span>
	</div>
</div>

<div id="jDialog" title="" class="ui-helper-hidden"></div>
<div id="tmpcontent" title="" class="ui-helper-hidden"></div>

<script type="text/JavaScript">
$(document).ready(function()
{
	if(jQuery().prettyPhoto)
	{
		prettyPhotoInit();
	}

	$('img').bind('contextmenu',function(e)
	{
		e.preventDefault();
	});	
})
</script>

{snippet}{"google_analytics-`$smarty.server.SERVER_NAME`.html"}{/snippet}

{include file="../shared/_lng_productie_google_analytics.tpl"}

</div>
</body>
</html>
