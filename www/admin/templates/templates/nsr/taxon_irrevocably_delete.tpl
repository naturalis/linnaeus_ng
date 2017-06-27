{include file="../shared/admin-header.tpl"}

{include file="../shared/left_column_admin_menu.tpl"}

<div id="page-container-div">

<div id="page-main">

{t _s=$concept['taxon']}Taxon "%s" deleted.{/t}

{if $deletedNsrMedia}
<p>
	{t}Detached media:{/t}
    <ul>
    {foreach $deletedNsrMedia v}
    	<li>{$v.file_name}</li>
    {/foreach}
    </ul>
</p>
{/if}

<p>
    <a href="../nsr/">{t}index{/t}</a><br />
    <a href="taxon_deleted.php">{t}taxa marked as deleted{/t}</a><br />
    <a href="../projects/activity_log.php">{t}activity log{/t}</a><br />
</p>

</div>

{include file="../shared/admin-messages.tpl"}


</div>

{include file="../shared/admin-footer.tpl"}