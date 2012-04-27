<div id="allNavigationPane">
{if $useJavascriptLinks}
<input type="button" value="{t}< previous{/t}" {if $adjacentItems.prev}onclick="goTaxon({$adjacentItems.prev.id},{$activeCategory})" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $adjacentItems.next}onclick="goTaxon({$adjacentItems.next.id},{$activeCategory})" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
{else}
{if $adjacentItems.prev}<a href="../species/taxon.php?id={$adjacentItems.prev.id}&cat={$activeCategory}" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}>{t}< previous{/t}</a>&nbsp;&nbsp;{/if}
{if $adjacentItems.next}
<a href="../species/taxon.php?id={$adjacentItems.next.id}&cat={$activeCategory}" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}>{t}next >{/t}</a>{/if}
{/if}
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
<span style="margin-left:10px;cursor:pointer;" onclick="allLookupShowDialog()">contents</span>
</div>