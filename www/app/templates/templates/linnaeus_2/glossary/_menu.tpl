<div id="allNavigationPane">

{if $useJavascriptLinks}
<input type="button" value="{t}< previous{/t}" {if $adjacentItems.prev}onclick="goGlossaryTerm({$adjacentItems.prev.id})" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $adjacentItems.next}onclick="goGlossaryTerm({$adjacentItems.next.id})" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
{else}
{if $adjacentItems.prev}
<a href="../glossary/term.php?id={$adjacentItems.prev.id}" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}>{t}< previous{/t}</a>
&nbsp;
{/if}
{if $adjacentItems.next}
<a href="../glossary/term.php?id={$adjacentItems.next.id}" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}>{t}next >{/t}</a>
{/if}
{/if}

&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
{if $alpha}
&nbsp;&nbsp;
<span id="alphabet">
	{foreach from=$alpha key=k item=v}
	{if $letter==$v}
	<span class="letter-active">{$v}</span>
	{else}
	{if $useJavascriptLinks}
	<span class="letter" onclick="goAlpha('{$v}','index.php')">{$v}</span>
	{else}
	<a class="letter" href="index.php?letter={$v}">{$v}</a>
	{/if}
	{/if}
	{/foreach}
</span>
{/if}
</div>