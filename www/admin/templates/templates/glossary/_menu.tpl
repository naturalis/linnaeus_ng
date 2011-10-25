<input type="button" value="{t}< previous{/t}" {if $navList && $navList[$navCurrentId].prev.id}onclick="allNavigate({$navList[$navCurrentId].prev.id});" title="{$navList[$navCurrentId].prev.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $navList && $navList[$navCurrentId].next.id}onclick="allNavigate({$navList[$navCurrentId].next.id});" title="{$navList[$navCurrentId].next.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;
<a href="edit.php?action=new" class="allLookupLink">{t}Create new{/t}</a>
<p>
{if $alpha|@count==0}
{t}(no terms have been defined){/t}
{else}
{t}Click to browse:{/t}&nbsp;
{section name=i loop=$alpha}
{if $alpha[i]==$letter}
<span class="alphabet-active-letter">{$alpha[i]}</span>
{else}
<span class="alphabet-letter" onclick="$('#action').val('browse');$('#letter').val('{$alpha[i]}');$('#theForm').submit();">{$alpha[i]}</span>
{/if}
{/section}
{/if}
</p>