<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" CONTENT="{$session.app.project.keywords}">
	<meta name="description" CONTENT="{$session.app.project.description}">
	<meta name="ROBOTS" CONTENT="ALL">
	<meta name="lng-project-id" content="{$session.app.project.id}">
	<title>{$session.app.project.title}{if $pageName}: {$pageName}{/if}</title>
	<style type="text/css" media="all">
	    @import url("{$session.app.project.urls.projectCSS}yui/cssreset-min.css");
{if $cssToLoad}
{section name=i loop=$cssToLoad}
	    @import url("{$cssToLoad[i]}");
{/section}
{/if}
	</style>
        <!--[if lt IE 9]>
            <style type="text/css">@import "{$session.app.project.urls.projectCSS}ie7-8.css";</style> 
        <![endif]-->
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/jquery.tools.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/sprintf-0.7-beta1.js"></script>
	
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
