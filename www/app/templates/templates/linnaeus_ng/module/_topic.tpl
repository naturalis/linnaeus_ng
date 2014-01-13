{include file="_search-main-no-tabs.tpl"}
<div id="page-main">
	{if !$page}No or illegal page ID specified.{/if}
	<div id="content">
		{if $page.image}
		<div class="introduction-img" style="background: url('{$projectUrls.uploadedMedia}{$page.image|escape:'url'}');">
		</div>
		{/if}
		{$page.content}
	</div>
</div>