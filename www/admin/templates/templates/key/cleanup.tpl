{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if !$processed}

This function:
<ul>
	<li>deletes choices that belong to a non-existing step</li>
	<li>deletes choices that have no text, image or target</li>
	<li>resets non-existant target steps</li>
	<li>resets non-existant target taxa</li>
</ul>

<form action="" method="post">
<input type="hidden" name="action" value="clean" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="submit" value="{t}clean up{/t}" />
</form>
{/if}
<p>
<a href="index.php">back</a>
</p>
</div>
{include file="../shared/admin-footer.tpl"}
