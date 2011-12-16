{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
{assign var=process value=true}

<div id="page-main">
{if $processed==true}
<p>
<a href="l2_content.php">Import introduction and additional content</a>
</p>
{else}
<b>Literature and glossary</b>
<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
<label>Import literary references?&nbsp;&nbsp;<input type="checkbox" name="literature" checked="checked"></label><br />
<label>Import glossary items?&nbsp;&nbsp;<input type="checkbox" name="glossary" checked="checked"></label>
</p>
<input type="submit" value="import" />
</form>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}