<div id="search-main">
<!--
    <input
        type="text"
        name="search"
        id="search"
        class="search-input-shaded"
        value="{if $search}{$search}{else}{t}enter search term{/t}{/if}"
        onkeydown="setSearchKeyed(true);"
        onblur="setSearchKeyed(false);"
        onfocus="onSearchBoxSelect()" />
    <input type="image" src="{$session.app.project.urls.systemMedia}search.gif" style="border:0" />
-->
    <input
        type="search"
        name="search"
        id="search"
        class="search-box"
        value="{if $search}{$search}{else}{t}enter search term{/t}{/if}"
        onkeydown="setSearchKeyed(true);"
        onblur="setSearchKeyed(false);"
        onfocus="onSearchBoxSelect()" 
        results="5" 
        autosave="linnaeus_ng" />
    <input type="image" src="{$session.app.project.urls.systemMedia}search.gif" class="search-icon" />
 </div>