<div id="allLookupList" class="allLookupListInvisible"></div>
</div>

{if $controllerMenuOverride}
    {include file=$controllerMenuOverride}
{else}
    {if $controllerMenuExists}
        {if $controllerBaseName}{include file="../"|cat:$controllerBaseName|cat:"/_menu.tpl"}{else}{include file="_menu.tpl"}{/if}
    {/if}
{/if}


<div id="footer-container"></div>

</div>
<!-- /form -->
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
	{if !$v|strstr:"javascript"}
	addRequestVar('{$k}','{$v|@addslashes}')
	{/if}
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