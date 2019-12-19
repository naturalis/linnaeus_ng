<div id="page-main">
	<div id="content" class="proze">
		{if $page.image.file_name}
			<div class="introduction-img" style="background: url('{$projectUrls.uploadedMedia}{$page.image.file_name|escape:'url'}');"></div>
		{/if}
		{$page.content}
	</div>
</div>