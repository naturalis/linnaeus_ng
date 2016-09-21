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
		<div id="col-logo" style="visibility:hidden">
			<!-- <img  src='{$projectUrls.systemMedia}logo_lng.png'> -->
		</div>

	</div>
</div>

<div id="bottombar" class="navbar navbar-inverted">
	<div class="container">
		<p class="navbar-text navbar-left">
			<a href="http://linnaeus.naturalis.nl/">
				Linnaeus NG 2.0
			</a>
		</p>
		<p class="navbar-text navbar-right">
			<a href="http://www.naturalis.nl">
				Naturalis Biodiversity Center
			</a>
		</p>
	</div>
</div>

<script type="text/JavaScript">

function showMedia(url,name)
{
}

$(document).ready(function()
{
	// Apply prettyDialog behaviour to items with inline-image class
	$(".inline-image").each(function()
	{
		$_me = $(this);

		$_me.attr("rel","prettyDialog");

		arr_arguments = $_me.attr("onclick").split("'");

		$_me
			.attr("href",arr_arguments[1])
			.attr("title",arr_arguments[3])
			.removeAttr('onclick')
			.prettyDialog();
	});

	if( jQuery().prettyDialog )
	{
		$("a[rel^='prettyPhoto']").prettyDialog();
	}

	/*
	if( jQuery().shrinkText )
	{
		$("#title a").shrinkText();
		$("#header-title").shrinkText();

		$( window ).resize(function()
		{
			$("#title a").shrinkText();
			$("#header-title").shrinkText();
		});
	}
	*/

	{if $search}onSearchBoxSelect('{$search|@addslashes}');{/if}
	{foreach from=$requestData key=k item=v}
	addRequestVar('{$k}','{$v|addslashes}')
	{/foreach}
	chkPIDInLinks({$session.app.project.id},'{$addedProjectIDParam}');
	{if $searchResultIndexActive}
	searchResultIndexActive = {$searchResultIndexActive};
	{/if}

})

</script>

{snippet}google_analytics.html{/snippet}

{include file="../shared/_lng_productie_google_analytics.tpl"}

</body>
</html>