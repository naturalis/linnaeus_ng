<div id="allNavigationPane">
<input type="button" value="{t}< previous{/t}" {if $adjacentItems.prev}onclick="goMap({$adjacentItems.prev.id})" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $adjacentItems.next}onclick="goMap({$adjacentItems.next.id})" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;&nbsp;
<a href="compare.php">{t}Species comparison{/t}</a>
&nbsp;&nbsp;
<a href="search.php">{t}Map search{/t}</a>
&nbsp;&nbsp;
<a href="diversity.php">{t}Diversity index{/t}</a>
</div>

