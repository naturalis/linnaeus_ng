{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if !$cleared}
<p>
Click button to update the entire parentage lookup table.
<form method="post" name="theForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="generate" />
<input type="submit" value="Generate"/>
</form>
</p>
{/if}
<a href="manage.php">Back</a>
</div>
{include file="../shared/admin-footer.tpl"}
