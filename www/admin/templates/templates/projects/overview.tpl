{include file="../shared/_admin-head.tpl"}
{include file="../shared/_admin-body-start.tpl"}

<div id="page-main">

<style>

ul {
	margin-top:-10px;
	padding-left:0px;
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


<!--
NOTICES
- acquire images for matrix
- compress dich key
- define /shortname/
- project ubpublished
-->
	<br />

    <h3>{t}Management tasks{/t}</h3>

    <ul class="management">
        <li><a href="../module_settings/">{t}Settings{/t}</a></li>
    </ul>

    <ul class="management">
        <li><a href="../hotwords/">{t}Hotwords{/t}</a></li>
        <li><a href="../utilities/mass_upload.php">{t}Mass upload images{/t}</a></li>
        <li><a href="../projects/clear_cache.php">{t}Clear cache{/t}</a></li>
    </ul>

    <ul class="management">
        <li><a href="../import/export.php">{t}Generic export{/t}</a></li>
        <li><a href="../import/matrix_app_export.php">{t}Export multi-entry key for Linnaeus Mobile{/t}</a></li>
        <li><a href="../import/app_export.php">{t}Complete export for Linnaeus Mobile{/t}</a></li>
    
    </ul>
    
</div>

{include file="../shared/admin-footer.tpl"}
