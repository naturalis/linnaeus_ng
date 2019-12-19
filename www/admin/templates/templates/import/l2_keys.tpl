{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
{assign var=process value=true}

<div id="page-main">
{if $processed==true}
<p>
<a href="l2_map.php">Import map data</a>
</p>
{else}
<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
<b>Dichotomous and matrix keys</b><br />
<label><input type="checkbox" name="key_dich" checked="checked">&nbsp;Import dichotomous key(s)</label><br />
<label><input type="checkbox" name="key_matrix" checked="checked">&nbsp;Import matrix key(s)</label><br />
</p>
<input type="submit" value="import" />
</form>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}