{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h3>modules</h3>

<ul>
{foreach $modules v}
	<li>
    	{$v.module}<br />
        	<a href="settings.php?id={$v.id}">setting</a> |
        	<a href="values.php?id={$v.id}">values</a>
	</li>
{/foreach}
</ul>

</div>

{include file="../shared/admin-footer.tpl"}
