<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>{$session.project.name}{if $session.project.name!='' && $pageName != ''} - {/if}{$pageName}</title>

	<link href="{$baseUrl}admin/media/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="{$baseUrl}admin/media/system/favicon.ico" rel="icon" type="image/x-icon" />
	<style type="text/css" media="all">
		@import url("{$baseUrl}admin/style/main.css");
		@import url("{$baseUrl}admin/style/admin-inputs.css");
		@import url("{$baseUrl}admin/style/admin-help.css");
		@import url("{$baseUrl}admin/style/admin.css");
{if $cssToLoad}
{section name=i loop=$cssToLoad}
		@import url("{$baseUrl}admin/style/{$cssToLoad[i]}");
{/section}
{/if}
{if $session.project.css_url!=''}		@import url("{$session.project.css_url}");
{/if}
	</style>

	<script type="text/javascript" src="{$baseUrl}admin/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}admin/javascript/main.js"></script>
	<script type="text/javascript" src="{$baseUrl}admin/javascript/development.js"></script><!--this should be removed in production-->
{if $javascriptsToLoad}
{section name=i loop=$javascriptsToLoad}
	<script type="text/javascript" src="{$baseUrl}admin/javascript/{$javascriptsToLoad[i]}"></script>
{/section}
{/if}

{if $includeHtmlEditor}
{include file="../shared/tinymce-editor.tpl"}
{/if}

</head>

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
	<span style="float:right">
		<span class="pseudo-a" onclick="$('#locale').val('en_GB');$('#langForm').submit()">English</span>&nbsp;
		<span class="pseudo-a" onclick="$('#locale').val('nl_NL');$('#langForm').submit()">Dutch</span>
		<span id="page-header-version">{$session.user.currentLocale}</span>
		<form id="langForm" method="post" target=""><input id="locale" type="hidden" name="locale" value=""></form>
	</span>
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