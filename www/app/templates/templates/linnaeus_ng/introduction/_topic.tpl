{if $headerTitles.subtitle}
    <p id="header-titles-small">
		<span id="header-subtitle">: {$headerTitles.subtitle}</span>
    </p>
{/if}
<div id="page-main">
	<div id="content" class="proze">
		{if $page.image.file_name}
			{if $page.image.width eq 241 && $page.image.height eq 281}
				<div class="introduction-img" style="background: url('{$page.image.file_name}');"></div>
			{else}
				<a href="{$page.image.file_name}" title="{$page.image.title}" data-fancybox="gallery">
					<img src="{$page.image.file_name}" alt="{$page.image.title}" class="image-full introduction-img" />
				</a>
			{/if}
		{/if}
		{$page.content}
	</div>
</div>

<script>
$(document).ready(function()
{
	allLookupShowDialog();
});
</script>