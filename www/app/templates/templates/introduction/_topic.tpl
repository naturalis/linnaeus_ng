<div id="page-main">
	<div id="content">
		{if $page.image.thumb_name}
		<img id="image-thumb" onclick="showMedia('{$session.project.urls.project_media}{$page.image.file_name|escape:'url'}','{$page.topic}')" src="{$session.project.urls.project_thumbs}{$page.image.thumb_name|escape:'url'}" />
		{elseif $page.image.file_name}
		<img id="image-full" onclick="showMedia('{$session.project.urls.project_media}{$page.image.file_name|escape:'url'}','{$page.topic}')" src="{$session.project.urls.project_media}{$page.image.file_name|escape:'url'}" />
		{/if}
		{$page.content}
	</div>
</div>