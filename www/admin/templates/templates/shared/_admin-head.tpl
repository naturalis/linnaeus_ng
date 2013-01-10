<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- meta name="viewport" content="initial-scale=1.0, user-scalable=no" / --><!-- might be required for google map -->

	<title>{$session.admin.project.name}{if $session.admin.project.name!='' && $pageName != ''} - {/if}{$pageName|@strip_tags}</title>

	<link href="{$baseUrl}admin/media/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="{$baseUrl}admin/media/system/favicon.ico" rel="icon" type="image/x-icon" />
	<style type="text/css" media="all">
		@import url("{$baseUrl}admin/style/main.css");
		@import url("{$baseUrl}admin/style/admin-inputs.css");
		@import url("{$baseUrl}admin/style/admin-help.css");
		@import url("{$baseUrl}admin/style/admin.css");
{if $cssToLoad}
{section name=i loop=$cssToLoad}
		@import url("{$baseUrl}admin/style/{$cssToLoad[i]}");
{/section}
{/if}
{* if $session.admin.project.css_url!=''}		@import url("{$session.admin.project.css_url}");
{/if *}
	</style>

	<script type="text/javascript" src="{$baseUrl}admin/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}admin/javascript/jquery.tools.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}admin/javascript/sprintf-0.7-beta1.js"></script>
	<script type="text/javascript" src="{$baseUrl}admin/javascript/main.js"></script>
	<script type="text/javascript" src="{$baseUrl}admin/javascript/development.js"></script><!--this should be removed in production-->
{if $javascriptsToLoad}
{section name=i loop=$javascriptsToLoad.all}
{if $javascriptsToLoad.all[i]|strpos:"http:"===false && $javascriptsToLoad.all[i]|strpos:"https:"===false}
	<script type="text/javascript" src="{$baseUrl}admin/javascript/{$javascriptsToLoad.all[i]}"></script>
{else}
	<script type="text/javascript" src="{$javascriptsToLoad.all[i]}"></script>
{/if}
{/section}
{section name=i loop=$javascriptsToLoad.IE}
	<!--[if IE]><script type="text/javascript" src="{$baseUrl}admin/javascript/{$javascriptsToLoad.IE[i]}"></script><![endif]-->
{/section}
{/if}
{if $includeHtmlEditor}
{include file="../shared/tinymce-editor.tpl"}
{/if}

</head>