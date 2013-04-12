<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Dierenzoeker</title>
	<meta property="og:description" content="Zie je een dier in je huis of tuin, en weet je niet wat het is? Kijk goed en ontdek het in de Dierenzoeker."/>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />	<link rel="image_src" href="/app/webroot/img/dierenzoeker-logo.png" />
	<link rel="stylesheet" type="text/css" href="{$session.app.project.urls.projectCSS}basics.css" />
	<link rel="stylesheet" type="text/css" href="{$session.app.project.urls.projectCSS}jquery-ui-1.10.0.custom.min.css" />
    <link rel="stylesheet" type="text/css" href="{$session.app.project.urls.projectCSS}prettyPhoto/prettyPhoto.css" />

	<!-- link rel="stylesheet" type="text/css" href="/app/webroot/css/jquery.lightbox-0.5.css" />
	<link rel="stylesheet" type="text/css" href="/app/webroot/css/../js/fancybox/jquery.fancybox-1.3.4.css" / -->
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/dierenzoeker.js"></script>


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
