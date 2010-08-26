{include file="../shared/admin-header.tpl"}

<div id="admin-main">
<table>
<tr>
	<th onclick="tableColumnSort('id');">id</th>
	<th onclick="tableColumnSort('first_name');">first name</th>
	<th onclick="tableColumnSort('last_name');">last name</th>
	<th onclick="tableColumnSort('gender');">gender</th>
	<th onclick="tableColumnSort('email_address');">e-mail</th>
	<th onclick="tableColumnSort('role');">role</th>
	<th></th>
	<th></th>
</tr>
{section name=i loop=$users}
<tr>
	<td>{$users[i].id}</td>
	<td>{$users[i].first_name}</td>
	<td>{$users[i].last_name}</td>
	<td>{$users[i].gender}</td>
	<td>{$users[i].email_address}</td>
	<td>{$users[i].role}</td>
	<td>[<a href="view.php?id={$users[i].id}">view</a>]</td>
	<td>[<a href="edit.php?id={$users[i].id}">edit</a>]</td>
</tr>
{/section}
</table>
</div>

<form method="post" action="" name="postForm" id="postForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>

{include file="../shared/admin-footer.tpl"}
