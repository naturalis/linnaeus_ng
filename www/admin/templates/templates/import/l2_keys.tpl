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
<label>Import dichotomous key(s)?&nbsp;&nbsp;<input type="checkbox" name="key_dich" checked="checked"></label><br />
<label>Import matrix key(s)?&nbsp;&nbsp;<input type="checkbox" name="key_matrix" checked="checked"></label><br />
</p>
<input type="submit" value="import" />
</form>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}