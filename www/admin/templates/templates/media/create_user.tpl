{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<div id="page-main">
	{if $result == ''}

	<p>Enter the ResourceSpace master key to create a new user.</p>

	<form id="theForm" method="post">
	<input type="hidden" name="action" value="create" />

	<table>
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

	<p style="color: red; font-weight: bold;">IMPORTANT -- READ THIS INFORMATION BEFORE LEAVING THIS PAGE!</p>
	<p>The following information has to be added as Media module settings. The rs_collection_id
	and rs_user_key settings are mandatory and they will be displayed only once.
	Do not refresh or leave this page before taking note!</p>
	<p>To add the settings, go to <a href="../module_settings/settings.php"
	target="_blank">Project overview > Settings</a> (link opens in new tab/window)
	and click Media > edit settings > values. Add the following setting values to your project:</p>

	<ul>
	{foreach from=$result key=setting item=value}
		<li><strong>{$setting}</strong>: {$value}</li>
	{/foreach}
	</ul>

	<p>You are strongly advised to update these settings immediately, as there is
	no easy way to retrieve them post-facto.</p>
	{/if}

</div>

{include file="../shared/admin-footer.tpl"}
