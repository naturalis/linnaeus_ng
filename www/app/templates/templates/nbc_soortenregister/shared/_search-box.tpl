<input
    type="search"
    name="search"
    id="search"
    class="search-box"
    value="{if $search}{$search}{else}{t}Search...{/t}{/if}"
    onkeydown="setSearchKeyed(true);"
    onblur="setSearchKeyed(false);"
    onfocus="onSearchBoxSelect()" 
    results="5" 
    autosave="linnaeus_ng" />
<img onclick="doSearch()" src="{$session.app.project.urls.systemMedia}search.gif" class="search-icon" />