<body><div id="body-container"><a name="body-top"></a>
<div id="header-container">
	<a href="{$baseUrl}admin/admin-index.php"><img src="{$baseUrl}admin/media/system/linnaeus_logo.png" id="lng-logo" />
	<img src="{$baseUrl}admin/media/system/eti_logo.png" id="eti-logo" /></a>

{if !$excludeLogout}
	<div style="text-align:right;position:relative;top:-20px">
		{t}Logged in as{/t} {if $session.admin.user.last_name!=''}{$session.admin.user.first_name} {$session.admin.user.last_name} {if $session.admin.user.currentRole.role_name}({$session.admin.user.currentRole.role_name}) {/if}(<a href="{$baseUrl}admin/views/users/logout.php">{t}Log out{/t}</a>){/if}
	</div>
{/if}
</div>
<div id="page-container">

<div id="page-header-titles">
	<span id="page-header-title">{$app.name}</span>
	<span id="page-header-version">{$app.version} ({$app.versionTimestamp})</span>
{if $session.admin.system.server_addr=='127.0.0.1'}
	<span id="page-header-locality" style="color:#CC0000">[localhost]</span>
{/if}
	<br />
	
{if $breadcrumbs && $printBreadcrumbs}
	<div id="breadcrumbs">
	{section name=i loop=$breadcrumbs}
		{assign var=n value=$n+1}
		{if $hideControllerPublicName}
			{if $n<2}
				<span class="crumb"><a href="{$breadcrumbs[i].url}">{$breadcrumbs[i].name}</a></span>
				<span class="crumb-arrow">&rarr;</span>
			{elseif $n==2}
				<span class="crumb-current"><a href="{$breadcrumbs[i].url}">{$breadcrumbs[i].name}</a></span>
				<span class="crumb-arrow">&nbsp;</span>
			{/if}
		{else}
			{if $n==$breadcrumbs|@count}
				<span id="crumb-current">{$breadcrumbs[i].name}</span>
				<span class="crumb-arrow">&nbsp;</span>
			{else}
				<span class="crumb"><a href="{$breadcrumbs[i].url}">{$breadcrumbs[i].name}</a></span>
				<span class="crumb-arrow">&rarr;</span>
			{/if}
		{/if}
	{/section}
{if $isMultiLingual}
	<span style="float:right">
	{section name=i loop=$uiLanguages}
	{if $uiLanguages[i] == $uiCurrentLanguage}
		<span class="active-language">{$uiLanguages[i]}</span>&nbsp;
	{else}
		<span class="pseudo-a" onClick="$('#uiLang').val('{$uiLanguages[i]}');$('#langForm').submit()">{$uiLanguages[i]}</span>&nbsp;
	{/if}
	{/section}
	<br />
	<form id="langForm" method="post" action=""><input id="uiLang" type="hidden" name="uiLang" value="" /></form>
{/if}
	</div>
{/if}
</div>


{if $controllerMenuExists}
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

{if $helpTexts}
<div id="block-inline-help">
	<div id="title" onClick="allToggleHelpVisibility();">{t}Help{/t}</div>
	<div class="body-collapsed" id="body-visible">
{section name=i loop=$helpTexts}
		<div class="subject">{$helpTexts[i].subject}</div>
		<div class="text">{$helpTexts[i].helptext}</div>
{/section}
	</div>
</div>
{/if}