<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="nl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="{$session.app.project.keywords}" />
	<meta name="description" content="{$session.app.project.description}" />
	<meta name="robots" content="all" />
	<meta name="lng-project-id" content="{$session.app.project.id}" />

    <link rel="home" title="Homepage" href="http://www.nederlandsesoorten.nl/nsr/nsr/i000000.html" />
    <link rel="help" title="Help" href="http://www.nederlandsesoorten.nl/nsr/nsr/help.html" />
    <link rel="copyright" title="Copyright statement" href="http://www.nederlandsesoorten.nl/nsr/nsr/copyright.html" />
    <link rel="search" title="Zoeken" href="http://www.nederlandsesoorten.nl/nsr/nsr/zoeken.html" />
    <link rel="Shortcut Icon" href="http://www.nederlandsesoorten.nl/sites/nsr/images/favicon.ico" type="image/x-icon" />

	<title>{$session.app.project.title|@strip_tags:false}{if $pageName}: {$pageName|@strip_tags:false}{/if}</title>
    
    <link rel="stylesheet" type="text/css" media="print" title="default" href="http://www.nederlandsesoorten.nl/sites/nsr/css/20120928_print.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="http://www.nederlandsesoorten.nl/sites/nsr/css/20120928_default.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="http://www.nederlandsesoorten.nl/sites/nsr/css/20120928_menu.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="http://www.nederlandsesoorten.nl/sites/nsr/css/20120928_layout.css" />

    <!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="http://www.nederlandsesoorten.nl/sites/nsr/css/20120928_ie7.css" />
    <![endif]-->
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="http://www.nederlandsesoorten.nl/sites/nsr/css/20120928_ie6.css" />
	{literal}
    <style type="text/css">
        .iepngfix, .iepngfix img {  
            behavior: url(http://www.nederlandsesoorten.nl/sites/nsr/images/iepngfix/iepngfix.htc); 
        }
        .iepngfix a { 
            position: relative;  /* belangrijk ivm bug AlphaImageLoader filter positionering ! */
        }
	</style>
	{/literal}
    <![endif]-->

    <link rel="stylesheet" type="text/css" media="screen" title="default" href="http://www.nederlandsesoorten.nl/sites/nsr/css/20120928_conceptcard.css">
    <!--[if lte IE 7]>
	    <link rel="stylesheet" type="text/css" media="screen" title="default" href="http://www.nederlandsesoorten.nl/sites/nsr/css/20120928_conceptcardIeOnly.css" />
    <![endif]-->
    
    <link rel="stylesheet" type="text/css" href="{$session.app.project.urls.projectCSS}prettyPhoto/prettyPhoto.css" />
	<link rel="stylesheet" type="text/css" href="{$session.app.project.urls.projectCSS}jquery-ui-1.10.0.custom.min.css" />
{if $cssToLoad}
{section name=i loop=$cssToLoad}
	<link rel="stylesheet" type="text/css" href="{$cssToLoad[i]}" />
{/section}
{/if}
    
    <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="{$session.app.project.urls.projectCSS}ie.css" />
    <![endif]-->
    <!--[if IE 8]>
        <link rel="stylesheet" type="text/css" href="{$session.app.project.urls.projectCSS}ie7-8.css" />
    <![endif]-->
    <!--[if IE 7]>
        <link rel="stylesheet" type="text/css" href="{$session.app.project.urls.projectCSS}ie7.css" />
    <![endif]-->
    

	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-ui/jquery-ui-1.10.0.custom.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.urlparser.2.1.1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/sprintf-0.7-beta1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/nbc_boktorren.js"></script>
	{* <script type="text/javascript" src="{$baseUrl}app/javascript/jquery.tools.min.js"></script> "unofficial" library, seems to mess up jquery-ui *}



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

<!-- customized version of prettyPhoto, spcifically altered for boktorren key; overwrites the prettyPhoto -->
<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/jquery.prettyPhoto.custom.js"></script>

</head>
