{if $headerTitles}
    <p id="header-titles-small">
        <span id="header-title">
        	{$headerTitles.title}
    	</span>
    	{if $headerTitles.subtitle}
    		<span id="header-subtitle">: {$headerTitles.subtitle}</span>
		{/if}
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