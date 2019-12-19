{if $googleAnalyticsCode}
	{include file="../shared/_google_analytics_code.tpl"}
{/if}

{if $customTemplatePaths.footer}
	{include file=$customTemplatePaths.footer}
{else}
	{include file="../shared/_footer.tpl"}
{/if}