{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if $session.matrixkey.id}
<ul>
	<li><a href="matrices.php">{t}Matrices{/t}</a></li>
	<li><a href="edit.php">{t}Edit active matrix{/t}</a></li>
	<li><a href="links.php">{t}Display current links per taxon{/t}</a></li>
</ul>
{else}
{t}You have to choose an active matrix to edit. Click below to choose an active matrix.{/t}
<ul>
	<li><a href="matrices.php">{t}Matrices{/t}</a></li>
</ul>
{/if}

</div>

{include file="../shared/admin-footer.tpl"}
