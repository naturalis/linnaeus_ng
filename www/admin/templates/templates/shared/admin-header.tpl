<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>{$session.project.name}{if $session.project.name!='' && $pageName != ''} - {/if}{$pageName}</title>

	<link href="{$rootWebUrl}admin/images/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="{$rootWebUrl}admin/images/system/favicon.ico" rel="icon" type="image/x-icon" />

	<style type="text/css" media="all">
		@import url("{$rootWebUrl}admin/style/main.css");
		@import url("{$rootWebUrl}admin/style/admin-inputs.css");
		@import url("{$rootWebUrl}admin/style/admin-help.css");
		@import url("{$rootWebUrl}admin/style/admin.css");
	</style>

	<script type="text/javascript" src="{$rootWebUrl}admin/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="{$rootWebUrl}admin/javascript/main.js"></script>

</head>

<body><div id="body-container">
<div id="header-container">
	<a href="{$rootWebUrl}admin/admin-index.php"><img src="{$rootWebUrl}admin/images/system/linnaeus_logo.png" id="lng-logo" />
	<img src="{$rootWebUrl}admin/images/system/eti_logo.png" id="eti-logo" /></a>
</div>
<div id="page-container">

<div id="page-header-titles">
	<span id="page-header-title">{$applicationName} v{$applicationVersion}</span><br />
{if $session.project.name!=''}	<span id="page-header-projectname">{$session.project.name}</span>
<!--DEBUG ONLY:--><span style="color:white">{$session.project.id}</span>
<br />{/if}
{if $controllerPublicName!='' && !$hideControllerPublicName}	<span id="page-header-appname"><a href="index.php">{$controllerPublicName}</a></span><br />{/if}
	<span id="page-header-pageaction">{$pageName}</span>
</div>

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