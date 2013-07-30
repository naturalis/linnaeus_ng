<input type="button" value="{t}< previous{/t}" {if $navList && $navList[$navCurrentId].prev.id}onclick="allNavigate({$navList[$navCurrentId].prev.id});" title="{$navList[$navCurrentId].prev.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $navList && $navList[$navCurrentId].next.id}onclick="allNavigate({$navList[$navCurrentId].next.id});" title="{$navList[$navCurrentId].next.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;
{if $isHigherTaxa}
<a href="sp_list.php" class="allLookupLink">{t}Taxon list{/t}</a>
{else}
<a href="ht_list.php" class="allLookupLink">{t}Higher taxa list{/t}</a>
{/if}
<a href="manage.php" class="allLookupLink">{t}Management{/t}</a>
&nbsp;
<a href="../utilities/search_index.php">Extensive search</a>
<span id="message-container" style="float:right"></span>