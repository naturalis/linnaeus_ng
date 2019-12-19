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
				{include file="../shared/_search-box.tpl"}
				<a href="javascript:void(0)" class="close-search close-search-js">
					<i class="ion-close-round"></i>
				</a>
			</div>
		</div>
		<div class="menu-bar__container">
			<div class="menu-bar">
				<div class="menu-toggle menu-toggle-js" href="javascript:void(0)"><div class="menu-toggle-text">MENU</div></div>
				<!-- <a class="menu-toggle menu-toggle-over-js" href="javascript:void(0)"></a> -->
				<div class="responsive-site-title">
					{if !$session.app.project.logo}
						<a href="../linnaeus/index.php">{$session.app.project.title}</a>
					{else}
						{$session.app.project.title}
					{/if}
				</div>
				<a class="search-toggle search-toggle-js" href="javascript:void(0)"></a>
			</div>
		</div>
	</div>
</div>