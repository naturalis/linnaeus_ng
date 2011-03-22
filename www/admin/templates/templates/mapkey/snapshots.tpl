{include file="../shared/admin-header.tpl"}


<div id="page-main">
<table style=" border-collapse:collapse">
{foreach from=$snapshots key=k item=v}
	<tr>
		<td>{$v.name}</td>
		<td>[<a href="snapview.php?id={$v.id}">view</a>]</td>
		<td>[<a href="snapshot.php?id={$v.id}">edit</a>]</td>
	</tr>
{/foreach}
</table>
<a href="snapshot.php">Create new snapshot</a>
</div>

{include file="../shared/admin-footer.tpl"}
