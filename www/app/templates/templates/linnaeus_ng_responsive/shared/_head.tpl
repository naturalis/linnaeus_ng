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
		<link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/ionicons.min.css" />
		<link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/jquery.fancybox.css" />
    <!-- <link rel="stylesheet" type="text/css" media="print" title="default" href="{$projectUrls.projectCSS}20120928_print.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}20120928_default.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}20120928_menu.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}20120928_layout.css" /> -->


    <!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}20120928_ie7.css" />
    <![endif]-->
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}20120928_ie6.css" />
	{literal}
    <style type="text/css">
        .iepngfix, .iepngfix img {  
            behavior: url({$projectUrls.projectCSS}iepngfix.htc); 
        }
        .iepngfix a { 
            position: relative;  /* belangrijk ivm bug AlphaImageLoader filter positionering ! */
        }
	</style>
	{/literal}
    <![endif]-->

    <!-- <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}20120928_conceptcard.css"> -->
    <!--[if lte IE 7]>
	    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}20120928_conceptcardIeOnly.css" />
    <![endif]-->
    <!-- <link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}prettyPhoto/prettyPhoto.css" /> -->
	<!-- <link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}jquery-ui-1.10.0.custom.min.css" /> -->
    <!-- <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}morris.css" /> -->

<!-- {if $cssToLoad}
{section name=i loop=$cssToLoad}
	<link rel="stylesheet" type="text/css" href="{$cssToLoad[i]}" />
{/section}
{/if} -->
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
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.urlparser.2.1.1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/sprintf-0.7-beta1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-sortelements.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/main.js"></script>
	
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.flexslider-min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/soortenregister.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/soortenregister.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/morris.js-0.4.3/morris.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/raphael/raphael-min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.fancybox.pack.js"></script>


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
	
	<!-- customized version of prettyPhoto -->
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/jquery.prettyPhoto.custom.js"></script>
</head>