<body><div id="body-container">
<div id="header-container">
	<a href="{$baseUrl}admin/admin-index.php"><img src="{$baseUrl}admin/media/system/linnaeus_logo.png" id="lng-logo" />
	<img src="{$baseUrl}admin/media/system/eti_logo.png" id="eti-logo" /></a>

{if !$excludeLogout}
	<div style="text-align:right;position:relative;top:-20px">
		<a href="{$baseUrl}admin/views/users/logout.php">Log out (logged in as {if $session.user.last_name!=''}{$session.user.first_name} {$session.user.last_name})</a>{/if}
	</div>
{/if}

</div>
<div id="page-container">

<div id="page-header-titles">
	<span id="page-header-title">{$app.name}</span>
	<span id="page-header-version">{$app.version} ({$app.versionTimestamp})</span>
{if $session.system.server_addr=='127.0.0.1'}
	<span id="page-header-locality" style="color:#CC0000">[localhost]</span>
{/if}
	<br />
	
{if $breadcrumbs}
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
		<span class="pseudo-a" onclick="$('#uiLang').val('{$uiLanguages[i]}');$('#langForm').submit()">{$uiLanguages[i]}</span>&nbsp;
{/if}
{/section}
	</span>
	<form id="langForm" method="post" action=""><input id="uiLang" type="hidden" name="uiLang" value="" /></form>
{/if}
	</div>
{/if}
</div>
<span id="debug-message"></span><!--this should be removed in production-->
{if $welcomeMessage}
<div id="welcome-message">
{$welcomeMessage}
</div>
{/if}

{if $helpTexts}
<div id="block-inline-help">
	<div id="title" onclick="allToggleHelpVisibility();">Help</div>
	<div class="body-collapsed" id="body-visible">
{section name=i loop=$helpTexts}
		<div class="subject">{$helpTexts[i].subject}</div>
		<div class="text">{$helpTexts[i].helptext}</div>
{/section}
	</div>
</div>
{/if}