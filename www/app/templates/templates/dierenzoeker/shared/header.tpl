<!DOCTYPE html>
<html>
    <head>
        <title>Dierenzoeker</title>

	<link rel="image_src" href="{$projectUrls.systemMedia}dierenzoeker-logo.png" />

	<meta name="description" property="og:description" content="Zie je een dier in je huis of tuin en weet je niet wat het is? Kijk goed en ontdek het in de Dierenzoeker! Beschikbaar als website en app."/>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />	<link rel="image_src" href="{$projectUrls.systemMedia}dierenzoeker-logo.png" />
	<link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}basics.css" />
	<link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}jquery-ui-1.10.0.custom.min.css" />
    <link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}prettyPhoto/prettyPhoto.css" />
	<link href="{$projectUrls.systemMedia}favicon.ico" type="image/x-icon" rel="icon" />
    <link href="{$projectUrls.systemMedia}favicon.ico" type="image/x-icon" rel="shortcut icon" />        
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/dierenzoeker.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/backstretch.js"></script>


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

<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/jquery.prettyPhoto.custom.js"></script>

</head>
