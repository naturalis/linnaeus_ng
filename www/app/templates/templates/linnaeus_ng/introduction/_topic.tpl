{if $headerTitles.subtitle}
    <p id="header-titles-small">
		<span id="header-subtitle">: {$headerTitles.subtitle}</span>
    </p>
{/if}
<div id="page-main">
	<div id="content" class="proze">
		{if $page.image.file_name}
			<div class="introduction-img" style="background: url('{$page.image.file_name}');"></div>
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