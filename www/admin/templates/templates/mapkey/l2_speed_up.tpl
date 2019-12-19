{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
{t}Click the button below to have the system store the Linnaeus 2 map data in a more compact form.{/t}<br />
{t}Please note that, depending on the size of your data, this might take a few minutes.{/t}
</p>
<p>
Last save date: {if $lastChangeDate}{$lastChangeDate}{else}(never){$lastChangeDate}{/if}
</p>
<form action="" method="post">
<input type="hidden" name="action" value="store" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="submit" value="{t}store compacted data{/t}" />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
