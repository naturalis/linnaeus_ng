<body id="body"><form method="post" action="" id="theForm" onsubmit="return checkForm();"><div id="body-container">
<div id="header-container">
	<div id="image">
{if $session.project.logo}
	<a href="{$session.project.urls.project_start}"><img src="{$session.project.urls.project_media}{$session.project.logo|escape:'url'}" id="project-logo" /></a>
{/if}
	</div>
	<div id="title">
	{if !$session.project.logo}<a href="{$session.project.urls.project_start}">{$session.project.title}</a>{/if}
	{$session.project.title}
	</div>
</div>