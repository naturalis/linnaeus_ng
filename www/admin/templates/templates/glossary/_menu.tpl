<input type="button" value="{t}< previous{/t}" {if $navList && $navList[$gloss.id].prev.id}onclick="window.open('edit.php?id={$navList[$gloss.id].prev.id}','_self');" title="{$navList[$gloss.id].prev.title}"{else}disabled="disabled"{/if} />
<input type="button" value="{t}next >{/t}" {if $navList && $navList[$gloss.id].next.id}onclick="window.open('edit.php?id={$navList[$gloss.id].next.id}','_self');" title="{$navList[$gloss.id].next.title}"{else}disabled="disabled"{/if}/>
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" />
&nbsp;
<a href="edit.php">Create new</a>