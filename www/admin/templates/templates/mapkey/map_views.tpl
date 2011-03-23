{include file="../shared/admin-header.tpl"}


<div id="page-main">
Available map views:
<table style=" border-collapse:collapse">
{foreach from=$mapViews key=k item=v}
	<tr class="tr-highlight">
		<td style="width:250px">{$v.name}</td>
		<td>[<a href="map_view.php?id={$v.id}">view</a>]</td>
		<td>[<a href="map_view_edit.php?id={$v.id}">edit</a>]</td>
	</tr>
{/foreach}
</table>
<p>
<a href="map_view_edit.php">Create new map view</a>
</p>
</div>

{include file="../shared/admin-footer.tpl"}
