{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

	{t}Select a project to work on:{/t}

    <ul class="admin-list">
    {foreach $projects v}
    <li>
        <a href="?project_id={$v.id}" {if !$v.published}style="color:#779"{/if}>{if $v.title!=''}{$v.title}{else}{$v.sys_name}{/if}</a>
        {if $session.admin.project.id==$v.id}<span title="current active project">{t}(current){/t}</span>{/if}
        <a href="../../../app/views/linnaeus/set_project.php?p={$v.id}" style="color:#999;margin-left:10px" target="_project">view</a>
    </li>
    {/foreach}
    </ul>

    {t}System administration tasks:{/t}
    <ul>
        <!-- li><a href="../../views/projects/create.php">{t}Create a project{/t}</a></li -->
        <li><a href="../../views/projects/delete.php">{t}Delete a project{/t}</a></li>
        <li><a href="../../views/projects/change_id.php">{t}Change a project ID{/t}</a></li>
    </ul>
    <ul>
        <li><a href="../../views/import/index.php">{t}Import data{/t}</a></li>
        <li><a href="../../views/projects/delete_orphan.php">{t}Delete orphaned data{/t}</a></li>
    </ul>
    <ul>
        <li><a href="../../views/interface/index.php">{t}Interface translations{/t}</a></li>
    </ul>

</div>

{include file="../shared/admin-footer.tpl"}
