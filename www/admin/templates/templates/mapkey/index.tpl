{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if !$isOnline}
Your computer appears to be offline. Dynamic maps will not be displayed correctly.
{/if}
<ul>
	<li><a href="map_views.php">Map views</a></li>
	<li><a href="species.php">Species</a></li>
	<li><a href="test.php">Test map</a></li>
</ul>
</div>

{include file="../shared/admin-footer.tpl"}
