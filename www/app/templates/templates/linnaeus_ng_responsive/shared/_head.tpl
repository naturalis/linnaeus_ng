<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="nl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="{$session.app.project.keywords}" />
	<meta name="description" content="{$session.app.project.description}" />
	<meta name="lng-project-id" content="{$session.app.project.id}" />
	<meta name="viewport" content="width=device-width, initial-scale=1">

{snippet}development_no_follow.html{/snippet}

	{if $robotsDirective}<meta name="robots" content="{$robotsDirective|@implode:","}">{/if}

	<title>{$session.app.project.title|@strip_tags:false}</title>

    <link rel="Shortcut Icon" href="{$projectUrls.projectCSS}favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/soortenregister.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/linnaeus.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$baseUrl}app/vendor/ionicons/css/ionicons.min.css" />
	<link rel="stylesheet" type="text/css" media="screen" title="default" href="{$baseUrl}app/vendor/prettyPhoto/css/prettyPhoto.css" />
	<link rel="stylesheet" type="text/css" media="screen" title="default" href="{$baseUrl}app/vendor/fancybox/jquery.fancybox.css" />
	{if $cssToLoad}
		{foreach $cssToLoad v}
			<link rel="stylesheet" type="text/css" media="screen" href="{$v}" />
		{/foreach}
	{/if}
	<script type="text/javascript" src="{$baseUrl}app/vendor/raphael/raphael.min.js"></script>
    <script type="text/javascript" src="{$baseUrl}app/vendor/bundle.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/main.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/inline_templates.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/soortenregister.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/soortenregister.js"></script>

	{foreach $javascriptsToLoad.all v}
	{if $v|strpos:"http:"===false && $v|strpos:"https:"===false}
	<script type="text/javascript" src="{$baseUrl}app/javascript/{$v}"></script>
	{else}
	<script type="text/javascript" src="{$v}"></script>
	{/if}
	{/foreach}
	{foreach $javascriptsToLoad.IE v}
		<!--[if IE]><script type="text/javascript" src="{$baseUrl}app/javascript/{$v}"></script><![endif]-->
	{/foreach}
	
</head>
