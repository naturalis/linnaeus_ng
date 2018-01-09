{include file="../shared/admin-header.tpl"}

<div id="page-main">

	{t}Choose a matrix to edit:{/t}

	<ul>
	{foreach $matrices v}
		<li><a href="ext_matrix.php?id={$v.id}">{$v.sys_name}</a></li>
	{/foreach}
	</ul>

	<a href="ext_matrix.php?id={$v.id}">{t}Create a new matrix{/t}</a></li>
</div>

{include file="../shared/admin-footer.tpl"}