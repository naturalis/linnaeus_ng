<div id="page-main">
	{if !$page}No or illegal page ID specified.{/if}
	<div id="content">

        {if $headerTitles}
            <p id="header-titles-small">
                {if $headerTitles.subtitle}<span id="header-subtitle">{$headerTitles.subtitle}</span>{/if}
            </p>
        {/if}

		{if $page.image}
		<div class="introduction-img" style="background: url('{$page.image}');">
		</div>
		{/if}
		{$page.content}
	</div>
</div>