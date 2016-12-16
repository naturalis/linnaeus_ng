<!DOCTYPE html>
{foreach $languages v k}{if $v.language_id == $currentLanguageId}{assign var="iso" value=$v.iso2}{/if}{/foreach}
<html lang="{$iso}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="{$session.app.project.keywords}" />
	<meta name="description" content="{$session.app.project.description}" />
	<meta name="robots" content="all" />
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
	<!-- css to load -->
	<link rel="stylesheet" type="text/css" media="screen" href="{$v}" />
{/foreach}
{/if}
	<link rel="stylesheet" type="text/css" href="{$baseUrl}app/style/css/orchids.css">
	<link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/jquery.fancybox.css" />
    <link type="text/css" rel="stylesheet" href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" media="all">
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

	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-1.9.1.min.js"></script>
	<!-- script type="text/javascript" src="{$baseUrl}app/javascript/vendor/cdn.jquerytools.org/1.2.7/jquery.tools.min.js"></script -->
 	<script type="text/javascript" src="{$baseUrl}app/javascript/vendor/code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
 	
	<script type="text/javascript" src="{$baseUrl}app/javascript/sprintf-0.7-beta1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.urlparser.2.1.1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.shrinkText.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.prettyDialog.js"></script>
    <script type="text/javascript" src="{$baseUrl}app/javascript/inline_templates.js"></script>
    <script type="text/javascript" src="{$baseUrl}app/javascript/jquery.fancybox.pack.js"></script>


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
