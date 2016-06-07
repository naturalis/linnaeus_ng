{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<div id="page-main">
	{if $result == ''}

	<p>Enter the ResourceSpace master key to create a new user.</p>

	<form id="theForm" method="post">
	<input type="hidden" name="action" value="create" />

	<table>
	<tr>
		<td>{t}RS server{/t}:</td>
		<td><input type="text" name="rs_base_url" value="{$rsBaseUrl}" /></td>
	</tr>
	<tr>
		<td>{t}RS master key{/t}:</td>
		<td><input type="text" name="rs_master_key" value="" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="create" value="{t}create{/t}" /></td>
	</tr>
	</table>
	</form>

	{else}

	<p>The settings have been saved! <a href="index.php">Add media</a></p>

	{/if}

</div>

{include file="../shared/admin-footer.tpl"}
