</div ends="page-container">
<div id="footer-container">

{if $GitVars->commit->hash}
<div class="git-info">
commit <span title="{$GitVars->commit->hash}">{$GitVars->commit->hash_short}</span> ({$GitVars->commit->date}) ({$GitVars->branch} branch @ {$server_name})
</div>
{/if}

</div ends="footer-container">
</div ends="body-container">
<div id="loadingdiv" style="background-image:url(../../media/system/ajax-loader.gif);" class="loadingdiv-invisible"></div>
<span id="dummy-element"></span>
<div id="allLookupList" class="allLookupList"></div>

<script type="text/JavaScript">
var hasSearchResults=false;
</script>

<script type="text/JavaScript">

$(document).ready(function()
{
	if(jQuery().prettyPhoto) {
	 	$("a[rel^='prettyPhoto']").prettyPhoto({
			animation_speed:50,
	 		opacity: 0.70, 
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false
	 	});
	}

	if (hasSearchResults===true)
	{
	
		function positionSearchFloat(pos) {
			if(!pos || pos[0]<0 || pos[1]<0) pos=[115,568];
			$('#search-float').css( { top:pos[0]+'px',left:pos[1]+'px' } );
		}
		var pos=allGetSomething('search-float-position',positionSearchFloat);
		$('#search-float').css('visibility','visible');

		$('#search-float').draggable( { stop: function(event, ui)
		{
			var pos = $(this).position();
			allSetSomething('search-float-position',[pos.top,pos.left]);
		}, cancel:'#werwerwerwe'});
	}
	
})

</script>


</body>
</html>

