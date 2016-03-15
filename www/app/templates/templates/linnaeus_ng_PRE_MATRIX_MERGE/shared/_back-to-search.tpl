{if $showBackToSearch && $session.app.user.search.lastResultSetIndex}
<div id="back-to-search">
	<a class="navigation-icon icon-nav-back" id="back-to-search-icon" href="javascript:showSearchIndex();" title="{t}Search results{/t}">{t}Search results{/t}</a>
</div>
{/if}