<div id="header-container">
{if $session.app.project.logo}
	<div id="image">
	<a href="{$session.app.project.urls.projectHome}"><img alr="{$session.app.project.title}" src="{$session.app.project.urls.projectMedia}{$session.app.project.logo|escape:'url'}" id="project-logo" /></a>
	</div>
{/if}
	<div id="title">
	{if !$session.app.project.logo}<a href="{$session.app.project.urls.projectHome}">{$session.app.project.title}</a>{else}{$session.app.project.title}{/if}
	</div>
</div>