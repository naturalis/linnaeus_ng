<div id="allNavigationPane">
<input type="button" value="{t}< previous{/t}" {if $adjacentItems.prev}onclick="goGlossaryTerm({$adjacentItems.prev.id})" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $adjacentItems.next}onclick="goGlossaryTerm({$adjacentItems.next.id})" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
{if $alpha}
&nbsp;&nbsp;
<span id="alphabet">
	{foreach from=$alpha key=k item=v}
	{if $letter==$v}
	<span class="letter-active">{$v}</span>
	{else}
	<span class="letter" onclick="goAlpha('{$v}','index.php')">{$v}</span>
	{/if}
	{/foreach}
</span>
{/if}
</div>