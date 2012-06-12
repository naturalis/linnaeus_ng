{include file="../shared/_header-titles.tpl"}
{include file="../shared/_search-main.tpl"}
<div id="page-main">
	<div id="content">
		<div class="introduction-img" style="background: url('{$session.app.project.urls.uploadedMedia}{$page.image.file_name|escape:'url'}');">
		</div>
		{$page.content}
	</div>
</div>