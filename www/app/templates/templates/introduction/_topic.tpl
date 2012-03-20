<div id="page-main">
	<div id="content">
		{if $page.image.thumb_name}
		<img alt="{$page.image.thumb_name}" id="image-thumb" onclick="showMedia('{$session.app.project.urls.project_media}{$page.image.file_name|escape:'url'}','{$page.topic}')" src="{$session.app.project.urls.project_thumbs}{$page.image.thumb_name|escape:'url'}" />
		{elseif $page.image.file_name}
		<img alt="{$page.image.file_name}" id="image-full" onclick="showMedia('{$session.app.project.urls.project_media}{$page.image.file_name|escape:'url'}','{$page.topic}')" src="{$session.app.project.urls.project_media}{$page.image.file_name|escape:'url'}" />
		{/if}
		{$page.content}
	</div>
</div>