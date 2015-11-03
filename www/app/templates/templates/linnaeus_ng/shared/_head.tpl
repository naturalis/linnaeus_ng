<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="{$session.app.project.keywords}" />
	<meta name="description" content="{$session.app.project.description}" />
	<meta name="robots" content="all" />
	<meta name="lng-project-id" content="{$session.app.project.id}" />
	<title>{$session.app.project.title|@strip_tags:false}{if $pageName}: {$pageName|@strip_tags:false}{/if}</title>
	<link rel="stylesheet" type="text/css" media="screen" href="{$projectUrls.projectCSS}yui/cssreset-min.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="{$projectUrls.projectCSS}dialog/jquery.modaldialog.css" />
{if $cssToLoad}
{foreach $cssToLoad v}
	<link rel="stylesheet" type="text/css" media="screen" href="{$v}" />
{/foreach}
{/if}
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
	<!-- script type="text/javascript" src="{$baseUrl}app/javascript/jquery-1.9.1.min.js"></script -->
	<script src="http://cdn.jquerytools.org/1.2.7/jquery.tools.min.js"></script>
 	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
 	<!-- script type="text/javascript" src="{$baseUrl}app/javascript/jquery.tools.min.js"></script -->
	<script type="text/javascript" src="{$baseUrl}app/javascript/sprintf-0.7-beta1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.urlparser.2.1.1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.shrinkText.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.prettyDialog.js"></script>

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

</head>
