{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if !$isOnline}
Your computer appears to be offline. Dynamic maps will not be displayed correctly.
{/if}
{if !$dataTypeCount}
You have not yet defined any data types. Please do so before adding geographical information.
{/if}
<ul>
	<li><a href="choose_species.php">Add or edit data</a></li>
	<li><a href="species_select.php">View existing data</a></li>
	<li><a href="data_types.php">Define data types</a></li>
</ul>
</div>

{include file="../shared/admin-footer.tpl"}
