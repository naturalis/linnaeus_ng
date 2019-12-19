{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if !$cleared}
<p>
Click button to clear all runtime caches for this project.
<form method="post" name="theForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="clear" />
<input type="submit" value="Clear cache"/>
</form>
</p>
{/if}
<a href="../utilities/admin_index.php">Back</a>
</div>
{include file="../shared/admin-footer.tpl"}
