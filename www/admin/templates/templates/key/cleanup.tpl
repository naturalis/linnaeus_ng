{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if !$processed}

{t}This function:{/t}
<ul>
	<li>{t}deletes choices that belong to a non-existing step{/t}</li>
	<li>{t}deletes choices that have no text, image or target{/t}</li>
	<li>{t}resets non-existant target steps{/t}</li>
	<li>{t}resets non-existant target taxa{/t}</li>
</ul>

<form action="" method="post">
<input type="hidden" name="action" value="clean" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="submit" value="{t}clean up{/t}" />
</form>
{/if}
<p>
<a href="index.php">{t}back{/t}</a>
</p>
</div>
{include file="../shared/admin-footer.tpl"}
