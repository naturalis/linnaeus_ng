<div id="page-main">

	<div id="content">
    	<p>
            <h1>{$headerTitles.title}</h1>
            <h3>{$headerTitles.subtitle}</h3>
		</p>

		{if $page.image}
		<div class="introduction-img" style="background: url('{$page.image}');"></div>
		{/if}
		{$page.content}
	</div>
</div>