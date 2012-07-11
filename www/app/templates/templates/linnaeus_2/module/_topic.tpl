{include file="_search-main-no-tabs.tpl"}
<div id="page-main">
	<div id="content">
		{if $page.image}
		<div class="introduction-img" style="background: url('{$session.app.project.urls.uploadedMedia}{$page.image|escape:'url'}');">
		</div>
		{/if}
		{$page.content}
	</div>
</div>