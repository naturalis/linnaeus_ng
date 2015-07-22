{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h3>modules</h3>

<ul>
{foreach $modules v}
	<li><a href="module.php?module={$v.module}">{$v.module}</a></li>
{/foreach}
</ul>

<form method="get" action="module.php">
new module: <input type="text" value="" name="module" />
</form>


</div>

{include file="../shared/admin-footer.tpl"}
