{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if !$processed}
<p>
Click 'delete' to remove all orphaned data from the database.<br />
Orphaned data is data that is associated with a project id that no longer exists in the central projects table.<br />
No further confirmation will be asked for.
<form method="post" name="theForm">
<input type="hidden" name="action" value="delete" />
<input type="submit" value="Delete orphaned data" />
</form>
</p>
{/if}

<a href="../users/choose_project.php">Back</a>

</div>
{include file="../shared/admin-footer.tpl"}
