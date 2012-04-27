<div id="allNavigationPane">

{if $useJavascriptLinks}
<input type="button" value="{t}< previous{/t}" {if $adjacentItems.prev}onclick="goLiterature({$adjacentItems.prev.id})" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $adjacentItems.next}onclick="goLiterature({$adjacentItems.next.id})" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
{else}
{if $adjacentItems.prev}
<a href="../literature/reference.php?id={$adjacentItems.prev.id}" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}>{t}< previous{/t}</a>
&nbsp;
{/if}
{if $adjacentItems.next}
<a href="../literature/reference.php?id={$adjacentItems.next.id}" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}>{t}next >{/t}</a>
{/if}
{/if}

&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
<span style="margin-left:10px;cursor:pointer;" onclick="allLookupShowDialog()">contents</span>
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