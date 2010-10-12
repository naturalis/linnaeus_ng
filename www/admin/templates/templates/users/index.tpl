{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
<tr>
{section name=i loop=$columnsToShow}
	<th onclick="allTableColumnSort('{$columnsToShow[i].name}');" class="th-userlist-{$columnsToShow[i].align}-align" style="text-align:{$columnsToShow[i].align};">
	{$columnsToShow[i].label}
	{if $sortBy.key==$columnsToShow[i].name}
		<img src="{$baseUrl}admin/images/system/sort-{$sortBy.dir}.gif" class="sort-image" />
	{/if}
	</th>
{/section}
	<th></th>
	<th></th>
</tr>
{section name=i loop=$users}
<tr class="tr-highlight">
{section name=j loop=$columnsToShow}
{assign var=colname value=$columnsToShow[j].name}
	<td class="cell-userlist-{$columnsToShow[j].align}-align">{$users[i].$colname}</td>{/section}
	<td>[<a href="view.php?id={$users[i].id}">view</a>]</td>
	<td>[<a href="edit.php?id={$users[i].id}">edit</a>]</td>
	<td>{if $users[i].role_id != 2}[<span class="pseudo-a" onclick="userDeleteUser({$users[i].id});">delete</span>]{/if}</td>
</tr>
{/section}
</table>

<br />

<a href="create.php">Create new collaborator</a>


</div>

<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>

<form method="post" action="edit.php" name="deleteForm" id="deleteForm">
<input name="id" id="id" value="-1" type="hidden" />
<input name="delete" id="delete" value="1" type="hidden" />
</form>


{include file="../shared/admin-footer.tpl"}
