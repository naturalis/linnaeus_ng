{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
{assign var=process value=true}

<div id="page-main">
{if $processed==true}
<p>
<a href="l2_keys.php">Import keys</a>
</p>
{else}
<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
<b>Welcome, contributors and introduction.</b><br />
<label><input type="checkbox" name="welcome" checked="checked">&nbsp;Import welcome and contributors text</label><br />
<label><input type="checkbox" name="introduction" checked="checked">&nbsp;Import introduction</label><br />
</p>

<input type="submit" value="import" />
</form>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}