<body><div id="body-container"><a name="body-top"></a>

<div id="header-container">
	<a href="{$baseUrl}admin/views/utilities/admin_index.php">
		<img src="{$baseUrl}admin/media/system/logo_linnaeus_ng.png" id="lng-logo">
	</a>

	<div class="header-branding">
		Linnæus NG&trade; administration
	</div>

	<div class="header-user">
		{if $session.admin.user._logged_in}
		{t}Logged in as{/t}
		<a href="{$baseUrl}admin/views/users/edit.php?id={$session.admin.user.id}">
            {$session.admin.user.first_name} {$session.admin.user.last_name}
        </a> 
        (<a href="{$baseUrl}admin/views/users/logout.php">{t}Log out{/t}</a>)
		{/if}
	</div>
</div>

<div id="page-container">

<div id="page-header-titles">
	<span id="page-header-title">{$app.name}</span>
	<span id="page-header-version">{$app.version} ({$app.versionTimestamp})</span>

	{if $session.admin.system.server_addr=='127.0.0.1'}
	<span id="page-header-locality" style="color:#CC0000">[localhost]</span>
	{/if}

	<div style="float:right;margin-top:auto;margin-bottom:auto">
    <a href="{$wikiUrl}" target="wiki">
    	<img src="../../media/system/qm_grey.png" style="width:15px;border:0" />
	</a>
    </div>

	<br />
	
    {if $breadcrumbs && $printBreadcrumbs}
        <div id="breadcrumbs">
        {foreach $breadcrumbs v}
            {assign var=n value=$n+1}
            {if $hideControllerPublicName}
                {if $n<2}
                    <span class="crumb"><a href="{$v.url}">{$v.name}</a></span>
                    <span class="crumb-arrow">&rarr;</span>
                {elseif $n==2}
                    <span class="crumb-current"><a href="{$v.url}">{$v.name}</a></span>
                    <span class="crumb-arrow">&nbsp;</span>
                {/if}
            {else}
                {if $n==$breadcrumbs|@count}
                    <span id="crumb-current">{$v.name}</span>
                    <span class="crumb-arrow">&nbsp;</span>
                {else}
                    <span class="crumb"><a href="{$v.url}">{$v.name}</a></span>
                    <span class="crumb-arrow">&rarr;</span>
                {/if}
            {/if}
        {/foreach}
        
         <a href="../../../app/views/linnaeus/set_project.php?p={$session.admin.project.id}" style="color:#999;margin-left:10px" target="_project">view</a>
    
        {if $isMultiLingual && $uiLanguages|@count>1}
            <span style="float:right">
            {section name=i loop=$uiLanguages}
            {if $uiLanguages[i].id == $uiCurrentLanguage}
                <span class="active-language">{$uiLanguages[i].language}</span>&nbsp;
            {else}
                <span class="a" onClick="$('#uiLang').val('{$uiLanguages[i].id}');$('#langForm').submit()">{$uiLanguages[i].language}</span>&nbsp;
            {/if}
            {/section}
            </span>
            <br />
            <form id="langForm" method="post" action=""><input id="uiLang" type="hidden" name="uiLang" value="" /></form>
        {/if}
        </div>
    {/if}

</div>


{if $controllerMenuExists && $session.admin.user._logged_in}
<div id="page-header-localmenu">
<div id="page-header-localmenu-content">
{if $controllerBaseName}{include file="../"|cat:$controllerBaseName|cat:"/_menu.tpl"}{else}{include file="_menu.tpl"}{/if}
</div>
</div>
{/if}

<span id="debug-message"></span><!--should be removed in production-->
{if $welcomeMessage}
<div id="welcome-message">
{$welcomeMessage}
</div>
{/if}
