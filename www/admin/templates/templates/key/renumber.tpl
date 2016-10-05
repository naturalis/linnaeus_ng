{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
{t}This function will renumber the steps in your key.{/t}
</p>
<form action="" method="post">
<input type="hidden" name="action" value="renumber" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="submit" value="{t}renumber steps{/t}" />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
