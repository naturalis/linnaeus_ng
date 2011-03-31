{include file="../shared/header.tpl"}

<div id="page-main">
{if !$isOnline}
{t}Your computer appears to be offline. Unfortunately, the map key doesn't work without an internet connection.{/t}
{else}

	<table>
		<tr><td><a href="examine.php">Examine a species</a></td></tr>
		<tr><td><a href="compare.php">Compare two species</a></td></tr>
		<tr><td><a href="search.php">Search an area on the map</a></td></tr>
	</table>
{/if}
</div>

{include file="../shared/footer.tpl"}
