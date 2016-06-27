{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>{t}Update index table{/t}</h2>

<p>
{t}This function updates the extra index table holding parent-child relationships.{/t}<br />
{t}This table should be updated automatically, but e.g. if the number of child taxa in the taxonomic tree is off, the table can be force-updated.{/t}
{t}Please take into account that an update may take a minute or so.{/t}
<form method="post">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="update" />
<input type="submit" value="{t}update{/t}" />
</form>
</p>
<p>
	<a href="index.php">{t}back{/t}</a>
</p>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}