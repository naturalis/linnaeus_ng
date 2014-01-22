<div id="allLookupList" class="allLookupListInvisible"></div>
</div ends="page-container">

{if $controllerMenuOverride}
    {include file=$controllerMenuOverride}
{else}
    {if $controllerMenuExists}
        {if $controllerBaseName}{include file="../"|cat:$controllerBaseName|cat:"/_menu.tpl"}{else}{include file="_menu.tpl"}{/if}
    {/if}
{/if}



</div>
<!-- /form -->
</div>

<div id="footer">
	<div id="footer-container">
		<div> 
			<img id="logo" src='{$projectUrls.systemMedia}logo_lng.png'>
		</div>
		<div>
		<div>
			Powered by Linnæus NG
		</div>
	</div>
</div>

<div id="bottombar" class="navbar navbar-inverted">
	<div class="container">
		<p class="navbar-text navbar-right">
			<a href="http://www.naturalis.nl"> 
				© Naturalis Biodiversity Center
			</a>
		</p>
	</div>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	if(jQuery().prettyPhoto) {
	 	$("a[rel^='prettyPhoto']").prettyPhoto({
	 		opacity: 0.70, 
			animation_speed:50,
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false
	 	});
	}
{/literal}
	{if $search}onSearchBoxSelect('{$search|@addslashes}');{/if}
	{foreach from=$requestData key=k item=v}
	addRequestVar('{$k}','{$v|addslashes}')
	{/foreach}
	chkPIDInLinks({$session.app.project.id},'{$addedProjectIDParam}');
	{if $searchResultIndexActive}
	searchResultIndexActive = {$searchResultIndexActive};
	{/if}
				
})
{literal}
</script>
{/literal}

</body>
</html>