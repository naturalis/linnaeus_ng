{include file="../shared/admin-header.tpl"}
<div id="page-main">
    <ul>
		{foreach $matrices v}
		<li><a href="ext_matrix.php?id={$v.id}">{$v.sys_name}</a></li>
		{/foreach}
    </ul>
</div>

make new etc.

{include file="../shared/admin-footer.tpl"}
