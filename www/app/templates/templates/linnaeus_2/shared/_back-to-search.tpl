{if $showBackToSearch && $session.app.user.search.hasSearchResults}
<div id="back-to-search">
	<a class="navigation-icon" id="back-to-search-icon" href="javascript:window.open('../search/redosearch.php','_self');" title="{t}Back to search results{/t}">{t}Search results{/t}</a>
</div>
{/if}
