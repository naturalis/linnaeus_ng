<html lang='nl'>
  <head>
    <title>{$session.app.project.title|@strip_tags:false}</title>
	<meta charset="utf-8">
    <link href='../../../app/style/naturalis/style.css' rel='stylesheet' title='default' type='text/css'>

{if $cssToLoad}
{section name=i loop=$cssToLoad}
	<link rel="stylesheet" type="text/css" href="{$cssToLoad[i]}" />
{/section}
{/if}

	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-1.9.1.min.js"></script>
	<!-- script type="text/javascript" src="{$baseUrl}app/javascript/jquery-ui/jquery-ui-1.10.0.custom.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.urlparser.2.1.1.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/sprintf-0.7-beta1.js"></script -->
	<script type="text/javascript" src="{$baseUrl}app/javascript/naturalis/matrix.js"></script>
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
<!-- script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/jquery.prettyPhoto.custom.js"></script -->
  </head>
