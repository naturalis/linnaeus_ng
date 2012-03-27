{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
{t}Store the definite keymap for faster runtime access.{/t}
</p>
<form method="post" action="">
<input type="hidden" name="action" value="store" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="submit" value="{t}Store keymap{/t}" />
</form>
</div>
{include file="../shared/admin-footer.tpl"}
