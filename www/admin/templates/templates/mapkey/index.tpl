{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if !$isOnline}
{t}Your computer appears to be offline. Dynamic maps will not be displayed correctly.{/t}
{/if}
{if !$dataTypeCount}
{t}You have not yet defined any data types. Please do so before adding geographical information.{/t}
{/if}
<ul>
	<li><a href="choose_species.php">{t}Add or edit data{/t}</a></li>
	<li><a href="species_select.php">{t}View existing data{/t}</a></li>
	<li><a href="data_types.php">{t}Define data types{/t}</a></li>
</ul>
</div>

{include file="../shared/admin-footer.tpl"}
