<!-- <div id="header-container">
	<div id="title">
	{if !$session.app.project.logo}<a href="../linnaeus/index.php">{$session.app.project.title}</a>{else}{$session.app.project.title}{/if}
	</div>
</div> -->

<div class="menu-title-bar__container">
	<div class="site-title">
		{if !$session.app.project.logo}
			<a href="../linnaeus/index.php">{$session.app.project.title}</a>
		{else}
			{$session.app.project.title}
		{/if}
	</div>
	<div class="menu-search-bar__container">
		<div class="search-bar__container">
			<div class="search-bar">
				<input type="text" name="search" id="search" class="search-box" placeholder="{t}Search...{/t}" value="{if $search.search}{$search.search}{/if}" onkeyup="if (event.keyCode==13) { doSearch(); }" required />
				<a href="javascript:void(0)" class="close-search close-search-js">
					<i class="ion-close-round"></i>
				</a>
            	<img onclick="doSearch()" src="{$projectUrls.systemMedia}search.gif" class="search-icon" />
			</div>
		</div>
		<div class="menu-bar__container">
			<div class="menu-bar">
				<a class="menu-toggle menu-toggle-js" href="javascript:void(0)"></a>
				<!-- <a class="menu-toggle menu-toggle-over-js" href="javascript:void(0)"></a> -->
				<a class="search-toggle search-toggle-js" href="javascript:void(0)"></a>
			</div>
		</div>
	</div>
</div>