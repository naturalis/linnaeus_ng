<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

		<title>{$session.admin.project.name}{if $session.admin.project.name!='' && $pageName != ''} - {/if}{$pageName|@strip_tags}</title>

		<link href="{$baseUrl}admin/media/system/favicon.ico" rel="shortcut icon" type="image/x-icon">
		<link href="{$baseUrl}admin/media/system/favicon.ico" rel="icon" type="image/x-icon">

		<link rel="stylesheet" type="text/css" href="editing_species_files/jquery-ui-1.css">

		<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/main.css">
		<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/admin-inputs.css">
		<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/admin-help.css">
		<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/admin.css">
		<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/prettyPhoto/prettyPhoto.css">
		<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/taxon.css">
		<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/rank-list.css">
		<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/dialog/jquery.modaldialog.css">
		<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/lookup.css">
		<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/ui.css">
		<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/jquery-ui-1.10.0.custom.min.css">

{if $cssToLoad}
		<style type="text/css" media="all">
{section name=i loop=$cssToLoad}
		@import url("{$baseUrl}admin/style/{$cssToLoad[i]}");
{/section}
		</style>
{/if}

	<script type="text/javascript" src="{$baseUrl}admin/javascript/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="{$baseUrl}admin/javascript/jquery-ui/jquery-ui-1.9.1.min.js"></script>

	<!-- script type="text/javascript" src="{$baseUrl}admin/javascript/jquery.tools.min.js"></script -->
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
{if $session.admin.system.server_addr=='127.0.0.1'}
	<link rel="stylesheet" type="text/css" href="{$baseUrl}admin/style/localhost.css">
{/if}
</head>