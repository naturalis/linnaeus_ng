<body id="body" class="module-{$controllerBaseName}">
{if $customTemplatePaths.header_container}
    {include file=$customTemplatePaths.header_container}
{else}
    {include file="../shared/_header-container.tpl"}
{/if}
{include file="../shared/_top-strip.tpl"}
<div id="container">
