<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="nl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="{$session.app.project.keywords}" />
	<meta name="description" content="{$session.app.project.description}" />
	<meta name="lng-project-id" content="{$session.app.project.id}" />
	<meta name="viewport" content="width=device-width, initial-scale=1">

{snippet}development_no_follow.html{/snippet}

	<title>{$session.app.project.title|@strip_tags:false}</title>

    <link rel="Shortcut Icon" href="{$projectUrls.projectCSS}favicon.ico" type="image/x-icon" />

    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/flexslider.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/soortenregister.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/linnaeus.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/ionicons.min.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/jquery.fancybox.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/inline_templates.css" />
    {foreach $cssToLoad v}
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$v}" />
    {/foreach}

	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.urlparser.2.1.1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/sprintf-0.7-beta1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-sortelements.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/main.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/inline_templates.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.flexslider-min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/soortenregister.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/soortenregister.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/morris.js-0.4.3/morris.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/raphael/raphael-min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.fancybox.pack.js"></script>

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
	
	<!-- customized version of prettyPhoto -->
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/jquery.prettyPhoto.custom.js"></script>
</head>
