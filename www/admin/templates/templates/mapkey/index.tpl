{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if !$isOnline}
Your computer appears to be offline. Dynamic maps will not be displayed correctly.
{/if}
<ul>
	<li><a href="choose_species.php">Add occurrences for a species</a></li>
	<li><a href="species.php">View existing occurrences</a></li>
	<li></li>
	<li><a href="map_views.php">Map views</a></li>
	<li><a href="test.php">Test map</a></li>
</ul>
RETEST FILE LOAD<br />
FIX Polygon() MYSQL

</div>

{include file="../shared/admin-footer.tpl"}
