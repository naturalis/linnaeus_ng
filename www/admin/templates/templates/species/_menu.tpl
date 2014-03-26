{if $adjacentTaxa}
<input type="button" value="{t}< previous{/t}" {if $adjacentTaxa.prev.id}onclick="allNavigate({$adjacentTaxa.prev.id});" title="{$adjacentTaxa.prev.label}"{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $adjacentTaxa.next.id}onclick="allNavigate({$adjacentTaxa.next.id});" title="{$adjacentTaxa.next.label}"{else}disabled="disabled"{/if} class="allLookupButton" />
{/if}
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;
<a href="list.php" class="allLookupLink">{t}Taxon list{/t}</a>
<a href="branches.php" class="allLookupLink">Taxon list v2</a>
{*if $isHigherTaxa}
<a href="ht_list.php" class="allLookupLink">{t}Higher taxa list{/t}</a>
{else}
<a href="sp_list.php" class="allLookupLink">{t}Taxon list{/t}</a>
{/if*}
<a href="manage.php" class="allLookupLink">{t}Management{/t}</a>
&nbsp;
<a href="../search/index.php">Extensive search</a>
<span id="message-container" style="float:right"></span>