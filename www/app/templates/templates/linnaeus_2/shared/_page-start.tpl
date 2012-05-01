<div id="main-body">
<div id="page-container">

{if $customTemplatePaths.main_menu}
    {include file=$customTemplatePaths.main_menu}
{else}
    {include file="../shared/_main-menu.tpl"}
{/if}

{if $headerTitles}
	<div id="header-titles">
		<span id="header-title">{$headerTitles.title}</span><br />
		<span id="header-subtitle">{$headerTitles.subtitle}</span>
	</div>
{/if}