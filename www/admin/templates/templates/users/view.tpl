{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<style>
table tr th {
	text-align:right;
}
</style>


<div id="page-main">

<h2>{$user.first_name} {$user.last_name}</h2>

<table>
	<tr>
		<th>{t}Username:{/t}</th>
        <td>{$user.username}</td>
	</tr>
	<tr>
		<th>{t}First name:{/t}</th>
        <td>{$user.first_name}</td>
	</tr>
	<tr>
		<th>{t}Last name:{/t}</th>
        <td>{$user.last_name}</td>
	</tr>
	<tr>
		<th>{t}E-mail address:{/t}</th>
		<td>{$user.email_address}</td>
	</tr>
	<tr>
		<th>{t}Active:{/t}</th>
		<td>{if $user.active=='1'}y{else}n{/if}</td>
	</tr>
	<tr>
		<th>{t}Project role:{/t}</th>
		<td>{$user.project_role.role}</td>
	</tr>
	<tr>
		<th>{t}Last login:{/t}</th>
		<td>{$user.last_login}</td>
	</tr>
</table>

module_access
item_access

<p>
	<a href="edit.php?id={$user.id}">{t}edit{/t}</a>
</p>
<p>
	<a href="index.php">{t}back{/t}</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}
