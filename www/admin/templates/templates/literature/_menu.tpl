<input type="button" value="{t}< previous{/t}" {if $navList && $navList[$ref.id].prev.id}onclick="window.open('edit.php?id={$navList[$ref.id].prev.id}','_self');" title="{$navList[$ref.id].prev.title}"{else}disabled="disabled"{/if} />
<input type="button" value="{t}next >{/t}" {if $navList && $navList[$ref.id].next.id}onclick="window.open('edit.php?id={$navList[$ref.id].next.id}','_self');" title="{$navList[$ref.id].next.title}"{else}disabled="disabled"{/if}/>
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" />
&nbsp;
<a href="edit.php">Create new</a>