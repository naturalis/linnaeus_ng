</div ends="page-container">
<div id="footer-container">
</div ends="footer-container">
</div ends="body-container">
<div id="loadingdiv" style="background-image:url(../../media/system/ajax-loader.gif);" class="loadingdiv-invisible"></div>
<span id="dummy-element"></span>
<div id="allLookupList" class="allLookupList"></div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	if(jQuery().prettyPhoto) {
	 	$("a[rel^='prettyPhoto']").prettyPhoto({
	 		opacity: 0.70, 
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false
	 	});
	}
})
</script>
{/literal}

</body>
</html>

