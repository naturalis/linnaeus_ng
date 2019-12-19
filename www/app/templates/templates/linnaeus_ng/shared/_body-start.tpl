<body id="body" class="module-{$controllerBaseName}">
<div class="site__container">
	<div class="menu__container menu__container-js">
		<object class="logo-svg" data="{$baseUrl}app/style/img/naturalis-logo-wit.svg" type="image/svg+xml">
		</object>
		{if $customTemplatePaths.main_menu}
		    {include file=$customTemplatePaths.main_menu}
		{else}
		    {include file="../shared/_main-menu.tpl"}
		{/if}
	</div>
	<div class="content__container">
		{if $customTemplatePaths.header_container}
		    {include file=$customTemplatePaths.header_container}
		{else}
		    {include file="../shared/_header-container.tpl"}
		{/if}
		<div class="scroll__container">
			<div id="container">
				
			<form method="get" action="{$smarty.server.PHP_SELF}" id="theForm"></form>
