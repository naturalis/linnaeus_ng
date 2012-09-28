<body id="body">
{if $customTemplatePaths.header_container}
    {include file=$customTemplatePaths.header_container}
{else}
    {include file="../shared/_header-container.tpl"}
{/if}
{include file="../shared/_top-strip.tpl"}
<div id="container">
<form method="get" action="{$smarty.server.PHP_SELF}" id="theForm" onsubmit="return checkForm();">
<input type="hidden" name="rnd" value="{$rnd}" />
