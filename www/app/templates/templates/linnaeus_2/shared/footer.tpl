	<div id="footer">
	&#160;
	</div>
</div>
<div id="allLookupList" class="allLookupListInvisible"></div>

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
{/literal}
//	chkPIDInLinks({$session.app.project.id},'{$addedProjectIDParam}');
})
{literal}
</script>
{/literal}

</body>
</html>