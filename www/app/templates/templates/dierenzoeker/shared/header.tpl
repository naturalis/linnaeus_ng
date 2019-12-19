<!DOCTYPE html>
<html>
    <head>
        <title>Dierenzoeker</title>

	<link rel="image_src" href="{$projectUrls.systemMedia}dierenzoeker-logo.png" />

	<meta name="description" property="og:description" content="Zie je een dier in je huis of tuin en weet je niet wat het is? Kijk goed en ontdek het in de Dierenzoeker! Beschikbaar als website en app."/>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="image_src" href="{$projectUrls.systemMedia}dierenzoeker-logo.png" />
	<link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}basics.css" />
	<link href="{$projectUrls.systemMedia}favicon.ico" type="image/x-icon" rel="icon" />
    <link href="{$projectUrls.systemMedia}favicon.ico" type="image/x-icon" rel="shortcut icon" />        
	<link rel="stylesheet" type="text/css" media="screen" title="default" href="{$projectUrls.projectCSS}../css/inline_templates.css" />
	<script type="text/javascript" src="{$baseUrl}app/vendor/bundle.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/inline_templates.js"></script>
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

	<link rel="stylesheet" type="text/css" href="{$baseUrl}app/vendor/fancybox/jquery.fancybox.min.css">

</head>

{if $deviceInfo.isMobile}
<style>
.mobile.outer {
	background-color:#25aad5;
	height:180px;
	width:100%;
}

.mobile .inner {
	display: inline-block;;
	background-color:#25aad5;	
	height:100%;
	color:white;
	text-align:center;
	padding-top:35px;
	font-size: 2.1em;
	line-height: 1.2em;
	width:76%;
}
.mobile a {
	color: #fff;
}

.mobile a.small {
	font-size: 0.8em;
}

.support {
	background-color:#25aad5;
	width:10%;
	display: inline-block;
	text-align: center;
}

.close {
	display: inline-block;
	background-color:#25aad5;
	font-size: 2em;
	font-weight: bold;
    border-radius: 15px;
    padding: 2px 5px 7px 5px; 
    width: 40px;
    height: 40px;
    border: 1px solid #ddd;
    color: #fff;
    cursor: pointer;
}

</style>

<div class="mobile outer">
	<div class="support"></div>
	<div class="inner">
		<b>Hallo mobiele dierenzoeker!</b><br />
		{if $deviceInfo.isiOS}
		<a href="https://itunes.apple.com/nl/app/dierenzoeker/id699543364">
		{else}
		<a href="https://play.google.com/store/apps/details?id=nl.naturalis.dierenzoeker">
		{/if}
		Download de Dierenzoeker app voor je {if $deviceInfo.isTablet}tablet{else}telefoon{/if}</a>
	</div>
	<div class="support">
		<span class="close" title="mededeling sluiten" onclick="$('.mobile.outer').toggle(false);">&#10006;</span>
	</div>
</div>
{/if}


