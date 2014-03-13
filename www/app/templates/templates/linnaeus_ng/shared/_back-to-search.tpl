{if $showBackToSearch && $session.app.user.search.lastResultSetIndex}
<div id="back-to-search">
	{* <a class="navigation-icon icon-nav-back" id="back-to-search-icon" href="javascript:window.open('../search/redosearch.php','_self');" title="{t}Back to{/t} {t}search results{/t}">{t}Search results{/t}</a> *}
	<a class="navigation-icon icon-nav-back" id="back-to-search-icon" href="javascript:showSearchIndex();" title="{t}Search results{/t}">{t}Search results{/t}</a>
</div>
{/if}