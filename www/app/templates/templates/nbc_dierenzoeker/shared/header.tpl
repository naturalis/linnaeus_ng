<!DOCTYPE html>
<html>
    <head>
        <title>Dierenzoeker</title>

	<link rel="image_src" href="{$projectUrls.systemMedia}dierenzoeker-logo.png" />

	<meta property="og:description" content="Zie je een dier in je huis of tuin, en weet je niet wat het is? Kijk goed en ontdek het in de Dierenzoeker."/>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />	<link rel="image_src" href="{$projectUrls.systemMedia}dierenzoeker-logo.png" />
	<link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}basics.css" />
	<link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}jquery-ui-1.10.0.custom.min.css" />
    <link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}prettyPhoto/prettyPhoto.css" />
	<link href="{$projectUrls.projectMedia}favicon.ico" type="image/x-icon" rel="icon" />
    <link href="{$projectUrls.projectMedia}favicon.ico" type="image/x-icon" rel="shortcut icon" />        
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/dierenzoeker.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/backstretch.js"></script>


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

<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/jquery.prettyPhoto.custom.js"></script>

</head>
