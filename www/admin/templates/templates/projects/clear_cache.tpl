{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
Click button to clear all runtime caches for this project.
<form method="post" name="theForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="clear" />
<input type="submit" value="Clear cache"/>
</form>
</p>
<a href="../users/choose_project.php">Back</a>
</div>
{include file="../shared/admin-footer.tpl"}
