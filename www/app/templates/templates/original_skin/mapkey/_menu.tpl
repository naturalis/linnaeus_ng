<div id="allNavigationPane">
{if $useJavascriptLinks}
<input type="button" value="{t}< previous{/t}" {if $adjacentItems.prev}onclick="goMap({$adjacentItems.prev.id})" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $adjacentItems.next}onclick="goMap({$adjacentItems.next.id})" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
{else}
{if $allLookupNavigateOverrideUrl}
{assign var=url value=$allLookupNavigateOverrideUrl}
{else}
{assign var=url value='../mapkey/examine_species.php?id='}
{/if}
{if $adjacentItems.prev}
<a href="{$url}{$adjacentItems.prev.id}" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}>{t}< previous{/t}</a>
&nbsp;
{/if}
{if $adjacentItems.next}
<a href="{$url}{$adjacentItems.next.id}" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}>{t}next >{/t}</a>
{/if}
{/if}

&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
<span style="margin-left:10px;cursor:pointer;" onclick="allLookupShowDialog()">contents</span>
&nbsp;&nbsp;
<a href="compare.php{if $mapId}?mapId={$mapId}{/if}">{t}Species comparison{/t}</a>
&nbsp;&nbsp;
<a href="search.php{if $mapId}?mapId={$mapId}{/if}">{t}Map search{/t}</a>
&nbsp;&nbsp;
<a href="diversity.php{if $mapId}?mapId={$mapId}{/if}">{t}Diversity index{/t}</a>
</div>

