<div id="header-container">
{if $session.app.project.logo}
	<div id="image">
	<a href="{$session.app.project.urls.project_start}"><img src="{$session.app.project.urls.project_media}{$session.app.project.logo|escape:'url'}" id="project-logo" /></a>
	</div>
{/if}
	<div id="title">
	{if !$session.app.project.logo}<a href="{$session.app.project.urls.project_start}">{$session.app.project.title}</a>{else}{$session.app.project.title}{/if}
	</div>
</div>