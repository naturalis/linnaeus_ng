<!DOCTYPE html>
<html lang="nl">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="{$session.app.project.keywords}" />
    <meta name="description" content="{$session.app.project.description}" />
    <meta name="robots" content="all" />
    <meta name="lng-project-id" content="{$session.app.project.id}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <link rel="Shortcut Icon" href="{$projectUrls.systemMedia}naturalis.ico" type="image/x-icon" />

    <title>{$session.app.project.title|@strip_tags:false}</title>

    <link rel="stylesheet" type="text/css" href="{$baseUrl}app/style/css/style.css">
    <link rel="stylesheet" type="text/css" href="{$baseUrl}app/vendor/ionicons/css/ionicons.min.css">
    <link rel="stylesheet" type="text/css" href="{$baseUrl}app/style/css/inline_templates.css">
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$baseUrl}app/vendor/prettyPhoto/css/prettyPhoto.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="{$baseUrl}app/vendor/fancybox/jquery.fancybox.css" />

    <script type="text/javascript" src="{$baseUrl}app/vendor/raphael/raphael.min.js"></script>
    <script type="text/javascript" src="{$baseUrl}app/vendor/bundle.js"></script>
    <script type="text/javascript" src="{$baseUrl}app/javascript/scrollfix.js"></script>
    <script type="text/javascript" src="{$baseUrl}app/javascript/inline_templates.js"></script>
{foreach $javascriptsToLoad.all v}
    <script type="text/javascript" src="{if $v|strpos:"http:"===false && $v|strpos:"https:"===false}{$baseUrl}app/javascript/{/if}{$v}"></script>
{/foreach}
{foreach $javascriptsToLoad.IE v}
    <!--[if IE]><script type="text/javascript" src="{$baseUrl}app/javascript/{$v}"></script><![endif]-->
{/foreach}
</head>