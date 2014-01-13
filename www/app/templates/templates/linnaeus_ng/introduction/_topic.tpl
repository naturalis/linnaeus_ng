{include file="../shared/_search-main-no-tabs.tpl"}
<div id="page-main">
	<div id="content">
		{if $page.image.file_name}
			<div class="introduction-img" style="background: url('{$projectUrls.uploadedMedia}{$page.image.file_name|escape:'url'}');"></div>
		{/if}
		{$page.content}
	</div>
</div>