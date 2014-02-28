<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="{$session.app.project.keywords}" />
	<meta name="description" content="{$session.app.project.description}" />
	<meta name="robots" content="all" />
	<meta name="lng-project-id" content="{$session.app.project.id}" />
	<title>{$session.app.project.title|@strip_tags:false}{if $pageName}: {$pageName|@strip_tags:false}{/if}</title>
	<link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}yui/cssreset-min.css" />
	<link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}dialog/jquery.modaldialog.css" />
    <link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}prettyPhoto/prettyPhoto.css" />
{if $cssToLoad}
{section name=i loop=$cssToLoad}
	<link rel="stylesheet" type="text/css" href="{$cssToLoad[i]}" />
{/section}
{/if}
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
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.tools.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/sprintf-0.7-beta1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.urlparser.2.1.1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.fittext.js"></script>

{if $javascriptsToLoad}
{section name=i loop=$javascriptsToLoad.all}
{if $javascriptsToLoad.all[i]|strpos:"http:"===false && $javascriptsToLoad.all[i]|strpos:"https:"===false}
	<script type="text/javascript" src="{$baseUrl}app/javascript/{$javascriptsToLoad.all[i]}"></script>
{else}
	<script type="text/javascript" src="{$javascriptsToLoad.all[i]}"></script>
{/if}
{/section}
{section name=i loop=$javascriptsToLoad.IE}
	<!--[if IE]><script type="text/javascript" src="{$baseUrl}app/javascript/{$javascriptsToLoad.IE[i]}"></script><![endif]-->
{/section}
{/if}

</head>
