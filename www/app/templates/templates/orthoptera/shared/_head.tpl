<!DOCTYPE html>
<!-- Sorry no IE7 support! -->
<!-- @see http://foundation.zurb.com/docs/index.html#basicHTMLMarkup -->

<!--[if IE 8]><html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8" />
<meta name="viewport" content="width=device-width" />
<meta http-equiv="ImageToolbar" content="false" />
<link rel="shortcut icon" href="http://10.42.1.191/sites/all/themes/custom/eis_theme/favicon.ico" />
<link rel="shortlink" href="/node/17" />
<meta name="Generator" content="Drupal 7 (http://drupal.org)" />
<link rel="canonical" href="/content/status-taxonomy" />
  <title>Status taxonomy | The Orthoptera of Europe</title>
  <style>
@import url("http://10.42.1.191/profiles/naturalis/modules/contrib/views/css/views.css?ngf69p");
</style>
<style>
@import url("http://10.42.1.191/profiles/naturalis/modules/contrib/ctools/css/ctools.css?ngf69p");
</style>
<style>
@import url("http://10.42.1.191/sites/all/themes/custom/eis_theme/css/eis_theme.css?ngf69p");
</style>

{if $cssToLoad}
{section name=i loop=$cssToLoad}
	<link rel="stylesheet" type="text/css" href="{$cssToLoad[i]}" />
{/section}
{/if}

    <link rel="stylesheet" type="text/css" href="{$projectUrls.projectCSS}prettyPhoto/prettyPhoto.css" />

	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.urlparser.2.1.1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/sprintf-0.7-beta1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-sortelements.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/main.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/soortenregister.js"></script>


  <!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

<!-- WILL NEED TO FIX THESE

	<title>{$session.app.project.title|@strip_tags:false}</title>

	<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/jquery.prettyPhoto.custom.js"></script>

-->
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