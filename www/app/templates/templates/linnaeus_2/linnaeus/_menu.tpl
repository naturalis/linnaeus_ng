<!--
<div id="indexNavigationPane">
{t}Central index: {/t} <input type="text" id="allLookupBox" autocomplete="off" />
<p>
{if $showSpeciesIndexMenu}
<table><tr>
<td class="category{if $subject=='Welcome'}-active{/if}" onclick="window.open('?sub=Welcome','_self');">Welcome</td>
<td class="space"></td>
<td class="category{if $subject=='Contributors'}-active{/if}" onclick="window.open('?sub=Contributors','_self');">Contributors</td>
<td class="space"></td>
<td class="category{if $subject=='About ETI'}-active{/if}" onclick="window.open('?sub=About%20ETI','_self');">About ETI</td>
</tr></table>
{/if}
</p>
</div>
-->


<div id="allNavigationPane">
<div class="navigation-icon-wrapper">

<span onclick="allLookupShowDialog()" id="contents-icon" title="{t}Contents{/t}" class="navigation-icon" />
{t}Contents{/t}</span>
{if $backlink}
    <a class="navigation-icon" id="back-icon" href="{$backlink.url}" title="{t}Back to {/t}{$backlink.name}">{t}Back{/t}</a>
{else}
    <span class="navigation-icon" id="back-icon-inactive">{t}Back{/t}</span>
{/if}

</div>
</div>