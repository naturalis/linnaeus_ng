<input type="button" value="{t}< previous{/t}" {if $navList && $navList[$navCurrentId].prev.id}onclick="allNavigate({$navList[$navCurrentId].prev.id});" title="{$navList[$navCurrentId].prev.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $navList && $navList[$navCurrentId].next.id}onclick="allNavigate({$navList[$navCurrentId].next.id});" title="{$navList[$navCurrentId].next.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
&nbsp;
{if $CRUDstates.can_read}
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;
{/if}
{if $CRUDstates.can_read}
<a href="contents.php" class="allLookupLink">{t}Contents{/t}</a>
&nbsp;
{/if}
{if $CRUDstates.can_create}
<a href="edit.php" class="allLookupLink">{t}Create new{/t}</a>
&nbsp;
{/if}
{if $CRUDstates.can_update}
<a href="order.php" class="allLookupLink">{t}Change page order{/t}</a>
{/if}
<span id="message-container" style="float:right"></span>

