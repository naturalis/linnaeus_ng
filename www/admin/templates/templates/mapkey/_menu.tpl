<input type="button" value="{t}< previous{/t}" {if $navList && $navList[$navCurrentId].prev.id}onclick="allNavigate({$navList[$navCurrentId].prev.id});" title="{$navList[$navCurrentId].prev.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $navList && $navList[$navCurrentId].next.id}onclick="allNavigate({$navList[$navCurrentId].next.id});" title="{$navList[$navCurrentId].next.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;
<a href="choose_species.php" class="allLookupLink">{t}Contents{/t}</a>
&nbsp;
<a href="management.php" class="allLookupLink">{t}Management{/t}</a>
{if $L2Maps|@count>0}
&nbsp;<a href="l2_species_show.php?id={$taxon.id}" class="allLookupLink">{t}Linnaeus 2 maps{/t}</a>
{/if}
&nbsp;
<a href="../utilities/search_index.php">Extensive search</a>

