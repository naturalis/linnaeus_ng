<div id="header-container">
{if $session.project.logo}
	<div id="image">
	<a href="{$session.project.urls.project_start}"><img src="{$session.project.urls.project_media}{$session.project.logo|escape:'url'}" id="project-logo" /></a>
	</div>
{/if}
	<div id="title">
	{if !$session.project.logo}<a href="{$session.project.urls.project_start}">{$session.project.title}</a>{else}{$session.project.title}{/if}
	</div>
</div>