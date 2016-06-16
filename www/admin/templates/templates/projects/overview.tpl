{include file="../shared/_admin-head.tpl"}
{include file="../shared/_admin-body-start.tpl"}

<div id="page-main">

<style>

ul {
	margin-top:-10px;
	padding-left:0px;
	margin-bottom:25px;
}

ul.modules li {
	list-style-type: none;
	list-style-position: inside;
    padding:2px;
}

ul.management li {
	list-style-type: none;
	list-style-position: inside;
}

</style>

    <h3>Modules</h3>
    
    <ul class="modules">
    {foreach $modules.modules v}
    <li><a href="../{$v.controller}/">{$v.module}</a></li>
    {/foreach}
    </ul>

{if $modules.custom|@count>0}

    <h3>Custom modules</h3>
    
    <ul class="modules">
    {foreach $modules.custom v}
    <li><a href="../module/?freeId={$v.id}">{$v.module}</a></li>
    {/foreach}
    </ul>

{/if}

	{if $show_lead_expert_modules}

    <h3>{t}Management tasks{/t}</h3>

    <ul class="management">
        <li><a href="../module_settings/">{t}Settings{/t}</a></li>
        <li><a href="../hotwords/">{t}Hotwords{/t}</a></li>
        <li><a href="activity_log.php">{t}Activity log{/t}</a></li>
        <!-- li><a href="../utilities/mass_upload.php">{t}Mass upload images{/t}</a></li -->
        <!-- li><a href="../projects/clear_cache.php">{t}Clear cache{/t}</a></li -->
        <li><a href="../import/index.php">{t}Import data{/t}</a></li>
    </ul>

	{/if}

	{if $show_sys_management_modules}

    <h3>{t}System tasks{/t}</h3>

	<ul class="management">
        <!-- li><a href="../../views/projects/create.php">{t}Create a project{/t}</a></li -->
        <li><a href="../projects/delete_orphan.php">{t}Delete orphaned data{/t}</a></li>
        <li><a href="../projects/delete.php">{t}Delete a project{/t}</a></li>
        <li><a href="../projects/change_id.php">{t}Change a project ID{/t}</a></li>
        <!-- li><a href="../import/export.php">{t}Generic export{/t}</a></li -->
        <li><a href="../import/matrix_app_export.php">{t}Export multi-entry key for Linnaeus Mobile{/t}</a></li>
        <li><a href="../import/app_export.php">{t}Complete export for Linnaeus Mobile{/t}</a></li>
        <li><a href="../interface/index.php">{t}Interface translations{/t}</a></li>
    </ul>

	{/if}
    
</div>

{include file="../shared/admin-footer.tpl"}
