{include file="../shared/_search-main-no-tabs.tpl"}
<div id="page-main">
	<div id="content" class="proze">
		{if $page.image.file_name}
			<div class="introduction-img" style="background: url('{$page.image.file_name}');"></div>
		{/if}
		{$page.content}
	</div>
</div>