<div id="page-container">
{if $controllerMenuExists}
{if $controllerBaseName}{include file="../"|cat:$controllerBaseName|cat:"/_menu.tpl"}{else}{include file="_menu.tpl"}{/if}
{/if}
{if $headerTitles}
	<div id="header-titles">
		<span id="header-title">{$headerTitles.title}</span><br />
		<span id="header-subtitle">{$headerTitles.subtitle}</span>
	</div>
{/if}