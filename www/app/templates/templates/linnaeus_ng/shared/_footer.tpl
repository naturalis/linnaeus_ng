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
	<div id="footer-container" class="container">
		<div id="col-logo"> 
			<img  src='{$projectUrls.systemMedia}logo_lng.png'>
		</div>

		<div id="col-footer-text">
			<p>
				Powered by Linnæus Next Generation™
			</p>
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

	if( jQuery().prettyPhoto ) {
		$("a[rel^='prettyPhoto']").prettyDialog();
	}


	if( jQuery().shrinkText ){
		$("#title a").shrinkText();
		$("#header-title").shrinkText();
	
		$( window ).resize(function(){
			$("#title a").shrinkText();
			$("#header-title").shrinkText();
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