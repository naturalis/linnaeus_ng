{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{t}Select a project to work on:{/t}
<ul class="admin-list">
{if $isSysAdmin}

{foreach from=$projects item=v}
{if $v.active == '1' && $v.id != 0}
<li>
	<a href="?project_id={$v.id}" {if !$v.published}style="color:#779"{/if}>{if $v.title!=''}{$v.title}{else}{$v.name}{/if}</a>
	{if !$v.member}<span title="you are not actually asssigned to this project">(*)</span>{/if}
	{if $session.admin.project.id==$v.id}<span title="current active project">{t}(current){/t}</span>{/if}
    <a href="../../../app/views/linnaeus/index.php?epi={$v.id}" style="color:#999;margin-left:10px">view</a>
</li>
{/if}
{/foreach}
</ul>


{t}System administration tasks:{/t}
<ul>
	<li><a href="../../views/projects/create.php">{t}Create a project{/t}</a></li>
	<li><a href="../../views/projects/delete.php">{t}Delete a project{/t}</a></li>
	<li><a href="../../views/projects/change_id.php">{t}Change a project ID{/t}</a></li>
</ul>
<ul>
	<li><a href="../../views/import/index.php">{t}Import data{/t}</a></li>
	<li><a href="../../views/projects/delete_orphan.php">{t}Delete orphaned data{/t}</a></li>
</ul>
<ul>
	<li><a href="../../views/users/all.php">{t}Collaborator overview{/t}</a></li>
	<li><a href="../../views/users/rights_matrix.php">{t}Rights matrix{/t}</a></li>
</ul>
<ul>
	<li><a href="../../views/interface/index.php">{t}Interface translations{/t}</a></li>
</ul>
{else}

{section name=i loop=$projects}
{if $projects[i].active == '1' && $projects[i].id != 0}
<li>
	<a href="?project_id={$projects[i].id}">{if $projects[i].title!=''}{$projects[i].title}{else}{$projects[i].name}{/if}</a>
	{if $session.admin.project.id==$projects[i].id}<span title="current active project">{t}(current){/t}</span>{/if}
</li>
{/if}
{/section}
</ul>


{/if}


</div>

{include file="../shared/admin-footer.tpl"}
