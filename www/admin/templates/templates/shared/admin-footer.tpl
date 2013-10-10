</div ends="page-container">
<div id="footer-container">
</div ends="footer-container">
</div ends="body-container">
<div id="loadingdiv" style="background-image:url(../../media/system/ajax-loader.gif);" class="loadingdiv-invisible"></div>
<span id="dummy-element"></span>
<div id="allLookupList" class="allLookupList"></div>

{if $userSearch && 1==2}
<div id="search-float" style="width:350px;height:450px;overflow-y:scroll;overflow-x:hidden;border:1px solid #666;background-color:#fff;position:absolute;visibility:hidden">
	<div id="search-float-header" style="width:100%;background-color:#eee;padding:10px 4px 10px 4px;cursor:move;">search: <span class="searched-term">{$userSearch.term}</span></div>
	<div id="werwerwerwe" style="padding:4px;">
	{foreach from=$userSearch.results.data item=v}
		{if $v.numOfResults>0}
			{foreach from=$v.results item=r}
				{if $r.numOfResults>0}
					<b>{$r.label} ({$r.data|@count})</b><br />
					{foreach from=$r.data item=d}
						<a href="{$r.url|sprintf:$d.id}">{$d.label}</a> ({$d.matches|@count})
						<ul style="margin-top:0;padding:0;list-style:inside;list-style-type:none;">
						{foreach from=$d.matches item=match}
						<li style="font-size:9px;white-space:nowrap;">{$match}</li>
						{/foreach}
						</ul>
					{/foreach}
				{/if}
			{/foreach}
		{/if}
	{/foreach}
	</div>
</div>
<script type="text/JavaScript">
var hasSearchResults=true;
</script>
{/if}

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	if(jQuery().prettyPhoto) {
	 	$("a[rel^='prettyPhoto']").prettyPhoto({
			animation_speed:50,
	 		opacity: 0.70, 
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false
	 	});
	}
	if (hasSearchResults===true) {
	
		function positionSearchFloat(pos) {
			if(!pos || pos[0]<0 || pos[1]<0) pos=[115,568];
			$('#search-float').css({top:pos[0]+'px',left:pos[1]+'px'});
		}
		var pos=allGetSomething('search-float-position',positionSearchFloat);
		$('#search-float').css('visibility','visible');

		$('#search-float').draggable({stop: function(event, ui) {
			var pos = $(this).position();
			allSetSomething('search-float-position',[pos.top,pos.left]);
		}, cancel:'#werwerwerwe'});
	}
	
})
</script>
{/literal}

</body>
</html>

