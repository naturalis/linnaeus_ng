{include file="../shared/_head.tpl"}
{include file="../shared/_body-start.tpl"}

{if $customTemplatePaths.header_container}
	{include file=$customTemplatePaths.header_container}
{else}
	{include file="../shared/_header-container.tpl"}
{/if}

{if $customTemplatePaths.main_menu}
	{include file=$customTemplatePaths.main_menu}
{else}
	{include file="../shared/_main-menu.tpl"}
{/if}

{include file="../shared/_page-start.tpl"}