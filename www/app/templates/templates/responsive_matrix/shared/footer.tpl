</div>
<div class="footerContainer">
	<div id="footer">
		<div id="footerInfo">
			<span class="copyright">© Naturalis en partners</span>
			<a href="admin/views/users/login.php" class="adminLink">
                <span class="powered">Powered by</span>
                <img src="{$session.app.system.urls.systemMedia}lng.png" id="lng-logo">
                <span class="linnaeus">Linnaeus NG</span>
            </a>
		</div>
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

</div>
</body>
</html>
