<div id="indexNavigationPane">
{t}Central index: {/t} <input type="text" id="allLookupBox" autocomplete="off" />
<span style="margin-left:10px;cursor:pointer;" onclick="allLookupShowDialog()">contents</span>
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


