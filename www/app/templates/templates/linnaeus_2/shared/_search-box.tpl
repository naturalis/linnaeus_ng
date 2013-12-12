<input
    type="search"
    name="search"
    id="search"
    class="search-box"
	placeholder="{t}Search...{/t}"
    value="{if $search.search}{$search.search}{/if}"
	onkeyup="if (event.keyCode==13){literal}{doSearch();}{/literal}"
	required
/>
<img onclick="doSearch()" src="{$projectUrls.systemMedia}search.gif" class="search-icon" />