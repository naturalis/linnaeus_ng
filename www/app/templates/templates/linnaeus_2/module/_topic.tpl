<div id="page-main">
	<div id="content">
		{if $page.image}
		<img alt="{$page.image.file_name}" id="image-full" onclick="showMedia('{$session.app.project.urls.uploadedMedia}{$page.image.file_name|escape:'url'}','{$page.topic}')" src="{$session.app.project.urls.uploadedMedia}{$page.image.file_name|escape:'url'}" />
		{/if}
		{$page.content}
	</div>
</div>
