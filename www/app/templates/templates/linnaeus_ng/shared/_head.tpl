<!DOCTYPE html>
{foreach $languages v k}{if $v.language_id == $currentLanguageId}{assign var="iso" value=$v.iso2}{/if}{/foreach}
<html lang="{$iso}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="{$session.app.project.keywords}" />
	<meta name="description" content="{$session.app.project.description}" />

	{if $robotsDirective}<meta name="robots" content="{$robotsDirective|@implode:","}">{/if}
    
	<meta name="lng-project-id" content="{$session.app.project.id}" />
	<meta name="server" content="{$server_name}" />
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>{$session.app.project.title|@strip_tags:false}{if $pageName}: {$pageName|@strip_tags:false}{/if}</title>

    <link href="{$baseUrl}app/style/naturalis/images/favicon.ico" rel="shortcut icon" >
    <link href="{$baseUrl}app/style/naturalis/images/favicon.ico" rel="icon" type="image/x-icon">
	<link rel="stylesheet" type="text/css" media="screen" href="{$projectUrls.projectCSS}dialog/jquery.modaldialog.css" />
    <link rel="stylesheet" type="text/css" href="{$baseUrl}app/style/css/inline_templates.css">
{if $cssToLoad}
{foreach $cssToLoad v}
	<link rel="stylesheet" type="text/css" media="screen" href="{$v}" />
{/foreach}
{/if}
	<link rel="stylesheet" type="text/css" href="{$baseUrl}app/style/css/orchids.css">
	<link rel="stylesheet" type="text/css" media="screen" title="default" href="{$baseUrl}app/vendor/ionicons/css/ionicons.min.css" />
	<link rel="stylesheet" type="text/css" media="screen" title="default" href="{$baseUrl}app/vendor/prettyPhoto/css/prettyPhoto.css" />
	<link rel="stylesheet" type="text/css" media="screen" title="default" href="{$baseUrl}app/vendor/fancybox/jquery.fancybox.css" />
	<link rel="stylesheet" type="text/css" media="print" href="{$projectUrls.projectCSS}print.css" />

    <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}ie.css" />
    <![endif]-->
    <!--[if IE 8]>
        <link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}ie7-8.css" />
    <![endif]-->
    <!--[if IE 7]>
        <link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}ie7.css" />
    <![endif]-->


	<script type="text/javascript" src="{$baseUrl}app/vendor/raphael/raphael.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/vendor/bundle.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.shrinkText.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.prettyDialog.js"></script>
    <script type="text/javascript" src="{$baseUrl}app/javascript/inline_templates.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/traits.js"></script>



{if $javascriptsToLoad}
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
{/if}
	<script type="text/javascript" src="{$baseUrl}app/javascript/orchid.js"></script>
</head>
